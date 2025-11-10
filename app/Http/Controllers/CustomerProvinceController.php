<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Outlet;
use App\Models\Region;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Helpers\AppHelper;
use App\Models\CustomerProvince;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomerProvinceExport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CustomerProvinceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('type.permission:view customer province', ['only' => ['index']]);
        $this->middleware('type.permission:create customer province', ['only' => ['create', 'store']]);
        $this->middleware('type.permission:update customer province', ['only' => ['edit', 'update']]);
        $this->middleware('type.permission:delete customer province', ['only' => ['destroy']]);
    }
    public function index()
    {
        $loggedInUser = auth()->check() ? auth()->user() : null;

        // Initialize customer query with user relationship
        $query = CustomerProvince::with('user', 'outlet', 'region');

        if ($loggedInUser) {
            $loggedInUserRole = $loggedInUser->role_id;
            $loggedInUserId = $loggedInUser->id;
            $loggedInUserType = $loggedInUser->type;

            // Collect user IDs to filter customers
            $userIds = [$loggedInUserId]; // Always include own customers

            // Define allowed user types for subordinates
            $allowedTypes = [AppHelper::SALE, AppHelper::SE];

            if ($loggedInUserType == AppHelper::ALL || in_array($loggedInUserRole, [
                AppHelper::USER_SUPER_ADMIN,
                AppHelper::USER_ADMINISTRATOR,
                AppHelper::USER_ADMIN,
                AppHelper::USER_DIRECTOR
            ])) {
                // Users with type ALL or roles Super Admin, Admin, Director see all customers
                // No additional filtering needed
            } elseif ($loggedInUserRole == AppHelper::USER_MANAGER) {
                // Manager sees customers of RSMs, Supervisors, ASMs, Employees under them
                $managedUserIds = \App\Models\User::where(function ($q) use ($loggedInUserId) {
                    $q->where('manager_id', $loggedInUserId)
                        ->orWhere('rsm_id', $loggedInUserId)
                        ->orWhere('sup_id', $loggedInUserId)
                        ->orWhere('asm_id', $loggedInUserId);
                })->whereIn('type', $allowedTypes)
                    ->pluck('id')
                    ->toArray();
                $userIds = array_merge($userIds, $managedUserIds);
            } elseif ($loggedInUserRole == AppHelper::USER_RSM) {
                // RSM sees customers of Supervisors, ASMs, Employees under them
                $managedUserIds = \App\Models\User::where(function ($q) use ($loggedInUserId) {
                    $q->where('rsm_id', $loggedInUserId)
                        ->orWhere('sup_id', $loggedInUserId)
                        ->orWhere('asm_id', $loggedInUserId);
                })->whereIn('type', $allowedTypes)
                    ->pluck('id')
                    ->toArray();
                $userIds = array_merge($userIds, $managedUserIds);
            } elseif ($loggedInUserRole == AppHelper::USER_SUP) {
                // Supervisor sees customers of ASMs, Employees under them
                $managedUserIds = \App\Models\User::where(function ($q) use ($loggedInUserId) {
                    $q->where('sup_id', $loggedInUserId)
                        ->orWhere('asm_id', $loggedInUserId);
                })->whereIn('type', $allowedTypes)
                    ->pluck('id')
                    ->toArray();
                $userIds = array_merge($userIds, $managedUserIds);
            } elseif ($loggedInUserRole == AppHelper::USER_ASM) {
                // ASM sees customers of Employees under them
                $managedUserIds = \App\Models\User::where('asm_id', $loggedInUserId)
                    ->whereIn('type', $allowedTypes)
                    ->pluck('id')
                    ->toArray();
                $userIds = array_merge($userIds, $managedUserIds);
            }

            // Apply user ID filter unless Super Admin, Admin, Director, or type ALL
            if (!($loggedInUserType == AppHelper::ALL || in_array($loggedInUserRole, [
                AppHelper::USER_SUPER_ADMIN,
                AppHelper::USER_ADMIN,
                AppHelper::USER_DIRECTOR
            ]))) {
                $query->whereIn('user_id', array_unique($userIds));
            }

            // Ensure customers belong to users with allowed types (except for ALL/Super Admin/Admin/Director)
            if (!($loggedInUserType == AppHelper::ALL || in_array($loggedInUserRole, [
                AppHelper::USER_SUPER_ADMIN,
                AppHelper::USER_ADMIN,
                AppHelper::USER_DIRECTOR
            ]))) {
                $query->whereHas('user', function ($q) use ($allowedTypes) {
                    $q->whereIn('type', $allowedTypes);
                });
            }
        } else {
            // No authenticated user, return no customers
            $query->where('id', 0);
        }

        $data['customers'] = $query->orderBy('id', 'desc')->get();

        return view('backend.customer-province.list', $data);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['customer_province'] = null;
        $data['depoName'] = [];
        $data['customerType'] = AppHelper::CUSTOMER_TYPE_PROVINCE;
        $data['regions'] = Region::where('active_status', 1)
            ->select('region_name', 'rg_manager_kh', 'rg_manager_en', 'se_code', 'id')
            ->orderBy('region_name', 'asc')
            ->get()
            ->groupBy('rg_manager_kh');

        $data['outlets'] = Outlet::where('active_status', 1)->orderBy('id', 'desc')->get();
        return view('backend.customer-province.add', $data);
    }

    public function getDeposByArea(Request $request)
    {
        $areaId = $request->query('area_id');
        $authUser = auth()->user();

        if (!$areaId) {
            return response()->json([], 400);
        }

        $query = Outlet::where('area_id', $areaId)->where('active_status', 1);

        if (in_array($authUser->type, [AppHelper::SALE, AppHelper::SE])) {
            // Filter depos accessible by SALE or SE users
            $query->where('user_type', $authUser->type);
        }

        $depos = $query->pluck('name', 'id')->toArray();
        return response()->json($depos);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Get all valid area IDs (numeric keys)
        $areaIds = [];
        foreach (AppHelper::getAreas() as $group) {
            $areaIds = array_merge($areaIds, array_keys($group));
        }

        // Validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'area' => 'required|in:' . implode(',', $areaIds),
            'depo_id' => 'required|exists:outlets,id',
            'customer_type' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'outlet_photo' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            'outlet_photo_base64' => 'nullable|string',
        ];

        // Make outlet_photo required if neither file nor base64 is provided
        if (!$request->hasFile('outlet_photo') && !$request->filled('outlet_photo_base64')) {
            $rules['outlet_photo'] = 'required|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $userType = auth()->user()->type;
        // Determine prefix based on user type
        switch ($userType) {
            case AppHelper::SALE:
                $prefix = 'CPP';
                break;
            case AppHelper::SE:
                $prefix = 'CPV';
                break;
            default:
                $prefix = 'CUS'; // fallback/default prefix
                break;
        }

        // Generate unique customer code
        $lastCustomer = CustomerProvince::orderBy('id', 'desc')->first();
        $lastCodeNumber = $lastCustomer && $lastCustomer->code ? (int) substr($lastCustomer->code, 4) : 0;
        $newCodeNumber = $lastCodeNumber + 1;
        $code = $prefix . '-' . str_pad($newCodeNumber, 5, '0', STR_PAD_LEFT);

        // Prepare data for storage
        $data = [
            'user_id' => auth()->user()->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'area_id' => $request->area,
            'depo_id' => $request->depo_id,
            'customer_type' => $request->customer_type,
            'user_type' => auth()->user()->type,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'city' => $request->city,
            'country' => $request->country,
            'code' => $code,
        ];

        // Handle outlet photo file upload if exists
        if ($request->hasFile('outlet_photo')) {
            $file = $request->file('outlet_photo');
            $fileName = 'outlet_' . time() . '_' . md5($file->getClientOriginalName()) . '.' . $file->extension();
            $filePath = 'Uploads/' . $fileName;
            Storage::disk('public')->put($filePath, file_get_contents($file));
            $data['outlet_photo'] = $filePath;
        }
        // Handle outlet base64 image if provided and no file is uploaded
        elseif ($request->filled('outlet_photo_base64')) {
            $image = str_replace('data:image/png;base64,', '', $request->outlet_photo_base64);
            $image = str_replace(' ', '+', $image);
            $imageData = base64_decode($image);
            $fileName = 'Uploads/outlet_' . time() . '_' . Str::random(10) . '.png';
            Storage::disk('public')->put($fileName, $imageData);
            $data['outlet_photo'] = $fileName;
        }

        try {
            CustomerProvince::create($data);

            if ($request->has('saveandcontinue')) {
                return redirect()->route('cp.create')->with('success', __('Customer created successfully.'));
            }
            return redirect()->route('cp.index')->with('success', __('Customer created successfully.'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to create customer: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $report = CustomerProvince::with('user', 'outlet', 'region')->find($id);
        if (!$report) {
            return response()->json(['error' => 'Report not found'], 404);
        }

        $user = $report->user;
        $employee_name = 'N/A';

        if ($user) {
            $employee_name = auth()->user()->user_lang == 'en'
                ? $user->getFullNameLatinAttribute()
                : $user->getFullNameAttribute();
        }

        return response()->json([
            'report' => [
                'region' => $report->region->region_name . ' '. $report->region->se_code,
                'depot_code' => $report->outlet->code,
                'depot_name' => $report->outlet->name,

                'customer_code' => $report->code,
                'customer_name' => $report->name,
                'phone_number' => $report->phone,
                'outlet_photo' => $report->outlet_photo ? asset('storage/' . $report->outlet_photo) : asset('images/post-placeholder.jpg'),
                'customer_type' => AppHelper::CUSTOMER_TYPE_PROVINCE[$report->customer_type] ?? 'N/A',

                'created_by' => session('user_lang') == 'en'
                                ? $report->user->family_name_latin . ' ' . $report->user->name_latin
                                : $report->user->family_name . ' ' . $report->user->name,


                'date' => Carbon::parse($report->created_at)->format('d M Y'),
                'city' => $report->city,
                'country' => $report->country,
                'lat' => $report->latitude,
                'long' => $report->longitude,
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        $data['customer_province'] = CustomerProvince::findOrFail($id);
        $data['depoName'] = [];
        $data['customerType'] = AppHelper::CUSTOMER_TYPE_PROVINCE;
        $data['regions'] = Region::where('active_status', 1)
            ->select('region_name', 'rg_manager_kh', 'rg_manager_en', 'se_code', 'id')
            ->orderBy('region_name', 'asc')
            ->get()
            ->groupBy('rg_manager_kh');
        $data['outlets'] = Outlet::where('active_status', 1)->orderBy('id', 'desc')->get();
        return view('backend.customer-province.add', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CustomerProvince $customer)
    {
        // Get all valid area IDs (numeric keys)
        // $areaIds = [];
        // foreach (AppHelper::getAreas() as $group) {
        //     $areaIds = array_merge($areaIds, array_keys($group));
        // }

        // Validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'area' => 'required|string',
            'depo_id' => 'required|exists:outlets,id',
            'customer_type' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'outlet_photo' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            'outlet_photo_base64' => 'nullable|string',
        ];
        // dd($customer->outlet_photo);
        // Make outlet_photo required if neither file nor base64 is provided and no existing photo exists
        if (!$request->hasFile('outlet_photo') && !$request->filled('outlet_photo_base64') && !$customer->outlet_photo && empty($request->old_outlet_photo)) {
            $rules['outlet_photo'] = 'required|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Prepare data for update
        $data = [
            // 'user_id' => auth()->user()->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'area_id' => $request->area,
            'depo_id' => $request->depo_id,
            'customer_type' => $request->customer_type,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'city' => $request->city,
            'country' => $request->country,
        ];

        // Generate code if none exists
        if (!$customer->code) {
            $userType = auth()->user()->type;
            // Determine prefix based on user type
            switch ($userType) {
                case AppHelper::SALE:
                    $prefix = 'CPP';
                    break;
                case AppHelper::SE:
                    $prefix = 'CPV';
                    break;
                default:
                    $prefix = 'CUS'; // fallback/default prefix
                    break;
            }
            // Generate unique customer code
            $lastCustomer = CustomerProvince::orderBy('id', 'desc')->first();
            $lastCodeNumber = $lastCustomer && $lastCustomer->code ? (int) substr($lastCustomer->code, 4) : 0;
            $newCodeNumber = $lastCodeNumber + 1;
            $data['code'] = $prefix . '-' . str_pad($newCodeNumber, 5, '0', STR_PAD_LEFT);
        }

        // Handle outlet photo file upload if exists
        if ($request->hasFile('outlet_photo')) {
            // Delete existing photo if it exists
            if ($customer->outlet_photo && Storage::disk('public')->exists($customer->outlet_photo)) {
                Storage::disk('public')->delete($customer->outlet_photo);
            }
            $file = $request->file('outlet_photo');
            $fileName = 'outlet_' . time() . '_' . md5($file->getClientOriginalName()) . '.' . $file->extension();
            $filePath = 'Uploads/' . $fileName;
            Storage::disk('public')->put($filePath, file_get_contents($file));
            $data['outlet_photo'] = $filePath;
        }
        // Handle outlet base64 image if provided and no file is uploaded
        elseif ($request->filled('outlet_photo_base64')) {
            // Delete existing photo if it exists
            if ($customer->outlet_photo && Storage::disk('public')->exists($customer->outlet_photo)) {
                Storage::disk('public')->delete($customer->outlet_photo);
            }
            $image = str_replace('data:image/png;base64,', '', $request->outlet_photo_base64);
            $image = str_replace(' ', '+', $image);
            $imageData = base64_decode($image);
            $fileName = 'Uploads/outlet_' . time() . '_' . Str::random(10) . '.png';
            Storage::disk('public')->put($fileName, $imageData);
            $data['outlet_photo'] = $fileName;
        }
        // Clear outlet_photo if existing photo is deleted and no new photo is provided
        elseif ($request->has('delete_outlet_photo') && $customer->outlet_photo) {
            if (Storage::disk('public')->exists($customer->outlet_photo)) {
                Storage::disk('public')->delete($customer->outlet_photo);
            }
            $data['outlet_photo'] = null;
        }

        try {
            $customer->update($data);
            return redirect()->route('cp.index')->with('success', __('Customer updated successfully.'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update customer: ' . $e->getMessage())->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function export()
    {
        return Excel::download(new CustomerProvinceExport(), 'customers_province_' . now()->format('Y_m_d_His') . '.xlsx');
    }
}

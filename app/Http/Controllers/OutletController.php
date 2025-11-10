<?php

namespace App\Http\Controllers;

use App\Exports\OutletExport;
use Exception;
use App\Models\Outlet;
use App\Models\Region;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Helpers\AppHelper;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OutletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('type.permission:view depot management', ['only' => ['index']]);
        $this->middleware('type.permission:create depot management', ['only' => ['create', 'store']]);
        $this->middleware('type.permission:edit depot management', ['only' => ['update', 'edit']]);
        $this->middleware('type.permission:delete depot management', ['only' => ['destroy']]);
    }
    public function index(Request $request)
    {
        try {
            $loggedInUser = auth()->check() ? auth()->user() : null;

            $query = Outlet::with('user', 'region');

            if ($loggedInUser) {
                $loggedInUserRole = $loggedInUser->role_id;
                $loggedInUserId = $loggedInUser->id;
                $loggedInUserType = $loggedInUser->type;

                // Collect user IDs to filter depos
                $userIds = [$loggedInUserId]; // Always include own depos

                // Define allowed user types for subordinates
                $allowedTypes = [AppHelper::SALE, AppHelper::SE];

                if ($loggedInUserType == AppHelper::ALL || in_array($loggedInUserRole, [
                    AppHelper::USER_SUPER_ADMIN,
                    AppHelper::USER_ADMINISTRATOR,
                    AppHelper::USER_ADMIN,
                    AppHelper::USER_DIRECTOR
                ])) {
                    // No additional filtering needed
                } elseif ($loggedInUserRole == AppHelper::USER_MANAGER) {
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
                    $managedUserIds = \App\Models\User::where(function ($q) use ($loggedInUserId) {
                        $q->where('rsm_id', $loggedInUserId)
                            ->orWhere('sup_id', $loggedInUserId)
                            ->orWhere('asm_id', $loggedInUserId);
                    })->whereIn('type', $allowedTypes)
                        ->pluck('id')
                        ->toArray();
                    $userIds = array_merge($userIds, $managedUserIds);
                } elseif ($loggedInUserRole == AppHelper::USER_SUP) {
                    $managedUserIds = \App\Models\User::where(function ($q) use ($loggedInUserId) {
                        $q->where('sup_id', $loggedInUserId)
                            ->orWhere('asm_id', $loggedInUserId);
                    })->whereIn('type', $allowedTypes)
                        ->pluck('id')
                        ->toArray();
                    $userIds = array_merge($userIds, $managedUserIds);
                } elseif ($loggedInUserRole == AppHelper::USER_ASM) {
                    $managedUserIds = \App\Models\User::where('asm_id', $loggedInUserId)
                        ->whereIn('type', $allowedTypes)
                        ->pluck('id')
                        ->toArray();
                    $userIds = array_merge($userIds, $managedUserIds);
                }

                // Apply user ID filter unless Super Admin, Admin, Director, or type ALL
                // if (!($loggedInUserType == AppHelper::ALL || in_array($loggedInUserRole, [
                //     AppHelper::USER_SUPER_ADMIN,
                //     AppHelper::USER_ADMINISTRATOR,
                //     AppHelper::USER_ADMIN,
                //     AppHelper::USER_DIRECTOR
                // ]))) {
                //     $query->whereIn('user_id', array_unique($userIds));
                // }

                // Ensure depos belong to users with allowed types
                if (!($loggedInUserType == AppHelper::ALL || in_array($loggedInUserRole, [
                    AppHelper::USER_SUPER_ADMIN,
                    AppHelper::USER_ADMINISTRATOR,
                    AppHelper::USER_ADMIN,
                    AppHelper::USER_DIRECTOR
                ]))) {
                    $query->whereHas('user', function ($q) use ($allowedTypes) {
                        $q->whereIn('type', $allowedTypes);
                    });
                }
            } else {
                // No authenticated user, return no depos
                $query->where('id', 0);
            }

            $data['outlets'] = $query->orderBy('id', 'desc')->get();
            return view('backend.outlet.list', $data);
        } catch (\Exception $e) {
            Log::error('DataTables AJAX Error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred. Please check the server logs.'], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['outlet'] = false;
        // $data['regions'] = Region::where('active_status', 1)->orderBy('region_name', 'asc')->get();

        // $data['regions'] = Region::where('active_status', 1)
        // ->select('region_name')  // Select only the region_name column
        // ->distinct()             // Get distinct region names
        // ->orderBy('region_name', 'asc')
        // ->get();
        $data['regions'] = Region::where('active_status', 1)
            ->select('region_name', 'rg_manager_kh', 'rg_manager_en', 'se_code', 'id')
            ->orderBy('region_name', 'asc')
            ->get()
            ->groupBy('rg_manager_kh');

        // echo '<pre>';
        // foreach ($data['regions'] as $regionName => $regions) {
        //     foreach ($regions as $region) {
        //         echo $region->rg_manager_kh . "\n"; // Access the rg_manager_kh property correctly
        //     }
        // }
        // echo '</pre>';

        return view('backend.outlet.add', $data);
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
            // 'depo_id' => 'required|exists:outlets,id',
            'customer_type' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'outlet_photo' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            'outlet_photo_base64' => 'nullable|string',
            'code' => 'required|unique:outlets,code'
        ];

        // Make outlet_photo required if neither file nor base64 is provided
        if (!$request->hasFile('outlet_photo') && !$request->filled('outlet_photo_base64')) {
            $rules['outlet_photo'] = 'required|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Prepare data for storage
        $data = [
            'user_id' => auth()->user()->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'area_id' => $request->area,
            'customer_type' => $request->customer_type,
            'user_type' => auth()->user()->type,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'city' => $request->city,
            'country' => $request->country,
            'code' => $request->code,
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
            Outlet::create($data);

            if ($request->has('saveandcontinue')) {
                return redirect()->route('outlet.create')->with('success', __('Depot created successfully.'));
            }
            return redirect()->route('outlet.index')->with('success', __('Depot created successfully.'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to create Depot: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $report = Outlet::with('user', 'region')->find($id);

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
                'depot_code' => $report->code,
                'depot_name' => $report->name,
                'phone_number' => $report->phone,
                'outlet_photo' => $report->outlet_photo ? asset('storage/' . $report->outlet_photo) : asset('images/post-placeholder.jpg'),
                'customer_type' => $report->customer_type,
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
        $data['outlet'] = Outlet::findOrFail($id);

        $data['regions'] = Region::where('active_status', 1)
            ->select('region_name', 'rg_manager_kh', 'rg_manager_en', 'se_code', 'id')
            ->orderBy('region_name', 'asc')
            ->get()
            ->groupBy('rg_manager_kh');


        return view('backend.outlet.add', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $outlet = Outlet::findOrFail($id);
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
            'customer_type' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'outlet_photo' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            'outlet_photo_base64' => 'nullable|string',
            'code' => 'required|unique:outlets,code,' . $id

        ];
        // dd($customer->outlet_photo);
        // Make outlet_photo required if neither file nor base64 is provided and no existing photo exists
        if (!$request->hasFile('outlet_photo') && !$request->filled('outlet_photo_base64') && !$outlet->outlet_photo && empty($request->old_outlet_photo)) {
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
            'customer_type' => $request->customer_type,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'city' => $request->city,
            'country' => $request->country,
            'code' => $request->code,
        ];


        // Handle outlet photo file upload if exists
        if ($request->hasFile('outlet_photo')) {
            // Delete existing photo if it exists
            if ($outlet->outlet_photo && Storage::disk('public')->exists($outlet->outlet_photo)) {
                Storage::disk('public')->delete($outlet->outlet_photo);
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
            if ($outlet->outlet_photo && Storage::disk('public')->exists($outlet->outlet_photo)) {
                Storage::disk('public')->delete($outlet->outlet_photo);
            }
            $image = str_replace('data:image/png;base64,', '', $request->outlet_photo_base64);
            $image = str_replace(' ', '+', $image);
            $imageData = base64_decode($image);
            $fileName = 'Uploads/outlet_' . time() . '_' . Str::random(10) . '.png';
            Storage::disk('public')->put($fileName, $imageData);
            $data['outlet_photo'] = $fileName;
        }
        // Clear outlet_photo if existing photo is deleted and no new photo is provided
        elseif ($request->has('delete_outlet_photo') && $outlet->outlet_photo) {
            if (Storage::disk('public')->exists($outlet->outlet_photo)) {
                Storage::disk('public')->delete($outlet->outlet_photo);
            }
            $data['outlet_photo'] = null;
        }

        try {
            $outlet->update($data);
            return redirect()->route('outlet.index')->with('success', __('Outlet updated successfully.'));
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
        return Excel::download(new OutletExport(), 'depot_' . now()->format('Y_m_d_His') . '.xlsx');
    }
}

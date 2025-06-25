<?php

namespace App\Http\Controllers;

use App\Exports\CustomerExport;
use App\Http\Helpers\AppHelper;
use App\Models\Customer;
use App\Models\Depo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('type.permission:view customer', ['only' => ['index']]);
        $this->middleware('type.permission:create customer', ['only' => ['create', 'store']]);
        $this->middleware('type.permission:update customer', ['only' => ['edit', 'update']]);
        $this->middleware('type.permission:delete customer', ['only' => ['destroy']]);
    }
    public function index(Request $request)
    {
        $loggedInUser = auth()->check() ? auth()->user() : null;

        if ($request->ajax()) {
            // Initialize customer query with user relationship
            $query = Customer::with('user','depo');

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

            $customers = $query->orderBy('id', 'desc');
            return DataTables::of($customers)
                ->addIndexColumn()
                ->addColumn('created_by', function ($customer) {
                    if (!$customer->user) {
                        return 'N/A';
                    }
                    return $customer->user->user_lang === 'en'
                        ? ($customer->user->full_name_latin ?? 'N/A')
                        : ($customer->user->user_lang === 'kh' ? ($customer->user->full_name ?? 'N/A') : 'N/A');
                })
                ->addColumn('area_id', fn($customer) => AppHelper::getAreaNameById($customer->area_id))
                ->addColumn('depo_id', fn($customer) => $customer->depo->name ?? 'N/A')
                ->addColumn('customer_code', fn($customer) => $customer->code)
                ->addColumn('customer_name', fn($customer) => $customer->name)
                ->addColumn('customer_type', fn($customer) =>
                    AppHelper::CUSTOMER_TYPE[$customer->customer_type] ?? 'N/A')
                ->addColumn('phone', fn($customer) => $customer->phone)
                ->addColumn('action', function ($customer) {
                    $button = '<div class="change-action-item">';
                    $actions = false;
                    if (auth()->user()->role_id == AppHelper::USER_SUPER_ADMIN || auth()->user()->role_id == AppHelper::USER_ADMINISTRATOR || auth()->user()->role_id == AppHelper::USER_DIRECTOR || auth()->user()->role_id == AppHelper::USER_ADMIN || auth()->user()->role_id == AppHelper::USER_MANAGER) {
                        $button .= '<a title="Edit" href="' . route('customer.edit', $customer->id) . '" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>';
                        $actions = true;
                    }
                    if (auth()->user()->can('delete customer')) {
                        $button .= '<a href="' . route('customer.destroy', $customer->id) . '" class="btn btn-danger btn-sm delete" title="Delete"><i class="fa fa-fw fa-trash"></i></a>';
                        $actions = true;
                    }
                    if (!$actions) {
                        $button .= '<span style="font-weight:bold; color:red;">No Action</span>';
                    }
                    $button .= '</div>';
                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('backend.customer.list');
    }


    public function create()
    {
        $customer = null;
        $depoName = [];
        $customerType = AppHelper::CUSTOMER_TYPE;
        return view('backend.customer.add', compact('customer', 'customerType','depoName'));
    }

     public function getDeposByArea(Request $request)
    {
        $areaId = $request->query('area_id');
        $authUser = auth()->user();

        if (!$areaId) {
            return response()->json([], 400);
        }

        $query = Depo::where('area_id', $areaId);

        if (in_array($authUser->type, [AppHelper::SALE, AppHelper::SE])) {
            // Filter depos accessible by SALE or SE users
            $query->where('user_type', $authUser->type)
                ->where(function ($q) use ($authUser) {
                    $q->where('user_id', $authUser->id);
                });
        }

        $depos = $query->pluck('name', 'id')->toArray();

        return response()->json($depos);
    }

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
        'depo_id' => 'required|exists:depos,id',
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
    $lastCustomer = Customer::orderBy('id', 'desc')->first();
    $lastCodeNumber = $lastCustomer && $lastCustomer->code ? (int) substr($lastCustomer->code, 4) : 0;
    $newCodeNumber = $lastCodeNumber + 1;
    $code = $prefix .'-' . str_pad($newCodeNumber, 5, '0', STR_PAD_LEFT);

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
        Customer::create($data);

        if ($request->has('saveandcontinue')) {
            return redirect()->route('customer.create')->with('success', 'Customer created successfully.');
        }
        return redirect()->route('customer.index')->with('success', 'Customer created successfully.');
    } catch (Exception $e) {
        return redirect()->back()->with('error', 'Failed to create customer: ' . $e->getMessage())->withInput();
    }
}

    public function edit(Customer $customer)
    {
        $customerType = AppHelper::CUSTOMER_TYPE;

        // Load depos based on the customer's area
        $depos = [];
        if ($customer->area_id) {
            $depos = \App\Models\Depo::where('area_id', $customer->area_id)->pluck('name', 'id');
        }
        return view('backend.customer.add', compact('customer', 'customerType', 'depos'));
    }


    public function update(Request $request, Customer $customer)
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
        'depo_id' => 'required|exists:depos,id',
        'customer_type' => 'required|string',
        'latitude' => 'required|numeric|between:-90,90',
        'longitude' => 'required|numeric|between:-180,180',
        'city' => 'required|string|max:255',
        'country' => 'required|string|max:255',
        'outlet_photo' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
        'outlet_photo_base64' => 'nullable|string',
    ];

    // Make outlet_photo required if neither file nor base64 is provided and no existing photo exists
    if (!$request->hasFile('outlet_photo') && !$request->filled('outlet_photo_base64') && !$customer->outlet_photo) {
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
        // 'user_type' => auth()->user()->type,
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
        $lastCustomer = Customer::orderBy('id', 'desc')->first();
        $lastCodeNumber = $lastCustomer && $lastCustomer->code ? (int) substr($lastCustomer->code, 4) : 0;
        $newCodeNumber = $lastCodeNumber + 1;
        $data['code'] = $prefix .'-' . str_pad($newCodeNumber, 5, '0', STR_PAD_LEFT);
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
        return redirect()->route('customer.index')->with('success', 'Customer updated successfully.');
    } catch (Exception $e) {
        return redirect()->back()->with('error', 'Failed to update customer: ' . $e->getMessage())->withInput();
    }
}

    public function export()
    {
        return Excel::download(new CustomerExport(), 'customers_' . now()->format('Y_m_d_His') . '.xlsx');
    }

    // public function destroy($id)
    // {
    //     $customer = Customer::findOrFail($id);
    //     $customer->delete();
    //     return redirect()->back()->with('success', "Customer has been deleted!");
    // }
}

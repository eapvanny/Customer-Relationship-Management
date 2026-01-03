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
            $query = Customer::with('user', 'depo');

            if ($loggedInUser) {

                $loggedInUserRole = $loggedInUser->role_id;
                $loggedInUserId   = $loggedInUser->id;
                $loggedInUserType = $loggedInUser->type;
                $rawAreaText      = $loggedInUser->area; // e.g. "ASM-R1-01" or "RSM-R1" or "S-04"
                $loggedUserAreaId = AppHelper::getAreaIdByText($rawAreaText);

                // === NEW CONDITION: If user type is SALE and role is USER_EMPLOYEE, show only their own records ===
                if ($loggedInUserType == AppHelper::SALE && $loggedInUserRole == AppHelper::USER_EMPLOYEE) {
                    $query->where('user_id', $loggedInUserId);
                } else {
                    // === Normalize area text and compute allowed area IDs ===
                    $allowedAreaIds = [];

                    if ($rawAreaText) {
                        // Remove role prefix like ASM-, RSM-, SUP-, etc.
                        $normalized = preg_replace('/^[A-Za-z]+-/', '', $rawAreaText);

                        // Get your mapping
                        $areas = AppHelper::getAreas(); // your const AREAS list

                        // === Case 1: Specific S-XX code (like S-04) ===
                        if (preg_match('/^S-\d+$/', $normalized)) {
                            foreach ($areas as $group => $subs) {
                                foreach ($subs as $id => $sText) {
                                    if ($sText === $normalized) {
                                        $allowedAreaIds[] = $id;
                                        break 2;
                                    }
                                }
                            }
                        }

                        // === Case 2: Specific subregion (R1-01, R2-02, etc.) ===
                        elseif (preg_match('/^R\d+-\d{2}$/', $normalized)) {
                            foreach ($areas as $group => $subs) {
                                if (strpos($group, "($normalized)") !== false) {
                                    foreach ($subs as $id => $sText) {
                                        $allowedAreaIds[] = $id;
                                    }
                                }
                            }
                        }

                        // === Case 3: RSM-level region (R1, R2) → all subregions under that region ===
                        elseif (preg_match('/^R\d+$/', $normalized)) {
                            foreach ($areas as $group => $subs) {
                                // Example group name: "Ussa (R1-01)"
                                if (strpos($group, "($normalized-") !== false) { // matches R1-01, R1-02...
                                    foreach ($subs as $id => $sText) {
                                        $allowedAreaIds[] = $id;
                                    }
                                }
                            }
                        }
                        // === Case 4: Fallback numeric area ID directly ===
                        elseif (is_numeric($loggedUserAreaId)) {
                            // Fallback: direct area ID match
                            $allowedAreaIds[] = $loggedUserAreaId;
                        }
                    }

                    // Remove duplicates
                    $allowedAreaIds = array_values(array_unique($allowedAreaIds));

                    $adminRoles = [
                        AppHelper::USER_SUPER_ADMIN,
                        AppHelper::USER_ADMINISTRATOR,
                        AppHelper::USER_ADMIN,
                        AppHelper::USER_DIRECTOR,
                        AppHelper::USER_MANAGER,
                    ];

                    // Full access for admin roles
                    if (!($loggedInUser->type == AppHelper::ALL ||
                        in_array($loggedInUserRole, $adminRoles))) {

                        $query->where(function ($q) use ($loggedInUser) {
                            $loggedInUserId = $loggedInUser->id;

                            // 1. Customers created by logged-in user
                            $q->where('user_id', $loggedInUserId)

                                // 2. Customers whose creator is managed by logged-in user
                                ->orWhereHas('user', function ($u) use ($loggedInUserId) {
                                    // Fix: Handle JSON array fields properly
                                    $u->where('manager_id', $loggedInUserId)
                                        ->orWhere('rsm_id', $loggedInUserId)
                                        ->orWhere(function ($jsonQuery) use ($loggedInUserId) {
                                            // Handle asm_id as JSON array
                                            $jsonQuery->whereJsonContains('asm_id', (string)$loggedInUserId)
                                                ->orWhere('asm_id', $loggedInUserId);
                                        })
                                        ->orWhere(function ($jsonQuery) use ($loggedInUserId) {
                                            // Handle sup_id as JSON array  
                                            $jsonQuery->whereJsonContains('sup_id', (string)$loggedInUserId)
                                                ->orWhere('sup_id', $loggedInUserId);
                                        });
                                });

                            // 3. Customers whose creator is the manager above
                            // Fix: Use the helper function to normalize IDs from both formats
                            $managerIds = AppHelper::normalizeIds($loggedInUser->manager_id);
                            $rsmIds     = AppHelper::normalizeIds($loggedInUser->rsm_id);
                            $asmIds     = AppHelper::normalizeIds($loggedInUser->asm_id);
                            $supIds     = AppHelper::normalizeIds($loggedInUser->sup_id);

                            if (!empty($managerIds)) {
                                $q->orWhereIn('user_id', $managerIds);
                            }

                            if (!empty($rsmIds)) {
                                $q->orWhereIn('user_id', $rsmIds);
                            }

                            if (!empty($asmIds)) {
                                $q->orWhereIn('user_id', $asmIds);
                            }

                            if (!empty($supIds)) {
                                $q->orWhereIn('user_id', $supIds);
                            }
                        });

                        // ✅ Apply area restriction at the end
                        if (!empty($allowedAreaIds)) {
                            $query->whereIn('area_id', $allowedAreaIds);
                        } else {
                            if ($rawAreaText) {
                                $query->where('id', 0); // no match
                            }
                        }
                    }
                }
            } else {
                $query->where('id', 0); // not logged in
            }

            $customers = $query->orderBy('id', 'desc');

            return DataTables::of($customers)
                ->addIndexColumn()
                ->addColumn('created_by', function ($customer) {
                    if (!$customer->user) return 'N/A';
                    return $customer->user->user_lang === 'en'
                        ? ($customer->user->full_name_latin ?? 'N/A')
                        : ($customer->user->full_name ?? 'N/A');
                })
                ->addColumn('area_id', fn($customer) => AppHelper::getAreaNameById($customer->area_id))
                ->addColumn('depo_id', fn($customer) => $customer->depo->name ?? 'N/A')
                ->addColumn('customer_code', fn($customer) => $customer->code)
                ->addColumn('customer_name', fn($customer) => $customer->name)
                ->addColumn('customer_type', fn($customer) =>
                AppHelper::CUSTOMER_TYPE[$customer->customer_type] ?? 'N/A')
                ->addColumn('phone', fn($customer) => $customer->phone)
                ->addColumn('action', function ($customer) {
                    $btn = '<div class="change-action-item">';
                    $canEdit = in_array(auth()->user()->role_id, [
                        AppHelper::USER_SUPER_ADMIN,
                        AppHelper::USER_ADMINISTRATOR,
                        AppHelper::USER_DIRECTOR,
                        AppHelper::USER_ADMIN,
                        AppHelper::USER_MANAGER,
                        AppHelper::USER_RSM,
                        AppHelper::USER_ASM,
                        AppHelper::USER_SUP,
                        AppHelper::USER_EMPLOYEE
                    ]);

                    if ($canEdit) {
                        $btn .= '<a title="Edit" href="' . route('customer.edit', $customer->id) . '" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>';
                    }

                    if (auth()->user()->can('delete customer')) {
                        $btn .= '<a href="' . route('customer.destroy', $customer->id) . '" class="btn btn-danger btn-sm delete" title="Delete"><i class="fa fa-fw fa-trash"></i></a>';
                    }

                    if (!$canEdit && !auth()->user()->can('delete customer')) {
                        $btn .= '<span style="font-weight:bold; color:red;">No Action</span>';
                    }

                    return $btn . '</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('backend.customer.list');
    }

    // public function create()
    // {
    //     $customer = null;
    //     $depoName = [];
    //     $customerType = AppHelper::CUSTOMER_TYPE;
    //     $user = auth()->user();
    //     $userAreaCode = $user->area ?? null; // Example: "R1", "R2", "R2-02", or null

    //     $areas = AppHelper::getAreas();

    //     // Only filter if area is defined and matches pattern R1 / R2 / R1-01 / R2-02
    //     if ($userAreaCode && preg_match('/^R\d(-\d{2})?$/', $userAreaCode)) {
    //         $areas = collect($areas)
    //             ->filter(function ($subItems, $areaName) use ($userAreaCode) {
    //                 // If userAreaCode = "R1" → include "R1-"
    //                 if (preg_match('/^R\d$/', $userAreaCode)) {
    //                     return str_contains($areaName, $userAreaCode . '-');
    //                 }

    //                 // If userAreaCode = "R1-01" → include exact match
    //                 return str_contains($areaName, $userAreaCode);
    //             })
    //             ->toArray();
    //     }
    //     return view('backend.customer.add', compact('customer', 'customerType','depoName', 'areas'));
    // }
    public function create()
    {
        $customer = null;
        $depoName = [];
        $customerType = AppHelper::CUSTOMER_TYPE;
        $user = auth()->user();
        $userAreaCode = $user->area ?? null; // Example: "R1", "R1-01", "S-04", etc.
        $userRoleId = $user->role_id ?? null;

        // Define which roles can see ALL areas (no filtering)
        $fullAccessRoles = [
            AppHelper::USER_SUPER_ADMIN,      // 1
            AppHelper::USER_ADMINISTRATOR,    // 2
            AppHelper::USER_ADMIN,            // 3
            AppHelper::USER_DIRECTOR,         // 4
            AppHelper::USER_MANAGER,          // 5
            // Add more if needed in the future
        ];
        $areas = AppHelper::getAreas();

        if ($userAreaCode && !in_array($userRoleId, $fullAccessRoles)) {
            $areas = collect($areas)
                ->filter(function ($subItems, $areaName) use ($userAreaCode) {

                    // === RSM LEVEL (ex: "R1", "R2") ===
                    if (preg_match('/^R\d$/', $userAreaCode)) {
                        // Keep all sub-areas under same region (e.g. R1-01, R1-02)
                        return str_contains($areaName, $userAreaCode . '-');
                    }

                    // === ASM LEVEL (ex: "R1-01") ===
                    if (preg_match('/^R\d-\d{2}$/', $userAreaCode)) {
                        // Keep only that specific ASM area
                        return str_contains($areaName, $userAreaCode);
                    }

                    // === SALE LEVEL (ex: "S-04") ===
                    if (preg_match('/^S-\d+$/', $userAreaCode)) {
                        // Keep only areas containing this sales code
                        foreach ($subItems as $code) {
                            if ($code === $userAreaCode) {
                                return true;
                            }
                        }
                        return false;
                    }

                    return false;
                })
                ->map(function ($subItems, $areaName) use ($userAreaCode) {
                    // If Sales (S-xx), keep only their own code in sublist
                    if (preg_match('/^S-\d+$/', $userAreaCode)) {
                        return collect($subItems)
                            ->filter(fn($code) => $code === $userAreaCode)
                            ->toArray();
                    }
                    return $subItems;
                })
                ->toArray();
        }

        return view('backend.customer.add', compact('customer', 'customerType', 'depoName', 'areas'));
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
            $query->where('user_type', $authUser->type);
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
             // File upload
            'outlet_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg,gif|max:10000|dimensions:min_width=50,min_height=50',
            // Base64 image
            'outlet_photo_base64' => 'nullable|string',
        ];

        // Make outlet_photo required if neither file nor base64 is provided
        if (!$request->hasFile('outlet_photo') && !$request->filled('outlet_photo_base64')) {
            $rules['outlet_photo'] = 'required|image|mimes:jpg,jpeg,png,webp,svg,gif|max:10000|dimensions:min_width=50,min_height=50';
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
        $areas = AppHelper::getAreas();
        if ($customer->area_id) {
            $depos = \App\Models\Depo::where('area_id', $customer->area_id)->pluck('name', 'id');
        }
        return view('backend.customer.add', compact('customer', 'customerType', 'depos', 'areas'));
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

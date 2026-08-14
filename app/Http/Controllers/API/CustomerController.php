<?php

namespace App\Http\Controllers\API;

use App\Exports\CustomerExport;
use App\Http\Controllers\Controller;
use App\Http\Helpers\AppHelper;
use App\Models\Customer;
use App\Models\Depo;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
class CustomerController extends Controller
{
    //
    public function index(Request $request)
    {
        $user = auth()->user();

        // ============================================================
        // Authentication
        // ============================================================
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        // ============================================================
        // Base Query
        // ============================================================
        $query = Customer::with(['user', 'depo']);

        $roleId   = $user->role_id;
        $userId   = $user->id;
        $userType = $user->type;

        $rawAreaText     = $user->area;
        $loggedUserAreaId = AppHelper::getAreaIdByText($rawAreaText);

        // ============================================================
        // SALE + EMPLOYEE
        // Show only customers created by the logged-in employee
        // ============================================================
        if (
            $userType == AppHelper::SALE &&
            $roleId == AppHelper::USER_EMPLOYEE
        ) {
            $query->where('user_id', $userId);
        } else {

            // ========================================================
            // AREA FILTER
            // Same logic as Web Application
            // ========================================================
            $allowedAreaIds = [];

            if ($rawAreaText) {

                // Remove role prefix:
                // ASM-R1-01 -> R1-01
                // RSM-R1    -> R1
                // SUP-S-04  -> S-04
                $normalized = preg_replace(
                    '/^[A-Za-z]+-/',
                    '',
                    $rawAreaText
                );

                $areas = AppHelper::getAreas();

                // ====================================================
                // Case 1: Specific S-XX
                // Example: S-04
                // ====================================================
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

                // ====================================================
                // Case 2: Specific subregion
                // Example: R1-01
                // ====================================================
                elseif (preg_match('/^R\d+-\d{2}$/', $normalized)) {

                    foreach ($areas as $group => $subs) {

                        if (strpos($group, "($normalized)") !== false) {

                            foreach ($subs as $id => $sText) {
                                $allowedAreaIds[] = $id;
                            }
                        }
                    }
                }

                // ====================================================
                // Case 3: RSM region
                // Example: R1
                // Get all R1-01, R1-02, R1-03...
                // ====================================================
                elseif (preg_match('/^R\d+$/', $normalized)) {

                    foreach ($areas as $group => $subs) {

                        if (strpos($group, "($normalized-") !== false) {

                            foreach ($subs as $id => $sText) {
                                $allowedAreaIds[] = $id;
                            }
                        }
                    }
                }

                // ====================================================
                // Case 4: Numeric Area ID
                // ====================================================
                elseif (is_numeric($loggedUserAreaId)) {

                    $allowedAreaIds[] = $loggedUserAreaId;
                }
            }

            // Remove duplicates
            $allowedAreaIds = array_values(
                array_unique($allowedAreaIds)
            );

            // ========================================================
            // ADMIN ROLES
            // Same as Web Application
            // ========================================================
            $adminRoles = [
                AppHelper::USER_SUPER_ADMIN,
                AppHelper::USER_ADMINISTRATOR,
                AppHelper::USER_ADMIN,
                AppHelper::USER_DIRECTOR,
                AppHelper::USER_MANAGER,
            ];

            // ========================================================
            // Non-admin + non-ALL users
            // Apply user hierarchy + area restriction
            // ========================================================
            if (
                !(
                    $userType == AppHelper::ALL ||
                    in_array($roleId, $adminRoles)
                )
            ) {

                $query->where(function ($q) use ($user, $userId) {

                    // =================================================
                    // 1. Customers created by logged-in user
                    // =================================================
                    $q->where('user_id', $userId)

                        // =============================================
                        // 2. Customers created by users under
                        //    logged-in user's hierarchy
                        // =============================================
                        ->orWhereHas('user', function ($u) use ($userId) {

                            // Manager
                            $u->where('manager_id', $userId)

                                // RSM
                                ->orWhere('rsm_id', $userId)

                                // ASM
                                ->orWhere(function ($jsonQuery) use ($userId) {

                                    $jsonQuery
                                        ->whereJsonContains(
                                            'asm_id',
                                            (string) $userId
                                        )
                                        ->orWhere(
                                            'asm_id',
                                            $userId
                                        );
                                })

                                // SUP
                                ->orWhere(function ($jsonQuery) use ($userId) {

                                    $jsonQuery
                                        ->whereJsonContains(
                                            'sup_id',
                                            (string) $userId
                                        )
                                        ->orWhere(
                                            'sup_id',
                                            $userId
                                        );
                                });
                        });

                    // =================================================
                    // 3. Customers created by user's managers
                    // =================================================

                    $managerIds = AppHelper::normalizeIds(
                        $user->manager_id
                    );

                    $rsmIds = AppHelper::normalizeIds(
                        $user->rsm_id
                    );

                    $asmIds = AppHelper::normalizeIds(
                        $user->asm_id
                    );

                    $supIds = AppHelper::normalizeIds(
                        $user->sup_id
                    );

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

                // ====================================================
                // Apply Area Restriction
                // ====================================================
                if (!empty($allowedAreaIds)) {

                    $query->whereIn(
                        'area_id',
                        $allowedAreaIds
                    );

                } else {

                    // Same behavior as Web Application
                    if ($rawAreaText) {
                        $query->where('id', 0);
                    }
                }
            }
        }

        // ============================================================
        // API FILTERS
        // ============================================================

        // ------------------------------------------------------------
        // Customer Type
        // ------------------------------------------------------------
        if ($request->filled('customer_type')) {

            $customerType = $request->customer_type;

            // Flutter sends ID
            if (is_numeric($customerType)) {

                $query->where(
                    'customer_type',
                    $customerType
                );

            } else {

                // Flutter sends text
                $typeId = array_search(
                    $customerType,
                    AppHelper::CUSTOMER_TYPE
                );

                if ($typeId !== false) {

                    $query->where(
                        'customer_type',
                        $typeId
                    );
                }
            }
        }

        // ------------------------------------------------------------
        // Area
        // ------------------------------------------------------------
        if ($request->filled('area_id')) {

            $query->where(
                'area_id',
                $request->area_id
            );
        }

        // ------------------------------------------------------------
        // Depo
        // ------------------------------------------------------------
        if ($request->filled('depo_id')) {

            $query->where(
                'depo_id',
                $request->depo_id
            );
        }

        // ------------------------------------------------------------
        // Search
        // Same useful search behavior as Web Application
        // ------------------------------------------------------------
        if ($request->filled('search')) {

            $search = trim($request->search);

            // Search area text such as:
            // S-04, R1-01, R1...
            $areaIds = AppHelper::getAreaIdsBySearch($search);

            $query->where(function ($q) use ($search, $areaIds) {

                // Customer name
                $q->where(
                    'name',
                    'LIKE',
                    "%{$search}%"
                )

                    // Customer code
                    ->orWhere(
                        'code',
                        'LIKE',
                        "%{$search}%"
                    )

                    // Phone
                    ->orWhere(
                        'phone',
                        'LIKE',
                        "%{$search}%"
                    )

                    // Search creator
                    ->orWhereHas('user', function ($userQuery) use ($search) {

                        $userQuery->where(function ($q) use ($search) {

                            $q->where(
                                'family_name',
                                'LIKE',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'name',
                                'LIKE',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'family_name_latin',
                                'LIKE',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'name_latin',
                                'LIKE',
                                "%{$search}%"
                            )

                            // Khmer full name
                            ->orWhereRaw(
                                "CONCAT(family_name, ' ', name) LIKE ?",
                                ["%{$search}%"]
                            )

                            // Latin full name
                            ->orWhereRaw(
                                "CONCAT(family_name_latin, ' ', name_latin) LIKE ?",
                                ["%{$search}%"]
                            );
                        });
                    });

                // Search by area
                if (!empty($areaIds)) {

                    $q->orWhereIn(
                        'area_id',
                        $areaIds
                    );
                }
            });
        }

        // ============================================================
        // Pagination
        // ============================================================
        $perPage = (int) $request->input('per_page', 20);

        // Prevent very large requests
        $perPage = min($perPage, 100);

        $customers = $query
            ->orderByDesc('id')
            ->paginate($perPage);

        // ============================================================
        // Response
        // ============================================================
        return response()->json([

            'status' => true,

            'data' => collect($customers->items())
                ->map(function ($customer) {

                    return [

                        'id' => $customer->id,

                        'created_by' => $customer->user
                            ? (
                                $customer->user->user_lang == 'en'
                                    ? (
                                        $customer->user->full_name_latin
                                        ?? 'N/A'
                                    )
                                    : (
                                        $customer->user->full_name
                                        ?? 'N/A'
                                    )
                            )
                            : 'N/A',

                        'area' => AppHelper::getAreaNameById(
                            $customer->area_id
                        ),

                        'depo' => optional(
                            $customer->depo
                        )->name ?? 'N/A',

                        'customer_code' => (string) $customer->code,

                        'customer_name' => (string) $customer->name,

                        'customer_type' => (string) (
                            AppHelper::CUSTOMER_TYPE[
                                $customer->customer_type
                            ] ?? 'N/A'
                        ),

                        'phone' => (string) $customer->phone,
                    ];
                })
                ->values(),

            'pagination' => [

                'current_page' => $customers->currentPage(),

                'last_page' => $customers->lastPage(),

                'per_page' => $customers->perPage(),

                'total' => $customers->total(),

                'from' => $customers->firstItem(),

                'to' => $customers->lastItem(),

                'has_more' => $customers->hasMorePages(),
            ]

        ], 200, [], JSON_UNESCAPED_UNICODE);
    }


    public function getAreas()
    {
        $user = auth()->user();
        $userAreaCode = $user->area ?? null;
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

        $areaId = AppHelper::getAreaIdByText($userAreaCode);
        $depos = Depo::where('area_id',$areaId)->pluck('name','id');
        $customer = Customer::where('user_id',$user->id)->pluck('name','id');

        return response()->json([
            'status' => true,
            'data' => [
                'depos' => $depos,
                'areas' => $areas,
                'customer' => $customer,
            ],
        ]);
    }

    // public function getAllDepoArea()
    // {
    //     $depos = Depo::select('id', 'name', 'area_id')->get();
    //     $areas = AppHelper::getAreas();
    //     $customer = Customer::select('id','name','user_id')->get();
    //     return response()->json([
    //         'status' => true,
    //         'data' => [
    //             'depos' => $depos,
    //             'areas' => $areas,
    //             'customer' => $customer,
    //         ],
    //     ]);
    // }

    public function getDeposByArea(Request $request)
    {
        $request->validate([
            'area_id' => 'required|integer'
        ]);

        $user = auth()->user();

        $query = Depo::where('area_id', $request->area_id);

        if ($user && in_array($user->type, [
            AppHelper::SALE,
            AppHelper::SE
        ])) {
            $query->where('user_type', $user->type);
        }

        return response()->json([
            'status' => true,
            'data' => $query->select('id', 'name')->get()
        ]);
    }

    public function show($id)
    {
        try {

            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            $customer = Customer::with('depo')
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$customer) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer not found'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => [
                    'id' => $customer->id,
                    'code' => $customer->code,
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'area_id' => $customer->customer && $customer->customer->area_id
                        ? AppHelper::getAreaNameById($customer->customer->area_id) ?? 'N/A'
                        : AppHelper::getAreaNameById($customer->area_id) ?? 'N/A',
                    'depo_name' => optional($customer->depo)->name,
                    'customer_type' => AppHelper::CUSTOMER_TYPE[$customer->customer_type] ?? 'N/A',
                    'latitude' => $customer->latitude,
                    'longitude' => $customer->longitude,
                    'city' => $customer->city,
                    'country' => $customer->country,
                    'outlet_photo' => $customer->outlet_photo
                        ? asset('storage/' . $customer->outlet_photo)
                        : null,
                    'created_at' => $customer->created_at,
                    'updated_at' => $customer->updated_at,
                ]
            ]);

        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {

            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }


            // Get valid area IDs
            $areaIds = [];

            foreach (AppHelper::getAreas() as $group) {
                $areaIds = array_merge($areaIds, array_keys($group));
            }


            $rules = [
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:255',
                'area_id' => 'required|in:' . implode(',', $areaIds),
                'depo_id' => 'required|exists:depos,id',
                'customer_type' => 'required|string',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'city' => 'required|string|max:255',
                'country' => 'required|string|max:255',

                'outlet_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp,heic|max:10000',
            ];

            $validator = Validator::make($request->all(), $rules);


            if ($validator->fails()) {

                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }



            // Generate customer code

            switch ($user->type) {

                case AppHelper::SALE:
                    $prefix = 'CPP';
                    break;

                case AppHelper::SE:
                    $prefix = 'CPV';
                    break;

                default:
                    $prefix = 'CUS';
                    break;
            }

            $lastCustomer = Customer::orderBy('id', 'desc')->first();

            $lastCodeNumber = $lastCustomer && $lastCustomer->code
                ? (int) substr($lastCustomer->code, 4)
                : 0;


            $code = $prefix . '-' . str_pad(
                $lastCodeNumber + 1,
                5,
                '0',
                STR_PAD_LEFT
            );



            $data = [

                'user_id' => $user->id,

                'name' => $request->name,

                'phone' => $request->phone,

                'area_id' => $request->area_id,

                'depo_id' => $request->depo_id,

                'customer_type' => $request->customer_type,

                'user_type' => $user->type,

                'latitude' => $request->latitude,

                'longitude' => $request->longitude,

                'city' => $request->city,

                'country' => $request->country,

                'code' => $code,
            ];
            if ($request->hasFile('outlet_photo')) {
                $file = $request->file('outlet_photo');
                // Resize & compress image
                $resizedImage = AppHelper::resizeAndCompressImage($file);

                // Generate filename
                $fileName = 'uploads/outlet_'
                    . time() . '_'
                    . Str::random(10)
                    . '.jpg'; // or use $file->extension() if your helper preserves the format

                // Save resized image
                Storage::disk('public')->put($fileName, $resizedImage);

                $data['outlet_photo'] = $fileName;
            }
            $customer = Customer::create($data);

            return response()->json([

                'status' => true,

                'message' => 'Customer created successfully',

                'data' => $customer

            ], 201);
        } catch (Exception $e) {


            return response()->json([

                'status' => false,

                'message' => $e->getMessage()

            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {

            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            $customer = Customer::find($id);

            if (!$customer) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer not found'
                ], 404);
            }

            // Get valid area IDs
            $areaIds = [];

            foreach (AppHelper::getAreas() as $group) {
                $areaIds = array_merge($areaIds, array_keys($group));
            }

            $rules = [
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:255',
                'area_id' => 'required|in:' . implode(',', $areaIds),
                'depo_id' => 'required|exists:depos,id',
                'customer_type' => 'required|string',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'city' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'outlet_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp,heic|max:10000',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = [
                'name' => $request->name,
                'phone' => $request->phone,
                'area_id' => $request->area_id,
                'depo_id' => $request->depo_id,
                'customer_type' => $request->customer_type,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'city' => $request->city,
                'country' => $request->country,
            ];

            if ($request->hasFile('outlet_photo')) {

                // Delete old photo
                if ($customer->outlet_photo && Storage::disk('public')->exists($customer->outlet_photo)) {
                    Storage::disk('public')->delete($customer->outlet_photo);
                }

                $file = $request->file('outlet_photo');

                // Resize & compress image
                $resizedImage = AppHelper::resizeAndCompressImage($file);

                // Generate filename
                $fileName = 'Uploads/outlet_' .
                    time() . '_' .
                    Str::random(10) . '.jpg'; // or use $file->extension() if your helper keeps the original format

                // Save resized image
                Storage::disk('public')->put($fileName, $resizedImage);

                $data['outlet_photo'] = $fileName;
            }

            $customer->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Customer updated successfully',
                'data' => $customer->fresh()
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function shortEncrypt($string)
    {
        $key = substr(hash('sha256', config('app.key')), 0, 32);
        $iv = random_bytes(16);

        $encrypted = openssl_encrypt(
            $string,
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        $result = base64_encode($iv . $encrypted);

        return rtrim(
            strtr($result, '+/', '-_'),
            '='
        );
    }

    public function export(Request $request)
    {
        $loggedInUser = auth()->user();

        if (!$loggedInUser) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        /*
        |--------------------------------------------------------------------------
        | Logged-in User
        |--------------------------------------------------------------------------
        */

        $loggedInUserRole = $loggedInUser->role_id;
        $loggedInUserId   = $loggedInUser->id;
        $loggedInUserType = $loggedInUser->type;

        $allowedTypes = [
            AppHelper::SALE,
            AppHelper::SE
        ];

        /*
        |--------------------------------------------------------------------------
        | Customer Query
        |--------------------------------------------------------------------------
        |
        | Eager load SUP and RSM to avoid N+1 queries.
        |
        */

        $query = Customer::with([
            'user.supervisor',
            'user.rsm',
            'depo'
        ])->orderByDesc('id');

        /*
        |--------------------------------------------------------------------------
        | User Permission
        |--------------------------------------------------------------------------
        */

        $userIds = [$loggedInUserId];

        $isAdmin = (
            $loggedInUserType == AppHelper::ALL ||
            in_array($loggedInUserRole, [
                AppHelper::USER_SUPER_ADMIN,
                AppHelper::USER_ADMIN,
                AppHelper::USER_DIRECTOR
            ])
        );

        if (!$isAdmin) {

            /*
            |--------------------------------------------------------------------------
            | Manager
            |--------------------------------------------------------------------------
            */

            if ($loggedInUserRole == AppHelper::USER_MANAGER) {

                $managedUserIds = User::where(function ($q) use ($loggedInUserId) {

                    $q->where('manager_id', $loggedInUserId)
                        ->orWhere('rsm_id', $loggedInUserId)
                        ->orWhere('sup_id', $loggedInUserId)
                        ->orWhere('asm_id', $loggedInUserId);

                })
                    ->whereIn('type', $allowedTypes)
                    ->pluck('id')
                    ->toArray();

            }

            /*
            |--------------------------------------------------------------------------
            | RSM
            |--------------------------------------------------------------------------
            */

            elseif ($loggedInUserRole == AppHelper::USER_RSM) {

                $managedUserIds = User::where(function ($q) use ($loggedInUserId) {

                    $q->where('rsm_id', $loggedInUserId)
                        ->orWhere('sup_id', $loggedInUserId)
                        ->orWhere('asm_id', $loggedInUserId);

                })
                    ->whereIn('type', $allowedTypes)
                    ->pluck('id')
                    ->toArray();

            }

            /*
            |--------------------------------------------------------------------------
            | SUP
            |--------------------------------------------------------------------------
            */

            elseif ($loggedInUserRole == AppHelper::USER_SUP) {

                $managedUserIds = User::where(function ($q) use ($loggedInUserId) {

                    $q->where('sup_id', $loggedInUserId)
                        ->orWhere('asm_id', $loggedInUserId);

                })
                    ->whereIn('type', $allowedTypes)
                    ->pluck('id')
                    ->toArray();

            }

            /*
            |--------------------------------------------------------------------------
            | ASM
            |--------------------------------------------------------------------------
            */

            elseif ($loggedInUserRole == AppHelper::USER_ASM) {

                $managedUserIds = User::where('asm_id', $loggedInUserId)
                    ->whereIn('type', $allowedTypes)
                    ->pluck('id')
                    ->toArray();

            }

            else {

                $managedUserIds = [];

            }

            /*
            |--------------------------------------------------------------------------
            | Combine Logged-in User + Managed Users
            |--------------------------------------------------------------------------
            */

            $userIds = array_unique(
                array_merge($userIds, $managedUserIds)
            );

            /*
            |--------------------------------------------------------------------------
            | Apply User Permission
            |--------------------------------------------------------------------------
            */

            $query->whereIn(
                'user_id',
                $userIds
            );

            /*
            |--------------------------------------------------------------------------
            | Only SALE / SE Users
            |--------------------------------------------------------------------------
            */

            $query->whereHas('user', function ($q) use ($allowedTypes) {

                $q->whereIn('type', $allowedTypes);

            });
        }

        /*
        |--------------------------------------------------------------------------
        | User Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('user_id')) {

            $query->where(
                'user_id',
                $request->input('user_id')
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Area Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('area_id')) {

            $query->where(
                'area_id',
                $request->input('area_id')
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Customer Type Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('customer_type')) {

            $query->where(
                'customer_type',
                $request->input('customer_type')
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Search Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim(
                $request->input('search')
            );

            $query->where(function ($q) use ($search) {

                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_code', 'like', "%{$search}%")
                    ->orWhere('owner_name', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");

            });
        }

        /*
        |--------------------------------------------------------------------------
        | Get Customers
        |--------------------------------------------------------------------------
        */

        $customers = $query->get();

        /*
        |--------------------------------------------------------------------------
        | Prepare Export Data
        |--------------------------------------------------------------------------
        */

        $fullDomain = url('/');

        $data = $customers->map(function ($row) use ($fullDomain) {

            /*
            |--------------------------------------------------------------------------
            | User / SUP / RSM
            |--------------------------------------------------------------------------
            */

            $user = $row->user;

            $sup = $user?->supervisor;

            $rsm = $user?->rsm;

            /*
            |--------------------------------------------------------------------------
            | Language
            |--------------------------------------------------------------------------
            |
            | Same logic as your Blade:
            |
            | en -> full_name_latin
            | other -> full_name
            |
            */

            $lang = $user?->user_lang ?? 'en';

            /*
            |--------------------------------------------------------------------------
            | SSP
            |--------------------------------------------------------------------------
            */

            $sspName = $lang === 'en'
                ? (
                    $user?->full_name_latin
                    ?? $user?->full_name
                    ?? 'N/A'
                )
                : (
                    $user?->full_name
                    ?? $user?->full_name_latin
                    ?? 'N/A'
                );

            /*
            |--------------------------------------------------------------------------
            | SUP
            |--------------------------------------------------------------------------
            */

            $supName = $lang === 'en'
                ? (
                    $sup?->full_name_latin
                    ?? $sup?->full_name
                    ?? 'N/A'
                )
                : (
                    $sup?->full_name
                    ?? $sup?->full_name_latin
                    ?? 'N/A'
                );

            /*
            |--------------------------------------------------------------------------
            | RSM
            |--------------------------------------------------------------------------
            */

            $rsmName = $lang === 'en'
                ? (
                    $rsm?->full_name_latin
                    ?? $rsm?->full_name
                    ?? 'N/A'
                )
                : (
                    $rsm?->full_name
                    ?? $rsm?->full_name_latin
                    ?? 'N/A'
                );

            /*
            |--------------------------------------------------------------------------
            | Area
            |--------------------------------------------------------------------------
            */

            $area = !empty($row->area_id)
                ? AppHelper::getAreaNameById($row->area_id)
                : ($user?->area ?? 'N/A');

            /*
            |--------------------------------------------------------------------------
            | Customer Type
            |--------------------------------------------------------------------------
            */

            $customerType = AppHelper::CUSTOMER_TYPE[$row->customer_type]
                ?? 'N/A';

            /*
            |--------------------------------------------------------------------------
            | Outlet Photo
            |--------------------------------------------------------------------------
            */

            $photoUrl = $row->outlet_photo
                ? $fullDomain
                    . '/photo/'
                    . $this->shortEncrypt($row->outlet_photo)
                : 'N/A';

            /*
            |--------------------------------------------------------------------------
            | Return Same Fields As Web Export
            |--------------------------------------------------------------------------
            */

            return [

                'area' => $area,

                'spp' => $sspName,

                'sup' => $supName,

                'rsm' => $rsmName,

                'depo_name' => $row->depo?->name
                    ?? $row->outlet
                    ?? 'N/A',

                'customer_name' => $row->name
                    ?? 'N/A',

                'customer_code' => $row->code
                    ?? 'N/A',

                'customer_type' => $customerType,

                'contact' => $row->phone
                    ?? 'N/A',

                'address' => (
                    $row->city &&
                    $row->country
                )
                    ? $row->city . ', ' . $row->country
                    : 'N/A',

                'latitude' => $row->latitude
                    ?? 'N/A',

                'longitude' => $row->longitude
                    ?? 'N/A',

                'outlet_photo' => $photoUrl,

            ];

        })->values();

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,
            'message' => 'Customer export data.',
            'total'   => $data->count(),
            'data'    => $data,
        ]);
    }
}

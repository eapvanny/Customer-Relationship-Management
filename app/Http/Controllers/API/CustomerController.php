<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Helpers\AppHelper;
use App\Models\Customer;
use App\Models\Depo;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    //
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $query = Customer::with(['user', 'depo']);

        $roleId   = $user->role_id;
        $userId   = $user->id;
        $userType = $user->type;
        $rawArea  = $user->area;
        $areaId   = AppHelper::getAreaIdByText($rawArea);

        /**
         * SALE + EMPLOYEE → only own customers
         */
        if ($userType == AppHelper::SALE && $roleId == AppHelper::USER_EMPLOYEE) {

            $query->where('user_id', $userId);

        } else {

            /** ---------------- AREA FILTER ---------------- */
            $allowedAreaIds = [];

            if ($rawArea) {

                $normalized = preg_replace('/^[A-Za-z]+-/', '', $rawArea);
                $areas = AppHelper::getAreas();

                // S-04
                if (preg_match('/^S-\d+$/', $normalized)) {

                    foreach ($areas as $subs) {
                        foreach ($subs as $id => $txt) {
                            if ($txt === $normalized) {
                                $allowedAreaIds[] = $id;
                            }
                        }
                    }

                }
                // R1-01
                elseif (preg_match('/^R\d+-\d{2}$/', $normalized)) {

                    foreach ($areas as $group => $subs) {
                        if (str_contains($group, "($normalized)")) {
                            $allowedAreaIds = array_keys($subs);
                        }
                    }

                }
                // R1
                elseif (preg_match('/^R\d+$/', $normalized)) {

                    foreach ($areas as $group => $subs) {
                        if (str_contains($group, "($normalized-")) {
                            $allowedAreaIds = array_merge($allowedAreaIds, array_keys($subs));
                        }
                    }

                } elseif (is_numeric($areaId)) {

                    $allowedAreaIds[] = $areaId;
                }
            }

            $allowedAreaIds = array_unique($allowedAreaIds);

            /** ---------------- ROLE FILTER ---------------- */
            $adminRoles = [
                AppHelper::USER_SUPER_ADMIN,
                AppHelper::USER_ADMINISTRATOR,
                AppHelper::USER_ADMIN,
                AppHelper::USER_DIRECTOR,
                AppHelper::USER_MANAGER,
            ];

            if (!($user->type == AppHelper::ALL || in_array($roleId, $adminRoles))) {

                $query->where(function ($q) use ($user) {

                    $q->where('user_id', $user->id)
                        ->orWhereHas('user', function ($u) use ($user) {

                            $u->where('manager_id', $user->id)
                                ->orWhere('rsm_id', $user->id)
                                ->orWhereJsonContains('asm_id', (string) $user->id)
                                ->orWhereJsonContains('sup_id', (string) $user->id);

                        });

                    foreach (['manager_id', 'rsm_id', 'asm_id', 'sup_id'] as $field) {

                        $ids = AppHelper::normalizeIds($user->$field);

                        if (!empty($ids)) {
                            $q->orWhereIn('user_id', $ids);
                        }
                    }
                });

                if (!empty($allowedAreaIds)) {
                    $query->whereIn('area_id', $allowedAreaIds);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }
        }

        // Pagination
        $perPage = $request->input('per_page', 20);

        $customers = $query
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'status' => true,

            'data' => collect($customers->items())->map(function ($c) {
                return [
                    'id' => $c->id,
                    'created_by' => $c->user
                        ? ($c->user->user_lang == 'en'
                            ? $c->user->full_name_latin
                            : $c->user->full_name)
                        : 'N/A',
                    'area' => AppHelper::getAreaNameById($c->area_id),
                    'depo' => optional($c->depo)->name ?? 'N/A',
                    'customer_code' => (string) $c->code,
                    'customer_name' => (string) $c->name,
                    'customer_type' => (string) (AppHelper::CUSTOMER_TYPE[$c->customer_type] ?? 'N/A'),
                    'phone' => (string) $c->phone,
                ];
            })->values(),

            'pagination' => [
                'current_page' => $customers->currentPage(),
                'last_page'    => $customers->lastPage(),
                'per_page'     => $customers->perPage(),
                'total'        => $customers->total(),
                'from'         => $customers->firstItem(),
                'to'           => $customers->lastItem(),
                'has_more'     => $customers->hasMorePages(),
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

        return response()->json([
            'status' => true,
            'data' => $areas
        ]);
    }

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
                $fileName = 'Uploads/outlet_'
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
}

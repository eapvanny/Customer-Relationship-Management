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
                                ->orWhereJsonContains('asm_id', (string)$user->id)
                                ->orWhereJsonContains('sup_id', (string)$user->id);
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

        $customers = $query->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $customers->map(function ($c) {
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
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function create()
    {
        $depo = Depo::pluck('name', 'id')->all();
        return response()->json([
            'status' => true,
            'data' => $depo
        ]);
    }

    public function getDeposByArea(Request $request)
    {
        $areaId = $request->query('area_id');

        $authUser = auth()->user();


        if (!$areaId) {
            return response()->json([
                'message' => 'Area is required'
            ], 400);
        }


        $query = Depo::where('area_id', $areaId);


        if ($authUser && in_array($authUser->type, [
            AppHelper::SALE,
            AppHelper::SE
        ])) {

            $query->where('user_type', $authUser->type);
        }


        return response()->json(
            $query->select('id', 'name')->get()
        );
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

                $fileName = 'Uploads/outlet_'
                    . time() . '_'
                    . Str::random(10)
                    . '.'
                    . $file->extension();


                Storage::disk('public')->put(
                    $fileName,
                    file_get_contents($file)
                );


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
}

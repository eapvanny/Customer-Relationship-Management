<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Helpers\AppHelper;
use App\Models\Customer;
use Illuminate\Http\Request;

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
}

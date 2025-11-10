<?php

namespace App\Exports;

use App\Http\Helpers\AppHelper;
use App\Models\CustomerProvince;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;

class CustomerProvinceExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        $loggedInUser = Auth::check() ? Auth::user() : null;
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

        $customers = $query->orderBy('id', 'desc')->get();

        return view('exports.customer-province', [
            'rows' => $customers,
            'title' => __('Customer (Province)'),
        ]);
    }
}

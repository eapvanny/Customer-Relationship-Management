<?php

namespace App\Exports;

use App\Http\Helpers\AppHelper;
use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;

class CustomerExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $date1;
    protected $date2;
    protected $user_id;
    protected $area_value;

    public function __construct($date1, $date2, $user_id)
    {
        $this->date1 = $date1;
        $this->user_id = $user_id;
        $this->date2 = $date2;
    }
     public function view(): View
    {
        $loggedInUser = Auth::check() ? Auth::user() : null;
        $query = Customer::with('user');

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
                $managerIds = User::query()
                    ->where('role_id', AppHelper::USER_MANAGER)
                    ->where(function ($q) use ($loggedInUser) {

                        // Current manager
                        $q->where('id', $loggedInUser->id);

                        // Managers with the same manager_id
                        if (!empty($loggedInUser->manager_id)) {
                            $q->orWhere(
                                'manager_id',
                                $loggedInUser->manager_id
                            );
                        }

                        // Managers directly under current manager
                        $q->orWhere(
                            'manager_id',
                            $loggedInUser->manager_id
                        );
                    })
                    ->pluck('id')
                    ->toArray();
                // Manager sees customers of RSMs, Supervisors, ASMs, Employees under them
                $managedUserIds = User::where(function ($q) use ($managerIds) {
                    $q->where('manager_id', $managerIds)
                      ->orWhere('rsm_id', $managerIds)
                      ->orWhere('sup_id', $managerIds)
                      ->orWhere('asm_id', $managerIds);
                })->whereIn('type', $allowedTypes)
                  ->pluck('id')
                  ->toArray();
                $userIds = array_merge($userIds, $managedUserIds);
            } elseif ($loggedInUserRole == AppHelper::USER_RSM) {
                // RSM sees customers of Supervisors, ASMs, Employees under them
                $managedUserIds = User::where(function ($q) use ($loggedInUserId) {
                    $q->where('rsm_id', $loggedInUserId)
                      ->orWhere('sup_id', $loggedInUserId)
                      ->orWhere('asm_id', $loggedInUserId);
                })->whereIn('type', $allowedTypes)
                  ->pluck('id')
                  ->toArray();
                $userIds = array_merge($userIds, $managedUserIds);
            } elseif ($loggedInUserRole == AppHelper::USER_SUP) {
                // Supervisor sees customers of ASMs, Employees under them
                $managedUserIds = User::where(function ($q) use ($loggedInUserId) {
                    $q->where('sup_id', $loggedInUserId)
                      ->orWhere('asm_id', $loggedInUserId);
                })->whereIn('type', $allowedTypes)
                  ->pluck('id')
                  ->toArray();
                $userIds = array_merge($userIds, $managedUserIds);
            } elseif ($loggedInUserRole == AppHelper::USER_ASM) {
                // ASM sees customers of Employees under them
                $managedUserIds = User::where('asm_id', $loggedInUserId)
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

        if ($this->date1 && $this->date2) {

            $query->whereBetween('created_at', [
                Carbon::parse($this->date1)->startOfDay(),
                Carbon::parse($this->date2)->endOfDay()
            ]);
        }
        if ($this->user_id) {
            $query->where('user_id',$this->user_id);
        }

        $customers = $query->orderBy('id', 'desc')->get();

        return view('exports.customer', [
            'rows' => $customers
        ]);
    }
}

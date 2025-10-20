<?php

namespace App\Exports;

use App\Http\Helpers\AppHelper;
use App\Models\Asm_program;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\Auth;

class AsmprogramExport implements FromView
{
    protected $date1;
    protected $date2;
    protected $area_id;

    public function __construct($date1, $date2, $area_id)
    {
        $this->date1 = $date1;
        $this->date2 = $date2;
        $this->area_id = $area_id;
    }

    public function view(): View
    {
        $user = Auth::check() ? Auth::user() : null;
        $query = Asm_program::with('user', 'CustomerProvince', 'region', 'outlet', 'posm1', 'posm2', 'posm3')->orderBy('id', 'desc');

        if ($user) {
            $userRole = $user->role_id;
            $userId = $user->id;
            $userType = $user->type;

            // Collect user IDs to filter reports
            $userIds = [$userId]; // Always include own reports

            // Define allowed user types for subordinates
            $allowedTypes = [AppHelper::SALE, AppHelper::SE];

            if ($userType == AppHelper::ALL || in_array($userRole, [
                AppHelper::USER_SUPER_ADMIN,
                AppHelper::USER_ADMIN,
                AppHelper::USER_DIRECTOR
            ])) {
                // Users with type ALL or roles Super Admin, Admin, Director see all reports
                // No additional filtering needed
            } elseif ($userRole == AppHelper::USER_MANAGER) {
                // Manager sees reports of RSMs, Supervisors, ASMs, Employees under them
                $managedUserIds = \App\Models\User::where(function ($q) use ($userId) {
                    $q->where('manager_id', $userId)
                      ->orWhere('rsm_id', $userId)
                      ->orWhere('sup_id', $userId)
                      ->orWhere('asm_id', $userId);
                })->whereIn('type', $allowedTypes)
                  ->pluck('id')
                  ->toArray();
                $userIds = array_merge($userIds, $managedUserIds);
            } elseif ($userRole == AppHelper::USER_RSM) {
                // RSM sees reports of Supervisors, ASMs, Employees under them
                $managedUserIds = \App\Models\User::where(function ($q) use ($userId) {
                    $q->where('rsm_id', $userId)
                      ->orWhere('sup_id', $userId)
                      ->orWhere('asm_id', $userId);
                })->whereIn('type', $allowedTypes)
                  ->pluck('id')
                  ->toArray();
                $userIds = array_merge($userIds, $managedUserIds);
            } elseif ($userRole == AppHelper::USER_SUP) {
                // Supervisor sees reports of ASMs, Employees under them
                $managedUserIds = \App\Models\User::where(function ($q) use ($userId) {
                    $q->where('sup_id', $userId)
                      ->orWhere('asm_id', $userId);
                })->whereIn('type', $allowedTypes)
                  ->pluck('id')
                  ->toArray();
                $userIds = array_merge($userIds, $managedUserIds);
            } elseif ($userRole == AppHelper::USER_ASM) {
                // ASM sees reports of Employees under them
                $managedUserIds = \App\Models\User::where('asm_id', $userId)
                    ->whereIn('type', $allowedTypes)
                    ->pluck('id')
                    ->toArray();
                $userIds = array_merge($userIds, $managedUserIds);
            }

            // Apply user ID filter unless Super Admin, Admin, Director, or type ALL
            if (!($userType == AppHelper::ALL || in_array($userRole, [
                AppHelper::USER_SUPER_ADMIN,
                AppHelper::USER_ADMIN,
                AppHelper::USER_DIRECTOR
            ]))) {
                $query->whereIn('user_id', array_unique($userIds));
            }

            // Ensure reports belong to users with allowed types (except for ALL/Super Admin/Admin/Director)
            if (!($userType == AppHelper::ALL || in_array($userRole, [
                AppHelper::USER_SUPER_ADMIN,
                AppHelper::USER_ADMIN,
                AppHelper::USER_DIRECTOR
            ]))) {
                $query->whereHas('user', function ($q) use ($allowedTypes) {
                    $q->whereIn('type', $allowedTypes);
                });
            }
        } else {
            // No authenticated user, return no reports
            $query->where('id', 0);
        }

        // Apply date range filter
        if ($this->date1 && $this->date2) {
            $startDate = \Carbon\Carbon::parse($this->date1)->startOfDay();
            $endDate = \Carbon\Carbon::parse($this->date2)->endOfDay();
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        // Apply area_id filter
        if ($this->area_id) {
            $query->where('area_id', $this->area_id);
        }

        return view('exports.asm-program', [
            'asm' => true,
            'rows' => $query->get(),
            'title' => __("ASM Program Export"),
        ]);
    }
}



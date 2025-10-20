<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Wholesale;
use App\Http\Helpers\AppHelper;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromView;

class WholesaleExport implements FromView
{
    protected $date1;
    protected $date2;
    protected $full_name;

    public function __construct($date1 = null, $date2 = null, $full_name = null)
    {
        $this->date1 = $date1;
        $this->date2 = $date2;
        $this->full_name = $full_name;
    }

    public function view(): View
    {
        $user = Auth::user();
        $query = Wholesale::with('user')->orderBy('id', 'desc');

        if (!$user) {
            // No authenticated user, return an empty result
            $query->where('id', 0);
        } else {
            $userRole = $user->role_id;
            $userId = $user->id;
            $userType = $user->type;
            $userIds = [$userId];
            $allowedTypes = [AppHelper::SALE, AppHelper::SE];

            // ✅ Role-based visibility
            if ($userType == AppHelper::ALL || in_array($userRole, [
                AppHelper::USER_SUPER_ADMIN,
                AppHelper::USER_ADMIN,
                AppHelper::USER_DIRECTOR
            ])) {
                // See all
            } elseif ($userRole == AppHelper::USER_MANAGER) {
                $managedUserIds = User::where(function ($q) use ($userId) {
                    $q->where('manager_id', $userId)
                        ->orWhere('rsm_id', $userId)
                        ->orWhere('sup_id', $userId)
                        ->orWhere('asm_id', $userId);
                })->whereIn('type', $allowedTypes)->pluck('id')->toArray();
                $userIds = array_merge($userIds, $managedUserIds);
            } elseif ($userRole == AppHelper::USER_RSM) {
                $managedUserIds = User::where(function ($q) use ($userId) {
                    $q->where('rsm_id', $userId)
                        ->orWhere('sup_id', $userId)
                        ->orWhere('asm_id', $userId);
                })->whereIn('type', $allowedTypes)->pluck('id')->toArray();
                $userIds = array_merge($userIds, $managedUserIds);
            } elseif ($userRole == AppHelper::USER_SUP) {
                $managedUserIds = User::where(function ($q) use ($userId) {
                    $q->where('sup_id', $userId)
                        ->orWhere('asm_id', $userId);
                })->whereIn('type', $allowedTypes)->pluck('id')->toArray();
                $userIds = array_merge($userIds, $managedUserIds);
            } elseif ($userRole == AppHelper::USER_ASM) {
                $managedUserIds = User::where('asm_id', $userId)
                    ->whereIn('type', $allowedTypes)
                    ->pluck('id')->toArray();
                $userIds = array_merge($userIds, $managedUserIds);
            }

            // Apply filters for non-super roles
            if (!($userType == AppHelper::ALL || in_array($userRole, [
                AppHelper::USER_SUPER_ADMIN,
                AppHelper::USER_ADMIN,
                AppHelper::USER_DIRECTOR
            ]))) {
                $query->whereIn('apply_user', array_unique($userIds))
                      ->whereHas('user', function ($q) use ($allowedTypes) {
                          $q->whereIn('type', $allowedTypes);
                      });
            }
        }

        // ✅ Apply date filters
        if (!empty($this->date1) && !empty($this->date2)) {
            $startDate = Carbon::parse($this->date1)->startOfDay();
            $endDate = Carbon::parse($this->date2)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            // Default to today if no date filters
            $query->whereDate('created_at', today());
        }

        // ✅ Apply employee filter
        if (!empty($this->full_name)) {
            $query->where('apply_user', $this->full_name);
        }

        // ✅ Load employees (for dropdown in view)
        $employeeQuery = User::query();
        if ($user && !($userType == AppHelper::ALL || in_array($userRole, [
            AppHelper::USER_SUPER_ADMIN,
            AppHelper::USER_ADMIN,
            AppHelper::USER_DIRECTOR
        ]))) {
            if ($userRole == AppHelper::USER_MANAGER) {
                $employeeQuery->where(function ($q) use ($userId) {
                    $q->where('manager_id', $userId)
                        ->orWhere('rsm_id', $userId)
                        ->orWhere('sup_id', $userId)
                        ->orWhere('asm_id', $userId);
                })->whereIn('type', $allowedTypes);
            } elseif ($userRole == AppHelper::USER_RSM) {
                $employeeQuery->where(function ($q) use ($userId) {
                    $q->where('rsm_id', $userId)
                        ->orWhere('sup_id', $userId)
                        ->orWhere('asm_id', $userId);
                })->whereIn('type', $allowedTypes);
            } elseif ($userRole == AppHelper::USER_SUP) {
                $employeeQuery->where(function ($q) use ($userId) {
                    $q->where('sup_id', $userId)
                        ->orWhere('asm_id', $userId);
                })->whereIn('type', $allowedTypes);
            } elseif ($userRole == AppHelper::USER_ASM) {
                $employeeQuery->where('asm_id', $userId)
                    ->whereIn('type', $allowedTypes);
            } else {
                $employeeQuery->where('id', $userId);
            }
        }

        $full_name = $employeeQuery->get()->mapWithKeys(function ($u) {
            return [$u->id => $u->user_lang === 'en'
                ? ($u->full_name_latin ?? 'N/A')
                : ($u->full_name ?? 'N/A')
            ];
        });

        $is_filter = !empty($this->date1) || !empty($this->date2) || !empty($this->full_name);
        $reports = $query->get();
        $title = 'Wholesale Export';
        return view('exports.wholesale-export', compact('is_filter', 'full_name', 'reports', 'title'));
    }
}

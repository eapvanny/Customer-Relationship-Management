<?php

namespace App\Exports;

use App\Http\Helpers\AppHelper;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;

class ReportsExport implements FromView
{
    protected $date1;
    protected $date2;
    protected $user_id;
    protected $area_id;
    protected $area_value;
    protected $staffIdCard;


    public function __construct($date1, $date2, $user_id, $area_id, $staffIdCard)
    {
        $this->date1 = $date1;
        $this->user_id = $user_id;
        $this->date2 = $date2;
        $this->area_id = $area_id;
        $this->staffIdCard = $staffIdCard;
        $this->area_value = AppHelper::getAreaValue($area_id);
    }

    public function view(): View
    {
        $user = Auth::user();
        $query = Report::with(['user', 'customer'])->orderBy('id', 'desc');

        if (!$user) {
            return view('exports.reports', ['rows' => collect()]);
        }

        $userRole = $user->role_id;
        $userId = $user->id;
        $userType = $user->type;

        $allowedTypes = [AppHelper::SALE, AppHelper::SE];

        // ✅ STEP 1: Get all managed user IDs
        $userIds = [$userId];

        if (!($userType == AppHelper::ALL || in_array($userRole, [
            AppHelper::USER_SUPER_ADMIN,
            AppHelper::USER_ADMIN,
            AppHelper::USER_DIRECTOR
        ]))) {

            $managedUserIds = User::where(function ($q) use ($userId) {
                $q->where('manager_id', $userId)
                    ->orWhere('rsm_id', $userId)
                    ->orWhere('sup_id', $userId)
                    ->orWhere('asm_id', $userId);
            })
                ->whereIn('type', $allowedTypes)
                ->pluck('id')
                ->toArray();

            $userIds = array_merge($userIds, $managedUserIds);
        }

        // ✅ STEP 2: Get ALL staff_id_cards (VERY IMPORTANT)
        $staffIdCards = User::whereIn('id', $userIds)
            ->pluck('staff_id_card')
            ->filter()
            ->toArray();

        // ✅ STEP 3: Apply MAIN FILTER (NO whereIn user_id anymore)
        if (!($userType == AppHelper::ALL || in_array($userRole, [
            AppHelper::USER_SUPER_ADMIN,
            AppHelper::USER_ADMIN,
            AppHelper::USER_DIRECTOR
        ]))) {

            $query->where(function ($q) use ($userIds, $staffIdCards, $allowedTypes) {

                // Normal reports
                $q->where(function ($q1) use ($userIds, $allowedTypes) {
                    $q1->whereIn('reports.user_id', $userIds)
                        ->whereHas('user', function ($q2) use ($allowedTypes) {
                            $q2->whereIn('type', $allowedTypes);
                        });
                });

                // Imported (ssp_id)
                if (!empty($staffIdCards)) {
                    $q->orWhereIn('reports.ssp_id', $staffIdCards);
                }

                // Imported (sup_id)
                if (!empty($staffIdCards)) {
                    $q->orWhereIn('reports.sup_id', $staffIdCards);
                }
            });
        }

        // ✅ STEP 4: Date filter
        if ($this->date1 && $this->date2) {
            $query->whereBetween('date', [
                Carbon::parse($this->date1)->startOfDay(),
                Carbon::parse($this->date2)->endOfDay()
            ]);
        }

        // ✅ STEP 5: User filter (dropdown)
        if ($this->user_id) {

            $selectedUserId = $this->user_id;

            $staffIdCard = User::where('id', $selectedUserId)->value('staff_id_card');

            $teamUserIds = User::where(function ($q) use ($selectedUserId) {
                $q->where('manager_id', $selectedUserId)
                    ->orWhere('rsm_id', $selectedUserId)
                    ->orWhere('sup_id', $selectedUserId)
                    ->orWhere('asm_id', $selectedUserId);
            })->pluck('id')->toArray();

            $teamStaffCards = User::whereIn('id', $teamUserIds)
                ->pluck('staff_id_card')
                ->filter()
                ->toArray();

            $query->where(function ($q) use (
                $selectedUserId,
                $staffIdCard,
                $teamUserIds,
                $teamStaffCards
            ) {

                // direct
                $q->where('reports.user_id', $selectedUserId);

                // team
                if (!empty($teamUserIds)) {
                    $q->orWhereIn('reports.user_id', $teamUserIds);
                }

                // imported self
                if ($staffIdCard) {
                    $q->orWhere('reports.ssp_id', $staffIdCard)
                        ->orWhere('reports.sup_id', $staffIdCard);
                }

                // imported team
                if (!empty($teamStaffCards)) {
                    $q->orWhereIn('reports.ssp_id', $teamStaffCards)
                        ->orWhereIn('reports.sup_id', $teamStaffCards);
                }
            });
        }

        // ✅ STEP 6: Area filter
        if ($this->area_id) {
            $query->where(function ($q) {
                $q->where('area_id', $this->area_id)
                    ->orWhere('area', 'like', '%' . $this->area_value . '%');
            });
        }

        return view('exports.reports', [
            'rows' => $query->get()
        ]);
    }
}

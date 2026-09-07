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

        $query = Report::with(['user', 'customer'])
            ->orderBy('id', 'desc');

        // ============================================================
        // NO LOGIN
        // ============================================================
        if (!$user) {
            return view('exports.reports', [
                'rows' => collect()
            ]);
        }

        $userRole = $user->role_id;
        $userId = $user->id;
        $userType = $user->type;

        $allowedTypes = [
            AppHelper::SALE,
            AppHelper::SE
        ];

        // ============================================================
        // FULL ACCESS CHECK
        // ============================================================
        $hasFullAccess =
            $userType == AppHelper::ALL ||
            in_array($userRole, [
                AppHelper::USER_SUPER_ADMIN,
                AppHelper::USER_ADMIN,
                AppHelper::USER_DIRECTOR
            ]);

        // ============================================================
        // STEP 1:
        // GET ALLOWED USER IDS
        // ============================================================

        // Always include own user ID
        $userIds = [$userId];

        if (!$hasFullAccess) {

            // ========================================================
            // MANAGER
            // ========================================================
            if ($userRole == AppHelper::USER_MANAGER) {

                /*
                |--------------------------------------------------------------------------
                | Get all managers in the same manager group
                |--------------------------------------------------------------------------
                |
                | Example:
                |
                | Manager A
                | id = 10
                | manager_id = 5
                |
                | Manager B
                | id = 20
                | manager_id = 5
                |
                | Both managers belong to the same group.
                |
                */

                $managerIds = User::query()
                    ->where('role_id', AppHelper::USER_MANAGER)
                    ->where(function ($q) use ($user) {

                        // Current manager
                        $q->where('id', $user->id);

                        // Managers directly under current manager
                        $q->orWhere(
                            'manager_id',
                            $user->manager_id
                        );
                    })
                    ->pluck('id')
                    ->toArray();

                /*
                |--------------------------------------------------------------------------
                | Get employees under all managers in the group
                |--------------------------------------------------------------------------
                */

                $managedUserIds = User::query()
                    ->whereIn('type', $allowedTypes)
                    ->where(function ($q) use ($managerIds) {

                        $q->whereIn(
                            'manager_id',
                            $managerIds
                        )
                        ->orWhereIn(
                            'rsm_id',
                            $managerIds
                        )
                        ->orWhereIn(
                            'asm_id',
                            $managerIds
                        )
                        ->orWhereIn(
                            'sup_id',
                            $managerIds
                        );
                    })
                    ->pluck('id')
                    ->toArray();

                $userIds = array_unique(
                    array_merge(
                        $userIds,
                        $managedUserIds
                    )
                );
            }

            // ========================================================
            // OTHER ROLES
            // ========================================================
            else {

                $managedUserIds = User::query()
                    ->whereIn('type', $allowedTypes)
                    ->where(function ($q) use ($userId) {

                        $q->where(
                            'manager_id',
                            $userId
                        )
                        ->orWhere(
                            'rsm_id',
                            $userId
                        )
                        ->orWhere(
                            'asm_id',
                            $userId
                        )
                        ->orWhere(
                            'sup_id',
                            $userId
                        );
                    })
                    ->pluck('id')
                    ->toArray();

                $userIds = array_unique(
                    array_merge(
                        $userIds,
                        $managedUserIds
                    )
                );
            }
        }

        // ============================================================
        // STEP 2:
        // GET ALL STAFF ID CARDS
        // ============================================================

        $staffIdCards = User::whereIn('id', $userIds)
            ->pluck('staff_id_card')
            ->filter()
            ->toArray();

        // ============================================================
        // STEP 3:
        // APPLY MAIN ACCESS FILTER
        // ============================================================

        if (!$hasFullAccess) {

            $query->where(function ($q) use (
                $userIds,
                $staffIdCards,
                $allowedTypes,
            ) {

                // ----------------------------------------------------
                // Normal reports
                // ----------------------------------------------------
                $q->where(function ($q1) use (
                    $userIds,
                    $allowedTypes,
                ) {

                    $q1->whereIn(
                        'reports.user_id',
                        $userIds
                    )
                    ->whereHas('user', function ($q2) use (
                        $allowedTypes,
                    ) {
                        $q2->whereIn(
                            'type',
                            $allowedTypes
                        );
                    });
                });

                // ----------------------------------------------------
                // Imported reports - SSP
                // ----------------------------------------------------
                if (!empty($staffIdCards)) {
                    $q->orWhereIn(
                        'reports.ssp_id',
                        $staffIdCards
                    );
                }

                // ----------------------------------------------------
                // Imported reports - SUP
                // ----------------------------------------------------
                if (!empty($staffIdCards)) {
                    $q->orWhereIn(
                        'reports.sup_id',
                        $staffIdCards
                    );
                }
            });
        }

        // ============================================================
        // STEP 4:
        // DATE FILTER
        // ============================================================

        if ($this->date1 && $this->date2) {

            $query->whereBetween('date', [
                Carbon::parse($this->date1)->startOfDay(),
                Carbon::parse($this->date2)->endOfDay()
            ]);
        }

        // ============================================================
        // STEP 5:
        // USER DROPDOWN FILTER
        // ============================================================

        if ($this->user_id) {

            $selectedUserId = $this->user_id;

            $selectedUser = User::find($selectedUserId);

            $staffIdCard = null;
            $teamUserIds = [];
            $teamStaffCards = [];

            if ($selectedUser) {

                $staffIdCard = $selectedUser->staff_id_card;

                // ====================================================
                // SELECTED USER IS MANAGER
                // ====================================================

                if (
                    $selectedUser->role_id ==
                    AppHelper::USER_MANAGER
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Get managers in the same manager group
                    |--------------------------------------------------------------------------
                    */

                    $selectedManagerIds = User::query()
                        ->where(
                            'role_id',
                            AppHelper::USER_MANAGER
                        )
                        ->where(function ($q) use (
                            $selectedUser
                        ) {

                            // Selected manager
                            $q->where(
                                'id',
                                $selectedUser->id
                            );

                            // Same manager_id
                            if (!empty($selectedUser->manager_id)) {
                                $q->orWhere(
                                    'manager_id',
                                    $selectedUser->manager_id
                                );
                            }

                            // Managers under selected manager
                            $q->orWhere(
                                'manager_id',
                                $selectedUser->id
                            );
                        })
                        ->pluck('id')
                        ->toArray();

                    /*
                    |--------------------------------------------------------------------------
                    | Get employees under all managers
                    |--------------------------------------------------------------------------
                    */

                    $teamUserIds = User::query()
                        ->whereIn(
                            'type',
                            $allowedTypes
                        )
                        ->where(function ($q) use (
                            $selectedManagerIds
                        ) {

                            $q->whereIn(
                                'manager_id',
                                $selectedManagerIds
                            )
                            ->orWhereIn(
                                'rsm_id',
                                $selectedManagerIds
                            )
                            ->orWhereIn(
                                'asm_id',
                                $selectedManagerIds
                            )
                            ->orWhereIn(
                                'sup_id',
                                $selectedManagerIds
                            );
                        })
                        ->pluck('id')
                        ->toArray();
                }

                // ====================================================
                // SELECTED USER IS NOT MANAGER
                // ====================================================

                else {

                    $teamUserIds = User::query()
                        ->whereIn(
                            'type',
                            $allowedTypes
                        )
                        ->where(function ($q) use (
                            $selectedUserId
                        ) {

                            $q->where(
                                'manager_id',
                                $selectedUserId
                            )
                            ->orWhere(
                                'rsm_id',
                                $selectedUserId
                            )
                            ->orWhere(
                                'asm_id',
                                $selectedUserId
                            )
                            ->orWhere(
                                'sup_id',
                                $selectedUserId
                            );
                        })
                        ->pluck('id')
                        ->toArray();
                }

                // ====================================================
                // GET TEAM STAFF CARDS
                // ====================================================

                $teamStaffCards = User::whereIn(
                    'id',
                    $teamUserIds
                )
                    ->pluck('staff_id_card')
                    ->filter()
                    ->toArray();
            }

            // ========================================================
            // APPLY SELECTED USER FILTER
            // ========================================================

            $query->where(function ($q) use (
                $selectedUserId,
                $staffIdCard,
                $teamUserIds,
                $teamStaffCards
            ) {

                // ----------------------------------------------------
                // Selected user direct reports
                // ----------------------------------------------------

                $q->where(
                    'reports.user_id',
                    $selectedUserId
                );

                // ----------------------------------------------------
                // Team users
                // ----------------------------------------------------

                if (!empty($teamUserIds)) {

                    $q->orWhereIn(
                        'reports.user_id',
                        $teamUserIds
                    );
                }

                // ----------------------------------------------------
                // Imported reports - selected user
                // ----------------------------------------------------

                if ($staffIdCard) {

                    $q->orWhere(
                        'reports.ssp_id',
                        $staffIdCard
                    )
                    ->orWhere(
                        'reports.sup_id',
                        $staffIdCard
                    );
                }

                // ----------------------------------------------------
                // Imported reports - team
                // ----------------------------------------------------

                if (!empty($teamStaffCards)) {

                    $q->orWhereIn(
                        'reports.ssp_id',
                        $teamStaffCards
                    )
                    ->orWhereIn(
                        'reports.sup_id',
                        $teamStaffCards
                    );
                }
            });
        }

        // ============================================================
        // STEP 6:
        // AREA FILTER
        // ============================================================

        if ($this->area_id) {

            $query->where(function ($q) {

                $q->where(
                    'area_id',
                    $this->area_id
                )
                ->orWhere(
                    'area',
                    'like',
                    '%' . $this->area_value . '%'
                );
            });
        }

        // ============================================================
        // STEP 7:
        // RETURN EXCEL VIEW
        // ============================================================

        return view('exports.reports', [
            'rows' => $query->get()
        ]);
    }
}
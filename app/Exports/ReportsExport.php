<?php

namespace App\Exports;

use App\Http\Helpers\AppHelper;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ReportsExport implements FromView, WithChunkReading
{
    protected $date1;
    protected $date2;
    protected $user_id;
    protected $area_id;
    protected $area_value;
    protected $staffIdCard;
    protected $userIds;
    protected $staffIdCards;
    protected $userRole;
    protected $userType;
    protected $allowedTypes;
    protected $currentUser;
    protected $isAdmin;
    protected $totals;

    public function __construct($date1, $date2, $user_id, $area_id, $staffIdCard)
    {
        $this->date1 = $date1;
        $this->date2 = $date2;
        $this->user_id = $user_id;
        $this->area_id = $area_id;
        $this->staffIdCard = $staffIdCard;
        $this->area_value = AppHelper::getAreaValue($area_id);
        $this->allowedTypes = [AppHelper::SALE, AppHelper::SE];
        
        // Initialize user data once
        $this->initializeUserData();
    }

    protected function initializeUserData()
    {
        $this->currentUser = Auth::user();
        
        if (!$this->currentUser) {
            $this->isAdmin = false;
            $this->userIds = [];
            $this->staffIdCards = [];
            return;
        }

        $this->userRole = $this->currentUser->role_id;
        $this->userType = $this->currentUser->type;
        
        $this->isAdmin = ($this->userType == AppHelper::ALL || 
            in_array($this->userRole, [
                AppHelper::USER_SUPER_ADMIN,
                AppHelper::USER_ADMIN,
                AppHelper::USER_DIRECTOR
            ]));

        // Get user IDs and staff cards only if not admin
        if (!$this->isAdmin) {
            $this->userIds = $this->getManagedUserIds();
            $this->staffIdCards = $this->getStaffIdCards();
        } else {
            $this->userIds = [];
            $this->staffIdCards = [];
        }
    }

    protected function getManagedUserIds()
    {
        $userId = $this->currentUser->id;
        
        // Get manager IDs
        $managerIds = User::query()
            ->where('role_id', AppHelper::USER_MANAGER)
            ->where(function ($q) use ($userId) {
                $q->where('id', $userId)
                    ->orWhere('manager_id', $this->currentUser->manager_id);
            })
            ->pluck('id')
            ->toArray();

        if (empty($managerIds)) {
            return [$userId];
        }

        // Get all managed users
        $managedUserIds = User::where(function ($q) use ($managerIds) {
            $q->whereIn('manager_id', $managerIds)
                ->orWhereIn('rsm_id', $managerIds)
                ->orWhereIn('sup_id', $managerIds)
                ->orWhereIn('asm_id', $managerIds);
        })
        ->whereIn('type', $this->allowedTypes)
        ->pluck('id')
        ->toArray();

        return array_merge([$userId], $managedUserIds);
    }

    protected function getStaffIdCards()
    {
        return User::whereIn('id', $this->userIds)
            ->pluck('staff_id_card')
            ->filter()
            ->toArray();
    }

    /**
     * Get the query for reports with optimized joins
     */
    protected function getOptimizedQuery()
    {
        // Start with base query - select only needed columns
        $query = Report::query()
            ->select([
                'reports.*',
                'users.id as user_id',
                'users.family_name',
                'users.name',
                'users.family_name_latin',
                'users.name_latin',
                'users.staff_id_card as user_staff_id_card',
                'users.sup_id as user_sup_id',
                'users.rsm_id as user_rsm_id',
                'users.asm_id as user_asm_id',
                'customers.name as customer_name',
                'customers.code as customer_code',
                'depos.name as depo_name'
            ])
            ->leftJoin('users', 'reports.user_id', '=', 'users.id')
            ->leftJoin('customers', 'reports.customer_id', '=', 'customers.id')
            ->leftJoin('depos', 'customers.depo_id', '=', 'depos.id');

        // Apply filters
        $this->applyFilters($query);
        
        // Order by for consistent results
        $query->orderBy('reports.id', 'desc');

        return $query;
    }

    protected function applyFilters($query)
    {
        // Date filter
        if ($this->date1 && $this->date2) {
            $query->whereBetween('reports.date', [
                Carbon::parse($this->date1)->startOfDay(),
                Carbon::parse($this->date2)->endOfDay()
            ]);
        }

        // Apply user/team filter if not admin
        if (!$this->isAdmin && !empty($this->userIds)) {
            $query->where(function ($q) {
                // Normal reports
                $q->where(function ($q1) {
                    $q1->whereIn('reports.user_id', $this->userIds)
                        ->whereHas('user', function ($q2) {
                            $q2->whereIn('type', $this->allowedTypes);
                        });
                });

                // Imported reports
                if (!empty($this->staffIdCards)) {
                    $q->orWhereIn('reports.ssp_id', $this->staffIdCards)
                        ->orWhereIn('reports.sup_id', $this->staffIdCards);
                }
            });
        }

        // User filter (dropdown)
        if ($this->user_id) {
            $this->applyUserFilter($query);
        }

        // Area filter
        if ($this->area_id) {
            $query->where(function ($q) {
                $q->where('reports.area_id', $this->area_id)
                    ->orWhere('reports.area', 'like', '%' . $this->area_value . '%');
            });
        }
    }

    protected function applyUserFilter($query)
    {
        $selectedUserId = $this->user_id;
        
        // Get team user IDs and staff cards in a single query
        $teamData = User::where(function ($q) use ($selectedUserId) {
            $q->where('manager_id', $selectedUserId)
                ->orWhere('rsm_id', $selectedUserId)
                ->orWhere('sup_id', $selectedUserId)
                ->orWhere('asm_id', $selectedUserId);
        })->select('id', 'staff_id_card')->get();

        $teamUserIds = $teamData->pluck('id')->toArray();
        $teamStaffCards = $teamData->pluck('staff_id_card')->filter()->toArray();

        $staffIdCard = User::where('id', $selectedUserId)->value('staff_id_card');

        $query->where(function ($q) use ($selectedUserId, $staffIdCard, $teamUserIds, $teamStaffCards) {
            // Direct user
            $q->where('reports.user_id', $selectedUserId);

            // Team members
            if (!empty($teamUserIds)) {
                $q->orWhereIn('reports.user_id', $teamUserIds);
            }

            // Imported self
            if ($staffIdCard) {
                $q->orWhere('reports.ssp_id', $staffIdCard)
                    ->orWhere('reports.sup_id', $staffIdCard);
            }

            // Imported team
            if (!empty($teamStaffCards)) {
                $q->orWhereIn('reports.ssp_id', $teamStaffCards)
                    ->orWhereIn('reports.sup_id', $teamStaffCards);
            }
        });
    }

    /**
     * Helper method to get user full name
     */
    protected function getUserFullName($user)
    {
        if (!$user) {
            return null;
        }
        
        $familyName = trim($user->family_name ?? '');
        $name = trim($user->name ?? '');
        
        if ($familyName === '' && $name === '') {
            return null;
        }
        
        return trim($familyName . ' ' . $name);
    }

    /**
     * Helper method to get user full name latin
     */
    protected function getUserFullNameLatin($user)
    {
        if (!$user) {
            return null;
        }
        
        $familyName = trim($user->family_name_latin ?? '');
        $name = trim($user->name_latin ?? '');
        
        if ($familyName === '' && $name === '') {
            return null;
        }
        
        return trim($familyName . ' ' . $name);
    }

    public function view(): View
    {
        // Get the query
        $query = $this->getOptimizedQuery();
        
        // Use chunking to process records efficiently
        $rows = collect();
        $total250ml = 0;
        $total350ml = 0;
        $total600ml = 0;
        $total1500ml = 0;

        // Process in chunks of 1000
        $query->chunk(1000, function ($chunk) use (&$rows, &$total250ml, &$total350ml, &$total600ml, &$total1500ml) {
            // Preload related user data for the chunk
            $userIds = $chunk->pluck('user_id')->filter()->unique()->toArray();
            $users = User::whereIn('id', $userIds)
                ->select('id', 'family_name', 'name', 'family_name_latin', 'name_latin', 'staff_id_card', 'sup_id', 'rsm_id', 'asm_id', 'area')
                ->get()
                ->keyBy('id');

            // Preload supervisor, RSM, ASM data
            $supervisorIds = $chunk->pluck('user_sup_id')->filter()->unique()->toArray();
            $rsmIds = $chunk->pluck('user_rsm_id')->filter()->unique()->toArray();
            $asmIds = [];
            foreach ($chunk as $row) {
                if ($row->user_asm_id) {
                    $decoded = is_array($row->user_asm_id) ? $row->user_asm_id : json_decode($row->user_asm_id, true);
                    if (is_array($decoded) && isset($decoded[0])) {
                        $asmIds[] = $decoded[0];
                    } elseif ($decoded) {
                        $asmIds[] = $decoded;
                    }
                }
            }
            $asmIds = array_unique(array_filter($asmIds));

            $supervisors = User::whereIn('id', array_merge($supervisorIds, $rsmIds, $asmIds))
                ->select('id', 'family_name', 'name', 'family_name_latin', 'name_latin', 'staff_id_card')
                ->get()
                ->keyBy('id');

            // Process each row
            foreach ($chunk as $row) {
                $val250ml = intval($row->{'250_ml'} ?? 0);
                $val350ml = intval($row->{'350_ml'} ?? 0);
                $val600ml = intval($row->{'600_ml'} ?? 0);
                $val1500ml = intval($row->{'1500_ml'} ?? 0);

                $total250ml += $val250ml;
                $total350ml += $val350ml;
                $total600ml += $val600ml;
                $total1500ml += $val1500ml;

                $user = $users->get($row->user_id);
                $sup = $supervisors->get($row->user_sup_id);
                $rsm = $supervisors->get($row->user_rsm_id);
                $asm = $this->getASM($row->user_asm_id, $supervisors);

                // Attach processed data to row
                $row->processed_data = [
                    'val_250ml' => $val250ml,
                    'val_350ml' => $val350ml,
                    'val_600ml' => $val600ml,
                    'val_1500ml' => $val1500ml,
                    'default' => $val250ml + $val350ml + $val600ml + $val1500ml,
                    'user' => $user,
                    'user_full_name' => $this->getUserFullName($user),
                    'user_full_name_latin' => $this->getUserFullNameLatin($user),
                    'sup' => $sup,
                    'sup_full_name' => $this->getUserFullName($sup),
                    'rsm' => $rsm,
                    'rsm_full_name' => $this->getUserFullName($rsm),
                    'asm' => $asm,
                    'asm_full_name' => $this->getUserFullName($asm),
                ];

                $rows->push($row);
            }
        });

        // Store totals for view
        $this->totals = [
            'total_250ml' => $total250ml,
            'total_350ml' => $total350ml,
            'total_600ml' => $total600ml,
            'total_1500ml' => $total1500ml,
        ];

        return view('exports.reports', [
            'rows' => $rows,
            'totals' => $this->totals,
            'fullDomain' => url('/'),
        ]);
    }

    protected function getASM($asmId, $supervisors)
    {
        if (empty($asmId)) {
            return null;
        }

        $decoded = is_array($asmId) ? $asmId : json_decode($asmId, true);
        
        if (is_array($decoded) && isset($decoded[0])) {
            return $supervisors->get($decoded[0]);
        } elseif ($decoded) {
            return $supervisors->get($decoded);
        }
        
        return null;
    }

    /**
     * Implement chunk reading for better memory management
     */
    public function chunkSize(): int
    {
        return 1000;
    }
}
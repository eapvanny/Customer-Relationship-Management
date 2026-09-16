<?php

namespace App\Exports;

use App\Http\Helpers\AppHelper;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldQueue;
use Maatwebsite\Excel\Events\AfterSheet;

class ReportsExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithChunkReading,
    WithEvents
{
    protected $date1;
    protected $date2;
    protected $user_id;
    protected $area_id;
    protected $area_value;
    protected $staffIdCard;

    /** Pre-loaded lookup maps (built once) */
    protected array $userCache = [];       // id => User
    protected array $areaCache = [];       // id => name
    protected array $teamUserIds = [];     // precomputed in constructor
    protected array $staffIdCards = [];    // precomputed in constructor

    public function __construct(
        $date1,
        $date2,
        $user_id,
        $area_id,
        $staffIdCard = null
    ) {
        $this->date1       = $date1;
        $this->date2       = $date2;
        $this->user_id     = $user_id;
        $this->area_id     = $area_id;
        $this->staffIdCard = $staffIdCard;

        $this->area_value = AppHelper::getAreaValue($area_id);

        // Pre-warm caches (runs once, not per row)
        $this->preloadLookups();
    }

    /**
     * Load all users / areas we might need in ONE pass.
     */
    protected function preloadLookups(): void
    {
        // Collect all user ids we may reference (sup, rsm, asm of report owners)
        $userIds = Report::query()
            ->select(['user_id'])
            ->distinct()
            ->pluck('user_id')
            ->filter()
            ->unique()
            ->values();

        // Load all users in chunks to avoid a huge IN() clause
        User::query()
            ->whereIn('id', $userIds)
            ->select([
                'id', 'name', 'family_name', 'name_latin', 'family_name_latin',
                'staff_id_card', 'sup_id', 'rsm_id', 'asm_id',
                'driver_name', 'driver_id', 'area',
            ])
            ->chunkById(5000, function ($users) {
                foreach ($users as $u) {
                    $this->userCache[$u->id] = $u;
                }
            });

        // Load related sup / rsm / asm users
        $relatedIds = [];
        foreach ($this->userCache as $u) {
            if ($u->sup_id) $relatedIds[] = (int) $u->sup_id;
            if ($u->rsm_id) $relatedIds[] = (int) $u->rsm_id;
            $asmId = $this->extractAsmId($u->asm_id);
            if ($asmId) $relatedIds[] = $asmId;
        }
        $relatedIds = array_unique(array_filter($relatedIds));

        if (!empty($relatedIds)) {
            User::query()
                ->whereIn('id', $relatedIds)
                ->select([
                    'id', 'name', 'family_name', 'name_latin', 'family_name_latin',
                    'staff_id_card',
                ])
                ->chunkById(5000, function ($users) {
                    foreach ($users as $u) {
                        $this->userCache[$u->id] = $u;
                    }
                });
        }

        // Pre-cache area names
        $areaIds = Report::query()
            ->select('area_id')
            ->distinct()
            ->pluck('area_id')
            ->filter()
            ->unique()
            ->values();

        foreach ($areaIds as $aid) {
            try {
                $this->areaCache[$aid] = AppHelper::getAreaNameById($aid);
            } catch (\Throwable $e) {
                $this->areaCache[$aid] = null;
            }
        }
    }

    /**
     * ============================================================
     * QUERY
     * ============================================================
     */
    public function query()
    {
        $user = Auth::user();

        if (!$user) {
            return Report::query()->whereRaw('1 = 0');
        }

        $query = Report::query()
            ->with([
                'user:id,name,family_name,name_latin,family_name_latin,staff_id_card,sup_id,rsm_id,asm_id,driver_name,driver_id,area',
                'customer:id,name,code',
                'depo:id,name',
            ])
            ->orderByDesc('reports.id');

        $userRole = $user->role_id;
        $userId   = $user->id;
        $userType = $user->type;

        $allowedTypes = [AppHelper::SALE, AppHelper::SE];

        $hasFullAccess =
            $userType == AppHelper::ALL ||
            in_array($userRole, [
                AppHelper::USER_SUPER_ADMIN,
                AppHelper::USER_ADMIN,
                AppHelper::USER_DIRECTOR,
            ]);

        // ---- STEP 1: allowed user ids (same logic, once) ----
        $userIds = [$userId];

        if (!$hasFullAccess) {
            if ($userRole == AppHelper::USER_MANAGER) {
                $managerIds = User::query()
                    ->where('role_id', AppHelper::USER_MANAGER)
                    ->where(function ($q) use ($user) {
                        $q->where('id', $user->id);
                        if (!empty($user->manager_id)) {
                            $q->orWhere('manager_id', $user->manager_id);
                        }
                    })
                    ->pluck('id')
                    ->toArray();

                $managedUserIds = User::query()
                    ->whereIn('type', $allowedTypes)
                    ->where(function ($q) use ($managerIds) {
                        $q->whereIn('manager_id', $managerIds)
                          ->orWhereIn('rsm_id', $managerIds)
                          ->orWhereIn('asm_id', $managerIds)
                          ->orWhereIn('sup_id', $managerIds);
                    })
                    ->pluck('id')
                    ->toArray();

                $userIds = array_unique(array_merge($userIds, $managedUserIds));
            } else {
                $managedUserIds = User::query()
                    ->whereIn('type', $allowedTypes)
                    ->where(function ($q) use ($userId) {
                        $q->where('manager_id', $userId)
                          ->orWhere('rsm_id', $userId)
                          ->orWhere('asm_id', $userId)
                          ->orWhere('sup_id', $userId);
                    })
                    ->pluck('id')
                    ->toArray();

                $userIds = array_unique(array_merge($userIds, $managedUserIds));
            }
        }

        // ---- STEP 2: staff id cards ----
        $staffIdCards = User::query()
            ->whereIn('id', $userIds)
            ->pluck('staff_id_card')
            ->filter()
            ->values()
            ->toArray();

        // ---- STEP 3: main access filter ----
        if (!$hasFullAccess) {
            $query->where(function ($q) use ($userIds, $staffIdCards, $allowedTypes) {
                $q->where(function ($q1) use ($userIds, $allowedTypes) {
                    $q1->whereIn('reports.user_id', $userIds)
                       ->whereHas('user', fn($q2) => $q2->whereIn('type', $allowedTypes));
                });

                if (!empty($staffIdCards)) {
                    $q->orWhereIn('reports.ssp_id', $staffIdCards);
                    $q->orWhereIn('reports.sup_id', $staffIdCards);
                }
            });
        }

        // ---- STEP 4: date ----
        if ($this->date1 && $this->date2) {
            $query->whereBetween('reports.date', [
                Carbon::parse($this->date1)->startOfDay(),
                Carbon::parse($this->date2)->endOfDay(),
            ]);
        }

        // ---- STEP 5: user dropdown ----
        if ($this->user_id) {
            $selectedUserId = $this->user_id;
            $selectedUser   = User::find($selectedUserId);

            $staffIdCard    = null;
            $teamUserIds    = [];
            $teamStaffCards = [];

            if ($selectedUser) {
                $staffIdCard = $selectedUser->staff_id_card;

                if ($selectedUser->role_id == AppHelper::USER_MANAGER) {
                    $selectedManagerIds = User::query()
                        ->where('role_id', AppHelper::USER_MANAGER)
                        ->where(function ($q) use ($selectedUser) {
                            $q->where('id', $selectedUser->id);
                            if (!empty($selectedUser->manager_id)) {
                                $q->orWhere('manager_id', $selectedUser->manager_id);
                            }
                            $q->orWhere('manager_id', $selectedUser->id);
                        })
                        ->pluck('id')
                        ->toArray();

                    $teamUserIds = User::query()
                        ->whereIn('type', $allowedTypes)
                        ->where(function ($q) use ($selectedManagerIds) {
                            $q->whereIn('manager_id', $selectedManagerIds)
                              ->orWhereIn('rsm_id', $selectedManagerIds)
                              ->orWhereIn('asm_id', $selectedManagerIds)
                              ->orWhereIn('sup_id', $selectedManagerIds);
                        })
                        ->pluck('id')
                        ->toArray();
                } else {
                    $teamUserIds = User::query()
                        ->whereIn('type', $allowedTypes)
                        ->where(function ($q) use ($selectedUserId) {
                            $q->where('manager_id', $selectedUserId)
                              ->orWhere('rsm_id', $selectedUserId)
                              ->orWhere('asm_id', $selectedUserId)
                              ->orWhere('sup_id', $selectedUserId);
                        })
                        ->pluck('id')
                        ->toArray();
                }

                if (!empty($teamUserIds)) {
                    $teamStaffCards = User::query()
                        ->whereIn('id', $teamUserIds)
                        ->pluck('staff_id_card')
                        ->filter()
                        ->values()
                        ->toArray();
                }
            }

            $query->where(function ($q) use (
                $selectedUserId, $staffIdCard, $teamUserIds, $teamStaffCards
            ) {
                $q->where('reports.user_id', $selectedUserId);

                if (!empty($teamUserIds)) {
                    $q->orWhereIn('reports.user_id', $teamUserIds);
                }
                if ($staffIdCard) {
                    $q->orWhere('reports.ssp_id', $staffIdCard)
                      ->orWhere('reports.sup_id', $staffIdCard);
                }
                if (!empty($teamStaffCards)) {
                    $q->orWhereIn('reports.ssp_id', $teamStaffCards)
                      ->orWhereIn('reports.sup_id', $teamStaffCards);
                }
            });
        }

        // ---- STEP 6: area ----
        if ($this->area_id) {
            $query->where(function ($q) {
                $q->where('reports.area_id', $this->area_id)
                  ->orWhere('reports.area', 'like', '%' . $this->area_value . '%');
            });
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Area', 'SSP_NAME', 'SSP_ID', 'Dri_Name', 'Dri_ID',
            'SUP_NAME', 'SUP_ID', 'ASM_NAME', 'RSM_NAME', 'Depo Name',
            'Customer Name', 'Customer Code', 'SO Number', 'SO Date',
            '250ml (Case)', '350ml (Case)', '600ml (Case)', '1500ml (Case)',
            'Default', 'Latitude', 'Longitude', 'Address',
            'Photo Outlet', 'POSM PHOTO',
            'POSM1', 'Quantity1', 'POSM2', 'Quantity2', 'POSM3', 'Quantity3',
            'Status',
        ];
    }

    public function map($row): array
    {
        $reportUser = $row->user;
        $lang = session('user_lang', 'kh');

        // SUP
        $sup = null;
        if ($reportUser?->sup_id) {
            $sup = $this->userCache[(int) $reportUser->sup_id] ?? null;
        }

        // RSM
        $rsm = null;
        if ($reportUser?->rsm_id) {
            $rsm = $this->userCache[(int) $reportUser->rsm_id] ?? null;
        }

        // ASM
        $asmId = $this->extractAsmId($reportUser?->asm_id);
        $asm   = $asmId ? ($this->userCache[$asmId] ?? null) : null;

        // SSP name
        $sspName = '';
        if ($reportUser) {
            $sspName = $lang === 'kh'
                ? trim(($reportUser->family_name ?? '') . ' ' . ($reportUser->name ?? ''))
                : trim(($reportUser->family_name_latin ?? '') . ' ' . ($reportUser->name_latin ?? ''));
        }

        // Helper for names
        $nameOf = function ($u) use ($lang) {
            if (!$u) return '';
            return $lang === 'kh'
                ? trim(($u->family_name ?? '') . ' ' . ($u->name ?? ''))
                : trim(($u->family_name_latin ?? '') . ' ' . ($u->name_latin ?? ''));
        };

        $supName = $nameOf($sup);
        $rsmName = $nameOf($rsm);
        $asmName = $nameOf($asm);

        // Area (cached)
        $areaName = 'N/A';
        if (!empty($row->area_id)) {
            $areaName = $this->areaCache[$row->area_id]
                ?? ($row->user?->area ?? 'N/A');
        } else {
            $areaName = $row->user?->area ?? 'N/A';
        }

        // Customer
        $customerName = '';
        $customerCode = '';
        if ($row->customer) {
            $customerName = $lang === 'kh'
                ? ($row->customer->family_name
                    ?? $row->customer->customer_name
                    ?? $row->customer->name
                    ?? '')
                : ($row->customer->name_latin
                    ?? $row->customer->customer_name
                    ?? $row->customer->name
                    ?? '');
            $customerCode = $row->customer->customer_code
                ?? $row->customer->code
                ?? '';
        }

        $depoName = $row->depo?->name ?? '';

        // ML values
        $val250ml  = ($row->{'250_ml'}  === null || $row->{'250_ml'}  === '') ? '0' : (string) $row->{'250_ml'};
        $val350ml  = ($row->{'350_ml'}  === null || $row->{'350_ml'}  === '') ? '0' : (string) $row->{'350_ml'};
        $val600ml  = ($row->{'600_ml'}  === null || $row->{'600_ml'}  === '') ? '0' : (string) $row->{'600_ml'};
        $val1500ml = ($row->{'1500_ml'} === null || $row->{'1500_ml'} === '') ? '0' : (string) $row->{'1500_ml'};

        $default = (int) $val250ml + (int) $val350ml + (int) $val600ml + (int) $val1500ml;

        // Address
        $address = !empty($row->address)
            ? $row->address
            : trim(
                ($row->city ?? '') .
                ((!empty($row->city) && !empty($row->country)) ? ', ' : '') .
                ($row->country ?? '')
            );

        // Photos — build base URL once
        static $baseUrl = null;
        if ($baseUrl === null) {
            $baseUrl = url('/');
        }

        $photoOutlet = '';

        if (!empty($row->outlet_photo)) {

            $photoUrl = url('/') . '/photo/' . AppHelper::shortEncrypt(
                $row->outlet_photo
            );

            $photoOutlet = '=HYPERLINK("' . $photoUrl . '","OUTLET_URL")';
        }

        /*
        |--------------------------------------------------------------------------
        | POSM PHOTO
        |--------------------------------------------------------------------------
        */

        $posmPhoto = '';

        if (!empty($row->photo)) {

            $posmUrl = url('/') . '/photo/' . AppHelper::shortEncrypt(
                $row->photo
            );

            $posmPhoto = '=HYPERLINK("' . $posmUrl . '","POSM_URL")';
        }

        // POSM
        $posm1 = $this->getMaterialName($row->posm  ?? $row->posm_name1);
        $posm2 = $this->getMaterialName($row->posm2 ?? $row->posm_name2);
        $posm3 = $this->getMaterialName($row->posm3 ?? $row->posm_name3);

        $quantity1 = $row->qty  ?? 0;
        $quantity2 = $row->qty2 ?? 0;
        $quantity3 = $row->qty3 ?? 0;

        return [
            $areaName,
            $sspName,
            $reportUser?->staff_id_card ?? '',
            $reportUser?->driver_name ?? '',
            $reportUser?->driver_id   ?? '',
            $supName,
            $sup?->staff_id_card ?? '',
            $asmName,
            $rsmName,
            $depoName,
            $customerName,
            $customerCode,
            $row->so_number ?? '',
            $row->date ? Carbon::parse($row->date)->format('d-M-Y h:i A') : '',
            $val250ml,
            $val350ml,
            $val600ml,
            $val1500ml,
            $default,
            $row->latitude  ?? '',
            $row->longitude ?? '',
            $address,
            $photoOutlet,
            $posmPhoto,
            __($posm1),
            $quantity1,
            __($posm2),
            $quantity2,
            __($posm3),
            $quantity3,
            $row->status ?? '',
        ];
    }

    protected function extractAsmId($value)
    {
        if (empty($value)) return null;

        if (is_array($value)) {
            $first = reset($value);
            if (is_array($first)) $first = reset($first);
            return is_numeric($first) ? (int) $first : null;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                if (is_array($decoded)) {
                    $first = reset($decoded);
                    if (is_array($first)) $first = reset($first);
                    return is_numeric($first) ? (int) $first : null;
                }
                if (is_numeric($decoded)) return (int) $decoded;
            }

            if (str_contains($value, ',')) {
                $parts = array_filter(array_map('trim', explode(',', $value)));
                $first = reset($parts);
                return is_numeric($first) ? (int) $first : null;
            }

            return is_numeric($value) ? (int) $value : null;
        }

        return is_numeric($value) ? (int) $value : null;
    }

    protected function getMaterialName($value)
    {
        if (empty($value)) return '';

        static $materials = null;
        if ($materials === null) {
            $materials = AppHelper::MATERIAL;
        }

        if (is_array($materials) && array_key_exists($value, $materials)) {
            return $materials[$value];
        }

        if (is_numeric($value) && is_array($materials)) {
            $key = (int) $value;
            if (array_key_exists($key, $materials)) return $materials[$key];
        }

        return $value;
    }

    public function chunkSize(): int
    {
        return 2000; // bigger chunks = fewer round-trips, still memory-safe
    }

    /**
     * ============================================================
     * EXCEL EVENTS — now O(1) instead of O(rows × cols)
     * ============================================================
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                if ($highestRow < 2) return;

                // --- Hyperlink style: apply to ONLY the two columns ---
                // Use range styling (single call), not per-cell.
                $sheet->getStyle("W2:W{$highestRow}")->applyFromArray([
                    'font' => [
                        'underline' => 'single',
                        'color'     => ['argb' => 'FF0000FF'],
                    ],
                ]);
                $sheet->getStyle("X2:X{$highestRow}")->applyFromArray([
                    'font' => [
                        'underline' => 'single',
                        'color'     => ['argb' => 'FF0000FF'],
                    ],
                ]);

                // --- Total row ---
                $totalRow = $highestRow + 1;
                $sheet->mergeCells("A{$totalRow}:N{$totalRow}");
                $sheet->setCellValue("A{$totalRow}", 'TOTAL');
                $sheet->setCellValue("O{$totalRow}", "=SUM(O2:O{$highestRow})");
                $sheet->setCellValue("P{$totalRow}", "=SUM(P2:P{$highestRow})");
                $sheet->setCellValue("Q{$totalRow}", "=SUM(Q2:Q{$highestRow})");
                $sheet->setCellValue("R{$totalRow}", "=SUM(R2:R{$highestRow})");
                $sheet->setCellValue("S{$totalRow}", "=SUM(S2:S{$highestRow})");

                // --- Bold header & total ---
                $sheet->getStyle("A1:AE1")->getFont()->setBold(true);
                $sheet->getStyle("A{$totalRow}:AE{$totalRow}")->getFont()->setBold(true);

                // --- ⚠️ REMOVED setAutoSize(true) loop ---
                // Auto-size on 10k+ rows is the #1 killer. Instead,
                // set fixed widths (fast) or use approximate defaults.
                $widths = [
                    'A' => 14, 'B' => 22, 'C' => 14, 'D' => 18, 'E' => 14,
                    'F' => 22, 'G' => 14, 'H' => 18, 'I' => 18, 'J' => 18,
                    'K' => 24, 'L' => 14, 'M' => 16, 'N' => 20, 'O' => 12,
                    'P' => 12, 'Q' => 12, 'R' => 12, 'S' => 10, 'T' => 12,
                    'U' => 12, 'V' => 30, 'W' => 14, 'X' => 14,
                    'Y' => 20, 'Z' => 12, 'AA' => 20, 'AB' => 12,
                    'AC' => 20, 'AD' => 12, 'AE' => 12,
                ];
                foreach ($widths as $col => $w) {
                    $sheet->getColumnDimension($col)->setWidth($w);
                }
            },
        ];
    }
}
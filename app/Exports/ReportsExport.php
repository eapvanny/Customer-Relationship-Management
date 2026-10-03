<?php

namespace App\Exports;

use App\Http\Helpers\AppHelper;
use App\Models\Report;
use App\Models\User;
use App\Models\ReportExportCache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ReportsExport implements
    FromGenerator,
    WithHeadings,
    WithEvents
{
    protected $date1;
    protected $date2;
    protected $user_id;
    protected $area_id;
    protected $area_value;
    protected $staffIdCard;

    /**
     * Number of reports processed at one time.
     */
    protected int $chunkSize = 500;

    /**
     * Cache users to avoid repeated User::find()
     */
    protected array $userCache = [];

    /**
     * Current language.
     */
    protected string $lang;

    public function __construct(
        $date1,
        $date2,
        $user_id,
        $area_id,
        $staffIdCard = null
    ) {
        $this->date1 = $date1;
        $this->date2 = $date2;
        $this->user_id = $user_id;
        $this->area_id = $area_id;
        $this->staffIdCard = $staffIdCard;

        $this->area_value = AppHelper::getAreaValue($area_id);

        $this->lang = session('user_lang', 'kh');
    }

    /**
     * ============================================================
     * QUERY
     * ============================================================
     */
    protected function reportQuery()
    {
        $user = Auth::user();

        /**
         * No login
         */
        if (!$user) {
            return Report::query()
                ->whereRaw('1 = 0');
        }

        /**
         * IMPORTANT:
         *
         * Do NOT select:
         * reports.asm_name
         * reports.sup_name
         * reports.rsm_name
         *
         * Those columns do not exist.
         */
        $query = Report::query()
            ->orderByDesc('reports.id');

        $userRole = $user->role_id;
        $userId   = $user->id;
        $userType = $user->type;

        $allowedTypes = [
            AppHelper::SALE,
            AppHelper::SE,
        ];

        /**
         * ========================================================
         * FULL ACCESS
         * ========================================================
         */
        $hasFullAccess =
            $userType == AppHelper::ALL ||
            in_array($userRole, [
                AppHelper::USER_SUPER_ADMIN,
                AppHelper::USER_ADMIN,
                AppHelper::USER_DIRECTOR,
                AppHelper::USER_MANAGER,
            ]);

        /**
         * ========================================================
         * STEP 1
         * GET ALLOWED USER IDS
         * ========================================================
         */
        $userIds = [$userId];

        if (!$hasFullAccess) {

            /**
             * ====================================================
             * MANAGER
             * ====================================================
             */
            if ($userRole == AppHelper::USER_MANAGER) {

                $managerIds = User::query()
                    ->where(
                        'role_id',
                        AppHelper::USER_MANAGER
                    )
                    ->where(function ($q) use ($user) {

                        /**
                         * Current manager
                         */
                        $q->where(
                            'id',
                            $user->id
                        );

                        /**
                         * Same manager group
                         */
                        if (!empty($user->manager_id)) {
                            $q->orWhere(
                                'manager_id',
                                $user->manager_id
                            );
                        }
                    })
                    ->pluck('id')
                    ->toArray();

                /**
                 * Employees under managers
                 */
                $managedUserIds = User::query()
                    ->whereIn(
                        'type',
                        $allowedTypes
                    )
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

            /**
             * ====================================================
             * OTHER ROLES
             * ====================================================
             */
            else {

                $managedUserIds = User::query()
                    ->whereIn(
                        'type',
                        $allowedTypes
                    )
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

        /**
         * ========================================================
         * STEP 2
         * GET STAFF ID CARDS
         * ========================================================
         */
        $staffIdCards = User::query()
            ->whereIn('id', $userIds)
            ->pluck('staff_id_card')
            ->filter()
            ->values()
            ->toArray();

        /**
         * ========================================================
         * STEP 3
         * MAIN ACCESS FILTER
         * ========================================================
         */
        if (!$hasFullAccess) {

            $query->where(function ($q) use (
                $userIds,
                $staffIdCards,
                $allowedTypes
            ) {

                /**
                 * Normal reports
                 */
                $q->where(function ($q1) use (
                    $userIds,
                    $allowedTypes
                ) {

                    $q1->whereIn(
                        'reports.user_id',
                        $userIds
                    )
                        ->whereHas(
                            'user',
                            function ($q2) use ($allowedTypes) {
                                $q2->whereIn(
                                    'type',
                                    $allowedTypes
                                );
                            }
                        );
                });

                /**
                 * Imported SSP
                 */
                if (!empty($staffIdCards)) {

                    $q->orWhereIn(
                        'reports.ssp_id',
                        $staffIdCards
                    );
                }

                /**
                 * Imported SUP
                 */
                if (!empty($staffIdCards)) {

                    $q->orWhereIn(
                        'reports.sup_id',
                        $staffIdCards
                    );
                }
            });
        }

        /**
         * ========================================================
         * STEP 4
         * DATE FILTER
         * ========================================================
         */
        if ($this->date1 && $this->date2) {

            $query->whereBetween(
                'reports.date',
                [
                    Carbon::parse($this->date1)
                        ->startOfDay(),

                    Carbon::parse($this->date2)
                        ->endOfDay(),
                ]
            );
        }

        /**
         * ========================================================
         * STEP 5
         * USER FILTER
         * ========================================================
         */
        if ($this->user_id) {

            $selectedUserId = $this->user_id;

            $selectedUser = User::find(
                $selectedUserId
            );

            $staffIdCard = null;

            $teamUserIds = [];

            $teamStaffCards = [];

            if ($selectedUser) {

                $staffIdCard =
                    $selectedUser->staff_id_card;

                /**
                 * Selected user is manager
                 */
                if (
                    $selectedUser->role_id ==
                    AppHelper::USER_MANAGER
                ) {

                    /**
                     * Get managers in same group
                     */
                    $selectedManagerIds = User::query()
                        ->where(
                            'role_id',
                            AppHelper::USER_MANAGER
                        )
                        ->where(function ($q) use (
                            $selectedUser
                        ) {

                            /**
                             * Current manager
                             */
                            $q->where(
                                'id',
                                $selectedUser->id
                            );

                            /**
                             * Same manager group
                             */
                            if (
                                !empty(
                                    $selectedUser->manager_id
                                )
                            ) {

                                $q->orWhere(
                                    'manager_id',
                                    $selectedUser->manager_id
                                );
                            }

                            /**
                             * Managers under selected manager
                             */
                            $q->orWhere(
                                'manager_id',
                                $selectedUser->id
                            );
                        })
                        ->pluck('id')
                        ->toArray();

                    /**
                     * Employees under managers
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

                /**
                 * Selected user is not manager
                 */
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

                /**
                 * Team staff cards
                 */
                if (!empty($teamUserIds)) {

                    $teamStaffCards = User::query()
                        ->whereIn(
                            'id',
                            $teamUserIds
                        )
                        ->pluck('staff_id_card')
                        ->filter()
                        ->values()
                        ->toArray();
                }
            }

            /**
             * Apply selected user filter
             */
            $query->where(function ($q) use (
                $selectedUserId,
                $staffIdCard,
                $teamUserIds,
                $teamStaffCards
            ) {

                /**
                 * Selected user
                 */
                $q->where(
                    'reports.user_id',
                    $selectedUserId
                );

                /**
                 * Team users
                 */
                if (!empty($teamUserIds)) {

                    $q->orWhereIn(
                        'reports.user_id',
                        $teamUserIds
                    );
                }

                /**
                 * Imported selected user
                 */
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

                /**
                 * Imported team
                 */
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

        /**
         * ========================================================
         * STEP 6
         * AREA FILTER
         * ========================================================
         */
        if ($this->area_id) {

            $query->where(function ($q) {

                $q->where(
                    'reports.area_id',
                    $this->area_id
                )
                    ->orWhere(
                        'reports.area',
                        'like',
                        '%' . $this->area_value . '%'
                    );
            });
        }

        return $query;
    }

    /**
     * ============================================================
     * GENERATOR
     * ============================================================
     *
     * IMPORTANT:
     *
     * We DO NOT load all reports into memory.
     *
     * Processing:
     *
     * 500 reports
     *      ↓
     * check cache
     *      ↓
     * query only new/changed reports
     *      ↓
     * map
     *      ↓
     * update cache
     *      ↓
     * yield rows
     *      ↓
     * release chunk
     *      ↓
     * next 500
     *
     */
    public function generator(): \Generator
    {
        /**
         * Build query.
         */
        $query = $this->reportQuery();

        /**
         * IMPORTANT:
         *
         * lazyByIdDesc() processes records progressively.
         *
         * This prevents:
         *
         * ->get()
         *
         * from loading 20,000+ reports.
         */
        $reports = $query
            ->with([
                'user',
                'customer',
                'depo',
            ])
            ->lazyByIdDesc(
                $this->chunkSize,
                'reports.id'
            );

        $chunk = [];

        foreach ($reports as $report) {

            $chunk[] = $report;

            if (count($chunk) >= $this->chunkSize) {

                yield from $this->processChunk(
                    $chunk
                );

                /**
                 * Release chunk memory.
                 */
                unset($chunk);

                $chunk = [];

                /**
                 * Force garbage collection.
                 */
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }
            }
        }

        /**
         * Process remaining records.
         */
        if (!empty($chunk)) {

            yield from $this->processChunk(
                $chunk
            );

            unset($chunk);
        }

        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }

    /**
     * ============================================================
     * PROCESS ONE CHUNK
     * ============================================================
     */
    protected function processChunk(array $reports): \Generator
    {
        if (empty($reports)) {
            return;
        }

        /**
         * Get report IDs.
         */
        $reportIds = [];

        foreach ($reports as $report) {
            $reportIds[] = (int) $report->id;
        }

        /**
         * ========================================================
         * GET CACHE ONLY FOR THIS CHUNK
         * ========================================================
         */
        $cachedData = ReportExportCache::query()
            ->whereIn(
                'report_id',
                $reportIds
            )
            ->get([
                'report_id',
                'source_updated_at',
                'data',
            ])
            ->keyBy('report_id');

        /**
         * Reports that need fresh mapping.
         */
        $reportsToRefresh = [];

        /**
         * Cached rows.
         *
         * Only maximum 500 rows.
         */
        $cachedRows = [];

        foreach ($reports as $report) {

            $reportId = (int) $report->id;

            $cache = $cachedData->get(
                $reportId
            );

            /**
             * No cache.
             */
            if (!$cache) {

                $reportsToRefresh[] = $report;

                continue;
            }

            /**
             * Compare updated_at.
             */
            $cacheUpdatedAt =
                $cache->source_updated_at;

            $reportUpdatedAt =
                $report->updated_at;

            $isChanged = false;

            if (
                !$cacheUpdatedAt ||
                !$reportUpdatedAt
            ) {

                $isChanged = true;
            } else {

                $cacheTimestamp =
                    $cacheUpdatedAt instanceof Carbon
                        ? $cacheUpdatedAt->timestamp
                        : Carbon::parse(
                            $cacheUpdatedAt
                        )->timestamp;

                $reportTimestamp =
                    $reportUpdatedAt instanceof Carbon
                        ? $reportUpdatedAt->timestamp
                        : Carbon::parse(
                            $reportUpdatedAt
                        )->timestamp;

                $isChanged =
                    $cacheTimestamp !==
                    $reportTimestamp;
            }

            /**
             * Cache valid.
             */
            if (!$isChanged) {

                $data = $cache->data;

                /**
                 * In case model does not have cast.
                 */
                if (is_string($data)) {

                    $data = json_decode(
                        $data,
                        true
                    );
                }

                if (is_array($data)) {

                    $cachedRows[$reportId] =
                        $data;
                }
            } else {

                /**
                 * Report edited.
                 */
                $reportsToRefresh[] = $report;
            }
        }

        /**
         * ========================================================
         * MAP ONLY NEW / CHANGED REPORTS
         * ========================================================
         */
        $cacheRows = [];

        foreach ($reportsToRefresh as $report) {

            $reportId =
                (int) $report->id;

            /**
             * Map fresh row.
             */
            $mappedRow =
                $this->mapReport(
                    $report
                );

            /**
             * Save into local chunk cache.
             */
            $cachedRows[$reportId] =
                $mappedRow;

            /**
             * Prepare database cache.
             */
            $cacheRows[] = [
                'report_id' =>
                    $reportId,

                'source_updated_at' =>
                    $report->updated_at,

                'data' =>
                    json_encode(
                        $mappedRow,
                        JSON_UNESCAPED_UNICODE |
                        JSON_UNESCAPED_SLASHES
                    ),

                'created_at' =>
                    now(),

                'updated_at' =>
                    now(),
            ];
        }

        /**
         * ========================================================
         * SAVE CACHE
         * ========================================================
         *
         * One bulk upsert instead of:
         *
         * updateOrInsert()
         *
         * for every record.
         *
         */
        if (!empty($cacheRows)) {

            DB::table(
                'report_export_caches'
            )->upsert(
                $cacheRows,
                ['report_id'],
                [
                    'source_updated_at',
                    'data',
                    'updated_at',
                ]
            );
        }

        /**
         * ========================================================
         * RESTORE ORIGINAL ORDER
         * ========================================================
         *
         * reports came in DESC id order.
         */
        foreach ($reports as $report) {

            $reportId =
                (int) $report->id;

            if (
                isset(
                    $cachedRows[$reportId]
                )
            ) {

                yield $cachedRows[$reportId];
            }
        }

        /**
         * Release memory.
         */
        unset(
            $cachedData,
            $reportsToRefresh,
            $cachedRows,
            $cacheRows,
            $reportIds
        );
    }

    /**
     * ============================================================
     * HEADINGS
     * ============================================================
     */
    public function headings(): array
    {
        return [
            'Area',
            'SSP_NAME',
            'SSP_ID',
            'Dri_Name',
            'Dri_ID',
            'SUP_NAME',
            'SUP_ID',
            'ASM_NAME',
            'RSM_NAME',
            'Depo Name',
            'Customer Name',
            'Customer Code',
            'SO Number',
            'SO Date',
            '250ml (Case)',
            '350ml (Case)',
            '600ml (Case)',
            '1500ml (Case)',
            'Default',
            'Latitude',
            'Longitude',
            'Address',
            'Photo Outlet',
            'POSM PHOTO',
            'POSM1',
            'Quantity1',
            'POSM2',
            'Quantity2',
            'POSM3',
            'Quantity3',
            'Status',
        ];
    }

    /**
     * ============================================================
     * MAP REPORT
     * ============================================================
     */
    protected function mapReport($row): array
    {
        /**
         * Main report user.
         */
        $reportUser = $row->user;

        /**
         * SUP.
         */
        $sup = null;

        if ($reportUser?->sup_id) {

            $sup = $this->getUserCached(
                $reportUser->sup_id
            );
        }

        /**
         * RSM.
         */
        $rsm = null;

        if ($reportUser?->rsm_id) {

            $rsm = $this->getUserCached(
                $reportUser->rsm_id
            );
        }

        /**
         * ASM.
         */
        $asmId = $this->extractAsmId(
            $reportUser?->asm_id
        );

        $asm = null;

        if ($asmId) {

            $asm = $this->getUserCached(
                $asmId
            );
        }

        /**
         * SSP NAME.
         */
        $sspName = '';

        if ($reportUser) {

            if ($this->lang === 'kh') {

                $sspName = trim(
                    ($reportUser->family_name ?? '') .
                    ' ' .
                    ($reportUser->name ?? '')
                );
            } else {

                $sspName = trim(
                    ($reportUser->family_name_latin ?? '') .
                    ' ' .
                    ($reportUser->name_latin ?? '')
                );
            }
        }

        /**
         * DRIVER.
         */
        $driverName =
            $reportUser?->driver_name ?? '';

        $driverId =
            $reportUser?->driver_id ?? '';

        /**
         * SUP NAME.
         */
        $supName = '';

        if ($sup) {

            if ($this->lang === 'kh') {

                $supName = trim(
                    ($sup->family_name ?? '') .
                    ' ' .
                    ($sup->name ?? '')
                );
            } else {

                $supName = trim(
                    ($sup->family_name_latin ?? '') .
                    ' ' .
                    ($sup->name_latin ?? '')
                );
            }
        }

        /**
         * RSM NAME.
         */
        $rsmName = '';

        if ($rsm) {

            if ($this->lang === 'kh') {

                $rsmName = trim(
                    ($rsm->family_name ?? '') .
                    ' ' .
                    ($rsm->name ?? '')
                );
            } else {

                $rsmName = trim(
                    ($rsm->family_name_latin ?? '') .
                    ' ' .
                    ($rsm->name_latin ?? '')
                );
            }
        }

        /**
         * ASM NAME.
         */
        $asmName = '';

        if ($asm) {

            if ($this->lang === 'kh') {

                $asmName = trim(
                    ($asm->family_name ?? '') .
                    ' ' .
                    ($asm->name ?? '')
                );
            } else {

                $asmName = trim(
                    ($asm->family_name_latin ?? '') .
                    ' ' .
                    ($asm->name_latin ?? '')
                );
            }
        }

        /**
         * AREA.
         */
        $areaName = 'N/A';

        if (!empty($row->area_id)) {

            try {

                $areaName =
                    AppHelper::getAreaNameById(
                        $row->area_id
                    );

            } catch (\Throwable $e) {

                $areaName =
                    $reportUser?->area ?? 'N/A';
            }

        } else {

            $areaName =
                $reportUser?->area ?? 'N/A';
        }

        /**
         * CUSTOMER.
         */
        $customerName = '';
        $customerCode = '';

        if ($row->customer) {

            if ($this->lang === 'kh') {

                $customerName =
                    $row->customer->family_name ??
                    $row->customer->customer_name ??
                    $row->customer->name ??
                    '';

            } else {

                $customerName =
                    $row->customer->name_latin ??
                    $row->customer->customer_name ??
                    $row->customer->name ??
                    '';
            }

            $customerCode =
                $row->customer->customer_code ??
                $row->customer->code ??
                '';
        }

        /**
         * DEPO.
         */
        $depoName =
            $row->depo?->name ?? '';

        /**
         * ML VALUES.
         */
        $val250ml =
            $row->{'250_ml'} === null ||
            $row->{'250_ml'} === ''
                ? '0'
                : (string) $row->{'250_ml'};

        $val350ml =
            $row->{'350_ml'} === null ||
            $row->{'350_ml'} === ''
                ? '0'
                : (string) $row->{'350_ml'};

        $val600ml =
            $row->{'600_ml'} === null ||
            $row->{'600_ml'} === ''
                ? '0'
                : (string) $row->{'600_ml'};

        $val1500ml =
            $row->{'1500_ml'} === null ||
            $row->{'1500_ml'} === ''
                ? '0'
                : (string) $row->{'1500_ml'};

        /**
         * DEFAULT.
         */
        $default =
            (int) $val250ml +
            (int) $val350ml +
            (int) $val600ml +
            (int) $val1500ml;

        /**
         * ADDRESS.
         */
        $address = '';

        if (!empty($row->address)) {

            $address = $row->address;

        } else {

            $address = trim(
                ($row->city ?? '') .
                (
                    !empty($row->city) &&
                    !empty($row->country)
                        ? ', '
                        : ''
                ) .
                ($row->country ?? '')
            );
        }

        /**
         * OUTLET PHOTO.
         */
        $photoOutlet = '';

        if (!empty($row->outlet_photo)) {

            $photoUrl =
                url('/') .
                '/photo/' .
                AppHelper::shortEncrypt(
                    $row->outlet_photo
                );

            $photoOutlet =
                '=HYPERLINK("' .
                $photoUrl .
                '","OUTLET_URL")';
        }

        /**
         * POSM PHOTO.
         */
        $posmPhoto = '';

        if (!empty($row->photo)) {

            $posmUrl =
                url('/') .
                '/photo/' .
                AppHelper::shortEncrypt(
                    $row->photo
                );

            $posmPhoto =
                '=HYPERLINK("' .
                $posmUrl .
                '","POSM_URL")';
        }

        /**
         * POSM MATERIAL.
         */
        $posm1 = $this->getMaterialName(
            $row->posm ??
            $row->posm_name1
        );

        $posm2 = $this->getMaterialName(
            $row->posm2 ??
            $row->posm_name2
        );

        $posm3 = $this->getMaterialName(
            $row->posm3 ??
            $row->posm_name3
        );

        /**
         * QUANTITY.
         */
        $quantity1 =
            $row->qty ?? 0;

        $quantity2 =
            $row->qty2 ?? 0;

        $quantity3 =
            $row->qty3 ?? 0;

        /**
         * RETURN 31 COLUMNS.
         */
        return [
            $areaName,
            $sspName,
            $reportUser?->staff_id_card ?? '',
            $driverName,
            $driverId,
            $supName,
            $sup?->staff_id_card ?? '',
            $asmName,
            $rsmName,
            $depoName,
            $customerName,
            $customerCode,
            $row->so_number ?? '',
            $row->date
                ? Carbon::parse(
                    $row->date
                )->format('d-M-Y h:i A')
                : '',
            $val250ml,
            $val350ml,
            $val600ml,
            $val1500ml,
            $default,
            $row->latitude ?? '',
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

    /**
     * ============================================================
     * USER CACHE
     * ============================================================
     */
    protected function getUserCached($id)
    {
        if (empty($id)) {
            return null;
        }

        $id = (int) $id;

        if (!array_key_exists(
            $id,
            $this->userCache
        )) {

            /**
             * Select only fields required by export.
             *
             * This reduces memory.
             */
            $this->userCache[$id] =
                User::query()
                    ->select([
                        'id',
                        'family_name',
                        'name',
                        'family_name_latin',
                        'name_latin',
                        'staff_id_card',
                        'area',
                    ])
                    ->find($id);
        }

        return $this->userCache[$id];
    }

    /**
     * ============================================================
     * EXTRACT ASM ID
     * ============================================================
     */
    protected function extractAsmId($value)
    {
        if (empty($value)) {
            return null;
        }

        /**
         * Array.
         */
        if (is_array($value)) {

            if (empty($value)) {
                return null;
            }

            $first = reset($value);

            if (is_array($first)) {
                $first = reset($first);
            }

            return is_numeric($first)
                ? (int) $first
                : null;
        }

        /**
         * JSON.
         */
        if (is_string($value)) {

            $decoded = json_decode(
                $value,
                true
            );

            if (
                json_last_error() ===
                JSON_ERROR_NONE
            ) {

                if (is_array($decoded)) {

                    if (empty($decoded)) {
                        return null;
                    }

                    $first =
                        reset($decoded);

                    if (is_array($first)) {
                        $first =
                            reset($first);
                    }

                    return is_numeric($first)
                        ? (int) $first
                        : null;
                }

                if (is_numeric($decoded)) {
                    return (int) $decoded;
                }
            }

            /**
             * Comma separated.
             */
            if (str_contains($value, ',')) {

                $parts =
                    array_filter(
                        array_map(
                            'trim',
                            explode(',', $value)
                        )
                    );

                $first =
                    reset($parts);

                return is_numeric($first)
                    ? (int) $first
                    : null;
            }

            /**
             * Normal ID.
             */
            return is_numeric($value)
                ? (int) $value
                : null;
        }

        return is_numeric($value)
            ? (int) $value
            : null;
    }

    /**
     * ============================================================
     * POSM MATERIAL
     * ============================================================
     */
    protected function getMaterialName($value)
    {
        if (empty($value)) {
            return '';
        }

        $materials =
            AppHelper::MATERIAL;

        /**
         * Direct key.
         */
        if (
            is_array($materials) &&
            array_key_exists(
                $value,
                $materials
            )
        ) {

            return $materials[$value];
        }

        /**
         * Numeric key.
         */
        if (
            is_numeric($value) &&
            is_array($materials)
        ) {

            $key = (int) $value;

            if (
                array_key_exists(
                    $key,
                    $materials
                )
            ) {

                return $materials[$key];
            }
        }

        return $value;
    }

    /**
     * ============================================================
     * EXCEL EVENTS
     * ============================================================
     */
    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function (
                AfterSheet $event
            ) {

                $sheet =
                    $event->sheet
                        ->getDelegate();

                $highestRow =
                    $sheet->getHighestRow();

                /**
                 * No records.
                 */
                if ($highestRow < 2) {
                    return;
                }

                /**
                 * ====================================================
                 * HYPERLINK STYLE
                 * ====================================================
                 */
                $sheet
                    ->getStyle(
                        "W2:W{$highestRow}"
                    )
                    ->getFont()
                    ->setUnderline('single')
                    ->getColor()
                    ->setARGB('FF0000FF');

                $sheet
                    ->getStyle(
                        "X2:X{$highestRow}"
                    )
                    ->getFont()
                    ->setUnderline('single')
                    ->getColor()
                    ->setARGB('FF0000FF');

                /**
                 * ====================================================
                 * TOTAL ROW
                 * ====================================================
                 */
                $totalRow =
                    $highestRow + 1;

                $sheet->mergeCells(
                    "A{$totalRow}:N{$totalRow}"
                );

                $sheet->setCellValue(
                    "A{$totalRow}",
                    'TOTAL'
                );

                /**
                 * Bottle totals.
                 */
                $sheet->setCellValue(
                    "O{$totalRow}",
                    "=SUM(O2:O{$highestRow})"
                );

                $sheet->setCellValue(
                    "P{$totalRow}",
                    "=SUM(P2:P{$highestRow})"
                );

                $sheet->setCellValue(
                    "Q{$totalRow}",
                    "=SUM(Q2:Q{$highestRow})"
                );

                $sheet->setCellValue(
                    "R{$totalRow}",
                    "=SUM(R2:R{$highestRow})"
                );

                $sheet->setCellValue(
                    "S{$totalRow}",
                    "=SUM(S2:S{$highestRow})"
                );

                /**
                 * ====================================================
                 * HEADING STYLE
                 * ====================================================
                 */
                $sheet
                    ->getStyle('A1:AE1')
                    ->getFont()
                    ->setBold(true);

                /**
                 * ====================================================
                 * TOTAL STYLE
                 * ====================================================
                 */
                $sheet
                    ->getStyle(
                        "A{$totalRow}:AE{$totalRow}"
                    )
                    ->getFont()
                    ->setBold(true);

                /**
                 * ====================================================
                 * IMPORTANT:
                 *
                 * DO NOT use AutoSize for 20,000+ rows.
                 *
                 * AutoSize makes PhpSpreadsheet inspect a large
                 * number of cells and can significantly increase
                 * memory/time.
                 * ====================================================
                 */
                $widths = [
                    'A' => 18,
                    'B' => 25,
                    'C' => 14,
                    'D' => 22,
                    'E' => 14,
                    'F' => 25,
                    'G' => 14,
                    'H' => 25,
                    'I' => 25,
                    'J' => 22,
                    'K' => 30,
                    'L' => 20,
                    'M' => 18,
                    'N' => 20,
                    'O' => 15,
                    'P' => 15,
                    'Q' => 15,
                    'R' => 15,
                    'S' => 15,
                    'T' => 15,
                    'U' => 15,
                    'V' => 35,
                    'W' => 18,
                    'X' => 18,
                    'Y' => 20,
                    'Z' => 12,
                    'AA' => 20,
                    'AB' => 12,
                    'AC' => 20,
                    'AD' => 12,
                    'AE' => 12,
                ];

                foreach ($widths as $column => $width) {

                    $sheet
                        ->getColumnDimension($column)
                        ->setWidth($width);
                }
            },
        ];
    }
}
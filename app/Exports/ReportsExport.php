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

    /**
     * Cache users to avoid repeated User::find()
     */
    protected array $userCache = [];

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
    }

    /**
     * ============================================================
     * QUERY
     * ============================================================
     */
    public function query()
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | No login
        |--------------------------------------------------------------------------
        */

        if (!$user) {
            return Report::query()
                ->whereRaw('1 = 0');
        }

        /*
        |--------------------------------------------------------------------------
        | Base query
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | Do NOT select:
        |
        | reports.asm_name
        | reports.sup_name
        | reports.rsm_name
        |
        | Those columns do not exist in reports.
        |
        */

        $query = Report::query()
            ->with([
                'user',
                'customer',
                'depo',
            ])
            ->orderByDesc('reports.id');

        $userRole = $user->role_id;
        $userId   = $user->id;
        $userType = $user->type;

        $allowedTypes = [
            AppHelper::SALE,
            AppHelper::SE,
        ];

        /*
        |--------------------------------------------------------------------------
        | FULL ACCESS
        |--------------------------------------------------------------------------
        */

        $hasFullAccess =
            $userType == AppHelper::ALL ||
            in_array($userRole, [
                AppHelper::USER_SUPER_ADMIN,
                AppHelper::USER_ADMIN,
                AppHelper::USER_DIRECTOR,
            ]);

        /*
        |--------------------------------------------------------------------------
        | STEP 1
        | GET ALLOWED USER IDS
        |--------------------------------------------------------------------------
        */

        $userIds = [$userId];

        if (!$hasFullAccess) {

            /*
            |--------------------------------------------------------------------------
            | MANAGER
            |--------------------------------------------------------------------------
            */

            if ($userRole == AppHelper::USER_MANAGER) {

                /*
                |--------------------------------------------------------------------------
                | Get managers in same manager group
                |--------------------------------------------------------------------------
                */

                $managerIds = User::query()
                    ->where(
                        'role_id',
                        AppHelper::USER_MANAGER
                    )
                    ->where(function ($q) use ($user) {

                        // Current manager
                        $q->where(
                            'id',
                            $user->id
                        );

                        // Same manager group
                        if (!empty($user->manager_id)) {
                            $q->orWhere(
                                'manager_id',
                                $user->manager_id
                            );
                        }
                    })
                    ->pluck('id')
                    ->toArray();

                /*
                |--------------------------------------------------------------------------
                | Get employees under managers
                |--------------------------------------------------------------------------
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

            /*
            |--------------------------------------------------------------------------
            | OTHER ROLES
            |--------------------------------------------------------------------------
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

        /*
        |--------------------------------------------------------------------------
        | STEP 2
        | GET STAFF ID CARDS
        |--------------------------------------------------------------------------
        */

        $staffIdCards = User::query()
            ->whereIn('id', $userIds)
            ->pluck('staff_id_card')
            ->filter()
            ->values()
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | STEP 3
        | MAIN ACCESS FILTER
        |--------------------------------------------------------------------------
        */

        if (!$hasFullAccess) {

            $query->where(function ($q) use (
                $userIds,
                $staffIdCards,
                $allowedTypes
            ) {

                /*
                |--------------------------------------------------------------------------
                | Normal reports
                |--------------------------------------------------------------------------
                */

                $q->where(function ($q1) use (
                    $userIds,
                    $allowedTypes
                ) {

                    $q1->whereIn(
                        'reports.user_id',
                        $userIds
                    )
                    ->whereHas('user', function ($q2) use (
                        $allowedTypes
                    ) {
                        $q2->whereIn(
                            'type',
                            $allowedTypes
                        );
                    });
                });

                /*
                |--------------------------------------------------------------------------
                | Imported reports - SSP
                |--------------------------------------------------------------------------
                */

                if (!empty($staffIdCards)) {

                    $q->orWhereIn(
                        'reports.ssp_id',
                        $staffIdCards
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Imported reports - SUP
                |--------------------------------------------------------------------------
                */

                if (!empty($staffIdCards)) {

                    $q->orWhereIn(
                        'reports.sup_id',
                        $staffIdCards
                    );
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 4
        | DATE FILTER
        |--------------------------------------------------------------------------
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

        /*
        |--------------------------------------------------------------------------
        | STEP 5
        | USER DROPDOWN FILTER
        |--------------------------------------------------------------------------
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

                /*
                |--------------------------------------------------------------------------
                | SELECTED USER IS MANAGER
                |--------------------------------------------------------------------------
                */

                if (
                    $selectedUser->role_id ==
                    AppHelper::USER_MANAGER
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Get managers in same group
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

                            /*
                            | Current selected manager
                            */
                            $q->where(
                                'id',
                                $selectedUser->id
                            );

                            /*
                            | Same manager group
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

                            /*
                            | Managers under selected manager
                            */
                            $q->orWhere(
                                'manager_id',
                                $selectedUser->id
                            );
                        })
                        ->pluck('id')
                        ->toArray();

                    /*
                    |--------------------------------------------------------------------------
                    | Employees under all managers
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

                /*
                |--------------------------------------------------------------------------
                | SELECTED USER IS NOT MANAGER
                |--------------------------------------------------------------------------
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

                /*
                |--------------------------------------------------------------------------
                | GET TEAM STAFF CARDS
                |--------------------------------------------------------------------------
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

            /*
            |--------------------------------------------------------------------------
            | APPLY SELECTED USER FILTER
            |--------------------------------------------------------------------------
            */

            $query->where(function ($q) use (
                $selectedUserId,
                $staffIdCard,
                $teamUserIds,
                $teamStaffCards
            ) {

                /*
                |--------------------------------------------------------------------------
                | Selected user
                |--------------------------------------------------------------------------
                */

                $q->where(
                    'reports.user_id',
                    $selectedUserId
                );

                /*
                |--------------------------------------------------------------------------
                | Team users
                |--------------------------------------------------------------------------
                */

                if (!empty($teamUserIds)) {

                    $q->orWhereIn(
                        'reports.user_id',
                        $teamUserIds
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Imported reports - selected user
                |--------------------------------------------------------------------------
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

                /*
                |--------------------------------------------------------------------------
                | Imported reports - team
                |--------------------------------------------------------------------------
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

        /*
        |--------------------------------------------------------------------------
        | STEP 6
        | AREA FILTER
        |--------------------------------------------------------------------------
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
     * MAP EACH REPORT TO EXCEL ROW
     * ============================================================
     */
    public function map($row): array
    {
        /*
        |--------------------------------------------------------------------------
        | Main report user
        |--------------------------------------------------------------------------
        */

        $reportUser = $row->user;

        /*
        |--------------------------------------------------------------------------
        | SUP
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | We get SUP from users.sup_id
        | NOT reports.sup_name
        |
        */

        $sup = null;

        if ($reportUser?->sup_id) {

            $sup = $this->getUserCached(
                $reportUser->sup_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | RSM
        |--------------------------------------------------------------------------
        */

        $rsm = null;

        if ($reportUser?->rsm_id) {

            $rsm = $this->getUserCached(
                $reportUser->rsm_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | ASM
        |--------------------------------------------------------------------------
        |
        | asm_id may be:
        |
        | 1
        | "1"
        | [1]
        | ["1"]
        | "[1]"
        | "[\"1\"]"
        |
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

        /*
        |--------------------------------------------------------------------------
        | LANGUAGE
        |--------------------------------------------------------------------------
        */

        $lang = session(
            'user_lang',
            'kh'
        );

        /*
        |--------------------------------------------------------------------------
        | SSP NAME
        |--------------------------------------------------------------------------
        */

        $sspName = '';

        if ($reportUser) {

            if ($lang === 'kh') {

                $sspName =
                    trim(
                        ($reportUser->family_name ?? '') .
                        ' ' .
                        ($reportUser->name ?? '')
                    );

            } else {

                $sspName =
                    trim(
                        ($reportUser->family_name_latin ?? '') .
                        ' ' .
                        ($reportUser->name_latin ?? '')
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | DRIVER NAME
        |--------------------------------------------------------------------------
        */

        $driverName =
            $reportUser?->driver_name ?? '';

        /*
        |--------------------------------------------------------------------------
        | DRIVER ID
        |--------------------------------------------------------------------------
        */

        $driverId =
            $reportUser?->driver_id ?? '';

        /*
        |--------------------------------------------------------------------------
        | SUP NAME
        |--------------------------------------------------------------------------
        */

        $supName = '';

        if ($sup) {

            if ($lang === 'kh') {

                $supName =
                    trim(
                        ($sup->family_name ?? '') .
                        ' ' .
                        ($sup->name ?? '')
                    );

            } else {

                $supName =
                    trim(
                        ($sup->family_name_latin ?? '') .
                        ' ' .
                        ($sup->name_latin ?? '')
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | RSM NAME
        |--------------------------------------------------------------------------
        */

        $rsmName = '';

        if ($rsm) {

            if ($lang === 'kh') {

                $rsmName =
                    trim(
                        ($rsm->family_name ?? '') .
                        ' ' .
                        ($rsm->name ?? '')
                    );

            } else {

                $rsmName =
                    trim(
                        ($rsm->family_name_latin ?? '') .
                        ' ' .
                        ($rsm->name_latin ?? '')
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | ASM NAME
        |--------------------------------------------------------------------------
        */

        $asmName = '';

        if ($asm) {

            if ($lang === 'kh') {

                $asmName =
                    trim(
                        ($asm->family_name ?? '') .
                        ' ' .
                        ($asm->name ?? '')
                    );

            } else {

                $asmName =
                    trim(
                        ($asm->family_name_latin ?? '') .
                        ' ' .
                        ($asm->name_latin ?? '')
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | AREA
        |--------------------------------------------------------------------------
        */

        $areaName = '';

        if (!empty($row->area_id)) {

            try {

                $areaName =
                    AppHelper::getAreaNameById(
                        $row->area_id
                    );

            } catch (\Throwable $e) {

                $areaName =
                    $row->area ?? '';
            }
        } else {

            $areaName =
                $row->area ?? '';
        }

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER
        |--------------------------------------------------------------------------
        */

        $customerName = '';

        $customerCode = '';

        if ($row->customer) {

            if ($lang === 'kh') {

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

        /*
        |--------------------------------------------------------------------------
        | DEPO
        |--------------------------------------------------------------------------
        */

        $depoName =
            $row->depo?->name ?? '';

        /*
        |--------------------------------------------------------------------------
        | ML VALUES
        |--------------------------------------------------------------------------
        */

        $val250ml = $row->{'250_ml'} === null || $row->{'250_ml'} === ''
            ? '0'
            : (string) $row->{'250_ml'};

        $val350ml = $row->{'350_ml'} === null || $row->{'350_ml'} === ''
            ? '0'
            : (string) $row->{'350_ml'};

        $val600ml = $row->{'600_ml'} === null || $row->{'600_ml'} === ''
            ? '0'
            : (string) $row->{'600_ml'};

        $val1500ml = $row->{'1500_ml'} === null || $row->{'1500_ml'} === ''
            ? '0'
            : (string) $row->{'1500_ml'};

        /*
        |--------------------------------------------------------------------------
        | DEFAULT
        |--------------------------------------------------------------------------
        */

        $default =
            (int) $val250ml +
            (int) $val350ml +
            (int) $val600ml +
            (int) $val1500ml;

        /*
        |--------------------------------------------------------------------------
        | ADDRESS
        |--------------------------------------------------------------------------
        */

        $address = '';

        if (!empty($row->address)) {

            $address =
                $row->address;

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

        /*
        |--------------------------------------------------------------------------
        | OUTLET PHOTO
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | POSM MATERIAL
        |--------------------------------------------------------------------------
        */

        $posm1 = $this->getMaterialName(
            $row->posm ?? $row->posm_name1
        );

        $posm2 = $this->getMaterialName(
            $row->posm2 ?? $row->posm_name2
        );

        $posm3 = $this->getMaterialName(
            $row->posm3 ?? $row->posm_name3
        );

        /*
        |--------------------------------------------------------------------------
        | POSM QUANTITY
        |--------------------------------------------------------------------------
        */

        $quantity1 =
            $row->qty ?? 0;

        $quantity2 =
            $row->qty2 ?? 0;

        $quantity3 =
            $row->qty3 ?? 0;

        /*
        |--------------------------------------------------------------------------
        | RETURN 31 COLUMNS
        |--------------------------------------------------------------------------
        */

        return [
            // 1
            $areaName,

            // 2
            $sspName,

            // 3
            $reportUser?->staff_id_card ?? '',

            // 4
            $driverName,

            // 5
            $driverId,

            // 6
            $supName,

            // 7
            $sup?->staff_id_card ?? '',

            // 8
            $asmName,

            // 9
            $rsmName,

            // 10
            $depoName,

            // 11
            $customerName,

            // 12
            $customerCode,

            // 13
            $row->so_number ?? '',

            // 14
            $row->date
                ? Carbon::parse($row->date)->format('d-M-Y h:i A')
                : '',

            // 15
            $val250ml,

            // 16
            $val350ml,

            // 17
            $val600ml,

            // 18
            $val1500ml,

            // 19
            $default,

            // 20
            $row->latitude ?? '',

            // 21
            $row->longitude ?? '',

            // 22
            $address,

            // 23
            $photoOutlet,

            // 24
            $posmPhoto,

            // 25
            $posm1,

            // 26
            $quantity1,

            // 27
            $posm2,

            // 28
            $quantity2,

            // 29
            $posm3,

            // 30
            $quantity3,

            // 31
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

            $this->userCache[$id] =
                User::find($id);
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

        /*
        |--------------------------------------------------------------------------
        | Already array
        |--------------------------------------------------------------------------
        */

        if (is_array($value)) {

            if (empty($value)) {
                return null;
            }

            $first = reset($value);

            if (is_array($first)) {

                $first =
                    reset($first);
            }

            return is_numeric($first)
                ? (int) $first
                : null;
        }

        /*
        |--------------------------------------------------------------------------
        | JSON
        |--------------------------------------------------------------------------
        */

        if (is_string($value)) {

            $decoded =
                json_decode(
                    $value,
                    true
                );

            if (
                json_last_error() === JSON_ERROR_NONE
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

            /*
            |--------------------------------------------------------------------------
            | Comma separated
            |--------------------------------------------------------------------------
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

            /*
            |--------------------------------------------------------------------------
            | Normal ID
            |--------------------------------------------------------------------------
            */

            return is_numeric($value)
                ? (int) $value
                : null;
        }

        /*
        |--------------------------------------------------------------------------
        | Numeric value
        |--------------------------------------------------------------------------
        */

        return is_numeric($value)
            ? (int) $value
            : null;
    }

    /**
     * ============================================================
     * POSM MATERIAL NAME
     * ============================================================
     */
    protected function getMaterialName($value)
    {
        if (empty($value)) {
            return '';
        }

        $materials =
            AppHelper::MATERIAL;

        /*
        |--------------------------------------------------------------------------
        | Direct key
        |--------------------------------------------------------------------------
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

        /*
        |--------------------------------------------------------------------------
        | Numeric key
        |--------------------------------------------------------------------------
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
     * CHUNK SIZE
     * ============================================================
     */
    public function chunkSize(): int
    {
        return 1000;
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

                $sheet = $event->sheet->getDelegate();

                $highestRow = $sheet->getHighestRow();

                /*
                |--------------------------------------------------------------------------
                | If there are no records
                |--------------------------------------------------------------------------
                */

                if ($highestRow < 2) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Hyperlink Style
                |--------------------------------------------------------------------------
                |
                | W = Photo Outlet
                | X = POSM PHOTO
                |
                | Blue + Underline
                |--------------------------------------------------------------------------
                */

                $sheet
                    ->getStyle("W2:W{$highestRow}")
                    ->getFont()
                    ->setUnderline('single')
                    ->getColor()
                    ->setARGB('FF0000FF');

                $sheet
                    ->getStyle("X2:X{$highestRow}")
                    ->getFont()
                    ->setUnderline('single')
                    ->getColor()
                    ->setARGB('FF0000FF');

                /*
                |--------------------------------------------------------------------------
                | Total row
                |--------------------------------------------------------------------------
                */

                $totalRow = $highestRow + 1;

                /*
                |--------------------------------------------------------------------------
                | TOTAL label
                |--------------------------------------------------------------------------
                */

                $sheet->mergeCells(
                    "A{$totalRow}:N{$totalRow}"
                );

                $sheet->setCellValue(
                    "A{$totalRow}",
                    'TOTAL'
                );

                /*
                |--------------------------------------------------------------------------
                | 250ml
                |--------------------------------------------------------------------------
                */

                $sheet->setCellValue(
                    "O{$totalRow}",
                    "=SUM(O2:O{$highestRow})"
                );

                /*
                |--------------------------------------------------------------------------
                | 350ml
                |--------------------------------------------------------------------------
                */

                $sheet->setCellValue(
                    "P{$totalRow}",
                    "=SUM(P2:P{$highestRow})"
                );

                /*
                |--------------------------------------------------------------------------
                | 600ml
                |--------------------------------------------------------------------------
                */

                $sheet->setCellValue(
                    "Q{$totalRow}",
                    "=SUM(Q2:Q{$highestRow})"
                );

                /*
                |--------------------------------------------------------------------------
                | 1500ml
                |--------------------------------------------------------------------------
                */

                $sheet->setCellValue(
                    "R{$totalRow}",
                    "=SUM(R2:R{$highestRow})"
                );

                /*
                |--------------------------------------------------------------------------
                | DEFAULT
                |--------------------------------------------------------------------------
                */

                $sheet->setCellValue(
                    "S{$totalRow}",
                    "=SUM(S2:S{$highestRow})"
                );

                /*
                |--------------------------------------------------------------------------
                | Bold headings
                |--------------------------------------------------------------------------
                */

                $sheet
                    ->getStyle("A1:AE1")
                    ->getFont()
                    ->setBold(true);

                /*
                |--------------------------------------------------------------------------
                | Bold total row
                |--------------------------------------------------------------------------
                */

                $sheet
                    ->getStyle("A{$totalRow}:AE{$totalRow}")
                    ->getFont()
                    ->setBold(true);

                /*
                |--------------------------------------------------------------------------
                | Auto size columns
                |--------------------------------------------------------------------------
                */

                foreach (range('A', 'AE') as $column) {

                    $sheet
                        ->getColumnDimension($column)
                        ->setAutoSize(true);
                }
            },
        ];
    }
}
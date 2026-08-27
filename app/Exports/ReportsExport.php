<?php

namespace App\Exports;

use App\Http\Helpers\AppHelper;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportsExport implements
    FromQuery,
    WithMapping,
    WithHeadings,
    WithChunkReading,
    ShouldAutoSize
{
    protected $date1;
    protected $date2;
    protected $user_id;
    protected $area_id;
    protected $area_value;
    protected $staffIdCard;

    public function __construct(
        $date1,
        $date2,
        $user_id,
        $area_id,
        $staffIdCard
    ) {
        $this->date1 = $date1;
        $this->date2 = $date2;
        $this->user_id = $user_id;
        $this->area_id = $area_id;
        $this->staffIdCard = $staffIdCard;

        $this->area_value = $area_id
            ? AppHelper::getAreaValue($area_id)
            : null;
    }

    public function query()
    {
        $user = Auth::user();

        $query = Report::query()
            ->from('reports')

            /*
            |--------------------------------------------------------------------------
            | SSP / Report User
            |--------------------------------------------------------------------------
            */
            ->leftJoin(
                'users as report_user',
                'report_user.id',
                '=',
                'reports.user_id'
            )

            /*
            |--------------------------------------------------------------------------
            | SUP
            |--------------------------------------------------------------------------
            */
            ->leftJoin(
                'users as sup_user',
                'sup_user.id',
                '=',
                'report_user.sup_id'
            )

            /*
            |--------------------------------------------------------------------------
            | RSM
            |--------------------------------------------------------------------------
            */
            ->leftJoin(
                'users as rsm_user',
                'rsm_user.id',
                '=',
                'report_user.rsm_id'
            )

            /*
            |--------------------------------------------------------------------------
            | Customer
            |--------------------------------------------------------------------------
            */
            ->leftJoin(
                'customers',
                'customers.id',
                '=',
                'reports.customer_id'
            )

            /*
            |--------------------------------------------------------------------------
            | Select
            |--------------------------------------------------------------------------
            */
            ->select([
                'reports.*',

                /*
                |--------------------------------------------------------------------------
                | SSP
                |--------------------------------------------------------------------------
                */

                DB::raw("
                    TRIM(
                        CONCAT(
                            COALESCE(report_user.family_name, ''),
                            ' ',
                            COALESCE(report_user.name, '')
                        )
                    ) AS user_full_name
                "),

                DB::raw("
                    TRIM(
                        CONCAT(
                            COALESCE(report_user.family_name_latin, ''),
                            ' ',
                            COALESCE(report_user.name_latin, '')
                        )
                    ) AS user_full_name_latin
                "),

                'report_user.staff_id_card as user_staff_id_card',
                'report_user.area as user_area',

                /*
                |--------------------------------------------------------------------------
                | SUP
                |--------------------------------------------------------------------------
                */

                DB::raw("
                    TRIM(
                        CONCAT(
                            COALESCE(sup_user.family_name, ''),
                            ' ',
                            COALESCE(sup_user.name, '')
                        )
                    ) AS sup_full_name
                "),

                DB::raw("
                    TRIM(
                        CONCAT(
                            COALESCE(sup_user.family_name_latin, ''),
                            ' ',
                            COALESCE(sup_user.name_latin, '')
                        )
                    ) AS sup_full_name_latin
                "),

                'sup_user.staff_id_card as sup_staff_id_card',

                /*
                |--------------------------------------------------------------------------
                | RSM
                |--------------------------------------------------------------------------
                */

                DB::raw("
                    TRIM(
                        CONCAT(
                            COALESCE(rsm_user.family_name, ''),
                            ' ',
                            COALESCE(rsm_user.name, '')
                        )
                    ) AS rsm_full_name
                "),

                DB::raw("
                    TRIM(
                        CONCAT(
                            COALESCE(rsm_user.family_name_latin, ''),
                            ' ',
                            COALESCE(rsm_user.name_latin, '')
                        )
                    ) AS rsm_full_name_latin
                "),

                'rsm_user.staff_id_card as rsm_staff_id_card',

                /*
                |--------------------------------------------------------------------------
                | Customer
                |--------------------------------------------------------------------------
                */

                'customers.name as customer_name_from_db',
                'customers.code as customer_code',
            ])
            ->orderByDesc('reports.id');

        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        $userRole = $user->role_id;
        $userId   = $user->id;
        $userType = $user->type;

        $allowedTypes = [
            AppHelper::SALE,
            AppHelper::SE,
        ];

        $isAdmin =
            $userType == AppHelper::ALL ||
            in_array($userRole, [
                AppHelper::USER_SUPER_ADMIN,
                AppHelper::USER_ADMIN,
                AppHelper::USER_DIRECTOR,
            ]);

        /*
        |--------------------------------------------------------------------------
        | MAIN PERMISSION FILTER
        |--------------------------------------------------------------------------
        */

        if (!$isAdmin) {

            $managerIds = User::query()
                ->where('role_id', AppHelper::USER_MANAGER)
                ->where(function ($q) use ($user) {
                    $q->where('id', $user->id)
                        ->orWhere('manager_id', $user->manager_id);
                })
                ->pluck('id')
                ->toArray();

            $managedUserIds = User::query()
                ->where(function ($q) use ($managerIds) {

                    $q->whereIn('manager_id', $managerIds)
                        ->orWhereIn('rsm_id', $managerIds)
                        ->orWhereIn('sup_id', $managerIds)
                        ->orWhereIn('asm_id', $managerIds);

                })
                ->whereIn('type', $allowedTypes)
                ->pluck('id')
                ->toArray();

            $userIds = array_unique(
                array_merge(
                    [$userId],
                    $managedUserIds
                )
            );

            /*
            |--------------------------------------------------------------------------
            | Staff IDs
            |--------------------------------------------------------------------------
            */

            $staffIdCards = User::query()
                ->whereIn('id', $userIds)
                ->whereNotNull('staff_id_card')
                ->pluck('staff_id_card')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

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
                    )->whereIn(
                        'report_user.type',
                        $allowedTypes
                    );
                });

                /*
                |--------------------------------------------------------------------------
                | Imported SSP / SUP
                |--------------------------------------------------------------------------
                */

                if (!empty($staffIdCards)) {

                    $q->orWhereIn(
                        'reports.ssp_id',
                        $staffIdCards
                    );

                    $q->orWhereIn(
                        'reports.sup_id',
                        $staffIdCards
                    );
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | DATE
        |--------------------------------------------------------------------------
        */

        if ($this->date1 && $this->date2) {

            $query->whereBetween('reports.date', [
                Carbon::parse($this->date1)->startOfDay(),
                Carbon::parse($this->date2)->endOfDay(),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | USER FILTER
        |--------------------------------------------------------------------------
        */

        if ($this->user_id) {

            $selectedUserId = $this->user_id;

            $selectedStaffIdCard = User::query()
                ->where('id', $selectedUserId)
                ->value('staff_id_card');

            $teamUserIds = User::query()
                ->where(function ($q) use ($selectedUserId) {

                    $q->where('manager_id', $selectedUserId)
                        ->orWhere('rsm_id', $selectedUserId)
                        ->orWhere('sup_id', $selectedUserId)
                        ->orWhere('asm_id', $selectedUserId);

                })
                ->pluck('id')
                ->toArray();

            $teamStaffCards = [];

            if (!empty($teamUserIds)) {

                $teamStaffCards = User::query()
                    ->whereIn('id', $teamUserIds)
                    ->whereNotNull('staff_id_card')
                    ->pluck('staff_id_card')
                    ->filter()
                    ->unique()
                    ->toArray();
            }

            $query->where(function ($q) use (
                $selectedUserId,
                $selectedStaffIdCard,
                $teamUserIds,
                $teamStaffCards
            ) {

                /*
                |--------------------------------------------------------------------------
                | Direct
                |--------------------------------------------------------------------------
                */

                $q->where(
                    'reports.user_id',
                    $selectedUserId
                );

                /*
                |--------------------------------------------------------------------------
                | Team
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
                | Imported self
                |--------------------------------------------------------------------------
                */

                if ($selectedStaffIdCard) {

                    $q->orWhere(
                        'reports.ssp_id',
                        $selectedStaffIdCard
                    );

                    $q->orWhere(
                        'reports.sup_id',
                        $selectedStaffIdCard
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Imported team
                |--------------------------------------------------------------------------
                */

                if (!empty($teamStaffCards)) {

                    $q->orWhereIn(
                        'reports.ssp_id',
                        $teamStaffCards
                    );

                    $q->orWhereIn(
                        'reports.sup_id',
                        $teamStaffCards
                    );
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | AREA
        |--------------------------------------------------------------------------
        */

        if ($this->area_id) {

            $query->where(function ($q) {

                $q->where(
                    'reports.area_id',
                    $this->area_id
                );

                if ($this->area_value) {

                    $q->orWhere(
                        'reports.area',
                        'like',
                        '%' . $this->area_value . '%'
                    );
                }
            });
        }

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | HEADERS
    |--------------------------------------------------------------------------
    */

    public function headings(): array
    {
        return [
            __('Area'),
            __('SSP_NAME'),
            __('SSP_ID'),
            __('SUP_NAME'),
            __('SUP_ID'),
            __('ASM_NAME'),
            __('RSM_NAME'),
            __('Depo Name'),
            __('Customer Name'),
            __('Customer Code'),
            __('SO Number'),
            __('SO Date'),
            __('250ml') . ' (Case)',
            __('350ml') . ' (Case)',
            __('600ml') . ' (Case)',
            __('1500ml') . ' (Case)',
            __('Default'),
            __('Latitude'),
            __('Longitude'),
            __('Address'),
            __('Photo Outlet'),
            __('POSM PHOTO'),
            __('POSM1'),
            __('Quantity1'),
            __('POSM2'),
            __('Quantity2'),
            __('POSM3'),
            __('Quantity3'),
            __('Status'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | MAP
    |--------------------------------------------------------------------------
    */

    public function map($row): array
    {
        $val250 = (int) ($row->{'250_ml'} ?? 0);
        $val350 = (int) ($row->{'350_ml'} ?? 0);
        $val600 = (int) ($row->{'600_ml'} ?? 0);
        $val1500 = (int) ($row->{'1500_ml'} ?? 0);

        $default =
            $val250 +
            $val350 +
            $val600 +
            $val1500;

        $lang = session('user_lang', 'kh');

        /*
        |--------------------------------------------------------------------------
        | SSP
        |--------------------------------------------------------------------------
        */

        $sspName = $lang === 'en'
            ? (
                $row->user_full_name_latin
                ?: $row->user_full_name
                ?: $row->ssp_name
                ?: 'N/A'
            )
            : (
                $row->user_full_name
                ?: $row->user_full_name_latin
                ?: $row->ssp_name
                ?: 'N/A'
            );

        /*
        |--------------------------------------------------------------------------
        | SUP
        |--------------------------------------------------------------------------
        */

        $supName = $lang === 'en'
            ? (
                $row->sup_full_name_latin
                ?: $row->sup_full_name
                ?: $row->sup_name
                ?: 'N/A'
            )
            : (
                $row->sup_full_name
                ?: $row->sup_full_name_latin
                ?: $row->sup_name
                ?: 'N/A'
            );

        /*
        |--------------------------------------------------------------------------
        | RSM
        |--------------------------------------------------------------------------
        */

        $rsmName = $lang === 'en'
            ? (
                $row->rsm_full_name_latin
                ?: $row->rsm_full_name
                ?: $row->rsm_name
                ?: 'N/A'
            )
            : (
                $row->rsm_full_name
                ?: $row->rsm_full_name_latin
                ?: $row->rsm_name
                ?: 'N/A'
            );

        /*
        |--------------------------------------------------------------------------
        | ASM
        |--------------------------------------------------------------------------
        |
        | Keep your current ASM behavior.
        |
        */

        $asmName = $this->getAsmName(
            $row->asm_id,
            $row->asm_name,
            $lang
        );

        /*
        |--------------------------------------------------------------------------
        | AREA
        |--------------------------------------------------------------------------
        */

        static $areaCache = [];

        if ($row->area_id) {

            if (!array_key_exists(
                $row->area_id,
                $areaCache
            )) {

                $areaCache[$row->area_id] =
                    AppHelper::getAreaNameById(
                        $row->area_id
                    );
            }

            $areaName =
                $areaCache[$row->area_id] ?? 'N/A';

        } else {

            $areaName =
                $row->user_area ?? 'N/A';
        }

        /*
        |--------------------------------------------------------------------------
        | POSM
        |--------------------------------------------------------------------------
        */

        $posm = isset(AppHelper::MATERIAL[$row->posm])
            ? __(AppHelper::MATERIAL[$row->posm])
            : ($row->posm_name1 ?? 'N/A');

        $posm2 = isset(AppHelper::MATERIAL[$row->posm2])
            ? __(AppHelper::MATERIAL[$row->posm2])
            : ($row->posm_name2 ?? 'N/A');

        $posm3 = isset(AppHelper::MATERIAL[$row->posm3])
            ? __(AppHelper::MATERIAL[$row->posm3])
            : ($row->posm_name3 ?? 'N/A');

        /*
        |--------------------------------------------------------------------------
        | PHOTO
        |--------------------------------------------------------------------------
        */

        $outletUrl = $row->outlet_photo
            ? url('/photo/' . $this->shortEncrypt(
                $row->outlet_photo
            ))
            : 'No_Photo';

        $posmUrl = $row->photo
            ? url('/photo/' . $this->shortEncrypt(
                $row->photo
            ))
            : 'No_Photo';

        /*
        |--------------------------------------------------------------------------
        | ADDRESS
        |--------------------------------------------------------------------------
        */

        if (
            !empty($row->city) &&
            !empty($row->country)
        ) {

            $address =
                $row->city . ', ' . $row->country;

        } elseif (!empty($row->address)) {

            $address = $row->address;

        } else {

            $address = 'N/A';
        }

        return [

            $areaName,

            $sspName,

            $row->user_staff_id_card
                ?? $row->ssp_id
                ?? 'N/A',

            $supName,

            $row->sup_staff_id_card
                ?? $row->sup_id
                ?? 'N/A',

            $asmName,

            $rsmName,

            $row->outlet_name ?? 'N/A',

            $row->customer_name_from_db
                ?? $row->customer_name
                ?? 'N/A',

            $row->customer_code ?? 'N/A',

            $row->so_number ?? 'N/A',

            $row->date
                ? Carbon::parse($row->date)
                    ->format('d-M-Y')
                : 'N/A',

            $val250,
            $val350,
            $val600,
            $val1500,
            $default,

            $row->latitude ?? 'N/A',
            $row->longitude ?? 'N/A',

            $address,

            $outletUrl,
            $posmUrl,

            $posm,
            $row->qty ?? 'N/A',

            $posm2,
            $row->qty2 ?? 'N/A',

            $posm3,
            $row->qty3 ?? 'N/A',

            $row->status ?? '',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | ASM
    |--------------------------------------------------------------------------
    */

    private function getAsmName(
        $asmId,
        $fallbackName,
        $lang
    ) {
        static $cache = [];

        if (!$asmId) {
            return $fallbackName ?? 'N/A';
        }

        /*
         * Handle JSON ASM IDs
         */
        if (is_string($asmId)) {

            $decoded = json_decode(
                $asmId,
                true
            );

            if (is_array($decoded)) {
                $asmId = $decoded[0] ?? null;
            }
        }

        if (!$asmId) {
            return $fallbackName ?? 'N/A';
        }

        /*
         * Cache ASM users
         */
        if (!array_key_exists($asmId, $cache)) {

            $cache[$asmId] = User::query()
                ->select([
                    'id',
                    'family_name',
                    'name',
                    'family_name_latin',
                    'name_latin',
                ])
                ->find($asmId);
        }

        $asm = $cache[$asmId];

        if (!$asm) {
            return $fallbackName ?? 'N/A';
        }

        if ($lang === 'en') {

            return trim(
                ($asm->family_name_latin ?? '') .
                ' ' .
                ($asm->name_latin ?? '')
            ) ?: ($fallbackName ?? 'N/A');
        }

        return trim(
            ($asm->family_name ?? '') .
            ' ' .
            ($asm->name ?? '')
        ) ?: ($fallbackName ?? 'N/A');
    }

    /*
    |--------------------------------------------------------------------------
    | CHUNK
    |--------------------------------------------------------------------------
    */

    public function chunkSize(): int
    {
        return 1000;
    }

    /*
    |--------------------------------------------------------------------------
    | ENCRYPT
    |--------------------------------------------------------------------------
    */

    private function shortEncrypt($string)
    {
        $key = substr(
            hash(
                'sha256',
                config('app.key')
            ),
            0,
            32
        );

        $iv = random_bytes(16);

        $encrypted = openssl_encrypt(
            $string,
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        $result = base64_encode(
            $iv . $encrypted
        );

        return rtrim(
            strtr($result, '+/', '-_'),
            '='
        );
    }
}
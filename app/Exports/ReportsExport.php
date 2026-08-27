<?php

namespace App\Exports;

use App\Http\Helpers\AppHelper;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class ReportsExport implements
    FromQuery,
    WithMapping,
    WithHeadings,
    WithChunkReading
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
    protected $currentUserId;

    protected $usersMap = [];
    protected $staffCardsMap = [];

    public function __construct(
        $date1,
        $date2,
        $user_id,
        $area_id,
        $staffIdCard,
        $currentUserId = null
    ) {
        $this->date1 = $date1;
        $this->date2 = $date2;
        $this->user_id = $user_id;
        $this->area_id = $area_id;
        $this->staffIdCard = $staffIdCard;
        $this->currentUserId = $currentUserId;

        $this->area_value = AppHelper::getAreaValue($area_id);

        $this->allowedTypes = [
            AppHelper::SALE,
            AppHelper::SE,
        ];

        $this->initializeUserData();
    }

    protected function initializeUserData()
    {
        $this->currentUser = $this->currentUserId
            ? User::find($this->currentUserId)
            : null;

        if (!$this->currentUser) {
            $this->isAdmin = false;
            $this->userIds = [];
            $this->staffIdCards = [];

            return;
        }

        $this->userRole = $this->currentUser->role_id;
        $this->userType = $this->currentUser->type;

        $this->isAdmin =
            $this->userType == AppHelper::ALL ||
            in_array($this->userRole, [
                AppHelper::USER_SUPER_ADMIN,
                AppHelper::USER_ADMIN,
                AppHelper::USER_DIRECTOR,
            ]);

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

        $managedUserIds = User::query()
            ->where(function ($q) use ($managerIds) {
                $q->whereIn('manager_id', $managerIds)
                    ->orWhereIn('rsm_id', $managerIds)
                    ->orWhereIn('sup_id', $managerIds)
                    ->orWhereIn('asm_id', $managerIds);
            })
            ->whereIn('type', $this->allowedTypes)
            ->pluck('id')
            ->toArray();

        return array_values(
            array_unique(
                array_merge([$userId], $managedUserIds)
            )
        );
    }

    protected function getStaffIdCards()
    {
        return User::query()
            ->whereIn('id', $this->userIds)
            ->whereNotNull('staff_id_card')
            ->pluck('staff_id_card')
            ->toArray();
    }

    /**
     * Main query
     */
    public function query()
    {
        $query = Report::query()
            ->select([
                'reports.*',

                // User
                'users.id as report_user_id',
                'users.family_name as user_family_name',
                'users.name as user_name',
                'users.family_name_latin as user_family_name_latin',
                'users.name_latin as user_name_latin',
                'users.staff_id_card as user_staff_id_card',
                'users.sup_id as user_sup_id',
                'users.rsm_id as user_rsm_id',
                'users.asm_id as user_asm_id',
                'users.area as user_area',

                // Customer
                'customers.name as customer_name',
                'customers.code as customer_code',

                // Depo
                'depos.name as depo_name',
            ])
            ->leftJoin(
                'users',
                'reports.user_id',
                '=',
                'users.id'
            )
            ->leftJoin(
                'customers',
                'reports.customer_id',
                '=',
                'customers.id'
            )
            ->leftJoin(
                'depos',
                'customers.depo_id',
                '=',
                'depos.id'
            );

        $this->applyFilters($query);

        return $query->orderByDesc('reports.id');
    }

    protected function applyFilters($query)
    {
        /*
        |--------------------------------------------------------------------------
        | Date
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
        | Current user's team
        |--------------------------------------------------------------------------
        */
        if (!$this->isAdmin && !empty($this->userIds)) {

            $query->where(function ($q) {

                // Normal reports
                $q->where(function ($q1) {
                    $q1->whereIn(
                        'reports.user_id',
                        $this->userIds
                    )
                    ->whereIn(
                        'users.type',
                        $this->allowedTypes
                    );
                });

                // Imported reports
                if (!empty($this->staffIdCards)) {
                    $q->orWhereIn(
                        'reports.ssp_id',
                        $this->staffIdCards
                    )
                    ->orWhereIn(
                        'reports.sup_id',
                        $this->staffIdCards
                    );
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Selected user
        |--------------------------------------------------------------------------
        */
        if ($this->user_id) {
            $this->applyUserFilter($query);
        }

        /*
        |--------------------------------------------------------------------------
        | Area
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
    }

    protected function applyUserFilter($query)
    {
        $selectedUserId = $this->user_id;

        $teamData = User::query()
            ->where(function ($q) use ($selectedUserId) {
                $q->where('manager_id', $selectedUserId)
                    ->orWhere('rsm_id', $selectedUserId)
                    ->orWhere('sup_id', $selectedUserId)
                    ->orWhere('asm_id', $selectedUserId);
            })
            ->select([
                'id',
                'staff_id_card',
            ])
            ->get();

        $teamUserIds = $teamData
            ->pluck('id')
            ->toArray();

        $teamStaffCards = $teamData
            ->pluck('staff_id_card')
            ->filter()
            ->toArray();

        $staffIdCard = User::where(
            'id',
            $selectedUserId
        )->value('staff_id_card');

        $query->where(function ($q) use (
            $selectedUserId,
            $staffIdCard,
            $teamUserIds,
            $teamStaffCards
        ) {

            // Direct user
            $q->where(
                'reports.user_id',
                $selectedUserId
            );

            // Team
            if (!empty($teamUserIds)) {
                $q->orWhereIn(
                    'reports.user_id',
                    $teamUserIds
                );
            }

            // Imported self
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

            // Imported team
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
     * Excel headings
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

    /**
     * Map each report to Excel row
     */
    public function map($row): array
    {
        $userLang = session('user_lang', 'kh');

        /*
        |--------------------------------------------------------------------------
        | Get related users
        |--------------------------------------------------------------------------
        */
        $user = $this->getUser($row->report_user_id);

        $sup = $this->getUser($row->user_sup_id);

        $rsm = $this->getUser($row->user_rsm_id);

        $asm = $this->getASM($row->user_asm_id);

        /*
        |--------------------------------------------------------------------------
        | ML values
        |--------------------------------------------------------------------------
        */
        $val250ml = (int) ($row->{'250_ml'} ?? 0);
        $val350ml = (int) ($row->{'350_ml'} ?? 0);
        $val600ml = (int) ($row->{'600_ml'} ?? 0);
        $val1500ml = (int) ($row->{'1500_ml'} ?? 0);

        $default =
            $val250ml +
            $val350ml +
            $val600ml +
            $val1500ml;

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
        | Photo URL
        |--------------------------------------------------------------------------
        */
        $fullDomain = url('/');

        $outletUrl = $row->outlet_photo
            ? url('/photo/' . AppHelper::shortEncrypt($row->outlet_photo))
            : 'No_Photo';

        $posmUrl = $row->photo
            ? url('/photo/' . AppHelper::shortEncrypt($row->photo))
            : 'No_Photo';
            

        /*
        |--------------------------------------------------------------------------
        | Address
        |--------------------------------------------------------------------------
        */
        $address = ($row->city && $row->country)
            ? $row->city . ', ' . $row->country
            : ($row->address ?? 'N/A');

        /*
        |--------------------------------------------------------------------------
        | Area
        |--------------------------------------------------------------------------
        */
        $area = !empty($row->area_id)
            ? AppHelper::getAreaNameById($row->area_id)
            : ($user?->area ?? 'N/A');

        /*
        |--------------------------------------------------------------------------
        | Names
        |--------------------------------------------------------------------------
        */
        $sspName = $this->getLocalizedName(
            $user,
            $userLang,
            $row->ssp_name ?? null
        );

        $supName = $this->getLocalizedName(
            $sup,
            $userLang,
            $row->sup_name ?? null
        );

        $asmName = $this->getLocalizedName(
            $asm,
            $userLang,
            $row->asm_name ?? null
        );

        $rsmName = $this->getLocalizedName(
            $rsm,
            $userLang,
            $row->rsm_name ?? null
        );

        /*
        |--------------------------------------------------------------------------
        | Return Excel row
        |--------------------------------------------------------------------------
        */
        return [
            $area,

            $sspName,

            $user?->staff_id_card
                ?? $row->ssp_id
                ?? 'N/A',

            $supName,

            $sup?->staff_id_card
                ?? $row->sup_id
                ?? 'N/A',

            $asmName,

            $rsmName,

            $row->depo_name
                ?? $row->outlet_name
                ?? 'N/A',

            $row->customer_name
                ?? 'N/A',

            $row->customer_code
                ?? 'N/A',

            $row->so_number
                ?? 'N/A',

            $row->date
                ? Carbon::parse($row->date)->format('d-M-Y')
                : 'N/A',

            $val250ml,
            $val350ml,
            $val600ml,
            $val1500ml,

            $default,

            $row->latitude
                ?? 'N/A',

            $row->longitude
                ?? 'N/A',

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

    /**
     * Cache user lookup
     *
     * Avoid querying User table for every Excel row.
     */
    protected function getUser($id)
    {
        if (empty($id)) {
            return null;
        }

        if (array_key_exists($id, $this->usersMap)) {
            return $this->usersMap[$id];
        }

        return $this->usersMap[$id] = User::query()
            ->select([
                'id',
                'family_name',
                'name',
                'family_name_latin',
                'name_latin',
                'staff_id_card',
                'area',
                'sup_id',
                'rsm_id',
                'asm_id',
            ])
            ->find($id);
    }

    protected function getASM($asmId)
    {
        if (empty($asmId)) {
            return null;
        }

        $ids = $this->extractAsmIds($asmId);

        foreach ($ids as $id) {
            if (!empty($id) && is_numeric($id)) {
                $user = $this->getUser((int) $id);

                if ($user) {
                    return $user;
                }
            }
        }

        return null;
    }

    protected function getLocalizedName($user, $lang, $fallback = null)
    {
        if (!$user) {
            return $fallback ?? 'N/A';
        }

        if ($lang === 'en') {
            return $this->getUserFullNameLatin($user)
                ?? $fallback
                ?? 'N/A';
        }

        return $this->getUserFullName($user)
            ?? $fallback
            ?? 'N/A';
    }

    protected function getUserFullName($user)
    {
        if (!$user) {
            return null;
        }

        $familyName = trim(
            $user->family_name ?? ''
        );

        $name = trim(
            $user->name ?? ''
        );

        $fullName = trim(
            $familyName . ' ' . $name
        );

        return $fullName !== ''
            ? $fullName
            : null;
    }

    protected function getUserFullNameLatin($user)
    {
        if (!$user) {
            return null;
        }

        $familyName = trim(
            $user->family_name_latin ?? ''
        );

        $name = trim(
            $user->name_latin ?? ''
        );

        $fullName = trim(
            $familyName . ' ' . $name
        );

        return $fullName !== ''
            ? $fullName
            : null;
    }

    protected function extractAsmIds($asmId)
    {
        if (empty($asmId)) {
            return [];
        }

        if (is_array($asmId)) {

            if (
                isset($asmId[0]) &&
                is_array($asmId[0])
            ) {
                return array_map(
                    function ($item) {
                        return is_array($item)
                            ? ($item['id'] ?? null)
                            : $item;
                    },
                    $asmId[0]
                );
            }

            return $asmId;
        }

        if (is_string($asmId)) {

            $decoded = json_decode(
                $asmId,
                true
            );

            if (json_last_error() === JSON_ERROR_NONE) {
                return $this->extractAsmIds($decoded);
            }

            if (strpos($asmId, ',') !== false) {
                return array_map(
                    'trim',
                    explode(',', $asmId)
                );
            }

            return [$asmId];
        }

        return [];
    }

    /**
     * Process 1000 rows at a time.
     */
    public function chunkSize(): int
    {
        return 1000;
    }
}
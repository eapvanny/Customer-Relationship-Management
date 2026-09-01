<?php

namespace App\Http\Controllers;

use App\Http\Helpers\AppHelper;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Report; // Assuming Report model represents chat reports
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\User;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('type.permission:view dashboard', ['only' => ['index']]);
    }
    public function index()
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Get Allowed User IDs Based On Management Hierarchy
        |--------------------------------------------------------------------------
        |
        | null = all users
        |
        */

        $allowedUserIds = null;

        // ============================================================
        // MANAGER
        // All managers see all manager employees
        // ============================================================
        if ($user->role_id === AppHelper::USER_MANAGER) {

            $allowedUserIds = User::where(function ($query) {

                $query->where('role_id', AppHelper::USER_MANAGER)
                    ->orWhere('role_id', AppHelper::USER_EMPLOYEE);
            })
                ->pluck('id')
                ->unique()
                ->values()
                ->toArray();
        }

        // ============================================================
        // RSM
        // RSM -> ASM -> SUP -> EMPLOYEE
        // ============================================================
        elseif ($user->role_id === AppHelper::USER_RSM) {

            // Get ASM under this RSM
            $asmIds = User::where('rsm_id', $user->id)
                ->pluck('id')
                ->toArray();

            // Get SUP under those ASM
            $supIds = [];

            if (!empty($asmIds)) {
                $supIds = User::whereIn('asm_id', $asmIds)
                    ->pluck('id')
                    ->toArray();
            }

            // Get Employees under those SUP
            $employeeIds = [];

            if (!empty($supIds)) {
                $employeeIds = User::whereIn('sup_id', $supIds)
                    ->pluck('id')
                    ->toArray();
            }

            $allowedUserIds = array_merge(
                [$user->id],
                $asmIds,
                $supIds,
                $employeeIds
            );

            $allowedUserIds = array_values(
                array_unique($allowedUserIds)
            );
        }

        // ============================================================
        // ASM
        // ASM -> SUP -> EMPLOYEE
        // ============================================================
        elseif ($user->role_id === AppHelper::USER_ASM) {

            // Get SUP under this ASM
            $supIds = User::where('asm_id', $user->id)
                ->pluck('id')
                ->toArray();

            // Get Employees under those SUP
            $employeeIds = [];

            if (!empty($supIds)) {
                $employeeIds = User::whereIn('sup_id', $supIds)
                    ->pluck('id')
                    ->toArray();
            }

            $allowedUserIds = array_merge(
                [$user->id],
                $supIds,
                $employeeIds
            );

            $allowedUserIds = array_values(
                array_unique($allowedUserIds)
            );
        }

        // ============================================================
        // SUP
        // SUP -> EMPLOYEE
        // ============================================================
        elseif ($user->role_id === AppHelper::USER_SUP) {

            $employeeIds = User::where('sup_id', $user->id)
                ->pluck('id')
                ->toArray();

            $allowedUserIds = array_merge(
                [$user->id],
                $employeeIds
            );

            $allowedUserIds = array_values(
                array_unique($allowedUserIds)
            );
        }

        // ============================================================
        // OTHER USERS
        // Only own data
        // ============================================================
        elseif (
            $user->role_id !== AppHelper::USER_SUPER_ADMIN &&
            $user->role_id !== AppHelper::USER_ADMIN
        ) {

            $allowedUserIds = [$user->id];
        }


        /*
        |--------------------------------------------------------------------------
        | Report Query
        |--------------------------------------------------------------------------
        */

        /*
        |--------------------------------------------------------------------------
        | Report Query + Date Filters
        |--------------------------------------------------------------------------
        */

        $selectedYear = request()->input('year', 'all');
        $fromDate = request()->input('from_date');
        $toDate = request()->input('to_date');

        $query = Report::with('user');

        if ($selectedYear !== 'all') {
            $query->whereYear('created_at', (int) $selectedYear);
        }

        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        if ($allowedUserIds !== null) {
            $query->whereIn('user_id', $allowedUserIds);
        }


        /*
        |--------------------------------------------------------------------------
        | All Reports
        |--------------------------------------------------------------------------
        */

        $allReports = (clone $query)->count();

        $totalCase = (clone $query)
            ->selectRaw("
                COALESCE(
                    SUM(
                        COALESCE(`250_ml`, 0) +
                        COALESCE(`350_ml`, 0) +
                        COALESCE(`600_ml`, 0) +
                        COALESCE(`1500_ml`, 0)
                    ),
                    0
                ) AS total_case
            ")
            ->value('total_case');
            

        /*
        |--------------------------------------------------------------------------
        | Today's Reports
        |--------------------------------------------------------------------------
        */

        $todayReports = (clone $query)
            ->whereDate('created_at', today())
            ->count();

        $todayCase = (clone $query)
            ->whereDate('created_at', today())
            ->selectRaw("
                COALESCE(
                    SUM(
                        COALESCE(`250_ml`, 0) +
                        COALESCE(`350_ml`, 0) +
                        COALESCE(`600_ml`, 0) +
                        COALESCE(`1500_ml`, 0)
                    ),
                    0
                ) AS today_case
            ")
            ->value('today_case');


        $totalCase = (int) $totalCase;
        $todayCase = (int) $todayCase;


        /*
        |--------------------------------------------------------------------------
        | All Active Users
        |--------------------------------------------------------------------------
        */

        $allUsersQuery = User::where('status', '1');

        if ($allowedUserIds !== null) {
            $allUsersQuery->whereIn('id', $allowedUserIds);
        }

        $allUsers = $allUsersQuery->count();


        /*
        |--------------------------------------------------------------------------
        | Employees For Dropdown
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | Only employees under the current management are shown.
        |
        */

        $allUsersEmployeeQuery = User::query()
            ->where('role_id', AppHelper::USER_EMPLOYEE)
            ->where('status', '1');

        if ($allowedUserIds !== null) {
            $allUsersEmployeeQuery->whereIn('id', $allowedUserIds);
        }

        if (app()->getLocale() === 'en') {
            $allUsersEmployeeQuery
                ->orderBy('family_name_latin')
                ->orderBy('name_latin');
        } else {
            $allUsersEmployeeQuery
                ->orderBy('family_name')
                ->orderBy('name');
        }

        $allUsersEmployee = $allUsersEmployeeQuery->get([
            'id',
            'username',
            'family_name',
            'name',
            'family_name_latin',
            'name_latin',
            'area',
            'rsm_id',
            'asm_id',
            'sup_id',
            'driver_id',
            'driver_name',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Get RSM / ASM / SUP User IDs
        |--------------------------------------------------------------------------
        */
        $managementUserIds = collect();

        foreach ($allUsersEmployee as $employee) {

            foreach (['rsm_id', 'asm_id', 'sup_id'] as $field) {

                $value = $employee->{$field};

                if (empty($value)) {
                    continue;
                }

                // JSON array: ["65","74"]
                if (is_string($value)) {
                    $decoded = json_decode($value, true);

                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $value = $decoded;
                    } else {
                        // Single ID
                        $value = [$value];
                    }
                }

                // Already an array
                if (is_array($value)) {
                    $managementUserIds = $managementUserIds->merge($value);
                } else {
                    $managementUserIds->push($value);
                }
            }
        }

        $managementUserIds = $managementUserIds
            ->flatten()
            ->map(function ($id) {
                return trim((string) $id);
            })
            ->filter()
            ->unique()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Get RSM / ASM / SUP Users
        |--------------------------------------------------------------------------
        */

        $managementUsers = User::whereIn('id', $managementUserIds)
            ->get([
                'id',
                'family_name',
                'name',
                'family_name_latin',
                'name_latin',
            ])
            ->keyBy('id');

        /*
        |--------------------------------------------------------------------------
        | Prepare Employee + RSM + ASM + SUP Names
        |--------------------------------------------------------------------------
        */

        $isEnglish = app()->getLocale() === 'en';

        $getName = function ($user) use ($isEnglish) {

            if (!$user) {
                return null;
            }

            return $isEnglish
                ? trim($user->family_name_latin . ' ' . $user->name_latin)
                : trim($user->family_name . ' ' . $user->name);
        };

        $getIds = function ($value) {

            if (empty($value)) {
                return [];
            }

            // JSON string: ["65","74"]
            if (is_string($value)) {

                $decoded = json_decode($value, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $value = $decoded;
                } else {
                    // Single ID
                    $value = [$value];
                }
            }

            if (!is_array($value)) {
                $value = [$value];
            }

            return collect($value)
                ->flatten()
                ->map(function ($id) {
                    return trim((string) $id);
                })
                ->filter()
                ->unique()
                ->values()
                ->toArray();
        };

        foreach ($allUsersEmployee as $employee) {

            /*
            |--------------------------------------------------------------------------
            | Employee
            |--------------------------------------------------------------------------
            */

            $employee->display_name = $getName($employee);

            /*
            |--------------------------------------------------------------------------
            | RSM
            |--------------------------------------------------------------------------
            */

            $rsmIds = $getIds($employee->rsm_id);

            $employee->rsm_name = collect($rsmIds)
                ->map(fn ($id) => $getName($managementUsers->get($id)))
                ->filter()
                ->implode(', ');

            /*
            |--------------------------------------------------------------------------
            | ASM
            |--------------------------------------------------------------------------
            */

            $asmIds = $getIds($employee->asm_id);

            $employee->asm_name = collect($asmIds)
                ->map(fn ($id) => $getName($managementUsers->get($id)))
                ->filter()
                ->implode(', ');

            /*
            |--------------------------------------------------------------------------
            | SUP
            |--------------------------------------------------------------------------
            */

            $supIds = $getIds($employee->sup_id);

            $employee->sup_name = collect($supIds)
                ->map(fn ($id) => $getName($managementUsers->get($id)))
                ->filter()
                ->implode(', ');
        }
        


        /*
        |--------------------------------------------------------------------------
        | Customers
        |--------------------------------------------------------------------------
        */
        $allCustomersQuery = Customer::query();
        
        if ($selectedYear !== 'all') {
            $allCustomersQuery->whereYear('created_at', (int) $selectedYear);
        }

        if ($fromDate) {
            $allCustomersQuery->whereDate('created_at', '>=', $fromDate);
        }

        if ($toDate) {
            $allCustomersQuery->whereDate('created_at', '<=', $toDate);
        }

        if ($allowedUserIds !== null) {
            $allCustomersQuery->whereIn('user_id', $allowedUserIds);
        }

        $allCustomers = $allCustomersQuery->count();


        /*
        |--------------------------------------------------------------------------
        | Monthly Report Data
        |--------------------------------------------------------------------------
        */

        $monthlyChatReports = (clone $query)
            ->selectRaw(
                'EXTRACT(MONTH FROM created_at) as month,
                COUNT(id) as count'
            )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $monthlyData = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[] = $monthlyChatReports[$i] ?? 0;
        }


        /*
        |--------------------------------------------------------------------------
        | Monthly Sales
        |--------------------------------------------------------------------------
        */

        /*
        |--------------------------------------------------------------------------
        | Sales Date Range
        |--------------------------------------------------------------------------
        */

        if ($fromDate || $toDate) {

            $salesStartDate = $fromDate
                ? \Carbon\Carbon::parse($fromDate)->startOfDay()
                : (
                    $selectedYear === 'all'
                        ? \Carbon\Carbon::create(2024, 1, 1)->startOfDay()
                        : \Carbon\Carbon::create((int) $selectedYear, 1, 1)->startOfDay()
                );

            $salesEndDate = $toDate
                ? \Carbon\Carbon::parse($toDate)->endOfDay()
                : (
                    $selectedYear === 'all'
                        ? now()->endOfDay()
                        : \Carbon\Carbon::create((int) $selectedYear, 12, 31)->endOfDay()
                );

        } elseif ($selectedYear === 'all') {

            // All years
            $salesStartDate = \Carbon\Carbon::create(2024, 1, 1)->startOfDay();
            $salesEndDate = now()->endOfDay();

        } elseif ((int) $selectedYear === now()->year) {

            // Current year
            $salesStartDate = now()->startOfMonth();
            $salesEndDate = now()->endOfMonth();

        } else {

            // Specific previous year
            $salesStartDate = \Carbon\Carbon::create(
                (int) $selectedYear,
                1,
                1
            )->startOfDay();

            $salesEndDate = \Carbon\Carbon::create(
                (int) $selectedYear,
                12,
                31
            )->endOfDay();
        }

        $employeeSales = (clone $query)
            ->selectRaw("
        user_id,

        COALESCE(
            SUM(
                COALESCE(`250_ml`, 0) +
                COALESCE(`350_ml`, 0) +
                COALESCE(`600_ml`, 0) +
                COALESCE(`1500_ml`, 0)
            ),
            0
        ) AS total
    ")
            ->whereBetween(
                'created_at',
                [$salesStartDate, $salesEndDate]
            )
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Top Employee
        |--------------------------------------------------------------------------
        */

        $topEmployee = $employeeSales->first();


        /*
        |--------------------------------------------------------------------------
        | Selected Employee
        |--------------------------------------------------------------------------
        */

        $selectedEmployeeId = request()->input('employee_id');


        /*
        |--------------------------------------------------------------------------
        | Get IDs Of Employees In Dropdown
        |--------------------------------------------------------------------------
        */

        $allowedEmployeeIds = $allUsersEmployee
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | Validate Selected Employee
        |--------------------------------------------------------------------------
        |
        | If employee_id is empty OR employee is not under current manager,
        | automatically select the highest-selling employee.
        |
        */

        if (
            !$selectedEmployeeId ||
            !in_array(
                (int) $selectedEmployeeId,
                $allowedEmployeeIds,
                true
            )
        ) {

            $selectedEmployeeId = $topEmployee->user_id ?? null;
        }


        /*
        |--------------------------------------------------------------------------
        | Selected Employee Sales
        |--------------------------------------------------------------------------
        */

        $selectedEmployeeSale = $employeeSales
            ->firstWhere(
                'user_id',
                (int) $selectedEmployeeId
            );

        $soldThisMonth = $selectedEmployeeSale->total ?? 0;


        /*
        |--------------------------------------------------------------------------
        | Rank Targets
        |--------------------------------------------------------------------------
        */

        $rankTargets = [
            'Rank C' => 2600,
            'Rank B' => 3000,
            'Rank A' => 3500,
        ];


        /*
        |--------------------------------------------------------------------------
        | Calculate Rank
        |--------------------------------------------------------------------------
        */

        $currentRank = 'No Rank';
        $nextRank = null;
        $remaining = 0;

        if ($soldThisMonth >= $rankTargets['Rank A']) {

            $currentRank = 'Rank A';
            $nextRank = null;
            $remaining = 0;
        } elseif ($soldThisMonth >= $rankTargets['Rank B']) {

            $currentRank = 'Rank B';
            $nextRank = 'Rank A';

            $remaining =
                $rankTargets['Rank A'] - $soldThisMonth;
        } elseif ($soldThisMonth >= $rankTargets['Rank C']) {

            $currentRank = 'Rank C';
            $nextRank = 'Rank B';

            $remaining =
                $rankTargets['Rank B'] - $soldThisMonth;
        } else {

            $currentRank = 'No Rank';
            $nextRank = 'Rank C';

            $remaining =
                $rankTargets['Rank C'] - $soldThisMonth;
        }


        /*
        |--------------------------------------------------------------------------
        | Target Percentage
        |--------------------------------------------------------------------------
        */

        if ($nextRank) {

            $targetPercent = round(
                ($soldThisMonth / $rankTargets[$nextRank]) * 100
            );

            $targetPercent = min(
                $targetPercent,
                100
            );
        } else {

            $targetPercent = 100;
        }


        /*
        |--------------------------------------------------------------------------
        | Progress Color
        |--------------------------------------------------------------------------
        */

        $progressColor = '#dc3545';

        if ($targetPercent >= 100) {

            $progressColor = '#198754';
        } elseif ($targetPercent >= 80) {

            $progressColor = '#20c997';
        } elseif ($targetPercent >= 60) {

            $progressColor = '#0d6efd';
        } elseif ($targetPercent >= 40) {

            $progressColor = '#ffc107';
        } elseif ($targetPercent >= 20) {

            $progressColor = '#fd7e14';
        }


        /*
        |--------------------------------------------------------------------------
        | Active Users
        |--------------------------------------------------------------------------
        */

        $userActiveQuery = Report::query()
            ->whereYear('created_at', now()->year)
            ->whereNotNull('user_id');

        if ($allowedUserIds !== null) {
            $userActiveQuery->whereIn(
                'user_id',
                $allowedUserIds
            );
        }

        $userActive = $userActiveQuery
            ->distinct('user_id')
            ->count('user_id');


        /*
        |--------------------------------------------------------------------------
        | Return Dashboard
        |--------------------------------------------------------------------------
        */

        return view('backend.dashboard', [

            'allReports' => $allReports,

            'todayReports' => $todayReports,

            'totalCase' => $totalCase,
            'todayCase' => $todayCase,

            'allUsers' => $allUsers,

            'allCustomers' => $allCustomers,

            'monthlyData' => $monthlyData,

            'show_popup' => true,

            'userActive' => $userActive,

            /*
            |--------------------------------------------------------------------------
            | Employee Dropdown
            |--------------------------------------------------------------------------
            */

            'allUsersEmployee' => $allUsersEmployee,

            'selectedEmployeeId' => $selectedEmployeeId,

            /*
            |--------------------------------------------------------------------------
            | Sales Target
            |--------------------------------------------------------------------------
            */

            'soldThisMonth' => $soldThisMonth,

            'currentRank' => $currentRank,

            'nextRank' => $nextRank,

            'remaining' => $remaining,

            'targetPercent' => $targetPercent,

            'progressColor' => $progressColor,
        ]);
    }

    // public function getReportData()
    // {
    //     $monthlyReports = Report::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
    //         ->groupBy('month')
    //         ->orderBy('month')
    //         ->pluck('count', 'month')
    //         ->toArray();

    //     $reportData = [
    //         'monthly' => array_fill(0, 12, 0)
    //     ];

    //     foreach ($monthlyReports as $month => $count) {
    //         $reportData['monthly'][$month - 1] = $count;
    //     }

    //     return response()->json($reportData);
    // }
}

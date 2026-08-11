<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Helpers\AppHelper;
use App\Models\Customer;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private function getUserIdsByRole($user)
    {
        switch ($user->role_id) {
            case AppHelper::USER_SUPER_ADMIN:
            case AppHelper::USER_ADMIN:
                return null; // all users

            case AppHelper::USER_MANAGER:
                return User::where('manager_id', $user->id)
                    ->pluck('id')
                    ->push($user->id)
                    ->unique()
                    ->toArray();

            case AppHelper::USER_RSM:
                $ids = User::where('rsm_id', $user->id)
                    ->orWhereIn('asm_id', function ($q) use ($user) {
                        $q->select('id')
                            ->from('users')
                            ->where('rsm_id', $user->id);
                    })
                    ->orWhereIn('sup_id', function ($q) use ($user) {
                        $q->select('id')
                            ->from('users')
                            ->whereIn('asm_id', function ($sub) use ($user) {
                                $sub->select('id')
                                    ->from('users')
                                    ->where('rsm_id', $user->id);
                            });
                    })
                    ->pluck('id')
                    ->push($user->id)
                    ->unique()
                    ->toArray();
                return $ids;

            case AppHelper::USER_ASM:
                $ids = User::where('asm_id', $user->id)
                    ->orWhereIn('sup_id', function ($q) use ($user) {
                        $q->select('id')
                            ->from('users')
                            ->where('asm_id', $user->id);
                    })
                    ->pluck('id')
                    ->push($user->id)
                    ->unique()
                    ->toArray();
                return $ids;

            case AppHelper::USER_SUP:
                return User::where('sup_id', $user->id)
                    ->pluck('id')
                    ->push($user->id)
                    ->unique()
                    ->toArray();

            default:
                return [$user->id];
        }
    }
    public function index(Request $request)
    {
        $user = auth()->user();

        $userIds = $this->getUserIdsByRole($user);

        // ==============================
        // Filter Parameters
        // ==============================
        $currentYear = now()->year;
        $currentMonth = now()->month;
        
        // Year filter
        $year = (int) $request->input('year', $currentYear);
        if ($year < 2025 || $year > $currentYear) {
            $year = $currentYear;
        }

        // Month filter
        $month = (int) $request->input('month', $currentMonth);
        if ($month < 1 || $month > 12) {
            $month = $currentMonth;
        }

        // User ID filter (from allUsersEmployee)
        $targetUserId = $request->input('user_id', null);
        if ($targetUserId && !empty($targetUserId)) {
            // Validate if the user exists and is an employee
            $targetUser = User::where('id', $targetUserId)
                ->where('role_id', AppHelper::USER_EMPLOYEE)
                ->where('status', 1)
                ->first();
                
            if ($targetUser) {
                // Override userIds to only include this specific user
                $userIds = [$targetUserId];
            } else {
                $targetUserId = null; // Reset if invalid
            }
        }

        // Week filters (keeping existing functionality)
        $startWeek = $request->input('start_week', now()->weekOfYear);
        $endWeek = $request->input('end_week', $startWeek);

        $startDate = Carbon::create($year, 1, 1)
            ->setISODate($year, $startWeek)
            ->startOfWeek(Carbon::MONDAY)
            ->startOfDay();

        $endDate = Carbon::create($year, 1, 1)
            ->setISODate($year, $endWeek)
            ->endOfWeek(Carbon::SUNDAY)
            ->endOfDay();

        // ==============================
        // Base Queries with Filters
        // ==============================
        $reportQuery = Report::query()
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);

        $customerQuery = Customer::query()
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);

        if ($userIds !== null) {
            $reportQuery->whereIn('user_id', $userIds);
            $customerQuery->whereIn('user_id', $userIds);
        }

        // ==============================
        // User Query
        // ==============================
        $userQuery = User::where('status', 1);

        if ($userIds !== null) {
            $userQuery->whereIn('id', $userIds);
        }

        // ==============================
        // Dashboard Counts
        // ==============================
        $allReports = (clone $reportQuery)->count();

        $todayReports = (clone $reportQuery)
            ->whereDate('created_at', today())
            ->count();

        $allCustomers = (clone $customerQuery)->count();

        $allUsers = (clone $userQuery)->count();
        
        // All Employee Users (for dropdown filter)
        $allUsersEmployee = User::where('status', 1)
            ->where('role_id', AppHelper::USER_EMPLOYEE)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'full_name_latin' => $user->full_name_latin,
                ];
            })
            ->values();

        $userActive = (clone $reportQuery)
            ->distinct('user_id')
            ->count('user_id');

        // ==============================
        // Monthly Report (by month)
        // ==============================
        $months = (clone $reportQuery)
            ->selectRaw('EXTRACT(MONTH FROM created_at) as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[] = $months[$i] ?? 0;
        }

        // ==============================
        // Weekly Total
        // ==============================
        $weeklyReports = (clone $reportQuery)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $weeklyCustomers = (clone $customerQuery)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // ==============================
        // Chart Monday -> Sunday
        // ==============================
        $reportByDay = (clone $reportQuery)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('WEEKDAY(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day')
            ->toArray();

        $customerByDay = (clone $customerQuery)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('WEEKDAY(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day')
            ->toArray();

        $dayLabels = [
            'Monday', 'Tuesday', 'Wednesday', 'Thursday', 
            'Friday', 'Saturday', 'Sunday'
        ];

        $reportChart = [];
        $customerChart = [];
        for ($i = 0; $i < 7; $i++) {
            $reportChart[] = $reportByDay[$i] ?? 0;
            $customerChart[] = $customerByDay[$i] ?? 0;
        }

        // ==============================
        // Sale Target (based on filters)
        // ==============================
        $saleTarget = (clone $reportQuery)
            ->whereBetween('created_at', [
                Carbon::create($year, $month, 1)->startOfMonth(),
                Carbon::create($year, $month, 1)->endOfMonth()
            ])
            ->selectRaw("
                COALESCE(SUM(
                    COALESCE(`250_ml`,0) +
                    COALESCE(`350_ml`,0) +
                    COALESCE(`600_ml`,0) +
                    COALESCE(`1500_ml`,0)
                ),0) as total_sale
            ")
            ->value('total_sale');

        $rank = 'No Rank';
        if ($saleTarget >= 3500) {
            $rank = 'Rank A';
        } elseif ($saleTarget >= 3000) {
            $rank = 'Rank B';
        } elseif ($saleTarget >= 2600) {
            $rank = 'Rank C';
        }

        // ==============================
        // Response
        // ==============================
        return response()->json([
            'status' => true,

            // Filter values
            'year' => $year,
            'month' => $month,
            'user_id' => $targetUserId,
            'start_week' => $startWeek,
            'end_week' => $endWeek,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),

            'userRole' => $user->role->name,

            'allReports' => $allReports,
            'todayReports' => $todayReports,
            'allUsers' => $allUsers,
            'allUsersEmployee' => $allUsersEmployee, // For dropdown filter
            'allCustomers' => $allCustomers,
            'userActive' => $userActive,

            'monthlyReports' => $monthlyData,

            'weeklyReports' => $weeklyReports,
            'weeklyCustomers' => $weeklyCustomers,

            // Chart Data
            'dayLabels' => $dayLabels,
            'reportData' => $reportChart,
            'customerData' => $customerChart,

            'saleTarget' => $saleTarget,
            'rank' => $rank,

            'rankTargets' => [
                'Rank A' => '2600 boxes',
                'Rank B' => '3000 boxes',
                'Rank C' => '3500 boxes',
            ],
        ]);
    }
}

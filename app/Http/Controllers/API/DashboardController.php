<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Helpers\AppHelper;
use App\Models\Customer;
use App\Models\Report;
use App\Models\User;
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
    public function index()
    {
        $user = auth()->user();

        $userIds = $this->getUserIdsByRole($user);

        // Report Query
        $reportQuery = Report::query();

        if ($userIds !== null) {
            $reportQuery->whereIn('user_id', $userIds);
            $weeklyReportQuery = Report::whereIn('user_id', $userIds)
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        }

        $reportQuery->whereYear('created_at', now()->year);

        // Customer Query
        $customerQuery = Customer::query();

        if ($userIds !== null) {
            $customerQuery->whereIn('user_id', $userIds);
            $weeklyCustomerQuery = Customer::whereIn('user_id', $userIds)
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        }

        // User Query
        $userQuery = User::where('status', 1);

        if ($userIds !== null) {
            $userQuery->whereIn('id', $userIds);
        }

        $allReports = (clone $reportQuery)->count();

        $todayReports = (clone $reportQuery)
            ->whereDate('created_at', today())
            ->count();

        $allCustomers = (clone $customerQuery)->count();

        $allUsers = (clone $userQuery)->count();

        $userActive = (clone $reportQuery)
            ->distinct('user_id')
            ->count('user_id');

        // Monthly Report
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

        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $saleTarget = (clone $reportQuery)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
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
            $rank = 'Rank C';
        } elseif ($saleTarget >= 3000) {
            $rank = 'Rank B';
        } elseif ($saleTarget >= 2600) {
            $rank = 'Rank A';
        }

        return response()->json([
            'status' => true,
            'userRole' => $user->role->name,
            'allReports' => $allReports,
            'todayReports' => $todayReports,
            'allUsers' => $allUsers,
            'allCustomers' => $allCustomers,
            'userActive' => $userActive,
            'monthlyReports' => $monthlyData,
            'weeklyReports' => $weeklyReportQuery->count(),
            'weeklyCustomers' => $weeklyCustomerQuery->count(),
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

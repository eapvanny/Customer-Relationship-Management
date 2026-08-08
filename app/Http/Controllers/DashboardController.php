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
        // $query = Report::query();
        // if (auth()->user()->role_id == AppHelper::USER_EMPLOYEE) {
        //     $query->where('user_id', auth()->user()->id);
        // }
        $query = Report::with('user');
        $user = auth()->user();
        if ($user->role_id === AppHelper::USER_MANAGER) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('manager_id', $user->id);
            });
        } elseif ($user->role_id === AppHelper::USER_RSM) {
            // Get all user IDs under this RSM (ASM, SUP, Employee)
            $userIdsUnderRsm = User::where('rsm_id', $user->id)
                ->orWhereIn('asm_id', function ($query) use ($user) {
                    $query->select('id')
                        ->from('users')
                        ->where('rsm_id', $user->id);
                })
                ->orWhereIn('sup_id', function ($query) use ($user) {
                    $query->select('id')
                        ->from('users')
                        ->whereIn('asm_id', function ($subQuery) use ($user) {
                            $subQuery->select('id')
                                ->from('users')
                                ->where('rsm_id', $user->id);
                        });
                })
                ->pluck('id')
                ->toArray();

            // Include the RSM's own reports if needed
            $userIdsUnderRsm[] = $user->id;

            $query->whereIn('user_id', $userIdsUnderRsm);
        } elseif ($user->role_id === AppHelper::USER_ASM) {
            // Get all user IDs under this ASM (SUP and Employee)
            $userIdsUnderAsm = User::where('asm_id', $user->id)
                ->orWhere('sup_id', 'in', function ($query) use ($user) {
                    $query->select('id')
                        ->from('users')
                        ->where('asm_id', $user->id);
                })
                ->pluck('id')
                ->toArray();

            $userIdsUnderAsm[] = $user->id;
            $query->whereIn('user_id', $userIdsUnderAsm);
        } elseif ($user->role_id === AppHelper::USER_SUP) {
            // Get all user IDs under this SUP (Employees)
            $userIdsUnderSup = User::where('sup_id', $user->id)
                ->pluck('id')
                ->toArray();

            $userIdsUnderSup[] = $user->id;
            $query->whereIn('user_id', $userIdsUnderSup);
        } elseif (
            $user->role_id !== AppHelper::USER_SUPER_ADMIN &&
            $user->role_id !== AppHelper::USER_ADMIN
        ) {
            $query->where('user_id', $user->id);
        }

        // Get All Reports count (total reports based on the filtered query)
        $query->whereYear('created_at', now()->year);

        // Get count
        $allReports = $query->count();

        // Get Today's Reports count (filtered query with date constraint)
        $todayReports = (clone $query)
            ->whereDate('created_at', today())
            ->count();

        // Get All Users count (from the User table)
        $allUsers = User::where('status', '1')->count();

        // Get All Customers count (from the Customer table)
        $allCustomers = Customer::count();

        // Get monthly chat report data
        $monthlyChatReports = (clone $query)
            ->selectRaw('EXTRACT(MONTH FROM created_at) as month, COUNT(id) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[] = $monthlyChatReports[$i] ?? 0;
        }

        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $soldThisMonth = (clone $query)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->selectRaw("
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
            ->value('total');


        // Rank Target
        $rankTargets = [
            'Rank A' => 2600,
            'Rank B' => 3000,
            'Rank C' => 3500,
        ];

        $currentRank = 'No Rank';
        $nextRank = null;
        $remaining = 0;


        // Find Current Rank and Next Rank
        if ($soldThisMonth >= $rankTargets['Rank C']) {

            $currentRank = 'Rank C';
            $nextRank = null;
            $remaining = 0;
        } elseif ($soldThisMonth >= $rankTargets['Rank B']) {

            $currentRank = 'Rank B';
            $nextRank = 'Rank C';
            $remaining = $rankTargets['Rank C'] - $soldThisMonth;
        } elseif ($soldThisMonth >= $rankTargets['Rank A']) {

            $currentRank = 'Rank A';
            $nextRank = 'Rank B';
            $remaining = $rankTargets['Rank B'] - $soldThisMonth;
        } else {

            $currentRank = 'No Rank';
            $nextRank = 'Rank A';
            $remaining = $rankTargets['Rank A'] - $soldThisMonth;
        }


        // Percentage based on next rank
        $targetPercent = 0;

        if ($nextRank) {
            $targetPercent = round(
                ($soldThisMonth / $rankTargets[$nextRank]) * 100
            );
        } else {
            $targetPercent = 100;
        }
        $progressColor = '#dc3545'; // Red

        if ($targetPercent >= 100) {

            $progressColor = '#198754'; // Green

        } elseif ($targetPercent >= 80) {

            $progressColor = '#20c997'; // Teal

        } elseif ($targetPercent >= 60) {

            $progressColor = '#0d6efd'; // Blue

        } elseif ($targetPercent >= 40) {

            $progressColor = '#ffc107'; // Yellow

        } elseif ($targetPercent >= 20) {

            $progressColor = '#fd7e14'; // Orange

        }

        $userActive = Report::pluck('user_id')->unique()->count();

        // Pass a flag to show the popup (if needed)
        return view('backend.dashboard', [
            'allReports' => $allReports,
            'todayReports' => $todayReports,
            'allUsers' => $allUsers,
            'allCustomers' => $allCustomers,
            'monthlyData' => $monthlyData,
            'show_popup' => true, // Optional: control the loader visibility
            'userActive' => $userActive,
            // Sales Target
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

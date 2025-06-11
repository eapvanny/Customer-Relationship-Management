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
        } elseif ($user->role_id !== AppHelper::USER_SUPER_ADMIN && $user->role_id !== AppHelper::USER_ADMIN) {
            $query->where('user_id', $user->id);
        }
       
        // Get All Reports count (total reports based on the filtered query)
        $allReports = $query->count();

        // Get Today's Reports count (filtered query with date constraint)
        $todayReports = (clone $query)
            ->whereDate('created_at', today())
            ->count();

        // Get All Users count (from the User table)
        $allUsers = User::count();

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
        
        // Pass a flag to show the popup (if needed)
            return view('backend.dashboard', [
            'allReports' => $allReports,
            'todayReports' => $todayReports,
            'allUsers' => $allUsers,
            'allCustomers' => $allCustomers,
            'monthlyData' => $monthlyData,
            'show_popup' => true // Optional: control the loader visibility
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
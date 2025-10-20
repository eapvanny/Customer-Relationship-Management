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
    public function index()
    {
        $query = Report::with('user');
        $user = auth()->user();

        // if ($user->role_id === AppHelper::USER_MANAGER) {
        //     $query->whereHas('user', function ($q) use ($user) {
        //         $q->where('manager_id', $user->id);
        //     });
        // } elseif ($user->role_id !== AppHelper::USER_SUPER_ADMIN && $user->role_id !== AppHelper::USER_ADMIN) {
        //     $query->where('user_id', $user->id);
        // }

        // Get All Reports count
        $allReports = $query->count();

        // Get Today's Reports count
        $todayReports = (clone $query)
            ->whereDate('created_at', today())
            ->count();

        // Get All Users count
        $allUsers = User::count();

        // Get All Customers count
        $allCustomers = Customer::count();

        return response()->json([
            'userRole' => $user->role->name,
            'allReports' => $allReports,
            'todayReports' => $todayReports,
            'allUsers' => $allUsers,
            'allCustomers' => $allCustomers,
        ]);
    }
}

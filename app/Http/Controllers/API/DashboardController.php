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


        /*
        |--------------------------------------------------------------------------
        | Filter Year + Week Range
        |--------------------------------------------------------------------------
        */

        $year = $request->year ?? now()->year;
        $startWeek = $request->start_week ?? 1;
        $endWeek = $request->end_week ?? 52;


        // Get start date from week
        $startDate = Carbon::now()
            ->setISODate($year, $startWeek)
            ->startOfWeek();


        // Get end date from week
        $endDate = Carbon::now()
            ->setISODate($year, $endWeek)
            ->endOfWeek();



        /*
        |--------------------------------------------------------------------------
        | Report Query
        |--------------------------------------------------------------------------
        */

        $reportQuery = Report::query();


        if ($userIds !== null) {
            $reportQuery->whereIn('user_id', $userIds);
        }


        $reportQuery->whereBetween(
            'created_at',
            [$startDate, $endDate]
        );

        $todayReports = (clone $reportQuery)
            ->whereDate('created_at', today())
            ->count();

        $userQuery = User::where('status',1);

        if ($userIds !== null) {
            $userQuery->whereIn('id',$userIds);
        }

        $allUsers = (clone $userQuery)->count();

        // Monthly Report
        $months = (clone $reportQuery)
            ->selectRaw('EXTRACT(MONTH FROM created_at) as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();



        $monthlyData = [];

        for($i=1;$i<=12;$i++)
        {
            $monthlyData[] = $months[$i] ?? 0;
        }

         $userActive = (clone $reportQuery)
            ->distinct('user_id')
            ->count('user_id');

        /*
        |--------------------------------------------------------------------------
        | Customer Query
        |--------------------------------------------------------------------------
        */

        $customerQuery = Customer::query();


        if ($userIds !== null) {
            $customerQuery->whereIn('user_id', $userIds);
        }


        $customerQuery->whereBetween(
            'created_at',
            [$startDate, $endDate]
        );



        /*
        |--------------------------------------------------------------------------
        | Daily Chart Data Monday - Sunday
        |--------------------------------------------------------------------------
        */

        $reportsByDay = (clone $reportQuery)
            ->selectRaw("
                WEEKDAY(created_at) + 1 AS day,
                COUNT(*) AS total
            ")
            ->groupByRaw("WEEKDAY(created_at)")
            ->pluck('total', 'day')
            ->toArray();



        $customersByDay = (clone $customerQuery)
            ->selectRaw("
                WEEKDAY(created_at) + 1 AS day,
                COUNT(*) AS total
            ")
            ->groupByRaw("WEEKDAY(created_at)")
            ->pluck('total', 'day')
            ->toArray();



        $days = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
        ];


        $weeklyReports = [];
        $weeklyCustomers = [];


        foreach($days as $key=>$day)
        {
            $weeklyReports[] = [
                'day'=>$day,
                'total'=>$reportsByDay[$key] ?? 0
            ];


            $weeklyCustomers[] = [
                'day'=>$day,
                'total'=>$customersByDay[$key] ?? 0
            ];
        }



        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'status'=>true,
            'userRole' => $user->role->name,
            'filter'=>[
                'year'=>$year,
                'start_week'=>$startWeek,
                'end_week'=>$endWeek,
                'start_date'=>$startDate->format('Y-m-d'),
                'end_date'=>$endDate->format('Y-m-d'),
            ],


            'weeklyReports'=>$weeklyReports,

            'weeklyCustomers'=>$weeklyCustomers,


            'allReports'=>(clone $reportQuery)->count(),
            'todayReports'=>$todayReports,
            'allUsers'=>$allUsers,
            'userActive'=>$userActive,
            'monthlyReports'=>$monthlyData,
            'allCustomers'=>(clone $customerQuery)->count(),

        ]);
    }
}

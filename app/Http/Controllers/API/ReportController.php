<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Helpers\AppHelper;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
   public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = Report::with(['user', 'customer', 'customer.depo']);

        // Check modal condition
        $hasNoReports = !Report::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->exists();

        $hasUnassignedReportToday = Report::where('user_id', $user->id)
            ->whereNull('driver_id')
            ->whereNull('driver_status')
            ->whereDate('created_at', Carbon::today())
            ->exists();

        $showModal = $hasNoReports || $hasUnassignedReportToday;

        try {
            $reports = $query->orderBy('id', 'desc')->limit(50)->get();

            $reportsData = $reports->map(function ($report) {
                $quantities = [
                    ['size' => '250ML', 'quantity' => $report->{'250_ml'} ?? 0],
                    ['size' => '350ML', 'quantity' => $report->{'350_ml'} ?? 0],
                    ['size' => '600ML', 'quantity' => $report->{'600_ml'} ?? 0],
                    ['size' => '1500ML', 'quantity' => $report->{'1500_ml'} ?? 0],
                ];

                return [
                    'id'            => $report->id,
                    'report_id'     => 'S-' . str_pad($report->id, 3, '0', STR_PAD_LEFT),
                    'customer_name' => $report->customer->name ?? 'N/A',
                    'customer_code' => $report->customer->code ?? 'N/A',
                    'customer_type' => $report->customer_type ?? 'អតិថិជនទូទៅ',
                    'outlet_name'   => $report->customer->depo->name ?? 'N/A',
                    'quantities'    => $quantities,
                    'formatted_date'=> Carbon::parse($report->date)->format('d F, Y'),
                ];
            });

            return response()->json([
                'success'    => true,
                'data'       => $reportsData,
                'show_modal' => $showModal,
            ], 200);

        } catch (\Exception $e) {
            Log::error('API Error in ReportController@index: ' . $e->getMessage());
            return response()->json(['error' => 'Server error.'], 500);
        }
    }

    public function show($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $query = Report::with(['user', 'customer', 'customer.depo'])
                ->where('id', $id);

            // Apply role-based access control (same logic as index)
            $userRole = $user->role_id;
            $userId = $user->id;
            $userType = $user->type;
            $allowedTypes = [AppHelper::SALE, AppHelper::SE];
            $userIds = [$userId];

            if ($userType == AppHelper::ALL || in_array($userRole, [
                AppHelper::USER_SUPER_ADMIN,
                AppHelper::USER_ADMIN,
                AppHelper::USER_DIRECTOR
            ])) {
                // No additional filtering needed
            } elseif ($userRole == AppHelper::USER_MANAGER) {
                $managedUserIds = User::where(function ($q) use ($userId) {
                    $q->where('manager_id', $userId)
                        ->orWhere('rsm_id', $userId)
                        ->orWhere('sup_id', $userId)
                        ->orWhere('asm_id', $userId);
                })->whereIn('type', $allowedTypes)
                    ->pluck('id')
                    ->toArray();
                $userIds = array_merge($userIds, $managedUserIds);
            } elseif ($userRole == AppHelper::USER_RSM) {
                $managedUserIds = User::where(function ($q) use ($userId) {
                    $q->where('rsm_id', $userId)
                        ->orWhere('sup_id', $userId)
                        ->orWhere('asm_id', $userId);
                })->whereIn('type', $allowedTypes)
                    ->pluck('id')
                    ->toArray();
                $userIds = array_merge($userIds, $managedUserIds);
            } elseif ($userRole == AppHelper::USER_SUP) {
                $managedUserIds = User::where(function ($q) use ($userId) {
                    $q->where('sup_id', $userId)
                        ->orWhere('asm_id', $userId);
                })->whereIn('type', $allowedTypes)
                    ->pluck('id')
                    ->toArray();
                $userIds = array_merge($userIds, $managedUserIds);
            } elseif ($userRole == AppHelper::USER_ASM) {
                $managedUserIds = User::where('asm_id', $userId)
                    ->whereIn('type', $allowedTypes)
                    ->pluck('id')
                    ->toArray();
                $userIds = array_merge($userIds, $managedUserIds);
            }

            if (!($userType == AppHelper::ALL || in_array($userRole, [
                AppHelper::USER_SUPER_ADMIN,
                AppHelper::USER_ADMIN,
                AppHelper::USER_DIRECTOR
            ]))) {
                $query->whereIn('user_id', array_unique($userIds));
            }

            $report = $query->first();

            if (!$report) {
                return response()->json(['error' => 'Report not found or access denied'], 404);
            }

            // Base URL for storage (adjust based on your Laravel setup)
            $baseUrl = url('/storage/');

            // Transform data for API response
            $reportData = [
                'id' => $report->id,
                'report_id' => 'S-' . str_pad($report->id, 3, '0', STR_PAD_LEFT),
                'area' => $report->customer && $report->customer->area_id
                    ? AppHelper::getAreaNameById($report->customer->area_id) ?? 'N/A'
                    : AppHelper::getAreaNameById($report->area_id) ?? 'N/A',
                'customer_name' => $report->customer ? $report->customer->name ?? 'N/A' : 'N/A',
                'customer_code' => $report->customer ? $report->customer->code ?? 'N/A' : 'N/A',
                'customer_type' => 'អតិថិជនទូទៅ', // Adjust if dynamic
                'outlet_name' => $report->customer->depo ? $report->customer->depo->name ?? 'N/A' : 'N/A',
                'quantities' => [
                    ['size' => '250ML', 'quantity' => $report->{'250_ml'} ?? 0],
                    ['size' => '350ML', 'quantity' => $report->{'350_ml'} ?? 0],
                    ['size' => '600ML', 'quantity' => $report->{'600_ml'} ?? 0],
                    ['size' => '1500ML', 'quantity' => $report->{'1500_ml'} ?? 0],
                ],
                'formatted_date' => Carbon::parse($report->date)->format('d F, Y'),
                'sale_photo_url' => $report->photo
                    ? ($baseUrl . $report->photo) // Assuming photo is stored in storage/app/public
                    : 'https://via.placeholder.com/200',
                'posm_photo_url' => $report->outlet_photo
                    ? ($baseUrl . $report->outlet_photo)
                    : 'https://via.placeholder.com/200',
                'material_type' => $report->material_type ?? 'T-Shirt',
                'material_quantity' => $report->material_quantity ?? 2,
                'latitude' => $report->latitude ?? 11.5241,
                'longitude' => $report->longitude ?? 104.9390,
                'address' => $report->address ?? 'Ta Ngov Kandal, Khan Chbar Ampov, Phnom Penh',
                'notes' => $report->notes ?? 'No additional notes provided.',
                'user' => [
                    'title' => $report->user->title ?? 'Mr.',
                    'name' => $report->user->name ?? 'Developer',
                    'id' => '000' . str_pad($report->user->id, 3, '0', STR_PAD_LEFT),
                    'phone' => $report->user->phone ?? '0124568888',
                    'gender' => $report->user->gender ?? 'Male',
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $reportData,
            ], 200);

        } catch (\Exception $e) {
            Log::error('API Error in ReportController@show: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString(),
                'user_id' => $user->id ?? null,
                'report_id' => $id,
            ]);
            return response()->json(['error' => 'Server error. Please try again.'], 500);
        }
    }
}
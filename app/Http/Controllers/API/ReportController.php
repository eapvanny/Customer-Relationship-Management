<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Helpers\AppHelper;
use App\Models\Customer;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {

            $query = Report::with([
                'user',
                'customer',
                'customer.depo'
            ]);

            $userRole = $user->role_id;
            $userId   = $user->id;
            $userType = $user->type;

            $userIds = [$userId];

            $staffIdCards = User::whereIn('id', $userIds)
                ->pluck('staff_id_card')
                ->filter()
                ->toArray();

            $allowedTypes = [
                AppHelper::SALE,
                AppHelper::SE,
            ];

            // ==============================
            // Permission
            // ==============================

            if (
                $userType == AppHelper::ALL ||
                in_array($userRole, [
                    AppHelper::USER_SUPER_ADMIN,
                    AppHelper::USER_ADMIN,
                    AppHelper::USER_DIRECTOR
                ])
            ) {

                // See everything

            } elseif ($userRole == AppHelper::USER_MANAGER) {

                $managedUserIds = User::where(function ($q) use ($userId) {
                    $q->where('manager_id', $userId)
                        ->orWhere('rsm_id', $userId)
                        ->orWhere('sup_id', $userId)
                        ->orWhere('asm_id', $userId);
                })
                    ->whereIn('type', $allowedTypes)
                    ->pluck('id')
                    ->toArray();

                $userIds = array_merge($userIds, $managedUserIds);

            } elseif ($userRole == AppHelper::USER_RSM) {

                $managedUserIds = User::where(function ($q) use ($userId) {
                    $q->where('rsm_id', $userId)
                        ->orWhere('sup_id', $userId)
                        ->orWhere('asm_id', $userId);
                })
                    ->whereIn('type', $allowedTypes)
                    ->pluck('id')
                    ->toArray();

                $userIds = array_merge($userIds, $managedUserIds);

            } elseif ($userRole == AppHelper::USER_SUP) {

                $managedUserIds = User::where(function ($q) use ($userId) {
                    $q->where('sup_id', $userId)
                        ->orWhere('asm_id', $userId);
                })
                    ->whereIn('type', $allowedTypes)
                    ->pluck('id')
                    ->toArray();

                $userIds = array_merge($userIds, $managedUserIds);

            } elseif ($userRole == AppHelper::USER_ASM) {

                $managedUserIds = User::where(function ($q) use ($userId) {
                    $q->whereJsonContains('asm_id', (string)$userId)
                        ->orWhere('asm_id', $userId);
                })
                    ->whereIn('type', $allowedTypes)
                    ->pluck('id')
                    ->toArray();

                $userIds = array_merge($userIds, $managedUserIds);
            }

            if (
                !(
                    $userType == AppHelper::ALL ||
                    in_array($userRole, [
                        AppHelper::USER_SUPER_ADMIN,
                        AppHelper::USER_ADMIN,
                        AppHelper::USER_DIRECTOR
                    ])
                )
            ) {

                $query->where(function ($q) use ($userIds, $staffIdCards, $allowedTypes) {

                    // Normal reports
                    $q->where(function ($q1) use ($userIds, $allowedTypes) {
                        $q1->whereIn('reports.user_id', array_unique($userIds))
                            ->whereHas('user', function ($q2) use ($allowedTypes) {
                                $q2->whereIn('type', $allowedTypes);
                            });
                    });

                    // Imported by SSP
                    if (!empty($staffIdCards)) {
                        $q->orWhereIn('reports.ssp_id', $staffIdCards);
                    }

                    // Imported by SUP
                    if (!empty($staffIdCards)) {
                        $q->orWhereIn('reports.sup_id', $staffIdCards);
                    }

                });
            }

            // ==============================
            // Modal
            // ==============================

            $hasNoReports = !Report::where('user_id', $userId)
                ->whereDate('created_at', Carbon::today())
                ->exists();

            $hasUnassignedReportToday = Report::where('user_id', $userId)
                ->whereNull('driver_id')
                ->whereNull('driver_status')
                ->whereDate('created_at', Carbon::today())
                ->exists();

            $showModal = $hasNoReports || $hasUnassignedReportToday;

            // ==============================
            // Reports
            // ==============================
            $perPage = (int) $request->get('per_page', 20);
            $reports = $query
                        ->orderByDesc('id')
                        ->paginate($perPage);

            $reportsData = $reports->getCollection()->map(function ($report) {

                return [

                    'id' => $report->id,

                    'report_id' => 'S-' . str_pad($report->id, 3, '0', STR_PAD_LEFT),

                    'customer_name' => $report->customer->name
                        ?? $report->customer_name
                        ?? 'N/A',

                    'customer_code' => $report->customer->code
                        ?? 'N/A',

                    'customer_type' => $report->customer_type
                        ?? 'អតិថិជនទូទៅ',

                    'outlet_name' => optional($report->customer?->depo)->name
                        ?? $report->outlet_name
                        ?? 'N/A',

                    'quantities' => [
                        [
                            'size' => '250ML',
                            'quantity' => (int) ($report->{'250_ml'} ?? 0)
                        ],
                        [
                            'size' => '350ML',
                            'quantity' => (int) ($report->{'350_ml'} ?? 0)
                        ],
                        [
                            'size' => '600ML',
                            'quantity' => (int) ($report->{'600_ml'} ?? 0)
                        ],
                        [
                            'size' => '1500ML',
                            'quantity' => (int) ($report->{'1500_ml'} ?? 0)
                        ],
                    ],

                    'other' => $report->other ?? '',

                    'formatted_date' => $report->date
                        ? Carbon::parse($report->date)->format('d F, Y')
                        : null,

                ];
            });

            return response()->json([
                'success' => true,
                'show_modal' => $showModal,
                'data' => $reportsData,

                'pagination' => [
                    'current_page' => $reports->currentPage(),
                    'last_page'    => $reports->lastPage(),
                    'per_page'     => $reports->perPage(),
                    'total'        => $reports->total(),
                    'from'         => $reports->firstItem(),
                    'to'           => $reports->lastItem(),
                    'has_more'     => $reports->hasMorePages(),
                ],
            ]);

        } catch (\Exception $e) {

            Log::error('API ReportController@index', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server Error'
            ], 500);
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
                'customer_type' => $report->customer
                    ? AppHelper::CUSTOMER_TYPE[$report->customer->customer_type] ?? 'N/A'
                    : 'N/A',
                'outlet_name' => $report->customer->depo ? $report->customer->depo->name ?? 'N/A' : 'N/A',
                'quantities' => [
                    ['size' => '250ML', 'quantity' => $report->{'250_ml'} ?? 0],
                    ['size' => '350ML', 'quantity' => $report->{'350_ml'} ?? 0],
                    ['size' => '600ML', 'quantity' => $report->{'600_ml'} ?? 0],
                    ['size' => '1500ML', 'quantity' => $report->{'1500_ml'} ?? 0],
                ],
                'formatted_date' => Carbon::parse($report->date)->format('d M, Y'),
                'sale_photo_url' => $report->outlet_photo
                    ? asset('storage/' . $report->outlet_photo)
                    : asset('images/avatar.png'),
                'posm_photo_url' => $report->photo
                    ? asset('storage/' . $report->photo)
                    : asset('images/avatar.png'),
                'material_type' => isset(AppHelper::MATERIAL[$report->posm])
                    ? __(AppHelper::MATERIAL[$report->posm])
                    : ($report->posm_name1 ?? 'N/A'),
                'material_quantity' => $report->qty ?? 'N/A',
                'material_type2' => isset(AppHelper::MATERIAL[$report->posm2])
                    ? __(AppHelper::MATERIAL[$report->posm2])
                    : ($report->posm_name2 ?? 'N/A'),
                'material_quantity2' => $report->qty2 ?? 'N/A',
                'material_type3' => isset(AppHelper::MATERIAL[$report->posm3])
                    ? __(AppHelper::MATERIAL[$report->posm3])
                    : ($report->posm_name3 ?? 'N/A'),
                'material_quantity3' => $report->qty3 ?? 'N/A',
                'latitude' => $report->latitude ?? 11.5241,
                'longitude' => $report->longitude ?? 104.9390,
                // 'address' => $report->city ?? 'N/A',
                'address' => $report->status === null
                    ? $report->city
                    : $report->address,
                'other' => $report->other ?? 'No additional notes provided.',
                'user' => [
                    'title' => $report->user->title ?? 'Mr.',
                    'name' => $report->user->name ?? 'Developer',
                    'id' => '000' . str_pad($report->user->id, 3, '0', STR_PAD_LEFT),
                    'phone' => $report->user->phone ?? '0124568888',
                    'gender' => AppHelper::GENDER[$report->user->gender] ?? 'N/A',
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

    public function getCustomerReport(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $request->validate([
            'area_id'   => 'required',
        ]);

        $query = Customer::where('area_id', $request->area_id)
            ->where('user_id', $user->id);

        // Restrict for SALE and SE
        if (in_array($user->type, [
            AppHelper::SALE,
            AppHelper::SE
        ])) {
            $query->where('user_type', $user->type);
        }

        $customers = $query
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $customers
        ]);
    }

    public function getCustomerType(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $request->validate([
            'customer_id' => 'required|exists:customers,id'
        ]);

        $customer = Customer::select(
                'customer_type',
                'user_type'
            )
            ->find($request->customer_id);

        if (
            in_array($user->type, [
                AppHelper::SALE,
                AppHelper::SE
            ]) &&
            $customer->user_type != $user->type
        ) {
            return response()->json([
                'status' => false,
                'message' => 'Permission denied.'
            ], 403);
        }

        $customerType = [
            'id'   => $customer->customer_type,
            'name' => AppHelper::CUSTOMER_TYPE[$customer->customer_type] ?? null,
        ];

        return response()->json([
            'status' => true,
            'data' => $customerType
        ]);
    }

    public function store(Request $request)
    {
        try {

            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            // Get valid area ids
            $areaIds = Cache::rememberForever('area_ids', function () {
                return collect(AppHelper::getAreas())
                    ->flatMap(fn($group) => array_keys($group))
                    ->toArray();
            });

            $validator = Validator::make($request->all(), [
                'area_id' => ['required', Rule::in($areaIds)],
                'outlet_id' => 'required',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'city' => 'required|string|max:255',
                'country' => 'required|string|max:255',

                'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

                'outlet_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

                'customer_id' => 'required|exists:customers,id',
                'customer_type' => 'required',

                '250_ml' => 'nullable|integer',
                '350_ml' => 'nullable|integer',
                '600_ml' => 'nullable|integer',
                '1500_ml' => 'nullable|integer',
                'other' => 'nullable|string',

                'qty' => 'nullable|integer',
                'posm' => 'nullable',
                'qty2' => 'nullable|integer',
                'posm2' => 'nullable',
                'qty3' => 'nullable|integer',
                'posm3' => 'nullable',
            ]);

            if (!$request->hasFile('outlet_photo') && !$request->filled('outlet_photo_base64')) {
                $validator->after(function ($validator) {
                    $validator->errors()->add('outlet_photo', 'Outlet photo is required.');
                });
            }

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $prefix = AppHelper::getAreaNameById($request->area);

            $data = [
                'user_id' => $user->id,
                'area_id' => $request->area,
                'outlet_id' => $request->outlet_id,
                'customer_id' => $request->customer_id,
                'customer_type' => $request->customer_type,
                'date' => now('Asia/Phnom_Penh'),

                '250_ml' => $request->input('250_ml'),
                '350_ml' => $request->input('350_ml'),
                '600_ml' => $request->input('600_ml'),
                '1500_ml' => $request->input('1500_ml'),
                'other' => $request->other,

                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'city' => $request->city,
                'country' => $request->country,

                'qty' => $request->qty,
                'posm' => $request->posm,
                'qty2' => $request->qty2,
                'posm2' => $request->posm2,
                'qty3' => $request->qty3,
                'posm3' => $request->posm3,
            ];

            // Photo
            if ($request->hasFile('photo')) {

                $file = 'uploads/photo_' . time() . '_' . Str::random(8) . '.jpg';

                Storage::put(
                    $file,
                    AppHelper::resizeToSpecificSize(
                        $request->file('photo'),
                        1024,
                        1024,
                        70
                    )
                );

                $data['photo'] = $file;
            } 
            // Outlet Photo
            if ($request->hasFile('outlet_photo')) {

                $file = 'uploads/outlet_' . time() . '_' . Str::random(8) . '.jpg';

                Storage::put(
                    $file,
                    AppHelper::resizeToSpecificSize(
                        $request->file('outlet_photo'),
                        1024,
                        1024,
                        70
                    )
                );

                $data['outlet_photo'] = $file;
            } 

            $report = Report::create($data);

            $report->update([
                'so_number' => $prefix . '-' . str_pad($report->id, 7, '0', STR_PAD_LEFT),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Report created successfully.',
                'data' => $report
            ], 201);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

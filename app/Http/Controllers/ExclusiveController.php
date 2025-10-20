<?php

namespace App\Http\Controllers;

use App\Http\Helpers\AppHelper;
use App\Models\CustomerProvince;
use App\Models\Outlet;
use App\Models\Posm;
use App\Models\Region;
use App\Models\Se_program;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ExclusiveController extends Controller
{
    public function __construct()
    {
        $this->middleware('type.permission:view exclusive', ['only' => ['index']]);
        $this->middleware('type.permission:create exclusive', ['only' => ['create', 'store']]);
        $this->middleware('type.permission:update exclusive', ['only' => ['update', 'edit']]);
        $this->middleware('type.permission:delete exclusive', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $data['reports'] = '';
        $type = $request->type;

        // Define base query based on type
        if ($type === 'school') {
            $query = Se_program::with('user', 'CustomerProvince', 'region', 'outlet')
                ->whereDate('created_at', today())
                ->orderBy('id', 'desc');
        } elseif ($type === 'sport-club') {
            $query = SportClubProgram::with('user', 'CustomerProvince', 'region', 'outlet')
                ->whereDate('created_at', today())
                ->orderBy('id', 'desc');
        } elseif ($type === 'restaurant') {
            $query = Se_program::with('user', 'CustomerProvince', 'region', 'outlet')
                ->whereDate('created_at', today())
                ->orderBy('id', 'desc');
        } elseif ($type === 'hotel') {
            $query = HotelProgram::with('user', 'CustomerProvince', 'region', 'outlet')
                ->whereDate('created_at', today())
                ->orderBy('id', 'desc');
        } else {
            // redirect to default type if invalid
            return redirect()->route('exclusive.index', 'school');
        }

        // ========== Regions ==========
        $regions = Region::where('active_status', 1)
            ->select('region_name', 'rg_manager_kh', 'rg_manager_en', 'se_code', 'id')
            ->orderBy('region_name', 'asc')
            ->get()
            ->groupBy('rg_manager_kh');

        // ========== Filter by user role ==========
        if ($user) {
            $userRole = $user->role_id;
            $userId = $user->id;
            $userType = $user->type;
            $userIds = [$userId];
            $allowedTypes = [AppHelper::SALE, AppHelper::SE];

            if (!($userType == AppHelper::ALL || in_array($userRole, [
                AppHelper::USER_SUPER_ADMIN,
                AppHelper::USER_ADMIN,
                AppHelper::USER_DIRECTOR
            ]))) {
                if ($userRole == AppHelper::USER_MANAGER) {
                    $managedUserIds = User::where(function ($q) use ($userId) {
                        $q
                            ->where('manager_id', $userId)
                            ->orWhere('rsm_id', $userId)
                            ->orWhere('sup_id', $userId)
                            ->orWhere('asm_id', $userId);
                    })->whereIn('type', $allowedTypes)->pluck('id')->toArray();
                } elseif ($userRole == AppHelper::USER_RSM) {
                    $managedUserIds = User::where(function ($q) use ($userId) {
                        $q
                            ->where('rsm_id', $userId)
                            ->orWhere('sup_id', $userId)
                            ->orWhere('asm_id', $userId);
                    })->whereIn('type', $allowedTypes)->pluck('id')->toArray();
                } elseif ($userRole == AppHelper::USER_SUP) {
                    $managedUserIds = User::where(function ($q) use ($userId) {
                        $q
                            ->where('sup_id', $userId)
                            ->orWhere('asm_id', $userId);
                    })->whereIn('type', $allowedTypes)->pluck('id')->toArray();
                } elseif ($userRole == AppHelper::USER_ASM) {
                    $managedUserIds = User::where('asm_id', $userId)
                        ->whereIn('type', $allowedTypes)
                        ->pluck('id')
                        ->toArray();
                } else {
                    $managedUserIds = [];
                }

                $userIds = array_merge($userIds, $managedUserIds);
                $query->whereIn('user_id', array_unique($userIds));

                $query->whereHas('user', function ($q) use ($allowedTypes) {
                    $q->whereIn('type', $allowedTypes);
                });
            }
        } else {
            // no user, no reports
            $query->where('id', 0);
        }

        // ========== Employee list for filter ==========
        $employeeQuery = User::query();
        if ($user && !($userType == AppHelper::ALL || in_array($userRole, [
            AppHelper::USER_SUPER_ADMIN,
            AppHelper::USER_ADMIN,
            AppHelper::USER_DIRECTOR
        ]))) {
            if ($userRole == AppHelper::USER_MANAGER) {
                $employeeQuery->where(function ($q) use ($userId) {
                    $q
                        ->where('manager_id', $userId)
                        ->orWhere('rsm_id', $userId)
                        ->orWhere('sup_id', $userId)
                        ->orWhere('asm_id', $userId);
                })->whereIn('type', $allowedTypes);
            } elseif ($userRole == AppHelper::USER_RSM) {
                $employeeQuery->where(function ($q) use ($userId) {
                    $q
                        ->where('rsm_id', $userId)
                        ->orWhere('sup_id', $userId)
                        ->orWhere('asm_id', $userId);
                })->whereIn('type', $allowedTypes);
            } elseif ($userRole == AppHelper::USER_SUP) {
                $employeeQuery->where(function ($q) use ($userId) {
                    $q
                        ->where('sup_id', $userId)
                        ->orWhere('asm_id', $userId);
                })->whereIn('type', $allowedTypes);
            } elseif ($userRole == AppHelper::USER_ASM) {
                $employeeQuery->where('asm_id', $userId)->whereIn('type', $allowedTypes);
            } else {
                $employeeQuery->where('id', $userId);
            }
        }

        $full_name = $employeeQuery->get()->mapWithKeys(function ($u) {
            return [$u->id => $u->user_lang === 'en' ? ($u->full_name_latin ?? 'N/A') : ($u->full_name ?? 'N/A')];
        });

        // ========== Filters ==========
        $is_filter = false;

        if ($request->filled(['date1', 'date2'])) {
            $is_filter = true;
            $startDate = Carbon::parse($request->date1)->startOfDay();
            $endDate = Carbon::parse($request->date2)->endOfDay();
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        if ($request->filled('area_id')) {
            $is_filter = true;
            $query->where('area_id', $request->area_id);
        }

        // ========== Fetch data ==========
        $reports = $query->get();
        return view('backend.exclusive.index', compact('is_filter', 'full_name', 'reports', 'regions', 'type'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($type)
    {
        $report = null;
        $customer = null;  // Assuming $customer is used for editing; null for create
        $customers = [];
        $customerType = AppHelper::CUSTOMER_TYPE_PROVINCE;
        $posms = Posm::where('status', 1)->orderBy('id', 'desc')->get();
        // If there's old input or a pre-selected area, fetch customers
        $areaId = old('area', $customer->area_id ?? '');
        if ($areaId) {
            $customers = CustomerProvince::where('area_id', $areaId)->get(['id', 'name']);
        }

        $regions = Region::where('active_status', 1)
            ->select('region_name', 'rg_manager_kh', 'rg_manager_en', 'se_code', 'id')
            ->orderBy('region_name', 'asc')
            ->get()
            ->groupBy('rg_manager_kh');

        $outlets = Outlet::where('active_status', 1)->orderBy('id', 'desc')->get();

        return view('backend.exclusive.add', compact('customer', 'customers', 'report', 'customerType', 'regions', 'outlets', 'posms'));
    }
    

    public function getCustomersByArea(Request $request)
    {
        // dd($request->all());

        $areaId = $request->query('area_id');
        $customers = CustomerProvince::where('area_id', $areaId)->where('active_status', 1)->get(['id', 'name']);

        // Extract unique outlet values

        // $outlets = $customers->pluck('outlet')->unique()->filter()->map(function ($outlet, $index) {
        //     return ['id' => $index + 1, 'name' => $outlet];
        // })->values();

        // dd($outlets);
        $outlets = Outlet::where('area_id', $areaId)->where('active_status', 1)->get();
        return response()->json([
            'customers' => $customers->map(function ($customer) {
                return ['id' => $customer->id, 'name' => $customer->name];
            }),
            'outlets' => $outlets
        ]);
    }

    public function getOutlets(Request $request)
    {
        $areaId = $request->query('area_id');
        $authUser = auth()->user();

        if (!$areaId) {
            return response()->json([], 400);
        }

        $query = Outlet::where('area_id', $areaId)->where('active_status', 1);

        if (in_array($authUser->type, [AppHelper::SALE, AppHelper::SE])) {
            // Filter outlets accessible by SALE or SE users
            $query->where('user_type', $authUser->type);
            // ->where(function ($q) use ($authUser) {
            //     $q->where('user_id', $authUser->id); // Include depos without specific user
            // });
        }

        $outlets = $query->pluck('name', 'id')->toArray();

        return response()->json($outlets);
    }

    // AJAX endpoint to fetch customers by area_id and outlet_id
    public function getCustomers(Request $request)
    {
        $areaId = $request->query('area_id');
        $outletId = $request->query('outlet_id');
        $authUser = auth()->user();

        if (!$areaId || !$outletId) {
            return response()->json(['success' => false, 'error' => 'Area ID and Outlet ID are required'], 400);
        }

        $query = CustomerProvince::where('area_id', $areaId)
            ->where('active_status', 1)
            ->where('depo_id', $outletId);

        if (in_array($authUser->type, [AppHelper::SALE, AppHelper::SE])) {
            // Further filter if needed (e.g., customers assigned to this user)
            $query->where('user_type', $authUser->type);  // adjust field if different
        }

        $customers = $query->select('id', 'name')->get();

        return response()->json(['success' => true, 'customers' => $customers]);
    }

    // AJAX endpoint to fetch customer types by customer_id
    public function getCustomerType(Request $request)
    {
        $customerId = $request->query('customer_id');
        $authUser = auth()->user();

        if (!$customerId) {
            return response()->json(['customer_types' => []], 400);
        }

        $customer = CustomerProvince::find($customerId, ['customer_type', 'user_type']);

        if (!$customer || !$customer->customer_type) {
            return response()->json(['customer_types' => []]);
        }

        // Check user access if SALE or SE
        if (in_array($authUser->type, [AppHelper::SALE, AppHelper::SE]) && $customer->user_type != $authUser->type) {
            return response()->json(['customer_types' => []]);
        }

        $customerTypes = collect(AppHelper::CUSTOMER_TYPE_PROVINCE)
            ->map(function ($name, $id) {
                return ['id' => $id, 'name' => $name];
            })
            ->filter(function ($type) use ($customer) {
                return $type['id'] == $customer->customer_type;
            })
            ->values();

        return response()->json(['customer_types' => $customerTypes]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

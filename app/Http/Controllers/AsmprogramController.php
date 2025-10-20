<?php

namespace App\Http\Controllers;

use App\Events\ReportRequest;
use App\Exports\AsmprogramExport;
use App\Exports\ReportsExport;
use App\Exports\SubwholesaleExport;
use App\Http\Helpers\AppHelper;
use App\Models\Asm_program;
use App\Models\CustomerProvince;
use App\Models\MCustomer;
use App\Models\Outlet;
use App\Models\Posm;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

// use Maatwebsite\Excel\Facades\Excel;

class AsmprogramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('type.permission:view asm', ['only' => ['index']]);
        $this->middleware('type.permission:create asm', ['only' => ['create', 'store']]);
        $this->middleware('type.permission:update asm', ['only' => ['update', 'edit']]);
        $this->middleware('type.permission:delete asm', ['only' => ['destroy']]);
    }

    public $indexof = 1;

    public function index(Request $request)
    {
        $indexof = $this->indexof;
        $query = Asm_program::with('user', 'CustomerProvince', 'region', 'outlet')->whereDate('created_at', today())->orderBy('id', 'desc');
        $user = auth()->user();

        $regions = Region::where('active_status', 1)
            ->select('region_name', 'rg_manager_kh', 'rg_manager_en', 'se_code', 'id')
            ->orderBy('region_name', 'asc')
            ->get()
            ->groupBy('rg_manager_kh');

        if ($user) {
            $userRole = $user->role_id;
            $userId = $user->id;
            $userType = $user->type;

            // Collect user IDs to filter reports
            $userIds = [$userId];  // Always include own reports

            // Define allowed user types for subordinates
            $allowedTypes = [AppHelper::SALE, AppHelper::SE];

            if ($userType == AppHelper::ALL || in_array($userRole, [
                AppHelper::USER_SUPER_ADMIN,
                AppHelper::USER_ADMIN,
                AppHelper::USER_DIRECTOR
            ])) {
                // Users with type ALL or roles Super Admin, Admin, Director see all reports
                // No additional filtering needed
            } elseif ($userRole == AppHelper::USER_MANAGER) {
                // Manager sees reports of RSMs, Supervisors, ASMs, Employees under them
                $managedUserIds = User::where(function ($q) use ($userId) {
                    $q
                        ->where('manager_id', $userId)
                        ->orWhere('rsm_id', $userId)
                        ->orWhere('sup_id', $userId)
                        ->orWhere('asm_id', $userId);
                })
                    ->whereIn('type', $allowedTypes)
                    ->pluck('id')
                    ->toArray();
                $userIds = array_merge($userIds, $managedUserIds);
            } elseif ($userRole == AppHelper::USER_RSM) {
                // RSM sees reports of Supervisors, ASMs, Employees under them
                $managedUserIds = User::where(function ($q) use ($userId) {
                    $q
                        ->where('rsm_id', $userId)
                        ->orWhere('sup_id', $userId)
                        ->orWhere('asm_id', $userId);
                })
                    ->whereIn('type', $allowedTypes)
                    ->pluck('id')
                    ->toArray();
                $userIds = array_merge($userIds, $managedUserIds);
            } elseif ($userRole == AppHelper::USER_SUP) {
                // Supervisor sees reports of ASMs, Employees under them
                $managedUserIds = User::where(function ($q) use ($userId) {
                    $q
                        ->where('sup_id', $userId)
                        ->orWhere('asm_id', $userId);
                })
                    ->whereIn('type', $allowedTypes)
                    ->pluck('id')
                    ->toArray();
                $userIds = array_merge($userIds, $managedUserIds);
            } elseif ($userRole == AppHelper::USER_ASM) {
                // ASM sees reports of Employees under them
                $managedUserIds = User::where('asm_id', $userId)
                    ->whereIn('type', $allowedTypes)
                    ->pluck('id')
                    ->toArray();
                $userIds = array_merge($userIds, $managedUserIds);
            }

            // Apply user ID filter unless Super Admin, Admin, Director, or type ALL
            if (!($userType == AppHelper::ALL || in_array($userRole, [
                AppHelper::USER_SUPER_ADMIN,
                AppHelper::USER_ADMIN,
                AppHelper::USER_DIRECTOR
            ]))) {
                $query->whereIn('user_id', array_unique($userIds));
            }

            // Ensure reports belong to users with allowed types (except for ALL/Super Admin/Admin/Director)
            if (!($userType == AppHelper::ALL || in_array($userRole, [
                AppHelper::USER_SUPER_ADMIN,
                AppHelper::USER_ADMIN,
                AppHelper::USER_DIRECTOR
            ]))) {
                $query->whereHas('user', function ($q) use ($allowedTypes) {
                    $q->whereIn('type', $allowedTypes);
                });
            }
        } else {
            // No authenticated user, return no reports
            $query->where('id', 0);
        }

        // Load employee list for filtering (based on role hierarchy)
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
                $employeeQuery
                    ->where('asm_id', $userId)
                    ->whereIn('type', $allowedTypes);
            } else {
                $employeeQuery->where('id', $userId);  // Employee sees only themselves
            }
        }

        $full_name = $employeeQuery->get()->mapWithKeys(function ($u) use ($user) {
            return [$u->id => $u->user_lang === 'en' ? ($u->full_name_latin ?? 'N/A') : ($u->full_name ?? 'N/A')];
        });

        $is_filter = false;

        // Date filtering
        if ($request->filled(['date1', 'date2'])) {
            $is_filter = true;
            $startDate = Carbon::parse($request->date1)->startOfDay();
            $endDate = Carbon::parse($request->date2)->endOfDay();
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        // if ($request->filled(['date1', 'date2'])) {
        //     $is_filter = true;
        //     $startDate = Carbon::parse($request->date1)->startOfDay();
        //     $endDate = Carbon::parse($request->date2)->endOfDay();
        //     $query->whereBetween('reports.date', [$startDate, $endDate]);
        // }

        if ($request->filled('area_id')) {
            $is_filter = true;
            $query->where('area_id', $request->area_id);
        }

        $reports = $query->get();
        // foreach($reports as $report) {
        //     dd($report);
        // }

        /*
         * if ($request->ajax()) {
         *     $reports = $query->get();
         *     // dd($reports);
         *     return DataTables::of($reports)
         *         ->addColumn('photo', function ($data) {
         *             $photoUrl = $data->user->photo ? asset('storage/' . $data->user->photo) : asset('images/avatar.png');
         *             return '<img class="img-responsive center" style="height: 35px; width: 35px; object-fit: cover; border-radius: 50%;" src="' . $photoUrl . '" >';
         *         })
         *         ->addColumn('photo_foc', function ($data) {
         *             $photoUrl = $data->user->photo_foc ? asset('storage/' . $data->user->photo_foc) : asset('images/avatar.png');
         *             return '<img class="img-responsive center" style="height: 35px; width: 35px; object-fit: cover; border-radius: 50%;" src="' . $photoUrl . '" >';
         *         })
         *         ->addColumn('id_card', function ($data) {
         *             return $data->user->staff_id_card ?? 'N/A';
         *         })
         *         ->addColumn('name', function ($data) {
         *             $user = optional($data->user);
         *             return auth()->user()->user_lang == 'en'
         *                 ? ($user->getFullNameLatinAttribute() ?? 'N/A')
         *                 : ($user->getFullNameAttribute() ?? 'N/A');
         *         })
         *
         *         ->addColumn('area', function ($data) {
         *             return __(AppHelper::getAreaName($data->area_id));
         *             // return isset(AppHelper::AREAS[$data->area_id]) ? __(AppHelper::AREAS[$data->area_id]) : __('N/A');
         *         })
         *
         *     ->addColumn('outlet_id', function ($data) {
         *             return $data->outlet_id ? $data->customer->outlet : 'N/A';
         *         })
         *
         *         ->addColumn('customer', function ($data) {
         *             return $data->customer ? $data->customer->name : 'N/A';
         *
         *         })
         *
         *         ->addColumn('customer_type', function ($data) {
         *             // return __($data->customer_type);
         *             return isset(AppHelper::CUSTOMER_TYPE[$data->customer_type]) ? __(AppHelper::CUSTOMER_TYPE[$data->customer_type]) : __('N/A');
         *
         *         })
         *
         *         ->addColumn('250ml', function ($data) {
         *             return __($data->{"250_ml"}) ?? 'N/A';
         *         })
         *         ->addColumn('350ml', function ($data) {
         *             return __($data->{"350_ml"}) ?? 'N/A';
         *         })
         *         ->addColumn('600ml', function ($data) {
         *             return __($data->{"600_ml"}) ?? 'N/A';
         *         })
         *         ->addColumn('1500ml', function ($data) {
         *             return __($data->{"1500_ml"}) ?? 'N/A';
         *         })
         *
         *         ->addColumn('phone', function ($data) {
         *             return $data->customer ? $data->customer->phone : 'N/A';
         *         })
         *
         *         ->addColumn('latitude', function ($data) {
         *             return __($data->latitude) ?? 'N/A';
         *         })
         *         ->addColumn('longitude', function ($data) {
         *             return __($data->longitude) ?? 'N/A';
         *         })
         *
         *         ->addColumn('location', function ($data) {
         *             return __($data->city . ',' . $data->country) ?? 'N/A';
         *         })
         *         ->addColumn('date', function ($data) {
         *             return $data->created_at ? Carbon::parse($data->created_at)->format('d-M-Y h:i A') : 'N/A';
         *         })
         *         ->addColumn('other', function ($data) {
         *             return __($data->other) ?? 'N/A';
         *         })
         *         ->addColumn('posm', function ($data) {
         *             return isset(AppHelper::MATERIAL[$data->posm]) ? __(AppHelper::MATERIAL[$data->posm]) : __('N/A');
         *         })
         *         ->addColumn('qty', function ($data) {
         *             return __($data->qty) ?? 'N/A';
         *         })
         *         ->addColumn('foc_qty', function ($data) {
         *             return __($data->foc_qty) ?? 'N/A';
         *         })
         *         ->addColumn('action', function ($data) {
         *             $editRoute = route('asm.edit', $data->id);
         *             $deleteRoute = route('asm.destroy', $data->id);
         *
         *             $actionButtons = '
         *             <span class="change-action-item">
         *                 <a href="javascript:void(0);" class="btn btn-primary btn-sm img-detail" data-id="' . $data->id . '" title="Show" data-bs-toggle="modal">
         *                     <i class="fa fa-fw fa-eye"></i>
         *                 </a>
         *             </span>';
         *
         *             if (auth()->user()->can('update user')) {
         *                 $actionButtons .= '
         *                 <span class="change-action-item">
         *                     <a title="Edit" href="' . $editRoute . '" class="btn btn-primary btn-sm">
         *                         <i class="fa fa-edit"></i>
         *                     </a>
         *                 </span>';
         *             }
         *
         *             return $actionButtons;
         *         })
         *
         *         //        <span class="change-action-item">
         *         //        <a href="' . $deleteRoute . '" class="btn btn-danger btn-sm delete" title="Delete">
         *         //            <i class="fa fa-fw fa-trash"></i>
         *         //        </a>
         *         //    </span>
         *         // })
         *         ->rawColumns(['photo', 'action'])
         *     ->make(true);
         * }
         */
        return view('backend.asm.index', compact('is_filter', 'full_name', 'reports', 'indexof', 'regions'));
        // dd('HI Wholesale');
        // return view('backend.asm.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $report = null;
        // dd('HI Wholesale');
        $customer = null;  // Assuming $customer is used for editing; null for create
        $customers = [];
        $report = null;
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

        return view('backend.asm.add', compact('customer', 'customers', 'report', 'customerType', 'regions', 'outlets', 'posms'));
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
        // dd($request->input());
        // $areaIds = [];
        // foreach (AppHelper::getAreas() as $group) {
        //     $areaIds = array_merge($areaIds, array_keys($group));
        // }
        $rules = [
            // 'area' => 'required',
            // 'outlet_id' => 'required',
            // 'latitude' => 'required|numeric',
            // 'longitude' => 'required|numeric',
            // 'city' => 'required|string',
            // 'country' => 'required|string',
            // 'photo' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            // 'photo_base64' => 'nullable|string',
            // 'customer_id' => 'required',
            // 'customer_type' => 'required',
            // 'phone' => 'nullable',
            // 'foc_qty' => 'nullable|numeric',
            // 'photo_foc' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            // 'photo_base64_foc' => 'nullable|string',
            // -------------
            'area' => 'required|string|max:255',
            'outlet_id' => 'required|integer',
            'customer_id' => 'required|integer',
            'customer_type' => 'required|string|max:255',
            'date' => 'nullable|date',
            // Product quantities (allow numeric or null)
            '250_ml' => 'nullable|numeric|min:0',
            '350_ml' => 'nullable|numeric|min:0',
            '600_ml' => 'nullable|numeric|min:0',
            '1500_ml' => 'nullable|numeric|min:0',
            'other' => 'nullable|string|max:255',
            // Old photos (optional)
            // 'old_photo' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            // 'old_photo_foc' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            // 'oldphoto' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            // 'oldphoto-foc' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            // FOC (Free of Charge) quantities
            'foc_250_qty' => 'nullable|numeric|min:0|max:100',
            'foc_350_qty' => 'nullable|numeric|min:0|max:100',
            'foc_600_qty' => 'nullable|numeric|min:0|max:100',
            'foc_1500_qty' => 'nullable|numeric|min:0|max:100',
            'foc_other' => 'nullable|string|max:255',
            'foc_other_qty' => 'nullable|numeric|min:0|max:100',
            // POSM fields
            'posm1' => 'nullable|integer|exists:posms,id',
            'posm_1_qty' => 'nullable|numeric|min:0|max:10',
            'posm2' => 'nullable|integer|exists:posms,id',
            'posm_2_qty' => 'nullable|numeric|min:0|max:10',
            'posm3' => 'nullable|integer|exists:posms,id',
            'posm_3_qty' => 'nullable|numeric|min:0|max:10',
            // Location
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            // Optional fields for images and FOC
            'photo' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            'photo_base64' => 'nullable|string',
            'photo_foc' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            'photo_base64_foc' => 'nullable|string',
            // Optional phone
            'phone' => 'nullable|string|max:20',
        ];

        $this->validate($request, $rules);

        $data = $request->except(['photo', 'photo_base64', 'photo_foc', 'photo_base64_foc']);
        $data['photo'] = null;
        $data['photo_foc'] = null;

        // Handle file upload if exists
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = time() . '_' . md5($file->getClientOriginalName()) . '.' . $file->extension();
            $filePath = 'uploads/' . $fileName;
            Storage::put($filePath, file_get_contents($file));
            $data['photo'] = $filePath;
        }

        if ($request->hasFile('photo_foc')) {
            $file = $request->file('photo_foc');
            $fileName = time() . '_' . md5($file->getClientOriginalName()) . '.' . $file->extension();
            $filePath = 'uploads/' . $fileName;
            Storage::put($filePath, file_get_contents($file));
            $data['photo_foc'] = $filePath;
        }

        // Handle base64 image if provided
        if ($request->photo_base64) {
            $image = str_replace('data:image/png;base64,', '', $request->photo_base64);
            $image = str_replace(' ', '+', $image);
            $imageData = base64_decode($image);

            $fileName = 'uploads/' . time() . '_' . Str::random(10) . '.png';
            Storage::put($fileName, $imageData);

            $data['photo'] = $fileName;
        }

        if ($request->photo_base64_foc) {
            $image = str_replace('data:image/png;base64,', '', $request->photo_base64_foc);
            $image = str_replace(' ', '+', $image);
            $imageData = base64_decode($image);

            $fileName = 'uploads/' . time() . '_' . Str::random(10) . '.png';
            Storage::put($fileName, $imageData);

            $data['photo_foc'] = $fileName;
        }

        // Store report data
        Asm_program::create([
            // 'user_id' => auth()->id(),
            // 'area_id' => $request->area,
            // 'outlet_id' => $request->outlet_id,
            // 'customer_id' => $request->customer_id,
            // 'customer_type' => $request->customer_type,
            // 'date' => Carbon::now('Asia/Phnom_Penh'),
            // '250_ml' => $request['250_ml'],
            // '350_ml' => $request['350_ml'],
            // '600_ml' => $request['600_ml'],
            // '1500_ml' => $request['1500_ml'],
            // 'phone' => $request['phone'],
            // 'other' => $request->other,
            // 'latitude' => $request->latitude,
            // 'longitude' => $request->longitude,
            // 'city' => $request->city,
            // 'country' => $request->country,
            // 'qty' => $request->qty,
            // 'posm' => $request->posm,
            // 'photo' => $data['photo'],
            // 'photo_foc' => $data['photo_foc'],
            // 'foc_qty' => $request->foc_qty,
            // ------
            'user_id' => auth()->id(),
            'area_id' => $request->area,
            'outlet_id' => $request->outlet_id,
            'customer_id' => $request->customer_id,
            'customer_type' => $request->customer_type,
            'date' => Carbon::now('Asia/Phnom_Penh'),
            // Product quantities
            '250_ml' => $request->input('250_ml'),
            '350_ml' => $request->input('350_ml'),
            '600_ml' => $request->input('600_ml'),
            '1500_ml' => $request->input('1500_ml'),
            'other' => $request->input('other'),
            // Contact
            'phone' => $request->input('phone'),
            // Location
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'city' => $request->city,
            'country' => $request->country,
            // Photos
            'photo' => $data['photo'] ?? null,
            'photo_foc' => $data['photo_foc'] ?? null,
            // FOC quantities
            'foc_250_qty' => $request->input('foc_250_qty'),
            'foc_350_qty' => $request->input('foc_350_qty'),
            'foc_600_qty' => $request->input('foc_600_qty'),
            'foc_1500_qty' => $request->input('foc_1500_qty'),
            'foc_other' => $request->input('foc_other'),
            'foc_other_qty' => $request->input('foc_other_qty'),
            // POSM items
            'posm_1' => $request->input('posm_1'),
            'posm_1_qty' => $request->input('posm_1_qty'),
            'posm_2' => $request->input('posm_2'),
            'posm_2_qty' => $request->input('posm_2_qty'),
            'posm_3' => $request->input('posm_3'),
            'posm_3_qty' => $request->input('posm_3_qty'),
        ]);

        $adminUsers = User::whereIn('role_id', [
            AppHelper::USER_SUPER_ADMIN,
            AppHelper::USER_ADMIN
        ])->pluck('id')->toArray();

        $managerId = auth()->user()->manager_id;

        $notificationUsers = $adminUsers;

        if ($managerId) {
            $notificationUsers[] = $managerId;
        }

        // Remove duplicate user IDs (if any)
        $notificationUsers = array_unique($notificationUsers);

        event(new ReportRequest(
            __('A new report has been created by ') . auth()->user()->family_name . ' ' . auth()->user()->name,
            $notificationUsers
        ));
        return redirect()->route('asm.index')->with('success', __('Report ASM Program has been created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    /*
    public function show($id)
    {
        $report = Asm_program::with('user', 'CustomerProvince', 'region', 'outlet')->find($id);
        if (!$report) {
            return response()->json(['error' => 'Report not found'], 404);
        }

        $user = $report->user;
        $employee_name = 'N/A';

        if ($user) {
            $employee_name = auth()->user()->user_lang == 'en'
                ? $user->getFullNameLatinAttribute()
                : $user->getFullNameAttribute();
        }
        $posm = isset(AppHelper::MATERIAL[$report->posm])
            ? __(AppHelper::MATERIAL[$report->posm])
            : 'Unknown';
        return response()->json([
            'report' => [
                'photo' => $report->photo ? asset('storage/' . $report->photo) : asset('images/avatar.png'),
                'employee_name' => $employee_name,
                'staff_id_card' => $user->staff_id_card ?? 'N/A',
                // 'area' => $report->area,
                'area' => $report->region->region_name . ' - ' . $report->region->se_code,
                // 'outlet' => $report->outlet,
                'outlet_id' => $report->outlet->name ?? 'N/A',
                'customer' => $report->CustomerProvince->name ?? 'N/A',
                // 'customer_type' => $report->customer_type,
                'customer_type' => isset(AppHelper::CUSTOMER_TYPE_PROVINCE[$report->customer_type])
                    ? __(AppHelper::CUSTOMER_TYPE_PROVINCE[$report->customer_type])
                    : __('N/A'),
                'date' => Carbon::parse($report->created_at)->format('d-m-Y h:i:s A'),
                'other' => $report->other ?? 'N/A',
                '250_ml' => $report->{'250_ml'},
                '350_ml' => $report->{'350_ml'},
                '600_ml' => $report->{'600_ml'},
                '1500_ml' => $report->{'1500_ml'},
                'phone' => $report->{'phone'},
                'city' => $report->city,
                'posm' => $posm,
                'qty' => $report->qty,
                'photo_foc' => $report->photo_foc ? asset('storage/' . $report->photo_foc) : asset('images/avatar.png'),
                'foc_qty' => $report->foc_qty,
            ]
        ]);
    }
    */

    public function show($id)
    {
        $data['report'] = Asm_program::with('user', 'CustomerProvince', 'region', 'outlet', 'posm1', 'posm2', 'posm3')->findOrFail($id);
        // dd($report);
        return view('backend.asm.show', $data);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $report = Asm_program::find($id);
        $posms = Posm::where('status', 1)->orderBy('id', 'desc')->get();
        if (!$report) {
            return redirect()->route('asm.index');
        }

        $customers = CustomerProvince::where('area_id', $report->area_id)->get(['id', 'name']);
        $customer = $report->customer;  // The related customer for the report
        $customerType = AppHelper::CUSTOMER_TYPE_PROVINCE;

        // dd('HI Wholesale');
        // $customer = null; // Assuming $customer is used for editing; null for create
        // $customers = [];
        // $report = null;
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

        return view('backend.asm.add', compact('report', 'customers', 'customer', 'customerType', 'regions', 'outlets', 'posms'));
    }

    public function update(Request $request, $id)
    {
        $report = Asm_program::find($id);
        if (!$report) {
            return redirect()->route('asm.index')->with('error', 'Report not found!');
        }

        // Get all valid area IDs (numeric keys)
        $areaIds = [];
        foreach (AppHelper::getAreas() as $group) {
            $areaIds = array_merge($areaIds, array_keys($group));
        }
        // dd($report);
        // Validation rules
        $rules = [
            // 'area' => 'required|in:' . implode(',', $areaIds),
            // 'outlet_id' => 'required',
            // 'outlet_id' => 'required',
            // 'customer_id' => 'required',
            // 'customer_type' => 'required',
            // 'latitude' => 'required|numeric',
            // 'longitude' => 'required|numeric',
            // 'city' => 'required|string',
            // 'country' => 'required|string',
            // 'photo' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            // 'photo_base64' => 'nullable|string',
            // 'phone' => 'nullable',
            // 'foc_qty' => 'nullable|numeric',
            // 'photo_foc' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            // 'photo_base64_foc' => 'nullable|string',
            // 'old_photo' => 'nullable|string',
            // 'old_photo_foc' => 'nullable|string',
            'area' => 'required|in:' . implode(',', $areaIds),
            'outlet_id' => 'required|integer',
            'customer_id' => 'required|integer',
            'customer_type' => 'required|string|max:255',
            'date' => 'nullable|date',
            // Product quantities (allow numeric or null)
            '250_ml' => 'nullable|numeric|min:0',
            '350_ml' => 'nullable|numeric|min:0',
            '600_ml' => 'nullable|numeric|min:0',
            '1500_ml' => 'nullable|numeric|min:0',
            'other' => 'nullable|string|max:255',
            // Old photos (optional)
            'old_photo' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            'old_photo_foc' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            'oldphoto' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            'oldphoto-foc' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            // FOC (Free of Charge) quantities
            'foc_250_qty' => 'nullable|numeric|min:0|max:100',
            'foc_350_qty' => 'nullable|numeric|min:0|max:100',
            'foc_600_qty' => 'nullable|numeric|min:0|max:100',
            'foc_1500_qty' => 'nullable|numeric|min:0|max:100',
            'foc_other' => 'nullable|string|max:255',
            'foc_other_qty' => 'nullable|numeric|min:0|max:100',
            // POSM fields
            'posm1' => 'nullable|integer|exists:posms,id',
            'posm_1_qty' => 'nullable|numeric|min:0|max:10',
            'posm2' => 'nullable|integer|exists:posms,id',
            'posm_2_qty' => 'nullable|numeric|min:0|max:10',
            'posm3' => 'nullable|integer|exists:posms,id',
            'posm_3_qty' => 'nullable|numeric|min:0|max:10',
            // Location
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            // Optional fields for images and FOC
            'photo' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            'photo_base64' => 'nullable|string',
            'photo_foc' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            'photo_base64_foc' => 'nullable|string',
            // Optional phone
            'phone' => 'nullable|string|max:20',
        ];
        // dd($request->oldphoto, $request->photo_base64);

        // $this->validate($request, $rules);

        // $data = [
        //     'area' => $request->area,
        //     'outlet' => $request->outlet,
        //     'customer' => $request->customer,
        //     'customer_type' => $request->customer_type,
        //     'date' => Carbon::now('Asia/Phnom_Penh'),
        //     '250_ml' => $request->input('250_ml'),
        //     '350_ml' => $request->input('350_ml'),
        //     '600_ml' => $request->input('600_ml'),
        //     '1500_ml' => $request->input('1500_ml'),
        //     'phone' => $request->input('phone'),
        //     'other' => $request->other,
        //     'latitude' => $request->latitude,
        //     'longitude' => $request->longitude,
        //     'city' => $request->city,
        //     'country' => $request->country,
        //     'qty' => $request->qty,
        //     'posm' => $request->posm,
        // ];

        $old_photo = $request->old_photo;
        $old_photo_foc = $request->old_photo_foc;

        /*
         * if ($request->hasFile('photo')) {
         *
         *     if ($report->photo && Storage::exists($report->photo)) {
         *         Storage::delete($report->photo);
         *     }
         *
         *     $file = $request->file('photo');
         *     $fileName = time() . '_' . md5($file->getClientOriginalName()) . '.' . $file->extension();
         *     $filePath = 'uploads/' . $fileName;
         *     Storage::put($filePath, file_get_contents($file));
         *     $data['photo'] = $filePath;
         * }else{
         *
         *     $data['photo'] = $old_photo;
         *
         * }
         *
         *
         * if($request->hasFile('photo_foc')){
         *     if ($report->photo_foc && Storage::exists($report->photo_foc)) {
         *         Storage::delete($report->photo_foc);
         *     }
         *
         *     $file = $request->file('photo_foc');
         *     $fileName = time() . '_' . md5($file->getClientOriginalName()) . '.' . $file->extension();
         *     $filePath = 'uploads/' . $fileName;
         *     Storage::put($filePath, file_get_contents($file));
         *     $data['photo_foc'] = $filePath;
         * }else{
         *     $data['photo_foc'] = $old_photo_foc;
         * }
         */

        // dd($request->photo_base64, $request->photo_base64_foc);

        // dd($data['photo'], $data['photo_foc'] );

        // dd($data['photo'], $data['photo_foc'] );

        // ///////////////////////////////////////////////////

        // Handle base64 image if provided
        if ($request->photo_base64 != $request->old_photo) {
            if ($report->photo && Storage::exists($report->photo)) {
                Storage::delete($report->photo);
            }
            $image = str_replace('data:image/png;base64,', '', $request->photo_base64);
            $image = str_replace(' ', '+', $image);
            $imageData = base64_decode($image);

            $fileName = 'uploads/' . time() . '_' . Str::random(10) . '.png';
            Storage::put($fileName, $imageData);

            $data['photo'] = $fileName;
        } else {
            $data['photo'] = $old_photo;
        }

        // dd($data['photo']);

        if ($request->photo_base64_foc != $request->old_photo_foc) {
            if ($report->photo_foc && Storage::exists($report->photo_foc)) {
                Storage::delete($report->photo_foc);
            }
            $image = str_replace('data:image/png;base64,', '', $request->photo_base64_foc);
            $image = str_replace(' ', '+', $image);
            $imageData = base64_decode($image);

            $fileName = 'uploads/' . time() . '_' . Str::random(10) . '.png';
            Storage::put($fileName, $imageData);

            $data['photo_foc'] = $fileName;
        } else {
            $data['photo_foc'] = $old_photo_foc;
        }

        // dd($request->photo_base64, $request->photo_base64_foc);

        // ///////////////////////////////////////////////////////

        // Handle base64 image if provided
        // if ($request->photo_base64) {
        //     if ($report->photo && Storage::exists($report->photo)) {
        //         Storage::delete($report->photo);
        //     }
        //     $image = str_replace('data:image/png;base64,', '', $request->photo_base64);
        //     $image = str_replace(' ', '+', $image);
        //     $imageData = base64_decode($image);

        //     $fileName = 'uploads/' . time() . '_' . Str::random(10) . '.png';
        //     Storage::put($fileName, $imageData);

        //     $data['photo'] = $fileName;
        // } else {
        //     $data['photo'] = $report->photo;
        // }

        // Update report
        // $report->update($data);
        $report->update(
            [
                // 'user_id' => auth()->id(),
                // 'area' => $request->area,
                // 'outlet' => $request->outlet,
                // 'customer_id' => $request->customer_id,
                // 'customer_type' => $request->customer_type,
                // 'date' => Carbon::now('Asia/Phnom_Penh'),
                // '250_ml' => $request['250_ml'],
                // '350_ml' => $request['350_ml'],
                // '600_ml' => $request['600_ml'],
                // '1500_ml' => $request['1500_ml'],
                // 'phone' => $request['phone'],
                // 'other' => $request->other,
                // 'latitude' => $request->latitude,
                // 'longitude' => $request->longitude,
                // 'city' => $request->city,
                // 'country' => $request->country,
                // 'qty' => $request->qty,
                // 'posm' => $request->posm,
                // 'photo' => $data['photo'],
                // 'photo_foc' => $data['photo_foc'],
                // 'foc_qty' => $request->foc_qty,
                'user_id' => auth()->id(),
                'area_id' => $request->area,
                'outlet_id' => $request->outlet_id,
                'customer_id' => $request->customer_id,
                'customer_type' => $request->customer_type,
                'date' => Carbon::now('Asia/Phnom_Penh'),
                // Product quantities
                '250_ml' => $request->input('250_ml'),
                '350_ml' => $request->input('350_ml'),
                '600_ml' => $request->input('600_ml'),
                '1500_ml' => $request->input('1500_ml'),
                'other' => $request->input('other'),
                // Contact
                'phone' => $request->input('phone'),
                // Location
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'city' => $request->city,
                'country' => $request->country,
                // Photos
                'photo' => $data['photo'] ?? null,
                'photo_foc' => $data['photo_foc'] ?? null,
                // FOC quantities
                'foc_250_qty' => $request->input('foc_250_qty'),
                'foc_350_qty' => $request->input('foc_350_qty'),
                'foc_600_qty' => $request->input('foc_600_qty'),
                'foc_1500_qty' => $request->input('foc_1500_qty'),
                'foc_other' => $request->input('foc_other'),
                'foc_other_qty' => $request->input('foc_other_qty'),
                // POSM items
                'posm_1' => $request->input('posm_1'),
                'posm_1_qty' => $request->input('posm_1_qty'),
                'posm_2' => $request->input('posm_2'),
                'posm_2_qty' => $request->input('posm_2_qty'),
                'posm_3' => $request->input('posm_3'),
                'posm_3_qty' => $request->input('posm_3_qty'),
            ]
        );

        return redirect()->route('asm.index')->with('success', __('Report ASM Program has been updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $report = Asm_program::find($id);
        if ($report) {
            $report->delete();
            return redirect()->back()->with('success', 'Report has been deleted!');
        }
        return redirect()->back()->with('error', 'Report not found!');
    }

    public function export(Request $request)
    {
        // dd($request->all());
        if ($request->has('date1') && $request->has('date2') && $request->has('area_id')) {
            return Excel::download(
                new AsmprogramExport($request->date1, $request->date2, $request->area_id, $request),
                'reports_asmprogram_' . now()->format('Y_m_d_His') . '.xlsx'
            );
        } else {
            return Excel::download(
                new AsmprogramExport($request->date1, $request->date2, $request->area_id, $request),
                'reports_asmprogram_' . now()->format('Y_m_d_His') . '.xlsx'
            );
        }
    }

    public function getReports()
    {
        // dd("HI Export");

        $user = Auth::user();

        $isAdmin = in_array($user->role_id, [AppHelper::USER_SUPER_ADMIN, AppHelper::USER_ADMIN]);
        $isManager = $user->role_id == AppHelper::USER_MANAGER;

        // $query = Asm_program::with('user')->whereNull('deleted_at')->where('is_seen', false);
        $query = Asm_program::with('user', 'CustomerProvince', 'region', 'outlet')->whereNull('deleted_at')->where('is_seen', false);

        if ($isManager) {
            // Managers can only see reports from their employees
            $query->whereIn('user_id', User::where('manager_id', $user->id)->pluck('id'));
        } elseif (!$isAdmin) {
            // Other users should not receive reports
            return response()->json([]);
        }

        $reports = $query->latest()->limit(5)->get()->map(function ($report) {
            return [
                'family_name' => $report->user->family_name ?? 'N/A',
                'name' => $report->user->name ?? 'N/A',
                'area' => $report->region->region_name . ' - ' . $report->region->se_code ?? 'Unknown',
                'photo' => $report->user->photo ? asset('storage/' . $report->user->photo) : asset('images/avatar.png')
            ];
        });

        return response()->json($reports);
    }

    public function markAsSeen()
    {
        $user = auth()->user();

        if (in_array($user->role_id, [AppHelper::USER_SUPER_ADMIN, AppHelper::USER_ADMIN])) {
            Asm_program::whereNull('deleted_at')->update(['is_seen' => true]);
        } elseif ($user->role_id == AppHelper::USER_MANAGER) {
            Asm_program::whereNull('deleted_at')
                ->whereIn('user_id', User::where('manager_id', $user->id)->pluck('id'))
                ->update(['is_seen' => true]);
        }

        return response()->json(['success' => true]);
    }
}

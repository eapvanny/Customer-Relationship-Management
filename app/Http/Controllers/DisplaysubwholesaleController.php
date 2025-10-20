<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Events\ReportRequest;
use App\Exports\SubwholesaleExport;
use Illuminate\Support\Carbon;
use App\Http\Helpers\AppHelper;
use App\Imports\SubwholesaleImport;
use App\Models\SubwholesalePicture;
use App\Models\Display_subwholesale;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class DisplaysubwholesaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('type.permission:view sub-wholesale', ['only' => ['index']]);
        $this->middleware('type.permission:create sub-wholesale', ['only' => ['create', 'store']]);
        $this->middleware('type.permission:update sub-wholesale', ['only' => ['update', 'edit']]);
        $this->middleware('type.permission:delete sub-wholesale', ['only' => ['destroy']]);
    }
    public $indexof = 1;

   public function index(Request $request)
    {
        $query = Display_subwholesale::with('user')->whereDate('created_at', today())->orderBy('id', 'desc');
        $user = auth()->user();

        if ($user) {
            $userRole = $user->role_id;
            $userId = $user->id;
            $userType = $user->type;
            $userIds = [$userId];  // Always include own reports
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
                $query->whereIn('apply_user', array_unique($userIds));
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
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($request->filled('full_name')) {
            $is_filter = true;
            $query->where('apply_user', $request->full_name);
        }

        $reports = $query->get();
        return view('backend.sub-wholesale.index', compact('is_filter', 'full_name', 'reports'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $report = null;
        $dataRetail = null;
        // dd('HI Wholesale');
        $customer = null; // Assuming $customer is used for editing; null for create
        $customers = [];
        $takePicture = null; // Assuming this is used for taking pictures, set to null for create
        $customerType = AppHelper::CUSTOMER_TYPE;
        // If there's old input or a pre-selected area, fetch customers
        $areaId = old('area', $customer->area_id ?? '');

        // if ($areaId) {
        //     $customers = Customer::where('area_id', $areaId)->get(['id', 'name', 'outlet']);
        // }
        return view('backend.sub-wholesale.add', compact('customer', 'customers', 'report', 'customerType', 'takePicture', 'dataRetail'));
    }

    public function store(Request $request)
    {

        $rules = [
            'region' => 'required|string',
            'sm_name' => 'required|string',
            'rsm_name' => 'required|string',
            'asm_name' => 'required|string',
            'sup_name' => 'required|string',
            'se_name' => 'required|string',
            'se_code' => 'required|string',
            'customer_code' => 'required|string',
            'depo_contact' => 'required|string',
            'depo_name' => 'required|string',
            'subwholesale_name' => 'required|string',
            'subwholesale_contact' => 'required|string',
            'business_type' => 'required|string',
            'sale_kpi' => 'required|string',
            'display_qty' => 'required|integer|min:0',
            'foc_qty' => 'required|integer|min:0',
            'remark' => 'nullable|string',
            'location' => 'required|string',
        ];

        $this->validate($request, $rules);
        $data['sub_wholesale'] = [
            'region' => $request->region,
            'sm_name' => $request->sm_name,
            'rsm_name' => $request->rsm_name,
            'asm_name' => $request->asm_name,
            'sup_name' => $request->sup_name,
            'se_name' => $request->se_name,
            'se_code' => $request->se_code,
            'customer_code' => $request->customer_code,
            'depo_contact' => $request->depo_contact,
            'depo_name' => $request->depo_name,
            'subwholesale_name' => $request->subwholesale_name,
            'subwholesale_contact' => $request->subwholesale_contact,
            'business_type' => $request->business_type,
            'sale_kpi' => $request->sale_kpi,
            'display_qty' => $request->display_qty,
            'foc_qty' => $request->foc_qty,
            'remark' => $request->remark,
            'apply_user' => auth()->id(),
            'location' => $request->location,
        ];

        // Store report data
        Display_subwholesale::create($data['sub_wholesale']);
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
            __("A new report has been created by ") . auth()->user()->family_name . ' ' . auth()->user()->name,
            $notificationUsers
        ));
        return redirect()->route('displaysub.index')->with('success', "Report has been created!");
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // dd('HI Show');

        $report = Display_subwholesale::with('user')->find($id);
        $picture = SubwholesalePicture::where('subwholesale_id', $id)->get();
        $showPicture = '';
        if ($picture) {
            foreach ($picture as $pic) {
                $showPicture .= '
                    <div class="col-md-6 mb-2">
                        <img src="' . asset('storage/' . $pic->picture) . '" class="img-fluid" style="max-width: 100%; height: auto;" />
                        <p class="text-center mt-2"><b>' . __("Picture dated") . '</b> : ' . Carbon::parse($pic->created_at)->format('d-m-Y H:i:s A') . '</p>
                    </div>';
            }
        }
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
                'modalEmployeeName' => $employee_name,
                'modalIdCard' => $user->staff_id_card ?? 'N/A',
                'modalRegion' => $report->region ?? 'N/A',
                'modalSmName' => $report->sm_name ?? 'N/A',
                'modalRsmName' => $report->rsm_name ?? 'N/A',
                'modalAsmName' => $report->asm_name ?? 'N/A',
                'modalSupName' => $report->sup_name ?? 'N/A',
                'modalSeName' => $report->se_name ?? 'N/A',
                'modalSeCode' => $report->se_code ?? 'N/A',
                'modalCustomerCode' => $report->customer_code ?? 'N/A',
                'modalDepoName' => $report->depo_name ?? 'N/A',
                'modalDepoContact' => $report->depo_contact ?? 'N/A',
                'modalWholesaleName' => $report->subwholesale_contact ?? 'N/A',
                'modalWholesaleContact' => $report->subwholesale_contact ?? 'N/A',
                'modalBusinessType' => $report->business_type ?? 'N/A',
                'modalSaleKPI' => $report->sale_kpi ?? 'N/A',
                'modalDisplayQty' => $report->display_qty ?? 'N/A',
                'modalFOC600ml' => $report->foc_qty ?? 'N/A',
                'modalRemark' => $report->remark ?? 'N/A',
                'modalLocation' => $report->location ?? 'N/A',
                'modalCreateDate' => $report->created_at ? $report->created_at->format('d-m-Y h:i:s A') : 'N/A',
            ],
            'picture' => $showPicture
        ]);
    }

    public function import(){
        // dd('Import data here');
        $report = null;
        // dd('HI Wholesale');
        $customer = null; // Assuming $customer is used for editing; null for create
        $customers = [];
        $report = null;
        $customerType = AppHelper::CUSTOMER_TYPE;
        // $areaId = old('area', $customer->area_id ?? '');
        // if ($areaId) {
        //     $customers = Customer::where('area_id', $areaId)->get(['id', 'name', 'outlet']);
        // }

        $selectUsers = User::where('type', AppHelper::SE)->orderBy('staff_id_card', 'asc')->get();

        // dd($customers);

        return view('backend.sub-wholesale.import', compact('customer', 'customers','report','customerType', 'selectUsers'));
        // return view('backend.sub-wholesale.import', compact('report', 'customers'));
    }


    // store import file as excel
    public function saveImport(Request $request)
    {
        // dd("HI iMport");
         $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv|max:2048', // Added max size limit
            'employee' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $file = $request->file('file');
        $employee_id = $request->employee;

        // Import the file
         $import = Excel::import(new SubwholesaleImport($employee_id), $file);
        if($import == true){
            return redirect()->back()->with('success', __('Data has imported successfully.'));
        }else return redirect()->back()->with('error', __('Failed to imported data.'))->withInput();

        // dd('File imported successfully.');
    }


    public function getCustomersByArea(Request $request)
    {
        $areaId = $request->query('area_id');
        $customers = Customer::where('area_id', $areaId)->get(['id', 'name', 'outlet']);

        // Extract unique outlet values
        $outlets = $customers->pluck('outlet')->unique()->filter()->map(function ($outlet, $index) {
            return ['id' => $index + 1, 'name' => $outlet];
        })->values();
        // dd($outlets);
        return response()->json([
            'customers' => $customers->map(function ($customer) {
                return ['id' => $customer->id, 'name' => $customer->name];
            }),
            'outlets' => $outlets
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // $report = Display_subwholesale::find($id);
        // if (!$report) {
        //     return redirect()->route('retail.index');
        // }
        // $takePicture = null; // Assuming this is used for taking pictures, set to null for edit
        // $customers = Customer::where('area_id', $report->area_id)->get(['id', 'name', 'outlet']);
        // $customer = $report->customer; // The related customer for the report
        // $customerType = AppHelper::CUSTOMER_TYPE;
        // $dataRetail = null;
        $report = Display_subwholesale::find($id);
        $dataRetail = null;
        $customer = null; // Assuming $customer is used for editing; null for create
        $customers = [];
        $takePicture = null; // Assuming this is used for taking pictures, set to null for create
        $customerType = AppHelper::CUSTOMER_TYPE;
        $areaId = old('area', $customer->area_id ?? '');
        return view('backend.sub-wholesale.add', compact('customer', 'customers', 'report', 'customerType', 'takePicture', 'dataRetail'));
    }



    public function update(Request $request, $id)
    {
        $report = Display_subwholesale::findOrFail($id);
        $rules = [
            'region' => 'required|string',
            'sm_name' => 'required|string',
            'rsm_name' => 'required|string',
            'asm_name' => 'required|string',
            'sup_name' => 'required|string',
            'se_name' => 'required|string',
            'se_code' => 'required|string',
            'customer_code' => 'required|string',
            'depo_contact' => 'required|string',
            'depo_name' => 'required|string',
            'subwholesale_name' => 'required|string',
            'subwholesale_contact' => 'required|string',
            'business_type' => 'required|string',
            'sale_kpi' => 'required|string',
            'display_qty' => 'required|integer|min:0',
            'foc_qty' => 'required|integer|min:0',
            'remark' => 'nullable|string',
            'location' => 'required|string',
        ];

        $this->validate($request, $rules);
        $data['sub-wholesale'] = [
            'region' => $request->region,
            'sm_name' => $request->sm_name,
            'rsm_name' => $request->rsm_name,
            'asm_name' => $request->asm_name,
            'sup_name' => $request->sup_name,
            'se_name' => $request->se_name,
            'se_code' => $request->se_code,
            'customer_code' => $request->customer_code,
            'depo_contact' => $request->depo_contact,
            'depo_name' => $request->depo_name,
            'subwholesale_name' => $request->subwholesale_name,
            'subwholesale_contact' => $request->subwholesale_contact,
            'business_type' => $request->business_type,
            'sale_kpi' => $request->sale_kpi,
            'display_qty' => $request->display_qty,
            'foc_qty' => $request->foc_qty,
            'remark' => $request->remark,
            'apply_user' => auth()->id(),
            'location' => $request->location,
        ];

        // Store report data
        $report->update($data['sub-wholesale']);
        return redirect()->route('displaysub.index')->with('success', 'Report has been updated!');

        // $update = $report->update($data['sub_wholesale']);
        // if($update) return redirect()->route('displaysub.index')->with('success', "Report has been updated!");
        // else return redirect()->route('displaysub.index')->with('error', "Report has not updated!");
    }

    public function getPictures($id){
        $takePicture = true;
        $customers = null;
        $customer = null;
        $customerType = null;
        // $report = null;
        $report = Display_subwholesale::find($id);
        if (!$report) {
            return redirect()->route('displaysub.index')->with('error', 'Report not found!');
        }
        return view('backend.sub-wholesale.take-photo', compact('report', 'customers', 'customer','customerType', 'takePicture'));
    }


    public function storePicture(Request $request, $id)
    {
        // dd('Hello');
        $report = Display_subwholesale::find($id);
        if (!$report) {
            return redirect()->route('displaysub.index')->with('error', 'Report not found!');
        }
        // dd($request->file());
        // dd($request->all());


        // Validate the request
        $request->validate([
            'photo' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            'photo_base64' => 'required|string',
            // 'photo' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',

        ]);



        if($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = time() . '_' . md5($file->getClientOriginalName()) . '.' . $file->extension();
            $filePath = 'uploads/subwholesale-img/' . $fileName;
            Storage::put($filePath, file_get_contents($file));
            $data['photo'] = $filePath;
        }

        if ($request->photo_base64) {
            $image = str_replace('data:image/png;base64,', '', $request->photo_base64);
            $image = str_replace(' ', '+', $image);
            $imageData = base64_decode($image);

            $fileName = 'uploads/subwholesale-img/' . time() . '_' . Str::random(10) . '.png';
            Storage::put($fileName, $imageData);

            $data['photo'] = $fileName;
        }
        // dd($data['photo']);

        $data['storePicture'] = [
            'subwholesale_id' =>  $id,
            'picture' => $data['photo']
        ];
        $storePicture = SubwholesalePicture::create($data['storePicture']);
        if($storePicture == true) return redirect()->route('displaysub.index')->with('success', 'Take picture has successfully.');
        else return redirect()->route('displaysub.index')->with('error', 'Take picture has not successfully.');


        // Handle file upload
        // if ($request->hasFile('photo')) {
        //     $file = $request->file('photo');
        //     $fileName = time() . '_' . md5($file->getClientOriginalName()) . '.' . $file->extension();
        //     $filePath = 'uploads/' . $fileName;
        //     Storage::put($filePath, file_get_contents($file));
        //     $report->photo = $filePath;
        //     $report->save();
        // }

        // return redirect()->route('retail.index')->with('success', "Picture has been uploaded!");
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

    }



    public function export(Request $request){
        if ($request->has('date1') && $request->has('date2') && $request->has('full_name')) {
            return Excel::download(
                new SubwholesaleExport ($request->date1, $request->date2, $request->full_name, $request),
                'sub-wholesale_export_' . now()->format('Y_m_d_His') . '.xlsx'
            );
        } else {
            return Excel::download(
                new SubwholesaleExport($request->date1, $request->date2, $request->full_name, $request),
                'sub-wholesale_export_' . now()->format('Y_m_d_His') . '.xlsx'
            );
        }
    }


    public function getReports()
    {
        // dd("HI Export");

        // $user = Auth::user();

        // $isAdmin = in_array($user->role_id, [AppHelper::USER_SUPER_ADMIN, AppHelper::USER_ADMIN]);
        // $isManager = $user->role_id == AppHelper::USER_MANAGER;

        // $query = Sub_wholesale::with('user')->whereNull('deleted_at')->where('is_seen', false);

        // if ($isManager) {
        //     // Managers can only see reports from their employees
        //     $query->whereIn('user_id', User::where('manager_id', $user->id)->pluck('id'));
        // } elseif (!$isAdmin) {
        //     // Other users should not receive reports
        //     return response()->json([]);
        // }

        // $reports = $query->latest()->limit(5)->get()->map(function ($report) {
        //     return [
        //         'family_name' => $report->user->family_name ?? 'N/A',
        //         'name' => $report->user->name ?? 'N/A',
        //         'area' => $report->area ?? 'Unknown',
        //         'photo' => $report->user->photo ? asset('storage/' . $report->user->photo) : asset('images/avatar.png')
        //     ];
        // });

        // return response()->json($reports);
    }


    public function markAsSeen()
    {
        // $user = auth()->user();

        // if (in_array($user->role_id, [AppHelper::USER_SUPER_ADMIN, AppHelper::USER_ADMIN])) {
        //     Sub_wholesale::whereNull('deleted_at')->update(['is_seen' => true]);
        // } elseif ($user->role_id == AppHelper::USER_MANAGER) {
        //     Sub_wholesale::whereNull('deleted_at')
        //         ->whereIn('user_id', User::where('manager_id', $user->id)->pluck('id'))
        //         ->update(['is_seen' => true]);
        // }

        // return response()->json(['success' => true]);
    }

}

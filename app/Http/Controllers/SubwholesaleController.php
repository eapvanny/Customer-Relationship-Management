<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Events\ReportRequest;
use App\Imports\RetailImport;
use App\Models\Sub_wholesale;
use App\Exports\ReportsExport;
use Illuminate\Support\Carbon;
use App\Http\Helpers\AppHelper;
use App\Exports\SubwholesaleExport;
use App\Imports\SubwholesaleImport;
// use App\Models\Retail;
use App\Models\SubwholesalePicture;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
// use Maatwebsite\Excel\Facades\Excel;


class SubwholesaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
        public function __construct()
    {
        $this->middleware('permission:view sub-wholesale', ['only' => ['index']]);
        $this->middleware('permission:create sub-wholesale', ['only' => ['create', 'store']]);
        $this->middleware('permission:update sub-wholesale', ['only' => ['update', 'edit']]);
        $this->middleware('permission:delete sub-wholesale', ['only' => ['destroy']]);
    }
    public $indexof = 1;

    public function index(Request $request)
    {
        // dd('HI Wholesale');
        $query = Sub_wholesale::with('user')->orderBy('id', 'desc');
        $user = auth()->user();
        if ($user->role_id === AppHelper::USER_MANAGER) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('manager_id', $user->id);
            });
        } elseif ($user->role_id !== AppHelper::USER_SUPER_ADMIN && $user->role_id !== AppHelper::USER_ADMIN) {
            $query->where('user_id', $user->id);
        }

        $is_filter = false;
        $authUser = auth()->user();


        $userRole = User::where('role_id', AppHelper::USER_EMPLOYEE);
        if ($authUser->role_id === AppHelper::USER_MANAGER) {
            $userRole->where('manager_id', $authUser->id);
        }
        $full_name = $userRole->get()->mapWithKeys(function ($names) use ($authUser) {
            return [
                $names->id => $authUser->user_lang === 'en'
                    ? $names->family_name_latin . ' ' . $names->name_latin
                    : $names->family_name . ' ' . $names->name
            ];
        });

        // Filter by date range
        if ($request->has(['date1', 'date2']) && !empty($request->date1) && !empty($request->date2)) {
            $is_filter = true;
            $startDate = Carbon::parse($request->date1)->startOfDay();
            $endDate = Carbon::parse($request->date2)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }


        if ($request->has('full_name') && !empty($request->full_name)){
            $is_filter = true;
            $query->where('user_id', $request->full_name);
        }


        if ($request->ajax()) {
            $reports = $query->get();
            return DataTables::of($reports)
                ->addColumn('id_card', function ($data) {
                    return $data->user->staff_id_card ?? 'N/A';
                })

                ->addColumn('name', function ($data) {
                    $user = optional($data->user);
                    return auth()->user()->user_lang == 'en'
                        ? ($user->getFullNameLatinAttribute() ?? 'N/A')
                        : ($user->getFullNameAttribute() ?? 'N/A');
                })

                ->addColumn('region', function ($data) {
                    return __(AppHelper::getAreaName($data->region));
                    // return isset(AppHelper::AREAS[$data->area_id]) ? __(AppHelper::AREAS[$data->area_id]) : __('N/A');
                })

               ->addColumn('asm_name', function ($data) {
                    return $data->asm_name;
                })

                ->addColumn('sup_name', function ($data) {
                    return $data->sup_name;

                })

                ->addColumn('se_name', function ($data) {
                    return $data->se_name;
                })

                ->addColumn('customer_name', function ($data) {
                    return $data->customer_name;
                })

                ->addColumn('contact_number', function ($data) {
                    return $data->contact_number;
                })
                ->addColumn('business_type', function ($data) {
                    return $data->business_type;
                })

                ->addColumn('ams', function ($data) {
                    return $data->ams;
                })

                ->addColumn('display_parasol', function ($data) {
                    return $data->display_parasol;
                })

                ->addColumn('foc', function ($data) {
                    return $data->foc;
                })

                ->addColumn('installation', function ($data) {
                    return $data->installation;
                })

                ->addColumn('action', function ($data) {
                    $editRoute = route('sub-wholesale.edit', $data->id);
                    $deleteRoute = route('sub-wholesale.destroy', $data->id);
                    $takePhotoRoute = route('sub-wholesale.picture', $data->id);

                    $actionButtons = '
                    <span class="change-action-item">
                        <a href="javascript:void(0);" class="btn btn-primary btn-sm img-detail" data-id="' . $data->id . '" title="Show" data-bs-toggle="modal">
                            <i class="fa fa-fw fa-eye"></i>
                        </a>
                    </span>';

                    if (auth()->user()->can('update user')) {
                        $actionButtons .= '
                        <span class="change-action-item">
                            <a title="Edit" href="' . $editRoute . '" class="btn btn-primary btn-sm">
                                <i class="fa fa-edit"></i>
                            </a>
                        </span>';
                    }
                    if (auth()->user()->can('update user')) {
                        $actionButtons .= '
                        <span class="change-action-item">
                            <a title="Take pictur" href="' . $takePhotoRoute . '" class="btn btn-primary btn-sm">
                                <i class="fa fa-camera"></i>
                            </a>
                        </span>';
                    }
                    return $actionButtons;
                })

                //        <span class="change-action-item">
                //        <a href="' . $deleteRoute . '" class="btn btn-danger btn-sm delete" title="Delete">
                //            <i class="fa fa-fw fa-trash"></i>
                //        </a>
                //    </span>
                // })
                ->rawColumns(['photo', 'action'])
                ->make(true);
        }

        return view('backend.sub-wholesale.index', compact('is_filter', 'full_name'));
        // dd('HI Wholesale');
        // return view('backend.retail.index');
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

        if ($areaId) {
            $customers = Customer::where('area_id', $areaId)->get(['id', 'name', 'outlet']);
        }

        // dd($customers);

        return view('backend.sub-wholesale.add', compact('customer', 'customers','report','customerType', 'takePicture', 'dataRetail'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());

        $rules = [
            'region' => 'required|string',
            'asm_name' => 'required|string',
            'sup_name' => 'required|string',
            'se_name' => 'required|string',
            'customer_name' => 'required|string',
            'contact_number' => 'required|string',
            'business_type' => 'required|string',
            'ams' => 'required|string',
            'display_water_boxes' => 'required|numeric',
            'foc' => 'required|string',
            'installation' => 'required|string',

            // 'photo_foc' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            // 'photo_base64_foc' => 'nullable|string',

        ];

        $this->validate($request, $rules);

        // $data = $request->except(['photo', 'photo_base64', 'photo_foc', 'photo_base64_foc']);
        // $data['photo'] = null;
        // $data['photo_foc'] = null;

        // Handle file upload if exists



        /*  /////  ### File include here ####

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $fileName = time() . '_' . md5($file->getClientOriginalName()) . '.' . $file->extension();
                $filePath = 'uploads/' . $fileName;
                Storage::put($filePath, file_get_contents($file));
                $data['photo'] = $filePath;
            }

            if($request->hasFile('photo_foc')) {
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

        */


        $data['sub_wholesale'] = [
            'region' => $request->region,
            'asm_name' => $request->asm_name,
            'sup_name' => $request->sup_name,
            'se_name' => $request->se_name,
            'customer_name' => $request->customer_name,
            'contact_number' => $request->contact_number,
            'business_type' => $request->business_type,
            'ams' => $request->ams,
            'display_parasol' => $request->display_water_boxes,
            'foc' => $request->foc,
            'installation' => $request->installation,
            'user_id' => auth()->id(),
        ];

        // Store report data
        Sub_wholesale::create($data['sub_wholesale']);


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
        return redirect()->route('sub-wholesale.index')->with('success', "Report has been created!");
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $report = Sub_wholesale::with('user')->find($id);
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
                'employee_name' => $employee_name,
                'staff_id_card' => $user->staff_id_card ?? 'N/A',
                'modalRegion' => $report->region ?? 'N/A',
                'modalAsmName' => $report->asm_name ?? 'N/A',
                'modalSupName' => $report->sup_name ?? 'N/A',
                'modalSeName' => $report->se_name ?? 'N/A',

                'modalCustomerName' => $report->customer_name ?? 'N/A',
                'modalContactNumber' => $report->contact_number ?? 'N/A',
                'modalBusinessType' => $report->business_type ?? 'N/A',
                'modalAMS' => $report->ams ?? 'N/A',
                'modalDisplayParasol' => $report->display_parasol ?? 'N/A',
                'modalFOC600ml' => $report->foc ?? 'N/A',
                'modalInstallation' => $report->installation ?? 'N/A',
                'modalCreateDate' => $report->created_at ? $report->created_at->format('d-m-Y H:i:s A') : 'N/A',
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
        // If there's old input or a pre-selected area, fetch customers
        $areaId = old('area', $customer->area_id ?? '');
        if ($areaId) {
            $customers = Customer::where('area_id', $areaId)->get(['id', 'name', 'outlet']);
        }

        // dd($customers);

        return view('backend.sub-wholesale.import', compact('customer', 'customers','report','customerType'));
        // return view('backend.sub-wholesale.import', compact('report', 'customers'));
    }


    // store import file as excel
    public function saveImport(Request $request)
    {
        // dd("HI iMport");
         $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv|max:2048', // Added max size limit
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $file = $request->file('file');
        // phpinfo();

        // exit;
        // Import the file
        Excel::import(new SubwholesaleImport, $file);
        // dd('File imported successfully.');
        return redirect()->route('sub-wholesale.index')->with('success', 'File imported successfully.');
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
        $report = Sub_wholesale::find($id);
        if (!$report) {
            return redirect()->route('retail.index');
        }
        $takePicture = null; // Assuming this is used for taking pictures, set to null for edit
        $customers = Customer::where('area_id', $report->area_id)->get(['id', 'name', 'outlet']);
        $customer = $report->customer; // The related customer for the report
        $customerType = AppHelper::CUSTOMER_TYPE;
        $dataRetail = null;
        return view('backend.sub-wholesale.add', compact('report', 'customers', 'customer','customerType', 'takePicture', 'dataRetail'));
    }



    public function update(Request $request, $id)
    {
        $report = Sub_wholesale::find($id);
        if (!$report) {
            return redirect()->route('sub-wholesale.index')->with('error', 'Report not found!');
        }
        $areaIds = [];
        foreach (AppHelper::getAreas() as $group) {
            $areaIds = array_merge($areaIds, array_keys($group));
        }
        $rules = [
            'region' => 'required|string',
            'asm_name' => 'required|string',
            'sup_name' => 'required|string',
            'se_name' => 'required|string',
            'customer_name' => 'required|string',
            'contact_number' => 'required|string',
            'business_type' => 'required|string',
            'ams' => 'required|string',
            'display_water_boxes' => 'required|numeric',
            'foc' => 'required|string',
            'installation' => 'required|string',
        ];

        $this->validate($request, $rules);
        $data['sub_wholesale'] = [
            'region' => $request->region,
            'asm_name' => $request->asm_name,
            'sup_name' => $request->sup_name,
            'se_name' => $request->se_name,
            'customer_name' => $request->customer_name,
            'contact_number' => $request->contact_number,
            'business_type' => $request->business_type,
            'ams' => $request->ams,
            'display_parasol' => $request->display_water_boxes,
            'foc' => $request->foc,
            'installation' => $request->installation,
            'user_id' => auth()->id(),
        ];

        $update = $report->update($data['sub_wholesale']);
        if($update) return redirect()->route('sub-wholesale.index')->with('success', "Report has been updated!");
        else return redirect()->route('sub-wholesale.index')->with('error', "Report has not updated!");
    }

    public function getPictures($id){
        $takePicture = true;
        $customers = null;
        $customer = null;
        $customerType = null;
        $report = null;
        $dataRetail = Sub_wholesale::find($id);
        if (!$dataRetail) {
            return redirect()->route('sub-wholesale.index')->with('error', 'Report not found!');
        }
        return view('backend.sub-wholesale.add', compact('report', 'customers', 'customer','customerType', 'takePicture', 'dataRetail'));
    }


    public function storePicture(Request $request, $id)
    {
        $report = Sub_wholesale::find($id);
        if (!$report) {
            return redirect()->route('sub-wholesale.index')->with('error', 'Report not found!');
        }
        // dd($request->file());
        // dd($request->all());


        // Validate the request
        $request->validate([
            'picture' => 'required|string',
            'photo_base64_foc' => 'required|string',
        ]);



        if($request->hasFile('picture')) {
            $file = $request->file('picture');
            $fileName = time() . '_' . md5($file->getClientOriginalName()) . '.' . $file->extension();
            $filePath = 'uploads/subwholesale-img/' . $fileName;
            Storage::put($filePath, file_get_contents($file));
            $data['photo'] = $filePath;
        }

        if ($request->photo_base64_foc) {
            $image = str_replace('data:image/png;base64,', '', $request->photo_base64_foc);
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
        if($storePicture == true) return redirect()->route('sub-wholesale.index')->with('success', 'Take picture has successfully.');
        else return redirect()->route('sub-wholesale.index')->with('error', 'Take picture has not successfully.');


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
        $report = Sub_wholesale::find($id);
        if ($report) {
            $report->delete();
            return redirect()->back()->with('success', "Report has been deleted!");
        }
        return redirect()->back()->with('error', "Report not found!");
    }



    public function export()
    {
        // dd('HI Export');
        return Excel::download(new SubwholesaleExport(), 'reports_subwholesale_' . now()->format('Y_m_d_His') . '.xlsx');
    }


    public function getReports()
    {
        // dd("HI Export");

        $user = Auth::user();

        $isAdmin = in_array($user->role_id, [AppHelper::USER_SUPER_ADMIN, AppHelper::USER_ADMIN]);
        $isManager = $user->role_id == AppHelper::USER_MANAGER;

        $query = Sub_wholesale::with('user')->whereNull('deleted_at')->where('is_seen', false);

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
                'area' => $report->area ?? 'Unknown',
                'photo' => $report->user->photo ? asset('storage/' . $report->user->photo) : asset('images/avatar.png')
            ];
        });

        return response()->json($reports);
    }




    public function markAsSeen()
    {
        $user = auth()->user();

        if (in_array($user->role_id, [AppHelper::USER_SUPER_ADMIN, AppHelper::USER_ADMIN])) {
            Sub_wholesale::whereNull('deleted_at')->update(['is_seen' => true]);
        } elseif ($user->role_id == AppHelper::USER_MANAGER) {
            Sub_wholesale::whereNull('deleted_at')
                ->whereIn('user_id', User::where('manager_id', $user->id)->pluck('id'))
                ->update(['is_seen' => true]);
        }

        return response()->json(['success' => true]);
    }


}


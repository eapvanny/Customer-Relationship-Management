<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Events\ReportRequest;
use App\Models\Sub_wholesale;
use App\Exports\ReportsExport;
use Illuminate\Support\Carbon;
use App\Http\Helpers\AppHelper;
use App\Exports\SubwholesaleExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
// use Maatwebsite\Excel\Facades\Excel;


class SubwholesaleController extends Controller
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
        $query = Sub_wholesale::with('user', 'customer')->orderBy('id', 'desc');
        // $data = $query->get();
        // dd($data);
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
            $query->whereBetween('date', [$startDate, $endDate]);
        }


        if ($request->has('full_name') && !empty($request->full_name)){
            $is_filter = true;
            $query->where('user_id', $request->full_name);
        }
        
        if ($request->ajax()) {
            $reports = $query->get();
            // dd($reports);
            return DataTables::of($reports)
                ->addColumn('photo', function ($data) {
                    $photoUrl = $data->user->photo ? asset('storage/' . $data->user->photo) : asset('images/avatar.png');
                    return '<img class="img-responsive center" style="height: 35px; width: 35px; object-fit: cover; border-radius: 50%;" src="' . $photoUrl . '" >';
                })
                 ->addColumn('photo_foc', function ($data) {
                    $photoUrl = $data->user->photo_foc ? asset('storage/' . $data->user->photo_foc) : asset('images/avatar.png');
                    return '<img class="img-responsive center" style="height: 35px; width: 35px; object-fit: cover; border-radius: 50%;" src="' . $photoUrl . '" >';
                })
                ->addColumn('id_card', function ($data) {
                    return $data->user->staff_id_card ?? 'N/A';
                })
                ->addColumn('name', function ($data) {
                    $user = optional($data->user);
                    return auth()->user()->user_lang == 'en'
                        ? ($user->getFullNameLatinAttribute() ?? 'N/A')
                        : ($user->getFullNameAttribute() ?? 'N/A');
                })

                ->addColumn('area', function ($data) {
                    return __(AppHelper::getAreaName($data->area_id));  
                })
                ->addColumn('outlet', function ($data) {
                    return __($data->outlet);
                })
                ->addColumn('customer', function ($data) {
                    return $data->customer ? $data->customer->name : 'N/A';

                })

                ->addColumn('customer_type', function ($data) {
                    // return __($data->customer_type);
                    return isset(AppHelper::CUSTOMER_TYPE[$data->customer_type]) ? __(AppHelper::CUSTOMER_TYPE[$data->customer_type]) : __('N/A');

                })

                ->addColumn('250ml', function ($data) {
                    return __($data->{"250_ml"}) ?? 'N/A';
                })
                ->addColumn('350ml', function ($data) {
                    return __($data->{"350_ml"}) ?? 'N/A';
                })
                ->addColumn('600ml', function ($data) {
                    return __($data->{"600_ml"}) ?? 'N/A';
                })
                ->addColumn('1500ml', function ($data) {
                    return __($data->{"1500_ml"}) ?? 'N/A';
                })

                  ->addColumn('phone', function ($data) {
                    return $data->customer ? $data->customer->phone : 'N/A';
                })

                ->addColumn('latitude', function ($data) {
                    return __($data->latitude) ?? 'N/A';
                })
                ->addColumn('longitude', function ($data) {
                    return __($data->longitude) ?? 'N/A';
                })

                ->addColumn('location', function ($data) {
                    return __($data->city . ',' . $data->country) ?? 'N/A';
                })
                ->addColumn('date', function ($data) {
                    return $data->date ? Carbon::parse($data->date)->format('d-M-Y h:i A') : 'N/A';
                })
                ->addColumn('other', function ($data) {
                    return __($data->other) ?? 'N/A';
                })
                ->addColumn('posm', function ($data) {
                    return isset(AppHelper::MATERIAL[$data->posm]) ? __(AppHelper::MATERIAL[$data->posm]) : __('N/A');
                })
                ->addColumn('qty', function ($data) {
                    return __($data->qty) ?? 'N/A';
                })
                ->addColumn('foc_qty', function ($data) {
                    return __($data->foc_qty) ?? 'N/A';
                })
                ->addColumn('action', function ($data) {
                    $editRoute = route('sub-wholesale.edit', $data->id);
                    $deleteRoute = route('sub-wholesale.destroy', $data->id);

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
        // return view('backend.sub-wholesale.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $report = null;
        // dd('HI Wholesale');
        $customer = null; // Assuming $customer is used for editing; null for create
        $customers = [];
        $report = null;
        $customerType = AppHelper::CUSTOMER_TYPE;
        // If there's old input or a pre-selected area, fetch customers
        $areaId = old('area', $customer->area_id ?? '');
        if ($areaId) {
            $customers = Customer::where('area_id', $areaId)->get(['id', 'name']);
        }
        return view('backend.sub-wholesale.add', compact('customer', 'customers','report','customerType'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        
        $rules = [
            'area' => 'required',
            'outlet' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'city' => 'required|string',
            'country' => 'required|string',
            'photo' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            'photo_base64' => 'nullable|string',
            'customer_id' => 'required',
            'customer_type' => 'required',
            'phone' => 'nullable',

            'foc_qty' => 'nullable|numeric',
            'photo_foc' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            'photo_base64_foc' => 'nullable|string',

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

        // Store report data
        Sub_wholesale::create([
            'user_id' => auth()->id(),
            'area' => $request->area,
            'outlet' => $request->outlet,
            'customer_id' => $request->customer_id,
            'customer_type' => $request->customer_type,
            'date' => Carbon::now('Asia/Phnom_Penh'),
            '250_ml' => $request['250_ml'],
            '350_ml' => $request['350_ml'],
            '600_ml' => $request['600_ml'],
            '1500_ml' => $request['1500_ml'],
            'phone' => $request['phone'],
            'other' => $request->other,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'city' => $request->city,
            'country' => $request->country,
            'qty' => $request->qty,
            'posm' => $request->posm,
            'photo' => $data['photo'],

            'photo_foc' => $data['photo_foc'],
            'foc_qty' => $request->foc_qty,
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
        $report = Sub_wholesale::with('user', 'customer')->find($id);

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
                'area' => AppHelper::getAreaName($report->area_id),

                'outlet' => $report->outlet,
                'customer' => $report->customer->name ?? 'N/A',
                // 'customer_type' => $report->customer_type,
                'customer_type' => isset(AppHelper::CUSTOMER_TYPE[$report->customer_type]) 
                    ? __(AppHelper::CUSTOMER_TYPE[$report->customer_type]) 
                    : __('N/A'),                
                'date' => $report->date,
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


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $report = Sub_wholesale::find($id);
        if (!$report) {
            return redirect()->route('sub-wholesale.index');
        }

        $customers = Customer::where('area_id', $report->area_id)->get(['id', 'name']);
        $customer = $report->customer; // The related customer for the report
        $customerType = AppHelper::CUSTOMER_TYPE;
        return view('backend.sub-wholesale.add', compact('report', 'customers', 'customer','customerType'));
    }

    public function update(Request $request, $id)
    {
        $report = Sub_wholesale::find($id);
        if (!$report) {
            return redirect()->route('report.index')->with('error', 'Report not found!');
        }
        // dd($report);
        // Validation rules
        $rules = [
            'area' => 'required',
            'outlet' => 'required',
            'customer_id' => 'required',
            'customer_type' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'city' => 'required|string',
            'country' => 'required|string',
            'photo' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            'photo_base64' => 'nullable|string',
            'phone' => 'nullable',

            'foc_qty' => 'nullable|numeric',
            'photo_foc' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            'photo_base64_foc' => 'nullable|string',

            'old_photo' => 'nullable|string',
            'old_photo_foc' => 'nullable|string',

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
            if ($request->hasFile('photo')) {

                if ($report->photo && Storage::exists($report->photo)) {
                    Storage::delete($report->photo);
                }

                $file = $request->file('photo');
                $fileName = time() . '_' . md5($file->getClientOriginalName()) . '.' . $file->extension();
                $filePath = 'uploads/' . $fileName;
                Storage::put($filePath, file_get_contents($file));
                $data['photo'] = $filePath;
            }else{

                $data['photo'] = $old_photo;

            }


            if($request->hasFile('photo_foc')){
                if ($report->photo_foc && Storage::exists($report->photo_foc)) {
                    Storage::delete($report->photo_foc);
                }

                $file = $request->file('photo_foc');
                $fileName = time() . '_' . md5($file->getClientOriginalName()) . '.' . $file->extension();
                $filePath = 'uploads/' . $fileName;
                Storage::put($filePath, file_get_contents($file));
                $data['photo_foc'] = $filePath;
            }else{
                $data['photo_foc'] = $old_photo_foc;
            }
                */



        // dd($request->photo_base64, $request->photo_base64_foc);
        

            // dd($data['photo'], $data['photo_foc'] );

            // dd($data['photo'], $data['photo_foc'] );


        /////////////////////////////////////////////////////


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
            }else{
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
            }else{
                $data['photo_foc'] = $old_photo_foc;
            }


        // dd($request->photo_base64, $request->photo_base64_foc);




            
        /////////////////////////////////////////////////////////



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
                'user_id' => auth()->id(),
                'area' => $request->area,
                'outlet' => $request->outlet,
                'customer_id' => $request->customer_id,
                'customer_type' => $request->customer_type,
                'date' => Carbon::now('Asia/Phnom_Penh'),
                '250_ml' => $request['250_ml'],
                '350_ml' => $request['350_ml'],
                '600_ml' => $request['600_ml'],
                '1500_ml' => $request['1500_ml'],
                'phone' => $request['phone'],
                'other' => $request->other,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'city' => $request->city,
                'country' => $request->country,
                'qty' => $request->qty,
                'posm' => $request->posm,
                'photo' => $data['photo'],

                'photo_foc' => $data['photo_foc'],
                'foc_qty' => $request->foc_qty,
            ]
        );
        

        return redirect()->route('sub-wholesale.index')->with('success', "Report has been updated!");
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

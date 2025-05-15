<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Asm_program;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Helpers\AppHelper;
// use App\Imports\Asm_programImport;
use App\Exports\AsmprogramExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class AsmprogramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('permission:view asm', ['only' => ['index']]);
        $this->middleware('permission:create asm', ['only' => ['create', 'store']]);
        $this->middleware('permission:update asm', ['only' => ['update', 'edit']]);
        $this->middleware('permission:delete asm', ['only' => ['destroy']]);
    }
    public function index(Request $request)
    {
        $query = Asm_program::orderBy('id', 'desc');
        // $data = $query->get();
        // dd($data);
        // $user = auth()->user();
        // if ($user->role_id === AppHelper::USER_MANAGER) {
        //     $query->whereHas('user', function ($q) use ($user) {
        //         $q->where('manager_id', $user->id);
        //     });
        // } elseif ($user->role_id !== AppHelper::USER_SUPER_ADMIN && $user->role_id !== AppHelper::USER_ADMIN) {
        //     $query->where('user_id', $user->id);
        // }

        $is_filter = false;
        $authUser = auth()->user();

        $userRole = User::where('role_id', AppHelper::USER_EMPLOYEE);
        // if ($authUser->role_id === AppHelper::USER_MANAGER) {
        //     $userRole->where('manager_id', $authUser->id);
        // }
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
                    $photoUrl = $data->photo ? asset('storage/' . $data->photo) : asset('images/avatar.png');
                    return '<img class="img-responsive center" style="height: 35px; width: 35px; object-fit: cover; border-radius: 50%;" src="' . $photoUrl . '" >';
                })
                // ->addColumn('id_card', function ($data) {
                //     return $data->user->staff_id_card ?? 'N/A';
                // })
                // ->addColumn('name', function ($data) {
                //     $user = optional($data->user);
                //     return auth()->user()->user_lang == 'en'
                //         ? ($user->getFullNameLatinAttribute() ?? 'N/A')
                //         : ($user->getFullNameAttribute() ?? 'N/A');
                // })

                ->addColumn('area', function ($data) {
                    return __($data->area);
                })
                ->addColumn('outlet', function ($data) {
                    return __($data->outlet);
                })
                ->addColumn('customer', function ($data) {
                    return __($data->customer);
                })

                ->addColumn('customer_type', function ($data) {
                    return __($data->customer_type);
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
                    return __($data->{"phone"}) ?? 'N/A';
                })

                ->addColumn('other', function ($data) {
                    return __($data->other) ?? 'N/A';
                })

                ->addColumn('latitude', function ($data) {
                    return __($data->latitude) ?? 'N/A';
                })
                ->addColumn('longitude', function ($data) {
                    return __($data->longitude) ?? 'N/A';
                })

                ->addColumn('city', function ($data) {
                    return __($data->city) ?? 'N/A';
                })
                ->addColumn('date', function ($data) {
                    return $data->date ? Carbon::parse($data->date)->format('d-M-Y h:i A') : 'N/A';
                })
               
                // ->addColumn('posm', function ($data) {
                //     return isset(AppHelper::MATERIAL[$data->posm]) ? __(AppHelper::MATERIAL[$data->posm]) : __('N/A');
                // })
                // ->addColumn('qty', function ($data) {
                //     return __($data->qty) ?? 'N/A';
                // })
                ->addColumn('action', function ($data) {
                    $editRoute = route('asm.edit', $data->id);
                    $deleteRoute = route('asm.destroy', $data->id);

                    $actionButtons = '
                    <span class="change-action-item">
                        <a href="javascript:void(0);" class="btn btn-primary btn-sm img-detail" data-id="' . $data->id . '" title="Show" data-bs-toggle="modal">
                            <i class="fa fa-fw fa-eye"></i>
                        </a>
                    </span>';

                    if (auth()->user()->can('update user')) {
                        $actionButtons .= '
                        <span class="change-action-item">
                            <a title="Take photo" href="' . $editRoute . '" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-camera"></i>
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
        // dd($query->get());
        return view('backend.asm.dev', compact('is_filter', 'full_name'));
        // dd('HI Wholesale');
        // return view('backend.retail.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $report = null;
        // dd('HI Wholesale');
        return view('backend.asm.add', compact('report'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $report = Asm_program::find($id);

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
        // $posm = isset(AppHelper::MATERIAL[$report->posm])
        //     ? __(AppHelper::MATERIAL[$report->posm])
        //     : 'Unknown';
        return response()->json([
            'report' => [
                'photo' => $report->photo ? asset('storage/' . $report->photo) : asset('images/avatar.png'),
                // 'employee_name' => $employee_name,
                // 'staff_id_card' => $user->staff_id_card ?? 'N/A',
                'area' => $report->area,
                'outlet' => $report->outlet,
                'customer' => $report->customer,
                'customer_type' => $report->customer_type,
                'date' => $report->date,
                'other' => $report->other ?? 'N/A',
                '250_ml' => $report->{'250_ml'},
                '350_ml' => $report->{'350_ml'},
                '600_ml' => $report->{'600_ml'},
                '1500_ml' => $report->{'1500_ml'},
                'phone' => $report->{'phone'},
                'city' => $report->city,
                // 'posm' => $posm,
                // 'qty' => $report->qty,
            ]
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $report = Asm_program::findOrFail($id);
        // dd('hi edit');
        return view('backend.asm.take-photo', compact('id', 'report'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd($request->photo_base64, $request->photo);

        $report = Asm_program::findOrFail($id);
        if (!$report) {
            return redirect()->route('asm.index')->with('error', 'Report not found!');
        }

        // Validation rules
        $rules = [
            // 'area' => 'required',
            // 'outlet' => 'required',
            // 'customer' => 'required',
            // 'customer_type' => 'required',
            // 'latitude' => 'required|numeric',
            // 'longitude' => 'required|numeric',
            // 'city' => 'required|string',
            // 'country' => 'required|string',
            'photo' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            'photo_base64' => 'nullable|string',
            // 'phone' => 'nullable',
        ];

        $this->validate($request, $rules);

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

        // Handle base64 image if provided
        if ($request->photo_base64) {
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
            $data['photo'] = $report->photo;
        }

        
        // Update report
        $report->update($data);
        // dd('Update');   
        return redirect()->route('asm.index')->with('success', "Report has been updated!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

        
    public function export()
    {
        // dd('HI Export');
        return Excel::download(new AsmprogramExport(), 'reports_asmprogram_' . now()->format('Y_m_d_His') . '.xlsx');
    }
}

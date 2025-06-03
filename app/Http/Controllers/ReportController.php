<?php

namespace App\Http\Controllers;

use App\Events\ReportRequest;
use App\Http\Helpers\AppHelper;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Exports\ReportsExport;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

use function Laravel\Prompts\error;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view report', ['only' => ['index']]);
        $this->middleware('permission:create report', ['only' => ['create', 'store']]);
        $this->middleware('permission:update report', ['only' => ['update', 'edit']]);
        $this->middleware('permission:delete report', ['only' => ['destroy']]);
    }
    public $indexof = 1;

    public function index(Request $request)
    {
        $query = Report::with(['user', 'customer'])->orderBy('id', 'desc');
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

        if ($request->has(['date1', 'date2']) && !empty($request->date1) && !empty($request->date2)) {
            $is_filter = true;
            $startDate = Carbon::parse($request->date1)->startOfDay();
            $endDate = Carbon::parse($request->date2)->endOfDay();
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        if ($request->has('full_name') && !empty($request->full_name)) {
            $is_filter = true;
            $query->where('user_id', $request->full_name);
        }

        if ($request->ajax()) {
            try {
                $reports = $query->get();

                return DataTables::of($reports)
                    ->addColumn('photo', function ($data) {
                        $photoUrl = $data->photo ? asset('storage/' . $data->photo) : asset('images/avatar.png');
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
                    ->addColumn('outlet_id', function ($data) {
                        return $data->customer->outlet ?? 'N/A';
                    })
                    ->addColumn('customer', function ($data) {
                        return $data->customer->name ?? 'N/A';
                    })
                    ->addColumn('customer_type', function ($data) {
                        return AppHelper::CUSTOMER_TYPE[$data->customer_type] ?? 'N/A';
                    })
                    ->addColumn('250ml', fn($data) => $data->{'250_ml'} ?? 'N/A')
                    ->addColumn('350ml', fn($data) => $data->{'350_ml'} ?? 'N/A')
                    ->addColumn('600ml', fn($data) => $data->{'600_ml'} ?? 'N/A')
                    ->addColumn('1500ml', fn($data) => $data->{'1500_ml'} ?? 'N/A')
                    ->addColumn('phone', fn($data) => $data->customer->phone ?? 'N/A')
                    ->addColumn('latitude', fn($data) => $data->latitude ?? 'N/A')
                    ->addColumn('longitude', fn($data) => $data->longitude ?? 'N/A')
                    ->addColumn('location', fn($data) => $data->city && $data->country ? "{$data->city}, {$data->country}" : 'N/A')
                    ->addColumn('date', fn($data) => $data->date ? Carbon::parse($data->date)->format('d-M-Y h:i A') : 'N/A')
                    ->addColumn('other', fn($data) => $data->other ?? 'N/A')
                    ->addColumn('posm', fn($data) => AppHelper::MATERIAL[$data->posm] ?? 'N/A')
                    ->addColumn('qty', fn($data) => $data->qty ?? 'N/A')
                    ->addColumn('action', function ($data) {
                        $editRoute = route('report.edit', $data->id);
                        $action = '<span class="change-action-item">
                            <a href="javascript:void(0);" class="btn btn-primary btn-sm img-detail" data-id="' . $data->id . '" title="Show" data-bs-toggle="modal">
                                <i class="fa fa-fw fa-eye"></i>
                            </a>
                        </span>';
                        if (auth()->user()->can('update report')) {
                            $action .= '<span class="change-action-item">
                                    <a title="Edit" href="' . $editRoute . '" class="btn btn-primary btn-sm">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </span>';
                        }
                        return $action;
                    })
                    ->rawColumns(['photo', 'action'])
                    ->make(true);
            } catch (\Exception $e) {
                Log::error('DataTables error in ReportController@index: ' . $e->getMessage(), [
                    'stack' => $e->getTraceAsString()
                ]);

                return response()->json(['error' => 'Server error. Check logs.'], 500);
            }
        }


        return view('backend.report.list', compact('is_filter', 'full_name'));
    }


    public function create()
    {
        $customer = null; // Assuming $customer is used for editing; null for create
        $customers = [];
        $report = null;
        $customerType = AppHelper::CUSTOMER_TYPE;
        // If there's old input or a pre-selected area, fetch customers
        $areaId = old('area', $customer->area_id ?? '');
        if ($areaId) {
            $customers = Customer::where('area_id', $areaId)->get(['id', 'name', 'outlet']);
        }

        return view('backend.report.add', compact('customer', 'customers', 'report', 'customerType'));
    }

    public function show($id)
    {
        $report = Report::with(['user', 'customer'])->find($id);

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
                'area' => AppHelper::getAreaName($report->area_id),
                'outlet' => $report->customer->outlet ?? 'N/A',
                'customer' => $report->customer->name ?? 'N/A',
                'customer_type' => $report->customer_type,
                'date' => $report->date,
                'other' => $report->other ?? 'N/A',
                '250_ml' => $report->{'250_ml'},
                '350_ml' => $report->{'350_ml'},
                '600_ml' => $report->{'600_ml'},
                '1500_ml' => $report->{'1500_ml'},
                'phone' => $report->customer->phone,
                'city' => $report->city,
                'posm' => $posm,
                'qty' => $report->qty,
            ]
        ]);
    }

    public function getCustomersByArea(Request $request)
    {
        $areaId = $request->query('area_id');
        $customers = Customer::where('area_id', $areaId)->get(['id', 'name', 'outlet']);

        // Extract unique outlet values
        $outlets = $customers->pluck('outlet')->unique()->filter()->map(function ($outlet, $index) {
            return ['id' => $index + 1, 'name' => $outlet];
        })->values();

        return response()->json([
            'customers' => $customers->map(function ($customer) {
                return ['id' => $customer->id, 'name' => $customer->name];
            }),
            'outlets' => $outlets
        ]);
    }

    public function store(Request $request)
    {
        $areaIds = [];
        foreach (AppHelper::getAreas() as $group) {
            $areaIds = array_merge($areaIds, array_keys($group));
        }

        $rules = [
            'area' => 'required|in:' . implode(',', $areaIds),
            'outlet_id' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'city' => 'required|string',
            'country' => 'required|string',
            'photo' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            'photo_base64' => 'nullable|string',
            'outlet_photo' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            'outlet_photo_base64' => 'nullable|string',
            'customer_id' => 'required|exists:customers,id',
            'customer_type' => 'required',
        ];

        // Make outlet_photo required if neither file nor base64 is provided
        if (!$request->hasFile('outlet_photo') && !$request->outlet_photo_base64) {
            $rules['outlet_photo'] = 'required|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50';
        }

        $this->validate($request, $rules);

        $data = $request->except(['photo', 'photo_base64', 'outlet_photo', 'outlet_photo_base64']);
        $data['photo'] = null;
        $data['outlet_photo'] = null;

        // Handle main photo file upload if exists
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = time() . '_' . md5($file->getClientOriginalName()) . '.' . $file->extension();
            $filePath = 'uploads/' . $fileName;
            Storage::put($filePath, file_get_contents($file));
            $data['photo'] = $filePath;
        }

        // Handle main base64 image if provided
        if ($request->photo_base64) {
            $image = str_replace('data:image/png;base64,', '', $request->photo_base64);
            $image = str_replace(' ', '+', $image);
            $imageData = base64_decode($image);

            $fileName = 'uploads/' . time() . '_' . Str::random(10) . '.png';
            Storage::put($fileName, $imageData);

            $data['photo'] = $fileName;
        }

        // Handle outlet photo file upload if exists
        if ($request->hasFile('outlet_photo')) {
            $file = $request->file('outlet_photo');
            $fileName = 'outlet_' . time() . '_' . md5($file->getClientOriginalName()) . '.' . $file->extension();
            $filePath = 'uploads/' . $fileName;
            Storage::put($filePath, file_get_contents($file));
            $data['outlet_photo'] = $filePath;
        }

        // Handle outlet base64 image if provided
        if ($request->outlet_photo_base64) {
            $image = str_replace('data:image/png;base64,', '', $request->outlet_photo_base64);
            $image = str_replace(' ', '+', $image);
            $imageData = base64_decode($image);

            $fileName = 'uploads/outlet_' . time() . '_' . Str::random(10) . '.png';
            Storage::put($fileName, $imageData);

            $data['outlet_photo'] = $fileName;
        }

        // Store report data
        Report::create([
            'user_id' => auth()->id(),
            'area_id' => $request->area,
            'outlet_id' => $request->outlet_id,
            'customer_id' => $request->customer_id,
            'customer_type' => $request->customer_type,
            'date' => Carbon::now('Asia/Phnom_Penh'),
            '250_ml' => $request['250_ml'],
            '350_ml' => $request['350_ml'],
            '600_ml' => $request['600_ml'],
            '1500_ml' => $request['1500_ml'],
            'other' => $request->other,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'city' => $request->city,
            'country' => $request->country,
            'qty' => $request->qty,
            'posm' => $request->posm,
            'photo' => $data['photo'],
            'outlet_photo' => $data['outlet_photo'],
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
            __("A new Request has been created by ") . auth()->user()->family_name . ' ' . auth()->user()->name,
            $notificationUsers
        ));

        return redirect()->route('report.index')->with('success', "Report has been created!");
    }


    public function edit($id)
    {
        $report = Report::with('customer')->find($id);
        if (!$report) {
            return redirect()->route('report.index')->with('error', 'Report not found.');
        }

        // Fetch customers for the report's area
        $customers = Customer::where('area_id', $report->area_id)->get(['id', 'name']);
        $customer = $report->customer; // The related customer for the report
        $customerType = AppHelper::CUSTOMER_TYPE;
        return view('backend.report.add', compact('report', 'customers', 'customer', 'customerType'));
    }


    public function update(Request $request, $id)
    {
        $report = Report::find($id);
        if (!$report) {
            return redirect()->route('report.index')->with('error', 'Report not found!');
        }

        // Get all valid area IDs (numeric keys)
        $areaIds = [];
        foreach (AppHelper::getAreas() as $group) {
            $areaIds = array_merge($areaIds, array_keys($group));
        }

        // Validation rules
        $rules = [
            'area' => 'required|in:' . implode(',', $areaIds),
            'outlet_id' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'city' => 'required|string',
            'country' => 'required|string',
            'photo' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            'photo_base64' => 'nullable|string',
            'outlet_photo' => 'nullable|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50',
            'outlet_photo_base64' => 'nullable|string',
            'customer_id' => 'required|exists:customers,id',
            'customer_type' => 'required',
        ];

        // Conditionally make outlet_photo required only if it's missing in the request AND in the existing record
        if (
            !$request->hasFile('outlet_photo') &&
            !$request->outlet_photo_base64 &&
            empty($report->outlet_photo)
        ) {
            $rules['outlet_photo'] = 'required|mimes:jpeg,jpg,png|max:10000|dimensions:min_width=50,min_height=50';
        }


        $this->validate($request, $rules);
        $data = [
            'area_id' => $request->area,
            'outlet_id' => $request->outlet_id,
            'customer_id' => $request->customer_id,
            'customer_type' => $request->customer_type,
            'date' => Carbon::now('Asia/Phnom_Penh'),
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
            'photo' => $report->photo, // Preserve existing photo by default
            'outlet_photo' => $report->outlet_photo,
        ];

        // Handle new photo upload via file input
        if ($request->hasFile('photo')) {
            if ($report->photo && Storage::exists($report->photo)) {
                Storage::delete($report->photo);
            }

            $file = $request->file('photo');
            $fileName = time() . '_' . md5($file->getClientOriginalName()) . '.' . $file->extension();
            $filePath = 'Uploads/' . $fileName;
            Storage::put($filePath, file_get_contents($file));
            $data['photo'] = $filePath;
        }
        // Handle base64 image if provided
        elseif ($request->filled('photo_base64') && preg_match('/^data:image\/(png|jpeg|jpg);base64,/', $request->photo_base64)) {
            if ($report->photo && Storage::exists($report->photo)) {
                Storage::delete($report->photo);
            }

            $imageData = base64_decode(preg_replace('/^data:image\/(png|jpeg|jpg);base64,/', '', $request->photo_base64));
            $fileName = time() . '_' . md5(uniqid()) . '.png';
            $filePath = 'Uploads/' . $fileName;
            Storage::put($filePath, $imageData);
            $data['photo'] = $filePath;
        }
        // Handle existing photo if no new photo is provided
        elseif ($request->filled('oldphoto') && Storage::exists($request->oldphoto)) {
            $data['photo'] = $request->oldphoto;
        }
        // Handle new photo upload via file input
        if ($request->hasFile('outlet_photo')) {
            if ($report->outlet_photo && Storage::exists($report->outlet_photo)) {
                Storage::delete($report->outlet_photo);
            }

            $file = $request->file('outlet_photo');
            $fileName = time() . '_' . md5($file->getClientOriginalName()) . '.' . $file->extension();
            $filePath = 'Uploads/' . $fileName;
            Storage::put($filePath, file_get_contents($file));
            $data['outlet_photo'] = $filePath;
        }
        // Handle base64 image if provided
        elseif ($request->filled('outlet_photo_base64') && preg_match('/^data:image\/(png|jpeg|jpg);base64,/', $request->outlet_photo_base64)) {
            if ($report->outlet_photo && Storage::exists($report->outlet_photo)) {
                Storage::delete($report->outlet_photo);
            }

            $imageData = base64_decode(preg_replace('/^data:image\/(png|jpeg|jpg);base64,/', '', $request->outlet_photo_base64));
            $fileName = time() . '_' . md5(uniqid()) . '.png';
            $filePath = 'Uploads/' . $fileName;
            Storage::put($filePath, $imageData);
            $data['outlet_photo'] = $filePath;
        } elseif ($request->filled('outlet_photo_base64') && Storage::exists($request->old_outlet_photo)) {
            $data['photo'] = $request->old_outlet_photo;
        }

        // Update report
        $report->update($data);

        return redirect()->route('report.index')->with('success', 'Report has been updated!');
    }





    public function destroy($id)
    {
        $report = Report::find($id);
        if ($report) {
            $report->delete();
            return redirect()->back()->with('success', "Report has been deleted!");
        }
        return redirect()->back()->with('error', "Report not found!");
    }

    public function getReports()
    {
        $user = Auth::user();

        $isAdmin = in_array($user->role_id, [AppHelper::USER_SUPER_ADMIN, AppHelper::USER_ADMIN]);
        $isManager = $user->role_id == AppHelper::USER_MANAGER;

        $query = Report::with('user')->whereNull('deleted_at')->where('is_seen', false);

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
                'area' => AppHelper::getAreaName($report->area_id) ?? 'Unknown',
                'photo' => $report->user->photo ? asset('storage/' . $report->user->photo) : asset('images/avatar.png')
            ];
        });

        return response()->json($reports);
    }
    public function export()
    {
        return Excel::download(new ReportsExport(), 'reports_' . now()->format('Y_m_d_His') . '.xlsx');
    }

    public function markAsSeen()
    {
        $user = auth()->user();

        if (in_array($user->role_id, [AppHelper::USER_SUPER_ADMIN, AppHelper::USER_ADMIN])) {
            Report::whereNull('deleted_at')->update(['is_seen' => true]);
        } elseif ($user->role_id == AppHelper::USER_MANAGER) {
            Report::whereNull('deleted_at')
                ->whereIn('user_id', User::where('manager_id', $user->id)->pluck('id'))
                ->update(['is_seen' => true]);
        }

        return response()->json(['success' => true]);
    }

    // public function getCustomerByOutlet(Request $request)
    // {
    //     $outlet = $request->query('outlet');

    //     $report = Report::where('outlet', $outlet)->orderBy('created_at', 'desc')->first();

    //     if ($report) {
    //         return response()->json([
    //             'success' => true,
    //             'area' => $report->area ?? '',
    //             'customer' => $report->customer ?? '',
    //             'customer_type' => $report->customer_type ?? '',
    //             '250_ml' => $report->getAttribute('250_ml') ?? '',
    //             '350_ml' => $report->getAttribute('350_ml') ?? '',
    //             '600_ml' => $report->getAttribute('600_ml') ?? '',
    //             '1500_ml' => $report->getAttribute('1500_ml') ?? '',
    //             'phone' => $report->phone ?? '',
    //             'other' => $report->other ?? '',
    //         ]);
    //     }

    //     return response()->json([
    //         'success' => false,
    //         'message' => 'No matching outlet found.',
    //     ]);
    // }

}

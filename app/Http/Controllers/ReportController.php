<?php

namespace App\Http\Controllers;

use App\Events\ReportRequest;
use App\Http\Helpers\AppHelper;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{
    public $indexof = 1;

    public function index(Request $request)
    {
        $query = Report::with('user')->orderBy('id', 'desc');
        if (auth()->user()->role_id !== AppHelper::USER_SUPER_ADMIN && auth()->user()->role_id !== AppHelper::USER_ADMIN) {
            $query->where('user_id', auth()->id());
        }
        $is_filter = false;
        if ($request->has('date') && !empty($request->date)) {
            $is_filter = true;
            $formattedDate = Carbon::parse($request->date)->format('Y-m-d');
            $query->whereRaw("DATE(date) = ?", [$formattedDate]);
        }
        
        if ($request->ajax()) {
            $reports = $query->get();

            return DataTables::of($reports)
                ->addColumn('photo', function ($data) {
                    $photoUrl = $data->user->photo ? asset('storage/' . $data->user->photo) : asset('images/avatar.png');
                    return '<img class="img-responsive center" style="height: 35px; width: 35px; object-fit: cover; border-radius: 50%;" src="' . $photoUrl . '" >';
                })
                ->addColumn('id_card', function ($data) {
                    return $data->user->staff_id_card ?? 'N/A';
                })
                ->addColumn('name', function ($data) {
                    return $data->user->family_name . ' ' . $data->user->name ?? 'N/A';
                })
                ->addColumn('area', function ($data) {
                    return __($data->area);
                })
                ->addColumn('outlet', function ($data) {
                    return __($data->outlet);
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
                ->addColumn('location', function ($data) {
                    return __($data->city .',' .$data->country) ?? 'N/A';
                })
                ->addColumn('date', function ($data) {
                    return $data->date ? Carbon::parse($data->date)->format('d-M-Y h:i A') : 'N/A';
                })
                ->addColumn('other', function ($data) {
                    return __($data->other) ?? 'N/A';
                })
                ->addColumn('action', function ($data) {
                    $editRoute = route('report.edit', $data->id);
                    $deleteRoute = route('report.destroy', $data->id);

                    return '<span class="change-action-item">
                            <a title="Edit" href="' . $editRoute . '" class="btn btn-primary btn-sm">
                                <i class="fa fa-edit"></i>
                            </a>
                        </span>
                        <span class="change-action-item">
                            <a href="' . $deleteRoute . '" class="btn btn-danger btn-sm delete" title="Delete">
                                <i class="fa fa-fw fa-trash"></i>
                            </a>
                        </span>';
                })
                ->rawColumns(['photo', 'action'])
                ->make(true);
        }

        return view('backend.report.list',compact('is_filter'));
    }

    public function create()
    {
        $report = null;
        return view('backend.report.add', compact('report'));
    }

    public function store(Request $request)
    {
        $rules = [
            'area' => 'required',
            'outlet' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'city' => 'required|string',
            'country' => 'required|string',
        ];
        $this->validate($request, $rules);
        // dd($request->all());
        Report::create([
            'user_id' => auth()->id(),
            'area' => $request->area,
            'outlet' => $request->outlet,
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
        ]);

        $allowedUserTypes = [
            AppHelper::USER_SUPER_ADMIN,
            AppHelper::USER_ADMIN
        ];

        // Check if current user's type is in allowed types before firing event
        if (!in_array(auth()->user()->role_id, $allowedUserTypes)) {
            event(new ReportRequest(__("A new Request has been created by ") . auth()->user()->family_name . ' ' . auth()->user()->name));
        }
        

        return redirect()->route('report.index')->with('success', "Report has been created!");
    }

    public function edit($id)
    {
        $report = Report::find($id);
        if (!$report) {
            return redirect()->route('report.index');
        }
        return view('backend.report.add', compact('report'));
    }

    public function update(Request $request, $id)
    {
        $report = Report::find($id);
        if (!$report) {
            return redirect()->route('report.index');
        }

        $rules = [
            'area' => 'required',
            'outlet' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'city' => 'required|string',
            'country' => 'required|string',
        ];
        $this->validate($request, $rules);

        $report->update([
            'area' => $request->area,
            'outlet' => $request->outlet,
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
        ]);

        return redirect()->route('report.index')->with('success', "Report has been updated!");
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
}

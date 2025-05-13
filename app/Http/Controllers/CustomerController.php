<?php

namespace App\Http\Controllers;

use App\Http\Helpers\AppHelper;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view customer', ['only' => ['index']]);
        $this->middleware('permission:create customer', ['only' => ['create', 'store']]);
        $this->middleware('permission:update customer', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete customer', ['only' => ['destroy']]);
    }
    public function index(Request $request)
{
    if ($request->ajax()) {
        $customers = Customer::query();
        return DataTables::of($customers)
            ->addIndexColumn()
            ->addColumn('area_id', fn($customer) => AppHelper::getAreaName($customer->area_id))
            ->addColumn('outlet', fn($customer) => $customer->outlet)
            ->addColumn('action', function ($customer) {
                $button = '<div class="change-action-item">';
                $actions = false;
                if (auth()->user()->can('update customer')) {
                    $button .= '<a title="Edit" href="' . route('customer.edit', $customer->id) . '" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>';
                    $actions = true;
                }
                if (auth()->user()->can('delete customer')) {
                    $button .= '<a href="' . route('customer.destroy', $customer->id) . '" class="btn btn-danger btn-sm delete" title="Delete"><i class="fa fa-fw fa-trash"></i></a>';
                    $actions = true;
                }
                if (!$actions) {
                    $button .= '<span style="font-weight:bold; color:red;">No Action</span>';
                }
                $button .= '</div>';
                return $button;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    return view('backend.customer.list');
}


    public function create()
    {
        $customer = null;
        return view('backend.customer.add', compact('customer'));
    }

    public function store(Request $request)
    {
        // Get all valid area IDs (numeric keys)
        $areaIds = [];
        foreach (AppHelper::getAreas() as $group) {
            $areaIds = array_merge($areaIds, array_keys($group));
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required',
            'phone' => 'required',
            'area' => 'required|in:' . implode(',', $areaIds),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            Customer::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'area_id' => $request->area,
                'outlet' => $request->outlet,
            ]);

            if ($request->has('saveandcontinue')) {
                return redirect()->route('customer.create')->with('success', 'Customer created successfully.');
            }
            return redirect()->route('customer.index')->with('success', 'Customer created successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to create customer: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Customer $customer)
    {
        return view('backend.customer.add', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        // Get all valid area IDs (numeric keys)
        $areaIds = [];
        foreach (AppHelper::getAreas() as $group) {
            $areaIds = array_merge($areaIds, array_keys($group));
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required',
            'outlet' => 'required',
            'area' => 'required|in:' . implode(',', $areaIds),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $customer->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'area_id' => $request->area,
                'outlet' => $request->outlet,
            ]);
            return redirect()->route('customer.index')->with('success', 'Customer updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update customer: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return redirect()->back()->with('success', "Customer has been deleted!");
    }
}
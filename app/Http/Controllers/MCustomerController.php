<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\MCustomer;
use Illuminate\Http\Request;
use App\Http\Helpers\AppHelper;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class MCustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('type.permission:view province customer', ['only' => ['index']]);
        $this->middleware('type.permission:create province customer', ['only' => ['create', 'store']]);
        $this->middleware('type.permission:update province customer', ['only' => ['edit', 'update']]);
        $this->middleware('type.permission:delete province customer', ['only' => ['destroy']]);
    }
    public function index(Request $request)
    {
        $data['authUser'] = auth()->user();
        $data['customers'] = MCustomer::with('user')->orderBy('id', 'desc')->get();
        // dd($data['customers']->user->family_name);

        /*
            if ($request->ajax()) {
                // $customers = MCustomer::select(['id', 'name', 'area_id', 'phone', 'outlet'])->with('user');
                    return DataTables::of($customers)
                        ->addIndexColumn()
                        ->addCulumn('staff_id', fn($customer) => $customer->user->staff_id_card ?? 'N/A')
                        ->addColumn('name', fn($customer) => $authUser->user_lang === 'en' ? $customer->user->family_name . ' ' . $customer->user->name : $customer->user->family_name_latin . ' ' . $customer->user->name_latin)


                        ->addColumn('area_id', fn($customer) => AppHelper::getAreaName($customer->area_id))
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
                        ->addColumn('outlet', fn($customer) => $customer->outlet)

                        ->rawColumns(['action'])
                        ->make(true);



            }
         */


        return view('backend.m-customer.list', $data);
    }


    public function create()
    {
        $customer = null;
        return view('backend.m-customer.add', compact('customer'));
    }

    public function store(Request $request)
    {
        // dd('Hi');
        // Get all valid area IDs (numeric keys)
        $areaIds = [];
        foreach (AppHelper::getAreas() as $group) {
            $areaIds = array_merge($areaIds, array_keys($group));
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable',
            'area' => 'required|in:' . implode(',', $areaIds),
            'outlet' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            MCustomer::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'area_id' => $request->area,
                'outlet' => $request->outlet,
                'created_by' => auth()->user()->id, // Store the ID of the user creating the customer
            ]);

            if ($request->has('saveandcontinue')) {
                return redirect()->route('mcustomer.create')->with('success', 'Customer created successfully.');
            }
            return redirect()->route('mcustomer.index')->with('success', 'Customer created successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to create customer: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $customer = MCustomer::findOrFail($id);
        // dd($customer);
        // Check if the customer exists
        if (!$customer) {
            return redirect()->back()->with('error', 'Customer not found.');
        }
        return view('backend.m-customer.add', compact('customer'));
    }


    public function update(Request $request, $id)
    {
        // Get all valid area IDs (numeric keys)
        $customer = MCustomer::findOrFail($id);
        // dd($customer);

        $areaIds = [];
        foreach (AppHelper::getAreas() as $group) {
            $areaIds = array_merge($areaIds, array_keys($group));
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|nullable',
            'area' => 'required|in:' . implode(',', $areaIds),
            'outlet' => 'required|string|max:255',
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
            return redirect()->route('mcustomer.index')->with('success', 'Customer updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update customer: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $customer = MCustomer::findOrFail($id);
        $customer->delete();
        return redirect()->back()->with('success', "Customer has been deleted!");
    }
}

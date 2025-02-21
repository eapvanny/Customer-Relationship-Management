<?php

namespace App\Http\Controllers;

// use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view permission', ['only' => ['index']]);
        $this->middleware('permission:create permission', ['only' => ['create', 'store']]);
        $this->middleware('permission:update permission', ['only' => ['update', 'edit']]);
        $this->middleware('permission:delete permission', ['only' => ['destroy']]);
    }
    public $indexof = 1;
    public function index(Request $request)
    {
        $permission = Permission::query();
        if ($request->ajax()) {
            return DataTables::of($permission)
            ->addIndexColumn()
                ->filter(function ($query) use ($request) {
                    if ($search = $request->input('search.value')) {
                        $query->where(function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%{$search}%");
                        });
                    }
                })
                ->addColumn('action', function ($data) {
                    $button = '<div class="change-action-item">';
                    $button .= '<a title="Edit"  href="' . route('permission.edit', $data->id) . '"  class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>';
                    $button .= '<a  href="' . route('permission.destroy', $data->id) . '"  class="btn btn-danger btn-sm delete" title="Delete"><i class="fa fa-fw fa-trash"></i></a>';
                    $button .= '</div>';
                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.permission.list');
    }

    public function create()
    {
        $permission = null;
        return view('backend.permission.add', compact('permission'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions|min:3',
        ]);

        if ($validator->passes()) {
            Permission::create([
                'name' => $request->name,
            ]);
            $permission = Permission::latest()->first()->id;

            if ($request->has('saveandcontinue')) {
                return redirect()->route('permission.create', $permission)->with('success', 'Permission added successfully!');
            } else {
                return redirect()->route('permission.index')->with('success', "Permission has been created!");
            }
        } else {
            return redirect()->route('permission.create')->withInput()->withErrors($validator);
        }
    }


    public function edit($id)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return redirect()->route('permission.index');
        }
        return view(
            'backend.permission.add',
            compact(
                'permission',
            )
        );
    }
    public function update(Request $request, $id)
    {
        $permission = Permission::find($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|unique:permissions,name,' . $id . ',id',
        ]);
        if ($validator->passes()) {
            $permission->name = $request->name;
            $permission->save();
            return redirect()->route('permission.index')->with('success', "Permission has been updated!");
        } else {
            return redirect()->back()->withInput()->withErrors($validator);
        }
    }
    public function destroy($id)
    {
        $permission = Permission::find($id);
        $permission->delete();
        return redirect()->back()->with('success', "Permission has been deleted!");
    }
}

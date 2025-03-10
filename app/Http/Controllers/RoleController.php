<?php

namespace App\Http\Controllers;

// use App\Models\Permission;
// use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view role', ['only' => ['index']]);
        $this->middleware('permission:create role', ['only' => ['create', 'store']]);
        $this->middleware('permission:update role', ['only' => ['update', 'edit']]);
        $this->middleware('permission:delete role', ['only' => ['destroy']]);
    }
    public $indexof = 1;
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $roles = Role::whereHas('permissions')->with('permissions');
            return DataTables::of($roles)
                ->addIndexColumn()
                ->filter(function ($query) use ($request) {
                    if ($search = $request->input('search.value')) {
                        $query->where(function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%{$search}%");
                        });
                    }
                })
                ->addColumn('permission', function ($data) {
                    return $data->permissions->pluck('name')->implode(', ');
                })
                ->addColumn('action', function ($data) {
                    $button = '<div class="change-action-item">';
                    $actions = false;

                    if (auth()->user()->can('update role')) {
                        $button .= '<a title="Edit" href="' . route('role.edit', $data->id) . '" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>';
                        $actions = true;
                    }
                    // if (auth()->user()->can('delete role')) {
                    //     $button .= '<a href="' . route('role.destroy', $data->id) . '" class="btn btn-danger btn-sm delete" title="Delete"><i class="fa fa-fw fa-trash"></i></a>';
                    //     $actions = true;
                    // }
                    if (!$actions) {
                        $button .= '<span style="font-weight:bold; color:red;">No Action</span>';
                    }
                    $button .= '</div>';
                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.role.list');
    }

    public function create()
    {
        $role = null;
        $all_role = Role::pluck('name', 'id'); // Fetch list of roles
        $permissions = Permission::get();
        $hasPermission = [];
        return view('backend.role.add', compact('role', 'permissions', 'hasPermission', 'all_role'));
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required', // Added exists validation
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Remove dd() in production code
        // dd($request->role_id);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        // Get the actual Role model instance
        $role = Role::find($request->role_id); // Assuming Role is your model class

        if (!$role) {
            return redirect()->back()->with('error', 'Role not found');
        }

        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->syncPermissions($permissions);
        }

        return redirect()->route('role.index')->with('success', "Role permissions have been updated!");
    }




    public function edit($id)
    {
        $role = Role::find($id);
        // dd($role);
        if (!$role) {
            return redirect()->route('ticket.index')->with('error', 'Role not found.');
        }

        $all_role = Role::pluck('name', 'id'); // Fetch list of roles
        $hasPermission = $role->permissions->pluck('name')->toArray();
        $permissions = Permission::all(); // Fetch full permission objects

        return view('backend.role.add', compact('role', 'permissions', 'hasPermission', 'all_role'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return redirect()->route('role.index')->with('error', 'Role not found.');
        }

        // Conditionally require role_id if it is not set
        $rules = [
            'role_id' => $role ? 'nullable|exists:roles,id' : 'required|exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ];

        $this->validate($request, $rules);

        $permissionIds = $request->input('permissions', []);
        $permissions = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();

        $role->syncPermissions($permissions);

        return redirect()->route('role.index')->with('success', "Role permissions have been updated!");
    }



    // public function destroy($id)
    // {
    //     $ticket = Role::find($id);
    //     $ticket->delete();
    //     return redirect()->back()->with('success', "Role has been deleted!");
    // }
}

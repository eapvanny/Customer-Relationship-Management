<?php

namespace App\Http\Controllers;

use App\Http\Helpers\AppHelper;
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
        $this->middleware('permission:update role', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete role', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of roles with their permissions.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $roles = Role::has('permissions')->with(['permissions' => fn ($query) => $query->withPivot('type')])->get();

            $transformedRoles = $roles->flatMap(function ($role) {
                $types = $role->permissions->pluck('pivot.type')->unique();

                return $types->map(function ($type) use ($role) {
                    return [
                        'role' => $role,
                        'type_value' => $type,
                        'type_label' => AppHelper::USER_TYPE[$type] ?? 'Unknown',
                        'permissions' => $role->permissions->where('pivot.type', $type),
                    ];
                });
            });

            return DataTables::of($transformedRoles)
                ->addIndexColumn()
                ->addColumn('name', fn ($data) => $data['role']->name)
                ->addColumn('permission', fn ($data) => $data['permissions']->pluck('name')->implode(', '))
                ->addColumn('type', fn ($data) => $data['type_label'])
                ->addColumn('action', function ($data) {
                    $role = $data['role'];
                    $type = $data['type_value'];
                    $actions = [];

                    if (auth()->user()->hasPermissionTo('update role', null, AppHelper::ALL)) {
                        $editUrl = route('role.edit', $role->id) . ($type ? '?type=' . $type : '');
                        $actions[] = '<a title="Edit" href="' . $editUrl . '" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>';
                    }

                    // if (auth()->user()->hasPermissionTo('delete role', null, AppHelper::ALL)) {
                    //     $actions[] = '<a title="Delete" href="' . route('role.destroy', $role->id) . '" class="btn btn-danger btn-sm delete"><i class="fa fa-trash"></i></a>';
                    // }

                    return '<div class="change-action-item">' . ($actions ? implode(' ', $actions) : '<span style="font-weight:bold; color:red;">No Action</span>') . '</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('backend.role.list');
    }

    /**
     * Show the form for creating a new role permission assignment.
     */
public function create(Request $request)
    {
        $selectedType = $request->input('type', AppHelper::ALL); // Default to ALL if no type is selected
        $permissions = Permission::all(); // Fetch all permissions

        return view('backend.role.add', [
            'role' => null,
            'typeGet' => $selectedType,
            'all_role' => Role::pluck('name', 'id'),
            'permissions' => $permissions,
            'hasPermission' => [],
            'type' => AppHelper::USER_TYPE,
        ]);
    }
    /**
     * Store a newly created role permission assignment.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'type' => 'required|in:1,2,3',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $role = Role::findOrFail($request->role_id);

        if ($role->permissions()->wherePivot('type', $request->type)->exists()) {
            return redirect()->back()->withInput()->with('error', "Role '{$role->name}' with type '" . (AppHelper::USER_TYPE[$request->type] ?? 'Unknown') . "' already exists.");
        }

        $permissions = $request->input('permissions', []);
        if ($permissions) {
            $role->permissions()->attach($permissions, ['type' => $request->type]);
        }

        return redirect()->route('role.index')->with('success', 'Role permissions added successfully.');
    }

    /**
     * Show the form for editing a role's permissions.
     */
    public function edit($id, Request $request)
    {
        $role = Role::findOrFail($id);
        $typeGet = $request->query('type');

        if ($typeGet && !in_array($typeGet, ['1', '2', '3'])) {
            return redirect()->route('role.index')->with('error', 'Invalid role type.');
        }

        $permissions = match ((int) $typeGet) {
            AppHelper::ALL => Permission::where('type', AppHelper::ALL)->get(),
            AppHelper::SALE => Permission::where('type', AppHelper::SALE)->get(),
            AppHelper::SE => Permission::where('type', AppHelper::SE)->get(),
            default => Permission::where('type', AppHelper::ALL)->get(), // Default to ALL for consistency
        };

        return view('backend.role.add', [
            'role' => $role,
            'typeGet' => $typeGet,
            'all_role' => Role::pluck('name', 'id'),
            'permissions' => $permissions,
            'hasPermission' => $role->permissions()->pluck('permissions.id')->toArray(),
            'type' => AppHelper::USER_TYPE,
        ]);
    }

    /**
     * Update the specified role's permissions.
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'type' => 'required|in:1,2,3',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $type = $request->input('type');
        $newPermissions = $request->input('permissions', []);
        $currentPermissions = $role->permissions()->wherePivot('type', $type)->pluck('permissions.id')->toArray();

        $role->permissions()->wherePivot('type', $type)->detach(array_diff($currentPermissions, $newPermissions));
        $role->permissions()->attach(array_diff($newPermissions, $currentPermissions), ['type' => $type]);

        return redirect()->route('role.index')->with('success', 'Role permissions updated successfully.');
    }

    /**
     * Remove the specified role.
     */
    // public function destroy($id)
    // {
    //     Role::findOrFail($id)->delete();
    //     return redirect()->back()->with('success', 'Role deleted successfully.');
    // }
}
<?php

namespace App\Http\Controllers;

use App\Http\Helpers\AppHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('type.permission:view permission', ['only' => ['index']]);
        $this->middleware('type.permission:create permission', ['only' => ['create', 'store']]);
        $this->middleware('type.permission:update permission', ['only' => ['edit', 'update']]);
        $this->middleware('type.permission:delete permission', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of permissions.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(Permission::query())
                ->addIndexColumn()
                ->filterColumn('name', fn ($query, $keyword) => $query->where('name', 'LIKE', "%{$keyword}%"))
                ->addColumn('name', fn ($permission) => $permission->name)
                ->addColumn('type', fn ($permission) => AppHelper::USER_TYPE[$permission->type] ?? 'N/A')
                ->addColumn('action', fn ($permission) => sprintf(
                    '<div class="change-action-item">
                        <a title="Edit" href="%s" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>
                        <a title="Delete" href="%s" class="btn btn-danger btn-sm delete"><i class="fa fa-trash"></i></a>
                    </div>',
                    route('permission.edit', $permission->id),
                    route('permission.destroy', $permission->id)
                ))
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('backend.permission.list');
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create()
    {
        return view('backend.permission.add', [
            'permission' => null,
            'type' => AppHelper::USER_TYPE,
        ]);
    }

    /**
     * Store a newly created permission.
     */
    public function store(Request $request)
    {
        $guardName = config('auth.defaults.guard');
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'min:3',
                function ($attribute, $value, $fail) use ($request, $guardName) {
                    if (Permission::where(['name' => $value, 'guard_name' => $guardName, 'type' => $request->type])->exists()) {
                        $fail("The permission '{$value}' already exists for guard '{$guardName}' and type '{$request->type}'.");
                    }
                },
            ],
            'type' => 'required|in:1,2,3',
        ]);

        if ($validator->fails()) {
            return redirect()->route('permission.create')->withInput()->withErrors($validator);
        }

        Permission::create([
            'name' => $request->name,
            'type' => $request->type,
            'guard_name' => $guardName,
        ]);

        $redirect = $request->has('saveandcontinue') ? route('permission.create') : route('permission.index');
        return redirect($redirect)->with('success', 'Permission created successfully.');
    }

    /**
     * Show the form for editing a permission.
     */
    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        return view('backend.permission.add', [
            'permission' => $permission,
            'type' => AppHelper::USER_TYPE,
        ]);
    }

    /**
     * Update the specified permission.
     */
    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);
        $guardName = $permission->guard_name;

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'min:3',
                function ($attribute, $value, $fail) use ($request, $guardName, $id) {
                    if (Permission::where(['name' => $value, 'guard_name' => $guardName, 'type' => $request->type])->where('id', '!=', $id)->exists()) {
                        $fail("The permission '{$value}' already exists for guard '{$guardName}' and type '{$request->type}'.");
                    }
                },
            ],
            'type' => 'required|in:1,2,3',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $permission->update([
            'name' => $request->name,
            'type' => $request->type,
        ]);

        return redirect()->route('permission.index')->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified permission.
     */
    public function destroy($id)
    {
        Permission::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Permission deleted successfully.');
    }
}
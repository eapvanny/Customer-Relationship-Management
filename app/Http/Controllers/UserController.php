<?php

namespace App\Http\Controllers;

use App\Http\Helpers\AppHelper;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('type.permission:view user', ['only' => ['index']]);
        $this->middleware('type.permission:create user', ['only' => ['create', 'store']]);
        $this->middleware('type.permission:update user', ['only' => ['update', 'edit']]);
        $this->middleware('type.permission:delete user', ['only' => ['destroy']]);
    }
    public $indexof = 1;
    public function index(Request $request)
    {
        $is_filter = false;
        $query = User::with(['role', 'manager']);

        $loggedInUser = auth()->user();
        $loggedInUserRole = $loggedInUser->role_id;
        $loggedInUserId = $loggedInUser->id;
        $loggedInUserType = $loggedInUser->type;

        // Define role visibility based on user role and type
        if ($loggedInUserType == AppHelper::SE) {
            if ($loggedInUserRole == AppHelper::USER_MANAGER) {
                // SE Manager can see RSM, ASM, Supervisor, Employee
                $query->whereIn('role_id', [
                    AppHelper::USER_RSM,
                    AppHelper::USER_ASM,
                    AppHelper::USER_SUP,
                    AppHelper::USER_EMPLOYEE
                ])->where('type', AppHelper::SE);
            } elseif ($loggedInUserRole == AppHelper::USER_RSM) {
                // RSM can see ASM, Supervisor, Employee
                $query->whereIn('role_id', [
                    AppHelper::USER_ASM,
                    AppHelper::USER_SUP,
                    AppHelper::USER_EMPLOYEE
                ])->where('type', AppHelper::SE);
            } elseif ($loggedInUserRole == AppHelper::USER_ASM) {
                // ASM can see Supervisor, Employee
                $query->whereIn('role_id', [
                    AppHelper::USER_SUP,
                    AppHelper::USER_EMPLOYEE
                ])->where('type', AppHelper::SE);
            } elseif ($loggedInUserRole == AppHelper::USER_SUP) {
                // Supervisor can see Employee
                $query->where('role_id', AppHelper::USER_EMPLOYEE)
                    ->where('type', AppHelper::SE);
            } elseif ($loggedInUserRole == AppHelper::USER_EMPLOYEE) {
                // Employee can see only themselves
                $query->where('id', $loggedInUserId);
            }
        } elseif ($loggedInUserType == AppHelper::SALE) {
            if ($loggedInUserRole == AppHelper::USER_MANAGER) {
                // SALE Manager can see RSM, ASM, Supervisor, Employee
                $query->whereIn('role_id', [
                    AppHelper::USER_RSM,
                    AppHelper::USER_ASM,
                    AppHelper::USER_SUP,
                    AppHelper::USER_EMPLOYEE
                ])->where('type', AppHelper::SALE);
            } elseif ($loggedInUserRole == AppHelper::USER_RSM) {
                // RSM can see ASM, Supervisor, Employee
                $query->whereIn('role_id', [
                    AppHelper::USER_ASM,
                    AppHelper::USER_SUP,
                    AppHelper::USER_EMPLOYEE
                ])->where('type', AppHelper::SALE);
            } elseif ($loggedInUserRole == AppHelper::USER_ASM) {
                // ASM can see Supervisor, Employee
                $query->whereIn('role_id', [
                    AppHelper::USER_SUP,
                    AppHelper::USER_EMPLOYEE
                ])->where('type', AppHelper::SALE);
            } elseif ($loggedInUserRole == AppHelper::USER_SUP) {
                // Supervisor can see Employee
                $query->where('role_id', AppHelper::USER_EMPLOYEE)
                    ->where('type', AppHelper::SALE);
            } elseif ($loggedInUserRole == AppHelper::USER_EMPLOYEE) {
                // Employee can see only themselves
                $query->where('id', $loggedInUserId);
            }
        } elseif ($loggedInUserRole == AppHelper::USER_MANAGER) {
            // Non-SE/SALE Manager logic
            $query->where(function ($q) use ($loggedInUserId) {
                $q->where('id', $loggedInUserId)
                    ->orWhere('manager_id', $loggedInUserId);
            });
        } elseif ($loggedInUserRole == AppHelper::USER_ADMIN) {
            // Admin can see all except Super Admin
            $query->where('role_id', '!=', AppHelper::USER_SUPER_ADMIN);
        } elseif (!in_array($loggedInUserRole, [AppHelper::USER_SUPER_ADMIN, AppHelper::USER_ADMIN])) {
            // Other roles see only themselves
            $query->where('id', $loggedInUserId);
        }

        // Fetch Area Managers for Filter Dropdown (filtered by type)
        $areaManager = User::whereIn('role_id', [AppHelper::USER_MANAGER])
            ->where('type', $loggedInUserType)
            ->get()->mapWithKeys(function ($manager) use ($loggedInUser) {
                return [
                    $manager->id => $loggedInUser->user_lang === 'en'
                        ? $manager->family_name_latin . ' ' . $manager->name_latin
                        : $manager->family_name . ' ' . $manager->name
                ];
            });

        // Fetch Employees for Filter Dropdown (filtered by type)
        $full_name = User::where('role_id', AppHelper::USER_EMPLOYEE)
            ->where('type', $loggedInUserType)
            ->get()->mapWithKeys(function ($employee) use ($loggedInUser) {
                return [
                    $employee->id => $loggedInUser->user_lang === 'en'
                        ? $employee->family_name_latin . ' ' . $employee->name_latin
                        : $employee->family_name . ' ' . $employee->name
                ];
            });

        // Apply filters
        if ($request->has('manager_id') && !empty($request->manager_id)) {
            $query->where('manager_id', $request->manager_id);
            $is_filter = true;
        }

        if ($request->has('full_name') && !empty($request->full_name)) {
            $query->where('id', $request->full_name);
            $is_filter = true;
        }

        if ($request->ajax()) {
            $users = $query->get();
            return DataTables::of($users)
                ->addColumn('photo', function ($data) {
                    $photoUrl = $data->photo ? asset('storage/' . $data->photo) : asset('images/avatar.png');
                    return '<img class="img-responsive center" style="height: 35px; width: 35px; object-fit: cover; border-radius: 50%;" src="' . $photoUrl . '" >';
                })
                ->addColumn('staff_id_card', function ($data) {
                    return __($data->staff_id_card);
                })
                ->addColumn('name', function ($data) {
                    return auth()->user()->user_lang == 'en' ? $data->getFullNameLatinAttribute() : $data->getFullNameAttribute();
                })
                ->addColumn('position', function ($data) {
                    return __($data->position);
                })
                ->addColumn('area', function ($data) {
                    return __($data->area);
                })
                ->addColumn('username', function ($data) {
                    return __($data->username);
                })
                ->addColumn('email', function ($data) {
                    return __($data->email);
                })
                ->addColumn('managed_by', function ($data) {
                    return $data->manager
                        ? (auth()->user()->user_lang == 'en' ? $data->manager->getFullNameLatinAttribute() : $data->manager->getFullNameAttribute())
                        : '<span class="text-danger">' . __("No Manager") . '</span>';
                })
                ->addColumn('phone_no', function ($data) {
                    return __($data->phone_no);
                })
                ->addColumn('role', function ($data) {
                    return $data->role ? $data->role->name : __('N/A');
                })
                ->addColumn('type', function ($data) {
                    return isset(AppHelper::USER_TYPE[$data->type]) ? __(AppHelper::USER_TYPE[$data->type]) : __('N/A');
                })
                ->addColumn('gender', function ($data) {
                    return isset(AppHelper::GENDER[$data->gender]) ? __(AppHelper::GENDER[$data->gender]) : __('N/A');
                })
                ->addColumn('status', function ($data) {
                    return $data->status == 1
                        ? '<span class="status-active">' . __('Active') . '</span>'
                        : '<span style="color: red;">' . __('Inactive') . '</span>';
                })
                ->addColumn('action', function ($data) {
                    $button = '<div class="change-action-item">';
                    $actions = false;

                    if (auth()->user()->can('update user')) {
                        $button .= '<a title="Edit" href="' . route('user.edit', $data->id) . '" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>';
                        $actions = true;
                    }
                    if (auth()->user()->can('update user') && $data->status == 1) {
                        $button .= '<a href="javascript:void(0)" class="btn btn-danger btn-sm disable-user" title="Disable" data-id="' . $data->id . '"><i class="fa fa-ban"></i></a>';
                        $actions = true;
                    }
                    if (auth()->user()->can('update user') && $data->status == 0) {
                        $button .= '<a href="javascript:void(0)" class="btn btn-success btn-sm enable-user" title="Enable" data-id="' . $data->id . '"><i class="fa fa-check"></i></a>';
                        $actions = true;
                    }
                    if (auth()->user()->role_id == AppHelper::USER_SUPER_ADMIN) {
                        $button .= '<a title="forgotPassword" href="' . route('forget.password', $data->id) . '" class="btn btn-primary btn-sm"><i class="fa-solid fa-arrows-rotate"></i></a>';
                        $actions = true;
                    }
                    if (!$actions) {
                        $button .= '<span style="font-weight:bold; color:red;">No Action</span>';
                    }
                    $button .= '</div>';
                    return $button;
                })
                ->rawColumns(['action', 'photo', 'status', 'managed_by'])
                ->make(true);
        }

        // Fetch Area Managers for Filter Dropdown
        return view('backend.user.list', compact('areaManager', 'is_filter', 'full_name'));
    }


    public function disable($id)
    {
        try {

            $user = User::findOrFail($id);

            // Check if user has permission
            if (!auth()->user()->can('update user')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action'
                ], 403);
            }

            if ($user->status == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is already disabled'
                ]);
            }

            $user->update(['status' => 0]);

            return response()->json([
                'success' => true,
                'message' => 'User disabled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error disabling user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function enable($id)
    {
        try {
            $user = User::findOrFail($id);

            // Check if user has permission
            if (!auth()->user()->can('update user')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action'
                ], 403);
            }

            if ($user->status == 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is already active'
                ]);
            }

            $user->update(['status' => 1]);

            return response()->json([
                'success' => true,
                'message' => 'User enabled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error enabling user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function create()
    {
        $user = null;
        $authUser = auth()->user();
        $type = AppHelper::USER_TYPE;

        return view('backend.user.add', compact('user', 'type'));
    }

    public function fetchRolesByType(Request $request)
    {
        $typeId = $request->get('type_id');
        $authUser = auth()->user();

        $query = Role::query();

        if ($authUser->role_id === AppHelper::USER_SUPER_ADMIN) {
            // Super Admin sees all roles
        } elseif ($authUser->role_id === AppHelper::USER_ADMIN) {
            $query->whereNotIn('id', [AppHelper::USER_SUPER_ADMIN]);
        } elseif ($authUser->role_id === AppHelper::USER_RSM) {
            $query->whereIn('id', [AppHelper::USER_EMPLOYEE, AppHelper::USER_ASM, AppHelper::USER_SUP]);
        } elseif ($authUser->role_id === AppHelper::USER_MANAGER) {
            $query->whereIn('id', [AppHelper::USER_EMPLOYEE, AppHelper::USER_ASM, AppHelper::USER_SUP, AppHelper::USER_RSM]);
        }

        $roles = $query->pluck('name', 'id');
        return response()->json(['roles' => $roles]);
    }

    public function fetchAsms(Request $request)
    {
        $typeId = $request->get('type_id');
        $roleId = $request->get('role_id');
        $authUser = auth()->user();

        $query = User::where('type', $typeId)
            ->where('role_id', AppHelper::USER_ASM);

        if ($authUser->role_id === AppHelper::USER_RSM) {
            $query->where('type', AppHelper::SE);
        } elseif ($authUser->role_id === AppHelper::USER_MANAGER) {
            $query->where('type', AppHelper::SALE);
        }

        $asms = $query->get()->mapWithKeys(function ($user) use ($authUser) {
            return [
                $user->id => ($authUser->user_lang === 'en'
                    ? $user->family_name_latin . ' ' . $user->name_latin
                    : $user->family_name . ' ' . $user->name)
            ];
        })->toArray();

        return response()->json(['asms' => $asms]);
    }

    public function fetchSupervisors(Request $request)
    {
        $typeId = $request->get('type_id');
        $roleId = $request->get('role_id');
        $asmId = $request->get('asm_id');
        $authUser = auth()->user();

        $query = User::where('type', $typeId)
            ->where('role_id', AppHelper::USER_SUP);

        if ($roleId == AppHelper::USER_EMPLOYEE && $asmId) {
            $supIds = User::where('id', $asmId)
                ->where('type', $typeId)
                ->pluck('sup_id')->toArray();
            $query->whereIn('id', $supIds);
        }

        if ($authUser->role_id === AppHelper::USER_RSM) {
            $query->where('type', AppHelper::SE);
        } elseif ($authUser->role_id === AppHelper::USER_MANAGER) {
            $query->where('type', AppHelper::SALE);
        }

        $supervisors = $query->get()->mapWithKeys(function ($user) use ($authUser) {
            return [
                $user->id => ($authUser->user_lang === 'en'
                    ? $user->family_name_latin . ' ' . $user->name_latin
                    : $user->family_name . ' ' . $user->name)
            ];
        })->toArray();

        return response()->json(['supervisors' => $supervisors]);
    }

    public function fetchRsms(Request $request)
    {
        $typeId = $request->get('type_id');
        $supId = $request->get('sup_id');
        $roleId = $request->get('role_id');
        $authUser = auth()->user();

        $rsmIds = [];

        if ($roleId == AppHelper::USER_EMPLOYEE && $supId) {
            $rsmIds = User::where('id', $supId)
                ->where('type', $typeId)
                ->pluck('rsm_id')->toArray();
        } elseif ($roleId == AppHelper::USER_SUP || $roleId == AppHelper::USER_ASM) {
            $rsmIds = User::where('type', $typeId)
                ->where('role_id', AppHelper::USER_RSM)
                ->pluck('id')->toArray();
        }

        $query = User::whereIn('id', $rsmIds);

        $rsms = $query->get()->mapWithKeys(function ($user) use ($authUser) {
            return [
                $user->id => ($authUser->user_lang === 'en'
                    ? $user->family_name_latin . ' ' . $user->name_latin
                    : $user->family_name . ' ' . $user->name)
            ];
        })->toArray();

        return response()->json(['rsms' => $rsms]);
    }
    public function fetchManagers(Request $request)
    {
        $typeId = $request->get('type_id');
        $rsmId = $request->get('rsm_id');
        $roleId = $request->get('role_id');
        $authUser = auth()->user();
        // dd($roleId);
        if ($roleId == AppHelper::USER_RSM) {
            $managerID = User::where('type', $typeId)
                ->where('role_id', AppHelper::USER_MANAGER)
                ->pluck('id')->toArray();
        }
        if ($roleId == AppHelper::USER_SUP) {
            $managerID = User::where('type', $typeId)
                ->where('id', $rsmId)
                ->pluck('manager_id')->toArray();
        }
        if ($roleId == AppHelper::USER_ASM) {
            $managerID = User::where('id', $rsmId)
                ->where('type', $typeId)
                ->pluck('manager_id')->toArray();
        }
        if ($roleId == AppHelper::USER_EMPLOYEE) {
            $managerID = User::where('rsm_id', $rsmId)
                ->where('type', $typeId)
                ->pluck('manager_id')->toArray();
        }

        $query = User::whereIn('id', $managerID);
        $managers = $query->get()->mapWithKeys(function ($user) use ($authUser) {
            return [
                $user->id => ($authUser->user_lang === 'en'
                    ? $user->family_name_latin . ' ' . $user->name_latin
                    : $user->family_name . ' ' . $user->name)
            ];
        })->toArray();

        return response()->json(['managers' => $managers]);
    }

    public function store(Request $request)
    {
        $rules = [
            'photo' => 'mimes:jpeg,jpg,png|max:2000|dimensions:min_width=50,min_height=50',
            'family_name' => 'required|min:2|max:255',
            'name' => 'required|min:2|max:255',
            'family_name_latin' => 'required|min:2|max:255',
            'name_latin' => 'required|min:2|max:255',
            'username' => 'required|min:5|max:255|unique:users,username',
            'password' => 'required|min:6|max:50',
            'phone_no' => 'required|max:50',
            'role_id' => 'required',
            'gender' => 'required',
            'staff_id_card' => 'required|min:3|max:10|unique:users,staff_id_card',
            'position' => 'required',
            'area' => 'required',
            'type' => 'required'
        ];

        if ($request->role_id == AppHelper::USER_EMPLOYEE) {
            $rules['manager_id'] = 'required';
            $rules['rsm_id'] = 'required';
            $rules['sup_id'] = 'required';
            $rules['asm_id'] = 'required';
        } elseif ($request->role_id == AppHelper::USER_ASM) {
            $rules['manager_id'] = 'required';
            $rules['rsm_id'] = 'required';
            $rules['sup_id'] = 'required';
        } elseif ($request->role_id == AppHelper::USER_RSM) {
            $rules['manager_id'] = 'required';
        }

        $this->validate($request, $rules);
        $createdBy = auth()->user()->role_id;
        $userData = [
            'family_name' => $request->family_name,
            'name' => $request->name,
            'family_name_latin' => $request->family_name_latin,
            'name_latin' => $request->name_latin,
            'role_id' => $request->role_id,
            'gender' => $request->gender,
            'username' => $request->username,
            'email' => $request->email,
            'phone_no' => $request->phone_no,
            'status' => $request->status,
            'staff_id_card' => $request->staff_id_card,
            'position' => $request->position,
            'area' => $request->area,
            'type' => $request->type,
            'password' => bcrypt($request->password),
            'manager_id' => $request->manager_id,
            'rsm_id' => $request->rsm_id,
            'sup_id' => $request->sup_id,
            'asm_id' => $request->asm_id,
            'created_by' => $createdBy,
        ];

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = time() . '_' . md5($file->getClientOriginalName()) . '.' . $file->extension();
            $filePath = 'Uploads/' . $fileName;
            Storage::put($filePath, file_get_contents($file));
            $userData['photo'] = $filePath;
        }

        $user = User::create($userData);

        // Fetch the role name before assigning it
        $role = Role::findOrFail($request->role_id);
        $user->syncRoles($role->name); // Assign role using name

        UserRole::create([
            'user_id' => $user->id,
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('user.index')->with('success', 'User added!');
    }


    public function edit($id)
    {
        $user = User::findOrFail($id);
        $authUser = auth()->user();
        $type = AppHelper::USER_TYPE;

        return view('backend.user.add', compact('user', 'type'));
    }


    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'photo' => 'nullable|mimes:jpeg,jpg,png|max:2000|dimensions:min_width=50,min_height=50',
            'family_name' => 'required|min:2|max:255',
            'name' => 'required|min:2|max:255',
            'family_name_latin' => 'required|min:2|max:255',
            'name_latin' => 'required|min:2|max:255',
            'username' => 'required|min:5|max:255|unique:users,username,' . $id,
            'password' => 'nullable|min:6|max:50',
            'phone_no' => 'required|max:50',
            'role_id' => 'required',
            'gender' => 'required',
            'staff_id_card' => 'required|min:3|max:10|unique:users,staff_id_card,' . $id,
            'position' => 'required',
            'area' => 'required',
            'type' => 'required',
        ];

       if ($request->role_id == AppHelper::USER_EMPLOYEE) {
            $rules['manager_id'] = 'required';
            $rules['rsm_id'] = 'required';
            $rules['sup_id'] = 'required';
            $rules['asm_id'] = 'required';
        } elseif ($request->role_id == AppHelper::USER_ASM) {
            $rules['manager_id'] = 'required';
            $rules['rsm_id'] = 'required';
            $rules['sup_id'] = 'required';
        } elseif ($request->role_id == AppHelper::USER_RSM) {
            $rules['manager_id'] = 'required';
        }

        $this->validate($request, $rules);

        $userData = [
            'family_name' => $request->family_name,
            'name' => $request->name,
            'family_name_latin' => $request->family_name_latin,
            'name_latin' => $request->name_latin,
            'role_id' => $request->role_id,
            'gender' => $request->gender,
            'username' => $request->username,
            'email' => $request->email,
            'phone_no' => $request->phone_no,
            'status' => $request->status,
            'staff_id_card' => $request->staff_id_card,
            'position' => $request->position,
            'area' => $request->area,
            'type' => $request->type,
            'manager_id' => $request->manager_id,
            'rsm_id' => $request->rsm_id,
            'sup_id' => $request->sup_id,
            'asm_id' => $request->asm_id,
        ];

        // Handle password update only if provided
        if ($request->filled('password')) {
            $userData['password'] = bcrypt($request->password);
        }

        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::exists($user->photo)) {
                Storage::delete($user->photo);
            }

            $file = $request->file('photo');
            $fileName = time() . '_' . md5($file->getClientOriginalName()) . '.' . $file->extension();
            $filePath = 'Uploads/' . $fileName;
            Storage::put($filePath, file_get_contents($file));
            $userData['photo'] = $filePath;
        }

        $user->update($userData);
        $role = Role::findOrFail($request->role_id);
        $user->syncRoles($role->name); // Assign role using name
        UserRole::updateOrCreate(
            ['user_id' => $user->id],
            ['role_id' => $request->role_id]
        );

        return redirect()->route('user.index')->with('success', 'User updated successfully!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        UserRole::where('user_id', $user->id)->delete();

        $user->delete();

        return redirect()->back()->with('success', "User has been deleted!");
    }


    public function profile(Request $request)
    {
        $storage = Storage::allFiles();

        $user = auth()->user();
        // return redirect()->route('profile')->with('success', 'Profile updated.');

        return view('backend.user.profile', compact('user'));
    }


    public function updateProfilePhoto(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            abort(404);
        }

        $this->validate($request, [
            'photo' => 'mimes:jpeg,jpg,png|max:2000|dimensions:min_width=50,min_height=50',
        ]);

        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::exists($user->photo)) {
                Storage::delete($user->photo);
            }

            $file = $request->file('photo');
            $fileName = time() . '_' . md5($file->getClientOriginalName()) . '.' . $file->extension();
            $filePath = 'uploads/' . $fileName;
            Storage::put($filePath, file_get_contents($file));

            $userData['photo'] = $filePath;

            $update = $user->update($userData);
            if ($update) {
                return redirect()->back()->with('success', 'Profile Photo updated!');
            } else {
                return redirect()->back()->with('error', 'Failed to update profile photo in the database.')->withInput();
            }
        }
        return redirect()->back()->with('error', 'No photo uploaded!');
    }

    public function setLanguage($lang)
    {
        if (in_array($lang, ['kh', 'en'])) {
            // Update the user's language preference (if logged in)
            if (auth()->check()) {
                auth()->user()->update(['user_lang' => $lang]);
            }

            // Store language in session
            session(['user_lang' => $lang]);

            // Set the application's locale
            app()->setLocale($lang);

            // Redirect back
            return redirect()->back();
        }

        return redirect()->route('/dashboard');
    }

    public function showChangePasswordForm()
    {
        return view('backend.user.change_password');
    }

    // Handle Change Password Form Submission
    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => ['required', 'min:6', 'max:50'],
            'password' => ['required', 'min:6', 'max:50', 'confirmed'],
        ]);

        if (!Hash::check($request->old_password, Auth::user()->password)) {
            throw ValidationException::withMessages([
                'old_password' => ['The old password does not match.'],
            ]);
        }

        // Update the user's password
        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('dashboard.index')->with('success', 'Password updated successfully.');
    }
    public function lock()
    {
        $user = auth()->user();
        session([
            'locked' => true,
            'locked_username' => $user->username,
            'locked_name' => $user->name,
            'locked_photo' => $user->photo,
        ]);

        return view('backend.user.lock', [
            'username' => $user->username,
            'name' => $user->name,
            'photo' => $user->photo,
        ]);
    }
    public function unlock(Request $request)
    {
        $request->validate([
            'password' => 'required',
            'username' => 'required',
        ]);

        if (session('locked') && Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            // Clear the lock session data
            session()->forget(['locked', 'locked_username', 'locked_name', 'locked_photo']);
            return redirect()->route('dashboard.index')
                ->with('success', 'Welcome to AdminPanel.')
                ->with('show_popup', true);
        }

        return redirect()->route('lockscreen')->with('error', 'Invalid password.');
    }
}

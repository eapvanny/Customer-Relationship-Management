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
                $query->where(function ($q) use ($loggedInUserId) {
                    $q->whereIn('role_id', [
                        AppHelper::USER_RSM,
                        AppHelper::USER_ASM,
                        AppHelper::USER_SUP,
                        AppHelper::USER_EMPLOYEE
                    ])->where('type', AppHelper::SE)
                        ->orWhere('id', $loggedInUserId);
                });
            } elseif ($loggedInUserRole == AppHelper::USER_RSM) {
                $query->where(function ($q) use ($loggedInUserId) {
                    $q->whereIn('role_id', [
                        AppHelper::USER_ASM,
                        AppHelper::USER_SUP,
                        AppHelper::USER_EMPLOYEE
                    ])->where('type', AppHelper::SE)
                        ->orWhere('id', $loggedInUserId);
                });
            } elseif ($loggedInUserRole == AppHelper::USER_ASM) {
                $query->where(function ($q) use ($loggedInUserId) {
                    $q->whereIn('role_id', [
                        AppHelper::USER_SUP,
                        AppHelper::USER_EMPLOYEE
                    ])->where('type', AppHelper::SE)
                        ->orWhere('id', $loggedInUserId);
                });
            } elseif ($loggedInUserRole == AppHelper::USER_SUP) {
                $query->where(function ($q) use ($loggedInUserId) {
                    $q->where('role_id', AppHelper::USER_EMPLOYEE)
                        ->where('type', AppHelper::SE)
                        ->orWhere('id', $loggedInUserId);
                });
            } elseif ($loggedInUserRole == AppHelper::USER_EMPLOYEE) {
                $query->where('id', $loggedInUserId);
            }
        } elseif ($loggedInUserType == AppHelper::SALE) {
            if ($loggedInUserRole == AppHelper::USER_MANAGER) {
                $query->where(function ($q) use ($loggedInUserId) {
                    $q->whereIn('role_id', [
                        AppHelper::USER_RSM,
                        AppHelper::USER_ASM,
                        AppHelper::USER_SUP,
                        AppHelper::USER_EMPLOYEE
                    ])->where('type', AppHelper::SALE)
                        ->orWhere('id', $loggedInUserId);
                });
            } elseif ($loggedInUserRole == AppHelper::USER_RSM) {
                $query->where(function ($q) use ($loggedInUserId) {
                    $q->whereIn('role_id', [
                        AppHelper::USER_ASM,
                        AppHelper::USER_SUP,
                        AppHelper::USER_EMPLOYEE
                    ])->where('type', AppHelper::SALE)
                        ->orWhere('id', $loggedInUserId);
                });
            } elseif ($loggedInUserRole == AppHelper::USER_ASM) {
                $query->where(function ($q) use ($loggedInUserId) {
                    $q->whereIn('role_id', [
                        AppHelper::USER_SUP,
                        AppHelper::USER_EMPLOYEE
                    ])->where('type', AppHelper::SALE)
                        ->orWhere('id', $loggedInUserId);
                });
            } elseif ($loggedInUserRole == AppHelper::USER_SUP) {
                $query->where(function ($q) use ($loggedInUserId) {
                    $q->where('role_id', AppHelper::USER_EMPLOYEE)
                        ->where('type', AppHelper::SALE)
                        ->orWhere('id', $loggedInUserId);
                });
            } elseif ($loggedInUserRole == AppHelper::USER_EMPLOYEE) {
                $query->where('id', $loggedInUserId);
            }
        } elseif ($loggedInUserRole == AppHelper::USER_MANAGER) {
            // Non-SE/SALE Manager logic
            $query->where(function ($q) use ($loggedInUserId) {
                $q->where('id', $loggedInUserId)
                    ->orWhere('manager_id', $loggedInUserId);
            });
        } elseif ($loggedInUserRole == AppHelper::USER_ADMINISTRATOR) {
            // Adminministrator can see all except Super Admin
            $query->where(function ($q) use ($loggedInUserId) {
                $q->whereNotIn('role_id', [AppHelper::USER_SUPER_ADMIN])
                    ->orWhere('id', $loggedInUserId);
            });
        } elseif ($loggedInUserRole == AppHelper::USER_ADMIN) {
            // Admin can see all except Super Admin and administrator
            $query->where(function ($q) use ($loggedInUserId) {
                $q->whereNotIn('role_id', [AppHelper::USER_SUPER_ADMIN, AppHelper::USER_ADMINISTRATOR])
                    ->orWhere('id', $loggedInUserId);
            });
        } elseif ($loggedInUserRole == AppHelper::USER_DIRECTOR) {
            // Director can see all except Super Admin administrator admin
            $query->where(function ($q) use ($loggedInUserId) {
                $q->whereNotIn('role_id', [AppHelper::USER_SUPER_ADMIN, AppHelper::USER_ADMINISTRATOR, AppHelper::USER_ADMIN])
                    ->orWhere('id', $loggedInUserId);
            });
        } elseif (!in_array($loggedInUserRole, [AppHelper::USER_SUPER_ADMIN, AppHelper::USER_ADMIN])) {
            $query->where('id', $loggedInUserId);
        }

        if ($request->ajax()) {
            $users = $query->orderBy('id','desc')->get();
            return DataTables::of($users)
                ->addColumn('photo', function ($data) {
                    $photoUrl = $data->photo ? asset('storage/' . $data->photo) : asset('images/avatar.png');
                    return '<img class="img-responsive center" style="height: 35px; width: 35px; object-fit: cover; border-radius: 50%;" src="' . $photoUrl . '" >';
                })
                ->addColumn('staff_id_card', function ($data) {
                    return __($data->staff_id_card);
                })
                ->addColumn('name', function ($data) {
                    // return auth()->user()->user_lang == 'en' ? $data->getFullNameLatinAttribute() : $data->getFullNameAttribute();
                    return  $data->getFullNameAttribute();
                })
                ->addColumn('name_latin', function ($data) {
                    return  $data->getFullNameLatinAttribute();
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

                    if (auth()->user()->role_id == AppHelper::USER_SUPER_ADMIN || auth()->user()->role_id == AppHelper::USER_ADMINISTRATOR || auth()->user()->role_id == AppHelper::USER_DIRECTOR || auth()->user()->role_id == AppHelper::USER_ADMIN || auth()->user()->role_id == AppHelper::USER_MANAGER) {
                        $button .= '<a title="Edit" href="' . route('user.edit', $data->id) . '" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>';
                        $actions = true;
                    }
                    if ((auth()->user()->role_id == AppHelper::USER_SUPER_ADMIN || auth()->user()->role_id == AppHelper::USER_ADMINISTRATOR || auth()->user()->role_id == AppHelper::USER_DIRECTOR || auth()->user()->role_id == AppHelper::USER_ADMIN || auth()->user()->role_id == AppHelper::USER_MANAGER) && $data->status == 1) {
                        $button .= '<a href="javascript:void(0)" class="btn btn-danger btn-sm disable-user" title="Disable" data-id="' . $data->id . '"><i class="fa fa-ban"></i></a>';
                        $actions = true;
                    }
                    if ((auth()->user()->role_id == AppHelper::USER_SUPER_ADMIN || auth()->user()->role_id == AppHelper::USER_ADMINISTRATOR || auth()->user()->role_id == AppHelper::USER_DIRECTOR || auth()->user()->role_id == AppHelper::USER_ADMIN || auth()->user()->role_id == AppHelper::USER_MANAGER) && $data->status == 0) {
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
        return view('backend.user.list', compact('is_filter'));
    }


    public function disable($id)
{
    try {
        $user = User::findOrFail($id);

        // Check if user has specific role
        if (!in_array(auth()->user()->role_id, [
            AppHelper::USER_SUPER_ADMIN,
            AppHelper::USER_ADMINISTRATOR,
            AppHelper::USER_DIRECTOR,
            AppHelper::USER_ADMIN,
            AppHelper::USER_MANAGER
        ])) {
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
            if (!in_array(auth()->user()->role_id, [
                AppHelper::USER_SUPER_ADMIN,
                AppHelper::USER_ADMINISTRATOR,
                AppHelper::USER_DIRECTOR,
                AppHelper::USER_ADMIN,
                AppHelper::USER_MANAGER
            ])) {
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

        if (in_array($authUser->role_id, [AppHelper::USER_SUPER_ADMIN, AppHelper::USER_ADMINISTRATOR, AppHelper::USER_DIRECTOR])) {
            $type = AppHelper::USER_TYPE; // See all
        } elseif ($authUser->type == AppHelper::SALE) {
            $type = [AppHelper::SALE => AppHelper::USER_TYPE[AppHelper::SALE]]; // Only SSP
        } elseif ($authUser->type == AppHelper::SE) {
            $type = [AppHelper::SE => AppHelper::USER_TYPE[AppHelper::SE]]; // Only SE
        } else {
            $type = []; // Optional: fallback or restrict entirely
        }

        return view('backend.user.add', compact('user', 'type'));
    }

    public function fetchRolesByType(Request $request)
    {
        $typeId = $request->get('type_id');
        $authUser = auth()->user();

        $query = Role::query();

        if ($authUser->role_id === AppHelper::USER_SUPER_ADMIN) {
            // Super Admin sees all roles
        } elseif ($authUser->role_id === AppHelper::USER_ADMINISTRATOR) {
            $query->whereNotIn('id', [AppHelper::USER_SUPER_ADMIN]);
        } elseif ($authUser->role_id === AppHelper::USER_ADMIN) {
            $query->whereNotIn('id', [AppHelper::USER_SUPER_ADMIN, AppHelper::USER_ADMINISTRATOR]);
        } elseif ($authUser->role_id === AppHelper::USER_DIRECTOR) {
            $query->whereNotIn('id', [AppHelper::USER_SUPER_ADMIN, AppHelper::USER_ADMINISTRATOR, AppHelper::USER_ADMIN]);
        } elseif ($authUser->role_id === AppHelper::USER_RSM) {
            $query->whereIn('id', [AppHelper::USER_EMPLOYEE, AppHelper::USER_ASM, AppHelper::USER_SUP]);
        } elseif ($authUser->role_id === AppHelper::USER_MANAGER) {
            $query->whereIn('id', [AppHelper::USER_EMPLOYEE, AppHelper::USER_ASM, AppHelper::USER_SUP, AppHelper::USER_RSM]);
        }

        $roles = $query->pluck('name', 'id');
        return response()->json(['roles' => $roles]);
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
            // For Employee: get supervisors assigned to the selected ASM
            $query->where('asm_id', $asmId);
        } elseif ($roleId == AppHelper::USER_ASM) {
            // For ASM: get all supervisors
            $query->where('type', $typeId);
        }

        if ($authUser->role_id === AppHelper::USER_RSM) {
            $query->where('type', AppHelper::SE);
        } elseif ($authUser->role_id === AppHelper::USER_MANAGER) {
            $query->where('type', AppHelper::SALE);
        }

        $supervisors = $query->get()->mapWithKeys(function ($user) use ($authUser) {
            if ($user->type == AppHelper::SALE) {
                $suffix = 'PP';
            } elseif ($user->type == AppHelper::SE) {
                $suffix = 'PV';
            } else {
                $suffix = AppHelper::USER_TYPE[$user->type] ?? '';
            }

            $fullName = $authUser->user_lang === 'en'
                ? $user->family_name_latin . ' ' . $user->name_latin
                : $user->family_name . ' ' . $user->name;

            return [
                $user->id => "{$fullName} ({$suffix})"
            ];
        })->toArray();

        return response()->json(['supervisors' => $supervisors]);
    }

    public function fetchAsms(Request $request)
    {
        $typeId = $request->get('type_id');
        $supId = $request->get('sup_id');
        $roleId = $request->get('role_id');
        $authUser = auth()->user();

        $asmIds = [];

        if ($roleId == AppHelper::USER_EMPLOYEE) {
            $asmIds = User::where('id', $supId)
                ->where('type', $typeId)
                ->pluck('asm_id')
                ->flatMap(function ($asmId) {
                    return is_array($asmId) ? $asmId : json_decode($asmId, true) ?? [];
                })->toArray();
        } elseif ($roleId == AppHelper::USER_SUP) {
            $asmIds = User::where('type', $typeId)
                ->where('role_id', AppHelper::USER_ASM)
                ->pluck('id')->toArray();
        } elseif ($authUser->role_id === AppHelper::USER_RSM) {
            $asmIds = User::where('type', $typeId)
                ->where('role_id', AppHelper::USER_ASM)
                ->pluck('id')->toArray();
        } elseif ($authUser->role_id === AppHelper::USER_MANAGER) {
            $asmIds = User::where('type', $typeId)
                ->pluck('rsm_id')->toArray();
        }

        $query = User::whereIn('id', array_unique($asmIds));

        $asms = $query->get()->mapWithKeys(function ($user) use ($authUser) {
            if ($user->type == AppHelper::SALE) {
                $suffix = 'PP';
            } elseif ($user->type == AppHelper::SE) {
                $suffix = 'PV';
            } else {
                $suffix = AppHelper::USER_TYPE[$user->type] ?? '';
            }

            $fullName = $authUser->user_lang === 'en'
                ? $user->family_name_latin . ' ' . $user->name_latin
                : $user->family_name . ' ' . $user->name;

            return [
                $user->id => "{$fullName} ({$suffix})"
            ];
        })->toArray();

        return response()->json(['asms' => $asms]);
    }

    public function fetchRsms(Request $request)
    {
        $typeId = $request->get('type_id');
        $asmId = $request->get('asm_id');
        $roleId = $request->get('role_id');
        $authUser = auth()->user();

        $rsmIds = [];

        if ($roleId == AppHelper::USER_EMPLOYEE || $roleId == AppHelper::USER_SUP) {
            $rsmIds = User::whereIn('id', is_array($asmId) ? $asmId : [$asmId])
                ->where('type', $typeId)
                ->pluck('rsm_id')->toArray();
        } elseif ($roleId == AppHelper::USER_ASM) {
            $rsmIds = User::where('type', $typeId)
                ->where('role_id', AppHelper::USER_RSM)
                ->pluck('id')->toArray();
        }

        $query = User::whereIn('id', array_unique($rsmIds));

        $rsms = $query->get()->mapWithKeys(function ($user) use ($authUser) {
            if ($user->type == AppHelper::SALE) {
                $suffix = 'PP';
            } elseif ($user->type == AppHelper::SE) {
                $suffix = 'PV';
            } else {
                $suffix = AppHelper::USER_TYPE[$user->type] ?? '';
            }

            $fullName = $authUser->user_lang === 'en'
                ? $user->family_name_latin . ' ' . $user->name_latin
                : $user->family_name . ' ' . $user->name;

            return [
                $user->id => "{$fullName} ({$suffix})"
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

        $managerIds = [];

        if ($roleId == AppHelper::USER_RSM) {
            $managerIds = User::where('type', $typeId)
                ->where('role_id', AppHelper::USER_MANAGER)
                ->pluck('id')->toArray();
        } elseif ($roleId == AppHelper::USER_SUP || $roleId == AppHelper::USER_ASM || $roleId == AppHelper::USER_EMPLOYEE) {
            $managerIds = User::where('id', $rsmId)
                ->where('type', $typeId)
                ->pluck('manager_id')->toArray();
        }

        $query = User::whereIn('id', array_unique($managerIds));

        $managers = $query->get()->mapWithKeys(function ($user) use ($authUser) {
            if ($user->type == AppHelper::SALE) {
                $suffix = 'PP';
            } elseif ($user->type == AppHelper::SE) {
                $suffix = 'PV';
            } else {
                $suffix = AppHelper::USER_TYPE[$user->type] ?? '';
            }

            $fullName = $authUser->user_lang === 'en'
                ? $user->family_name_latin . ' ' . $user->name_latin
                : $user->family_name . ' ' . $user->name;

            return [
                $user->id => "{$fullName} ({$suffix})"
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
            'username' => 'required|min:2|max:255|unique:users,username',
            'password' => 'required|min:6|max:50',
            'phone_no' => 'required',
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
            $rules['asm_id'] = 'required|array';
            $rules['asm_id.*'] = 'required|exists:users,id';
        } elseif ($request->role_id == AppHelper::USER_SUP) {
            $rules['asm_id'] = 'required|array';
            $rules['asm_id.*'] = 'required|exists:users,id';
            $rules['rsm_id'] = 'required';
            $rules['manager_id'] = 'required';
        } elseif ($request->role_id == AppHelper::USER_ASM) {
            $rules['manager_id'] = 'required';
            $rules['rsm_id'] = 'required';
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
            'asm_id' => $request->asm_id ? json_encode($request->asm_id) : null,
            'created_by' => $createdBy,
            'user_lang' => 'kh',
        ];

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = time() . '_' . md5($file->getClientOriginalName()) . '.' . $file->extension();
            $filePath = 'Uploads/' . $fileName;
            Storage::put($filePath, file_get_contents($file));
            $userData['photo'] = $filePath;
        }

        $user = User::create($userData);

        $role = Role::findOrFail($request->role_id);
        $user->syncRoles($role->name);

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

        if (in_array($authUser->type, [AppHelper::USER_SUPER_ADMIN, AppHelper::USER_ADMINISTRATOR, AppHelper::USER_DIRECTOR])) {
            $type = AppHelper::USER_TYPE;
        } elseif ($authUser->type == AppHelper::SALE) {
            $type = [AppHelper::SALE => AppHelper::USER_TYPE[AppHelper::SALE]];
        } elseif ($authUser->type == AppHelper::SE) {
            $type = [AppHelper::SE => AppHelper::USER_TYPE[AppHelper::SE]];
        } else {
            $type = [];
        }

        // Decode asm_id if it exists
        $user->asm_id = $user->asm_id ? json_decode($user->asm_id, true) : [];

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
            'username' => 'required|min:2|max:255|unique:users,username,' . $id,
            'password' => 'nullable|min:6|max:50',
            'phone_no' => 'required',
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
            $rules['asm_id'] = 'required|array';
            $rules['asm_id.*'] = 'required|exists:users,id';
        } elseif ($request->role_id == AppHelper::USER_SUP) {
            $rules['asm_id'] = 'required|array';
            $rules['asm_id.*'] = 'required|exists:users,id';
            $rules['rsm_id'] = 'required';
            $rules['manager_id'] = 'required';
        } elseif ($request->role_id == AppHelper::USER_ASM) {
            $rules['manager_id'] = 'required';
            $rules['rsm_id'] = 'required';
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
            'asm_id' => $request->asm_id ? json_encode($request->asm_id) : null,
        ];

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
        $user->syncRoles($role->name);
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
        if (auth()->user()->type == AppHelper::SALE && in_array(auth()->user()->role_id, [AppHelper::USER_EMPLOYEE, AppHelper::USER_SUP, AppHelper::USER_RSM, AppHelper::USER_ASM])) {
            return redirect()->route('report.index')->with('success', 'Password updated successfully.');
        }elseif (auth()->user()->type == AppHelper::SE && in_array(auth()->user()->role_id, [AppHelper::USER_EMPLOYEE, AppHelper::USER_SUP, AppHelper::USER_RSM, AppHelper::USER_ASM])) {
            return redirect()->route('sub-wholesale.index')->with('success', 'Password updated successfully.');
        } elseif ((auth()->user()->type == AppHelper::SE || auth()->user()->type == AppHelper::SALE) && auth()->user()->role_id == AppHelper::USER_MANAGER) {
            return redirect()->route('dashboard.index')->with('success', 'Password updated successfully.');
        } else {
            return redirect()->route('dashboard.index')->with('success', 'Password updated successfully.');
        }
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
    public function handleLoginRedirect()
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            if ($user->type == AppHelper::SALE && in_array($user->role_id, [
                AppHelper::USER_EMPLOYEE, 
                AppHelper::USER_SUP, 
                AppHelper::USER_RSM, 
                AppHelper::USER_ASM
            ])) {
                return redirect()->route('report.index')
                    ->with('success', 'Welcome to CRM system.')
                    ->with('show_popup', true);
            } elseif ($user->type == AppHelper::SE && in_array($user->role_id, [
                AppHelper::USER_EMPLOYEE, 
                AppHelper::USER_SUP, 
                AppHelper::USER_RSM, 
                AppHelper::USER_ASM
            ])) {
                return redirect()->route('sub-wholesale.index')
                    ->with('success', 'Welcome to CRM system.')
                    ->with('show_popup', true);
            } elseif (($user->type == AppHelper::SE || $user->type == AppHelper::SALE) && 
                     $user->role_id == AppHelper::USER_MANAGER) {
                return redirect()->route('dashboard.index')
                    ->with('success', 'Welcome to CRM system.')
                    ->with('show_popup', true);
            } else {
                return redirect()->route('dashboard.index')
                    ->with('success', 'Welcome to AdminPanel.')
                    ->with('show_popup', true);
            }
        }

        return redirect()->route('login')->with('error', 'Please login to continue.');
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
            
            // Reuse the login redirect logic
            return $this->handleLoginRedirect();
        }

        return redirect()->route('lockscreen')->with('error', 'Invalid password.');
    }
}

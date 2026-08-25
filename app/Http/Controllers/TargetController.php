<?php

namespace App\Http\Controllers;

use App\Http\Helpers\AppHelper;
use App\Models\Target;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
class TargetController extends Controller
{
    public function __construct()
    {
        $this->middleware('type.permission:view target', ['only' => ['index']]);
        $this->middleware('type.permission:create target', ['only' => ['create', 'store']]);
        $this->middleware('type.permission:update target', ['only' => ['edit', 'update']]);
        $this->middleware('type.permission:delete target', ['only' => ['destroy']]);
    }
    public function index(Request $request)
    {
        $loggedInUser = auth()->user();
        $query = User::query()
            ->where('role_id', AppHelper::USER_EMPLOYEE);
            if ($loggedInUser->type == AppHelper::SALE) {

                if ($loggedInUser->role_id == AppHelper::USER_MANAGER) {

                    // Manager sees all employees under Sale
                    $query->where('type', AppHelper::SALE);

                } elseif ($loggedInUser->role_id == AppHelper::USER_RSM) {

                    $query->where('type', AppHelper::SALE)
                        ->where('rsm_id', $loggedInUser->id);

                } elseif ($loggedInUser->role_id == AppHelper::USER_ASM) {

                    $query->where('type', AppHelper::SALE)
                        ->where('asm_id', $loggedInUser->id);

                } elseif ($loggedInUser->role_id == AppHelper::USER_SUP) {

                    $query->where('type', AppHelper::SALE)
                        ->where('sup_id', $loggedInUser->id);

                } elseif ($loggedInUser->role_id == AppHelper::USER_EMPLOYEE) {

                    $query->where('id', $loggedInUser->id);
                }

            } elseif ($loggedInUser->type == AppHelper::SE) {

                if ($loggedInUser->role_id == AppHelper::USER_MANAGER) {

                    $query->where('type', AppHelper::SE);

                } elseif ($loggedInUser->role_id == AppHelper::USER_RSM) {

                    $query->where('type', AppHelper::SE)
                        ->where('rsm_id', $loggedInUser->id);

                } elseif ($loggedInUser->role_id == AppHelper::USER_ASM) {

                    $query->where('type', AppHelper::SE)
                        ->where('asm_id', $loggedInUser->id);

                } elseif ($loggedInUser->role_id == AppHelper::USER_SUP) {

                    $query->where('type', AppHelper::SE)
                        ->where('sup_id', $loggedInUser->id);

                } elseif ($loggedInUser->role_id == AppHelper::USER_EMPLOYEE) {

                    $query->where('id', $loggedInUser->id);
                }
            }

        $is_filter = false;
        $employees = $query->where('role_id',AppHelper::USER_EMPLOYEE)
            ->get(['id', 'username', 'family_name', 'name'])
            ->mapWithKeys(function ($user) {
                return [$user->id => $user->username . ' (' . $user->full_name . ')'];
            })
            ->toArray();
        
        // Date filter
        if ($request->filled(['date1', 'date2'])) {
            $is_filter = true;

            $startOfTarget = Carbon::parse($request->date1)->startOfDay();
            $endOfTarget = Carbon::parse($request->date2)->endOfDay();
        } else {
            $startOfTarget = now()->startOfMonth();
            $endOfTarget = now()->endOfMonth();
        }

        // User filter
        if ($request->filled('user_id')) {
            $is_filter = true;

            $query->where('id', $request->user_id);
        }
        if ($request->ajax()) {
            
            $targetQuery = $query->withSum([
                'report as sale_target' => function ($q) use ($startOfTarget, $endOfTarget) {
                    $q->whereBetween('created_at', [
                        $startOfTarget,
                        $endOfTarget
                    ])->select(DB::raw("
                        COALESCE(SUM(
                            COALESCE(`250_ml`, 0) +
                            COALESCE(`350_ml`, 0) +
                            COALESCE(`600_ml`, 0) +
                            COALESCE(`1500_ml`, 0)
                        ), 0)
                    "));
                }
            ], DB::raw('1'))
            ->orderByDesc('sale_target');   // Highest sale_target first

            return DataTables::of($targetQuery)
                ->addIndexColumn()
                ->filter(function ($query) use ($request) {

                    if ($request->filled('search.value')) {

                        $searchValue = trim(preg_replace('/\s+/', ' ', $request->search['value']));

                        $query->where(function ($q) use ($searchValue) {

                            $q->where('family_name', 'LIKE', "%{$searchValue}%")
                            ->orWhere('name', 'LIKE', "%{$searchValue}%")
                            ->orWhere('family_name_latin', 'LIKE', "%{$searchValue}%")
                            ->orWhere('name_latin', 'LIKE', "%{$searchValue}%")

                            // Khmer full name
                            ->orWhereRaw(
                                "TRIM(CONCAT(family_name, ' ', name)) LIKE ?",
                                ["%{$searchValue}%"]
                            )

                            // Latin full name
                            ->orWhereRaw(
                                "TRIM(CONCAT(family_name_latin, ' ', name_latin)) LIKE ?",
                                ["%{$searchValue}%"]
                            );
                        });
                    }
                })
                ->addColumn('employee', function ($user) {
                    return $user->user_lang == 'en'
                        ? $user->full_name_latin
                        : $user->full_name;
                })

                ->addColumn('sale_target', function ($user) {
                    return number_format($user->sale_target ?? 0) . ' (កេស)';
                })

                ->addColumn('rank', function ($user) {
                    $total = $user->sale_target ?? 0;

                    if ($total >= 3500) {
                        return '<span class="badge bg-success">Rank A</span>';
                    } elseif ($total >= 3000) {
                        return '<span class="badge bg-primary">Rank B</span>';
                    } elseif ($total >= 2600) {
                        return '<span class="badge bg-warning">Rank C</span>';
                    }

                    return '<span class="badge bg-secondary">No Rank</span>';
                })

                ->rawColumns(['rank'])
                ->make(true);
        }

        return view('backend.sale-target.list', compact('employees','is_filter'));
    }

    // public function create()
    // {
    //     $target = null;

    //     $loggedInUser = auth()->user();
    //     $loggedInUserRole = $loggedInUser->role_id;
    //     $loggedInUserId = $loggedInUser->id;
    //     $loggedInUserType = $loggedInUser->type;

    //     $users = User::with(['role', 'manager', 'supervisor'])
    //         ->select(
    //             'id',
    //             'family_name',
    //             'name',
    //             'family_name_latin',
    //             'name_latin',
    //             'role_id',
    //             'type',
    //             'status',
    //             'manager_id',
    //             'rsm_id',
    //             'asm_id',
    //             'sup_id'
    //         );
    //     if ($loggedInUserType == AppHelper::SALE) {
    //         if ($loggedInUserRole == AppHelper::USER_MANAGER) {
    //             $users->where(function ($q) use ($loggedInUserId) {
    //                 $q->whereIn('role_id', [
    //                     AppHelper::USER_RSM,
    //                     AppHelper::USER_ASM,
    //                     AppHelper::USER_SUP,
    //                     AppHelper::USER_EMPLOYEE
    //                 ])->where('type', AppHelper::SALE)
    //                     ->where('status', 1)
    //                     ->orWhere('id', $loggedInUserId);
    //             });
    //         } elseif ($loggedInUserRole == AppHelper::USER_RSM) {
    //             $users->where(function ($q) use ($loggedInUserId) {
    //                 $q->whereIn('role_id', [
    //                     AppHelper::USER_ASM,
    //                     AppHelper::USER_SUP,
    //                     AppHelper::USER_EMPLOYEE
    //                 ])
    //                     ->where('type', AppHelper::SALE)
    //                     ->where('rsm_id', $loggedInUserId)
    //                     ->where('status', 1);
    //             })
    //                 ->orWhere('id', $loggedInUserId);
    //         } elseif ($loggedInUserRole == AppHelper::USER_ASM) {
    //             $users->where(function ($q) use ($loggedInUserId) {
    //                 $q->whereIn('role_id', [
    //                     AppHelper::USER_SUP,
    //                     AppHelper::USER_EMPLOYEE
    //                 ])->where('type', AppHelper::SALE)
    //                     ->where('asm_id', $loggedInUserId)
    //                     ->where('status', 1);
    //             })
    //                 ->orWhere('id', $loggedInUserId);
    //         } elseif ($loggedInUserRole == AppHelper::USER_SUP) {
    //             $users->where(function ($q) use ($loggedInUserId) {
    //                 $q->where('role_id', AppHelper::USER_EMPLOYEE)
    //                     ->where('type', AppHelper::SALE)
    //                     ->where('sup_id', $loggedInUserId)
    //                     ->where('status', 1)
    //                     ->orWhere('id', $loggedInUserId);
    //             });
    //         } elseif ($loggedInUserRole == AppHelper::USER_EMPLOYEE) {
    //             $users->where('id', $loggedInUserId);
    //         }
    //     } elseif ($loggedInUserRole == AppHelper::USER_MANAGER) {
    //         // Non-SE/SALE Manager logic
    //         $users->where(function ($q) use ($loggedInUserId) {
    //             $q->where('id', $loggedInUserId)
    //                 ->orWhere('manager_id', $loggedInUserId);
    //         });
    //     } elseif ($loggedInUserRole == AppHelper::USER_ADMINISTRATOR) {
    //         // Adminministrator can see all except Super Admin
    //         $users->where(function ($q) use ($loggedInUserId) {
    //             $q->whereNotIn('role_id', [AppHelper::USER_SUPER_ADMIN])
    //                 ->orWhere('id', $loggedInUserId);
    //         });
    //     } elseif ($loggedInUserRole == AppHelper::USER_ADMIN) {
    //         // Admin can see all except Super Admin and administrator
    //         $users->where(function ($q) use ($loggedInUserId) {
    //             $q->whereNotIn('role_id', [AppHelper::USER_SUPER_ADMIN, AppHelper::USER_ADMINISTRATOR])
    //                 ->orWhere('id', $loggedInUserId);
    //         });
    //     } elseif ($loggedInUserRole == AppHelper::USER_DIRECTOR) {
    //         // Director can see all except Super Admin administrator admin
    //         $users->where(function ($q) use ($loggedInUserId) {
    //             $q->whereNotIn('role_id', [AppHelper::USER_SUPER_ADMIN, AppHelper::USER_ADMINISTRATOR, AppHelper::USER_ADMIN])
    //                 ->orWhere('id', $loggedInUserId);
    //         });
    //     } elseif (!in_array($loggedInUserRole, [AppHelper::USER_SUPER_ADMIN, AppHelper::USER_ADMIN])) {
    //         $users->where('id', $loggedInUserId);
    //     }
    //     $users = $users
    //         ->where('role_id', [
    //             AppHelper::USER_EMPLOYEE,
    //         ])
    //         ->orderBy('family_name')
    //         ->orderBy('name')
    //         ->get();
    //     return view('backend.sale-target.add', compact('target', 'users'));
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'user_ids' => 'required|array|min:1',
    //         'user_ids.*' => 'exists:users,id',
    //         'amount' => 'nullable|numeric|min:0',
    //         'amounts' => 'nullable|array',
    //     ]);

    //     if ($request->filled('amount')) {
    //         // Select All mode
    //         if ($request->amount === null) {
    //             return back()->withErrors([
    //                 'amount' => 'Amount is required.'
    //             ])->withInput();
    //         }
    //     } else {
    //         // Individual mode
    //         foreach ($request->user_ids as $userId) {
    //             if (empty($request->amounts[$userId])) {
    //                 return back()->withErrors([
    //                     "amounts.$userId" => "Amount is required."
    //                 ])->withInput();
    //             }
    //         }
    //     }

    //     foreach ($request->user_ids as $userId) {

    //         Target::create([
    //             'user_id'    => $userId,
    //             'amount'     => $request->amount,
    //             'created_by' => auth()->id(),
    //         ]);
    //     }

    //     return redirect()
    //         ->route('target.index')
    //         ->with('success', 'Sale Target created successfully.');
    // }
    // public function edit($id)
    // {
    //     $target = Target::findOrFail($id);


    //     // Get only this target user
    //     $users = User::select(
    //             'id',
    //             'family_name',
    //             'name',
    //             'family_name_latin',
    //             'name_latin'
    //         )
    //         ->where('id', $target->user_id)
    //         ->get();



    //     $selectedUserIds = [
    //         $target->user_id
    //     ];


    //     $userAmounts = [
    //         $target->user_id => $target->amount
    //     ];



    //     return view('backend.sale-target.add', compact(
    //         'target',
    //         'users',
    //         'selectedUserIds',
    //         'userAmounts'
    //     ));
    // }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'user_ids' => 'required|array|min:1',
    //         'user_ids.*' => 'exists:users,id',
    //         'amounts' => 'required|array',
    //     ]);


    //     $target = Target::findOrFail($id);


    //     // get selected user
    //     $userId = $request->user_ids[0];


    //     // get amount by user id
    //     $amount = $request->amounts[$userId] ?? 0;


    //     $target->update([
    //         'user_id' => $userId,
    //         'amount' => $amount,
    //         'created_by' => auth()->id(),
    //     ]);


    //     return redirect()
    //         ->route('target.index')
    //         ->with('success', 'Target updated successfully');
    // }
    
}

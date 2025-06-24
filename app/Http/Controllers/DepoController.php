<?php

namespace App\Http\Controllers;

use App\Http\Helpers\AppHelper;
use App\Models\Depo;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DepoController extends Controller
{
    public function __construct()
    {
        $this->middleware('type.permission:view depo', ['only' => ['index']]);
        $this->middleware('type.permission:create depo', ['only' => ['create', 'store']]);
        $this->middleware('type.permission:update depo', ['only' => ['edit', 'update']]);
        $this->middleware('type.permission:delete depo', ['only' => ['destroy']]);
    }
    public $indexof = 1;

    public function index(Request $request)
    {
        try {
            $loggedInUser = auth()->check() ? auth()->user() : null;

            if ($request->ajax()) {
                $query = Depo::with('user');

                if ($loggedInUser) {
                    $loggedInUserRole = $loggedInUser->role_id;
                    $loggedInUserId = $loggedInUser->id;
                    $loggedInUserType = $loggedInUser->type;

                    // Collect user IDs to filter depos
                    $userIds = [$loggedInUserId]; // Always include own depos

                    // Define allowed user types for subordinates
                    $allowedTypes = [AppHelper::SALE, AppHelper::SE];

                    if ($loggedInUserType == AppHelper::ALL || in_array($loggedInUserRole, [
                        AppHelper::USER_SUPER_ADMIN,
                        AppHelper::USER_ADMIN,
                        AppHelper::USER_DIRECTOR
                    ])) {
                        // No additional filtering needed
                    } elseif ($loggedInUserRole == AppHelper::USER_MANAGER) {
                        $managedUserIds = \App\Models\User::where(function ($q) use ($loggedInUserId) {
                            $q->where('manager_id', $loggedInUserId)
                              ->orWhere('rsm_id', $loggedInUserId)
                              ->orWhere('sup_id', $loggedInUserId)
                              ->orWhere('asm_id', $loggedInUserId);
                        })->whereIn('type', $allowedTypes)
                          ->pluck('id')
                          ->toArray();
                        $userIds = array_merge($userIds, $managedUserIds);
                    } elseif ($loggedInUserRole == AppHelper::USER_RSM) {
                        $managedUserIds = \App\Models\User::where(function ($q) use ($loggedInUserId) {
                            $q->where('rsm_id', $loggedInUserId)
                              ->orWhere('sup_id', $loggedInUserId)
                              ->orWhere('asm_id', $loggedInUserId);
                        })->whereIn('type', $allowedTypes)
                          ->pluck('id')
                          ->toArray();
                        $userIds = array_merge($userIds, $managedUserIds);
                    } elseif ($loggedInUserRole == AppHelper::USER_SUP) {
                        $managedUserIds = \App\Models\User::where(function ($q) use ($loggedInUserId) {
                            $q->where('sup_id', $loggedInUserId)
                              ->orWhere('asm_id', $loggedInUserId);
                        })->whereIn('type', $allowedTypes)
                          ->pluck('id')
                          ->toArray();
                        $userIds = array_merge($userIds, $managedUserIds);
                    } elseif ($loggedInUserRole == AppHelper::USER_ASM) {
                        $managedUserIds = \App\Models\User::where('asm_id', $loggedInUserId)
                            ->whereIn('type', $allowedTypes)
                            ->pluck('id')
                            ->toArray();
                        $userIds = array_merge($userIds, $managedUserIds);
                    }

                    // Apply user ID filter unless Super Admin, Admin, Director, or type ALL
                    if (!($loggedInUserType == AppHelper::ALL || in_array($loggedInUserRole, [
                        AppHelper::USER_SUPER_ADMIN,
                        AppHelper::USER_ADMIN,
                        AppHelper::USER_DIRECTOR
                    ]))) {
                        $query->whereIn('user_id', array_unique($userIds));
                    }

                    // Ensure depos belong to users with allowed types
                    if (!($loggedInUserType == AppHelper::ALL || in_array($loggedInUserRole, [
                        AppHelper::USER_SUPER_ADMIN,
                        AppHelper::USER_ADMIN,
                        AppHelper::USER_DIRECTOR
                    ]))) {
                        $query->whereHas('user', function ($q) use ($allowedTypes) {
                            $q->whereIn('type', $allowedTypes);
                        });
                    }
                } else {
                    // No authenticated user, return no depos
                    $query->where('id', 0);
                }

                $depos = $query->orderBy('id', 'desc');
                return DataTables::of($depos)
                    ->addIndexColumn()
                    ->addColumn('created_by', function ($depo) {
                        return $depo->user ? ($depo->user->user_lang === 'en' ? ($depo->user->full_name_latin ?? 'N/A') : ($depo->user->user_lang === 'kh' ? ($depo->user->full_name ?? 'N/A') : 'N/A')) : 'N/A';
                    })
                    ->addColumn('area', fn($depo) => AppHelper::getAreaNameById($depo->area_id) ?? 'N/A')
                    ->addColumn('name', function ($depo) {
                        return $depo->name ?? 'N/A';
                    })
                    ->addColumn('action', function ($depo) {
                        $button = '<div class="change-action-item">';
                        $button .= '<a title="Edit" href="' . route('depo.edit', $depo->id) . '" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>';
                        $button .= '<a href="' . route('depo.destroy', $depo->id) . '" class="btn btn-danger btn-sm delete" title="Delete"><i class="fa fa-fw fa-trash"></i></a>';
                        $button .= '</div>';
                        return $button;
                    })
                    ->rawColumns(['action', 'created_by', 'name'])
                    ->make(true);
            }

            return view('backend.depo.list');
        } catch (\Exception $e) {
            Log::error('DataTables AJAX Error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred. Please check the server logs.'], 500);
        }
    }


    public function create()
    {
        $depo = null; // Assuming $depo is used for editing; null for create
        $depos = [];
        // If there's old input or a pre-selected area, fetch depos
        $areaId = old('area', $depo->area_id ?? '');
        if ($areaId) {
            $depos = Depo::where('area_id', $areaId)->get(['id', 'name']);
        }
        return view('backend.depo.add', compact('depo', 'depos'));
    }


    public function store(Request $request)
    {
        $areaIds = [];
        foreach (AppHelper::getAreas() as $group) {
            $areaIds = array_merge($areaIds, array_keys($group));
        }

        $rules = [
            'area' => 'required|in:' . implode(',', $areaIds),
            'name' => 'required|string|max:255',
        ];

        $this->validate($request, $rules);

        // Store report data
        Depo::create([
            'user_id' => auth()->id(),
            'area_id' => $request->area,
            'user_type' => auth()->user()->type,
            'name' => $request->name,
        ]);
        if ($request->has('saveandcontinue')) {
            return redirect()->route('depo.create')->with('success', 'Depo created successfully.');
        }
        return redirect()->route('depo.index')->with('success', 'Depo created successfully.');
    }


    public function edit($id)
    {
        $depo = Depo::findOrFail($id);
        if (!$depo) {
            return redirect()->route('depo.index')->with('error', 'Depo not found.');
        }

        // Fetch depos for the depo's area
        $depos = Depo::where('area_id', $depo->area_id)->get(['id', 'name']);
        return view('backend.depo.add', compact('depo', 'depos'));
    }


    public function update(Request $request, $id)
    {
        $depo = Depo::findOrFail($id);
        if (!$depo) {
            return redirect()->route('depo.index')->with('error', 'Depo not found!');
        }

        // Get all valid area IDs (numeric keys)
        $areaIds = [];
        foreach (AppHelper::getAreas() as $group) {
            $areaIds = array_merge($areaIds, array_keys($group));
        }

        // Validation rules
        $rules = [
            'area' => 'required|in:' . implode(',', $areaIds),
            'name' => 'required|string|max:255',
        ];
        $this->validate($request, $rules);
        $data = [
            'area_id' => $request->area,
            'name' => $request->name,
        ];
        // Update depo
        $depo->update($data);

        return redirect()->route('depo.index')->with('success', 'Depo has been updated!');
    }





    public function destroy($id)
    {
        $depo = Depo::find($id);
        if ($depo) {
            $depo->delete();
            return redirect()->back()->with('success', "Depo has been deleted!");
        }
        return redirect()->back()->with('error', "Depo not found!");
    }

}

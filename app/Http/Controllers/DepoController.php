<?php

namespace App\Http\Controllers;

use App\Http\Helpers\AppHelper;
use App\Models\Depo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
                    $loggedInUserArea = $loggedInUser->area;

                    $allowedTypes = [AppHelper::SALE, AppHelper::SE];
                    $userIds = [$loggedInUserId]; // Always include self

                    // Full-access roles — no restrictions
                    if (
                        $loggedInUserType == AppHelper::ALL ||
                        in_array($loggedInUserRole, [
                            AppHelper::USER_SUPER_ADMIN,
                            AppHelper::USER_ADMINISTRATOR,
                            AppHelper::USER_ADMIN,
                            AppHelper::USER_DIRECTOR
                        ])
                    ) {
                        // No filter for full-access rolesm,
                    } else {
                        // Find all managed users by hierarchy or same area
                        $managedUsers = User::query()
                            ->whereIn('type', $allowedTypes)
                            ->where(function ($q) use ($loggedInUser, $loggedInUserArea) {
                                $q->where('manager_id', $loggedInUser->id)
                                    ->orWhere('rsm_id', $loggedInUser->id)
                                    ->orWhere('asm_id', $loggedInUser->id)
                                    ->orWhere('sup_id', $loggedInUser->id)
                                    ->orWhere('area', $loggedInUserArea);
                            })
                            ->pluck('id')
                            ->toArray();

                        $userIds = array_unique(array_merge($userIds, $managedUsers));

                        // 🔹 FIXED: Get allowed area_ids based on user's area
                        $allowedAreaIds = $this->getAllowedAreaIdsForUserArea($loggedInUserArea);

                        $query->where(function ($q) use ($userIds, $allowedAreaIds, $loggedInUserRole, $loggedInUserArea) {
                            $q->whereIn('user_id', $userIds);

                            // Filter by allowed area_ids (the numeric keys from AREAS array)
                            if (!empty($allowedAreaIds)) {
                                $q->orWhereIn('area_id', $allowedAreaIds);
                            }

                            // Additional role-based area filtering
                            if (in_array($loggedInUserRole, [AppHelper::USER_MANAGER, AppHelper::USER_RSM])) {
                                // For RSM, get all area_ids under the parent region (R1 → R1-01, R1-02)
                                $parentArea = explode('-', $loggedInUserArea)[0]; // Extract "R1" from "R1-01"
                                $parentAreaIds = $this->getAreaIdsForParentRegion($parentArea);
                                if (!empty($parentAreaIds)) {
                                    $q->orWhereIn('area_id', $parentAreaIds);
                                }
                            }
                        });
                    }

                    // Restrict to allowed user types
                    $query->whereHas('user', function ($q) use ($allowedTypes) {
                        $q->whereIn('type', $allowedTypes);
                    });
                } else {
                    // No user logged in → no data
                    $query->where('id', 0);
                }

                $depos = $query->orderBy('id', 'desc')->get();
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
                        $actions = false;
                        if (auth()->user()->role_id == AppHelper::USER_SUPER_ADMIN || auth()->user()->role_id == AppHelper::USER_ADMINISTRATOR || auth()->user()->role_id == AppHelper::USER_DIRECTOR || auth()->user()->role_id == AppHelper::USER_ADMIN || auth()->user()->role_id == AppHelper::USER_MANAGER || auth()->user()->role_id == AppHelper::USER_SUP) {
                            $button .= '<a title="Edit" href="' . route('depo.edit', $depo->id) . '" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>';
                            $actions = true;
                        }
                        if (auth()->user()->can('delete depo')) {
                            $button .= '<a href="' . route('depo.destroy', $depo->id) . '" class="btn btn-danger btn-sm delete" title="Delete"><i class="fa fa-fw fa-trash"></i></a>';
                            $actions = true;
                        }
                        if (!$actions) {
                            $button .= '<span style="font-weight:bold; color:red;">No Action</span>';
                        }
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

    /**
     * Get allowed area_ids (numeric keys) for a user's area
     */
    private function getAllowedAreaIdsForUserArea($userArea)
    {
        $areaData = AppHelper::getAreas();
        $allowedAreaIds = [];

        foreach ($areaData as $areaName => $areaMapping) {
            // Extract area code from area name (e.g., "Ussa (R1-01)" → "R1-01")
            preg_match('/\((.*?)\)/', $areaName, $matches);
            $areaCode = $matches[1] ?? $areaName;

            // Check if this area matches the user's area
            if ($areaCode === $userArea) {
                // Get all numeric keys for this area
                $allowedAreaIds = array_merge($allowedAreaIds, array_keys($areaMapping));
            }
        }

        return $allowedAreaIds;
    }

    /**
     * Get all area_ids for a parent region (e.g., R1 → all R1-01, R1-02 area_ids)
     */
    private function getAreaIdsForParentRegion($parentRegion)
    {
        $areaData = AppHelper::getAreas();
        $areaIds = [];

        foreach ($areaData as $areaName => $areaMapping) {
            // Extract area code from area name
            preg_match('/\((.*?)\)/', $areaName, $matches);
            $areaCode = $matches[1] ?? $areaName;

            // Check if this area belongs to the parent region
            if (strpos($areaCode, $parentRegion . '-') === 0) {
                // Get all numeric keys for this area
                $areaIds = array_merge($areaIds, array_keys($areaMapping));
            }
        }

        return $areaIds;
    }


    public function create()
    {
        $depo = null;
        $depos = [];

        $user = auth()->user();
        $userAreaCode = $user->area ?? null; // Example: "R1", "R2", "R2-02", or null

        $areas = AppHelper::getAreas();

        // Only filter if area is defined and matches pattern R1 / R2 / R1-01 / R2-02
        if ($userAreaCode && preg_match('/^R\d(-\d{2})?$/', $userAreaCode)) {
            $areas = collect($areas)
                ->filter(function ($subItems, $areaName) use ($userAreaCode) {
                    // If userAreaCode = "R1" → include "R1-"
                    if (preg_match('/^R\d$/', $userAreaCode)) {
                        return str_contains($areaName, $userAreaCode . '-');
                    }

                    // If userAreaCode = "R1-01" → include exact match
                    return str_contains($areaName, $userAreaCode);
                })
                ->toArray();
        }
        // else → show all (no filtering)

        // Handle depos if area selected
        $areaId = old('area', $depo->area_id ?? '');
        if ($areaId) {
            $depos = Depo::where('area_id', $areaId)->get(['id', 'name']);
        }

        return view('backend.depo.add', compact('depo', 'depos', 'areas'));
    }



    public function store(Request $request)
    {
        $areaIds = [];
        foreach (AppHelper::getAreas() as $group) {
            $areaIds = array_merge($areaIds, array_keys($group));
        }

        $rules = [
            'area' => 'required|in:' . implode(',', $areaIds),
            'name' => 'required|unique:depos,name,NULL,id,area_id,' . $request->area . '|string|max:255',
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
        $areas = AppHelper::getAreas();
        // Fetch depos for the depo's area
        $depos = Depo::where('area_id', $depo->area_id)->get(['id', 'name']);
        return view('backend.depo.add', compact('depo', 'depos', 'areas'));
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
            'name' => 'required|unique:depos,name,' . $depo->id . ',id,area_id,' . $request->area . '|string|max:255',
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

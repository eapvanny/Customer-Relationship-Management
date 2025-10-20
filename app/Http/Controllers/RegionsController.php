<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;
use App\Http\Helpers\AppHelper;
use Illuminate\Support\Facades\Log;

class RegionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('type.permission:view region', ['only' => ['index']]);
        $this->middleware('type.permission:create region', ['only' => ['create', 'store']]);
        $this->middleware('type.permission:update region', ['only' => ['update', 'edit']]);
        $this->middleware('type.permission:delete region', ['only' => ['destroy']]);
    }
    public function index()
    {
        try {
            $loggedInUser = auth()->check() ? auth()->user() : null;

            $query = Region::with('user');

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
                    AppHelper::USER_ADMINISTRATOR,
                    AppHelper::USER_ADMIN,
                    AppHelper::USER_DIRECTOR,
                    AppHelper::USER_MANAGER
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
                // if (!($loggedInUserType == AppHelper::ALL || in_array($loggedInUserRole, [
                //     AppHelper::USER_SUPER_ADMIN,
                //     AppHelper::USER_ADMINISTRATOR,
                //     AppHelper::USER_ADMIN,
                //     AppHelper::USER_DIRECTOR
                // ]))) {
                //     $query->whereIn('user_id', array_unique($userIds));
                // }

                // Ensure depos belong to users with allowed types
                if (!($loggedInUserType == AppHelper::ALL || in_array($loggedInUserRole, [
                    AppHelper::USER_SUPER_ADMIN,
                    AppHelper::USER_ADMINISTRATOR,
                    AppHelper::USER_ADMIN,
                    AppHelper::USER_DIRECTOR,
                    AppHelper::USER_MANAGER

                ]))) {
                    $query->whereHas('user', function ($q) use ($allowedTypes) {
                        $q->whereIn('type', $allowedTypes);
                    });
                }
            } else {
                // No authenticated user, return no depos
                $query->where('id', 0);
            }

            $data['regions'] = $query->orderBy('region_name', 'asc')->get();
            return view('backend.region.list', $data);

        } catch (\Exception $e) {
            Log::error('DataTables AJAX Error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred. Please check the server logs.'], 500);
        }

        // ++++++++++++++
        // $data['regions'] = Region::with('user')->orderBy('region_name', 'asc')->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['region'] = false;
        return view('backend.region.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->input());
        $active_status = '';

        $request->validate([
            'region_name' => 'required|string',
            'sd_name' => 'required|string',
            'sm_name' => 'required|string',
            'se_code' => 'required|string|unique:regions,se_code',
            'rg_manager_kh' => 'required|string',
            'rg_manager_en' => 'required|string',
            'province' => 'required|string',
        ]);

        if ($request->has('active_status')) {
            $active_status = 1;
        } else {
            $active_status = 0;
        }

        $addRegion = Region::create([
            'region_name' => $request->region_name,
            'sm_name' => $request->sm_name,
            'sd_name' => $request->sd_name,
            'se_code' => $request->se_code,
            'rg_manager_kh' => $request->rg_manager_kh,
            'rg_manager_en' => $request->rg_manager_en,
            'province'  => $request->province,
            'active_status'  => $active_status,
            'created_by' => auth()->user()->id,
        ]);

        if ($addRegion == true) {
            return redirect()->back()->with('success', __("Add regionn has successfully."));
        } else {
            return redirect()->back()->with('error', __('Failed to add region.'))->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['region'] = Region::findOrFail($id);
        return view('backend.region.add', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = Region::findOrFail($id);
        $active_status = '';

        $request->validate([
            'region_name' => 'required|string',
            'sd_name' => 'required|string',
            'sm_name' => 'required|string',
            'se_code' => 'required|string|unique:regions,se_code,' . $id . ',id',
            'rg_manager_kh' => 'required|string',
            'rg_manager_en' => 'required|string',
            'province' => 'required|string',
        ]);

        if ($request->has('active_status')) {
            $active_status = 1;
        } else {
            $active_status = 0;
        }

        $updateRegion = $item->update([
            'region_name' => $request->region_name,
            'sm_name' => $request->sm_name,
            'sd_name' => $request->sd_name,
            'se_code' => $request->se_code,
            'rg_manager_kh' => $request->rg_manager_kh,
            'rg_manager_en' => $request->rg_manager_en,
            'province'  => $request->province,
            'active_status'  => $active_status,
        ]);

        if ($updateRegion == true) {
            return redirect()->route('region.index')->with('success', __("Update regionn has successfully."));
        } else {
            return redirect()->back()->with('error', __('Failed to update region.'))->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

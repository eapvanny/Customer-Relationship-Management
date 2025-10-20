<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Region;
use Illuminate\Http\Request;
use App\Http\Helpers\AppHelper;
use Illuminate\Support\Facades\Log;

class OutletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('type.permission:view depot management', ['only' => ['index']]);
        $this->middleware('type.permission:create depot management', ['only' => ['create', 'store']]);
        $this->middleware('type.permission:update depot management', ['only' => ['update', 'edit']]);
        $this->middleware('type.permission:delete depot management', ['only' => ['destroy']]);
    }
    public function index(Request $request)
    {
        try {
            $loggedInUser = auth()->check() ? auth()->user() : null;

            $query = Outlet::with('user', 'region');

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

            $data['outlets'] = $query->orderBy('id', 'desc')->get();
            return view('backend.outlet.list', $data);
        } catch (\Exception $e) {
            Log::error('DataTables AJAX Error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred. Please check the server logs.'], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['outlet'] = false;
        // $data['regions'] = Region::where('active_status', 1)->orderBy('region_name', 'asc')->get();

        // $data['regions'] = Region::where('active_status', 1)
        // ->select('region_name')  // Select only the region_name column
        // ->distinct()             // Get distinct region names
        // ->orderBy('region_name', 'asc')
        // ->get();
        $data['regions'] = Region::where('active_status', 1)
            ->select('region_name', 'rg_manager_kh', 'rg_manager_en', 'se_code', 'id')
            ->orderBy('region_name', 'asc')
            ->get()
            ->groupBy('rg_manager_kh');

        // echo '<pre>';
        // foreach ($data['regions'] as $regionName => $regions) {
        //     foreach ($regions as $region) {
        //         echo $region->rg_manager_kh . "\n"; // Access the rg_manager_kh property correctly
        //     }
        // }
        // echo '</pre>';

        return view('backend.outlet.add', $data);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->input());
        $active_status = '';

        $request->validate([
            'region' => 'required|string',
            'name' => 'required|string',
        ]);
        if ($request->has('active_status')) {
            $active_status = 1;
        } else {
            $active_status = 0;
        }

        $addOutlet = Outlet::create([
            'user_id' => auth()->id(),
            'area_id' => $request->region,
            'user_type' => auth()->user()->type,
            'name' => $request->name,
            'active_status' => $active_status
        ]);

        if ($addOutlet == true) {
            return redirect()->back()->with('success', 'Outlet created successfully.');
        } else {
            return redirect()->back()->with('error', 'Outlet created not successfully.')->withInput();
        }
        // if ($request->has('saveandcontinue')) {
        //     return redirect()->route('depo.create')->with('success', 'Depo created successfully.');
        // }
        // return redirect()->route('depo.index')->with('success', 'Depo created successfully.');
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
        $data['outlet'] = Outlet::findOrFail($id);

        $data['regions'] = Region::where('active_status', 1)
            ->select('region_name', 'rg_manager_kh', 'rg_manager_en', 'se_code', 'id')
            ->orderBy('region_name', 'asc')
            ->get()
            ->groupBy('rg_manager_kh');


        return view('backend.outlet.add', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = Outlet::findOrFail($id);
        $active_status = '';

        $request->validate([
            'region' => 'required|string',
            'name' => 'required|string',
        ]);
        if ($request->has('active_status')) {
            $active_status = 1;
        } else {
            $active_status = 0;
        }

        $updateOutlet = $item->update([
            'user_id' => auth()->id(),
            'area_id' => $request->region,
            'name' => $request->name,
            'active_status' => $active_status
        ]);

        if ($updateOutlet == true) {
            return redirect()->route('outlet.index')->with('success', 'Outlet updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Outlet updated not successfully.')->withInput();
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

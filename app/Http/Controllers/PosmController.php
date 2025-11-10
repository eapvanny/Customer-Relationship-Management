<?php

namespace App\Http\Controllers;

use App\Models\Posm;
use App\Exports\PosmExport;
use Illuminate\Http\Request;
use App\Http\Helpers\AppHelper;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PosmController extends Controller
{
     public function __construct()
    {
        $this->middleware('type.permission:view posm', ['only' => ['index']]);
        $this->middleware('type.permission:create posm', ['only' => ['create', 'store']]);
        $this->middleware('type.permission:update posm', ['only' => ['update', 'edit']]);
        $this->middleware('type.permission:delete posm', ['only' => ['destroy']]);
    }
    public function index()
    {
        try {
            $data['posms'] = Posm::with(['creator', 'updater', 'deleter'])->orderBy('id', 'desc')->get();
            return view('backend.posm.list', $data);

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
        $data['posm'] = false;
        return view('backend.posm.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->input());

        $request->validate([
            'code' => 'required|string|max:255|min:3|unique:posms,code',
            'name_kh' => 'required|string|max:255|min:3',
            'name_en' => 'required|string|max:255|min:3',
            'status' => 'nullable|in:0,1',
        ]);
        if($request->has('status')){
            $status = 1;
        }else{
            $status = 0;
        }
        $posm = Posm::create([
            'code' => $request->input('code'),
            'name_kh' => $request->input('name_kh'),
            'name_en' => $request->input('name_en'),
            'status' => $status,
            'created_by' => auth()->user() ? auth()->user()->id : null,
        ]);

        if ($posm) {
            return redirect()->back()->with('success', __('POSM created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Failed to create POSM. Please try again.'));
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
        $data['posm'] = Posm::findOrFail($id);
        return view('backend.posm.add', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd($request->input());
        $posm = Posm::findOrFail($id);
        $request->validate([
            'code' => 'required|string|max:255|min:3|unique:posms,code,'.$id.',id',
            'name_kh' => 'required|string|max:255|min:3',
            'name_en' => 'required|string|max:255|min:3',
            'status' => 'nullable|in:0,1',
        ]);
        if($request->has('status')){
            $status = 1;
        }else{
            $status = 0;
        }
        $posm->update([
            'code' => $request->input('code'),
            'name_kh' => $request->input('name_kh'),
            'name_en' => $request->input('name_en'),
            'status' => $status,
            'created_by' => auth()->user() ? auth()->user()->id : null,
        ]);

        // if ($posm) {
        return redirect()->route('posm.index')->with('success', __('POSM updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function export()
    {
        $fileName = 'posm_list_' . date('Y_m_d_H_i_s') . '.xlsx';
        return Excel::download(new PosmExport, $fileName);
    }
}

<?php

namespace App\Exports;

use App\Models\Outlet;
use App\Http\Helpers\AppHelper;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;

class OutletExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        // $loggedInUser = Auth::check() ? Auth::user() : null;
        $query = Outlet::with('user', 'region')->orderBy('id', 'desc')->get();
        return view('exports.outlet-export', [
            'rows' => $query,
            'title' => 'Depot Lists',
        ]);
    }
}

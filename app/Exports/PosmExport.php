<?php

namespace App\Exports;

use App\Models\Posm;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PosmExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        $posm = Posm::all();
        return view('exports.posm-export', [
            'posms' => $posm
        ]);
    }
}

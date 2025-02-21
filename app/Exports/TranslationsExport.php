<?php

namespace App\Exports;

use App\Models\Translation;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class TranslationsExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        return view('exports.translations', [
            'rows' => Translation::all()
        ]);
    }

}

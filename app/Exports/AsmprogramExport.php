<?php

namespace App\Exports;

use App\Http\Helpers\AppHelper;
use App\Models\Asm_program;
// use App\Models\Report;
use App\Models\Sub_wholesale;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\Auth;

class AsmprogramExport implements FromView
{
    /**
    * @return \Illuminate\Contracts\View\View
    */
    public function view(): View
    {
        // dd('Hi');
        $user = Auth::user();

        $query = Asm_program::with('user', 'customer')->orderBy('id', 'desc')->get();
        // dd($query);

        // if ($user->role_id === AppHelper::USER_MANAGER) {
        //     $query->whereHas('user', function ($q) use ($user) {
        //         $q->where('manager_id', $user->id);
        //     });
        // } elseif ($user->role_id !== AppHelper::USER_SUPER_ADMIN && $user->role_id !== AppHelper::USER_ADMIN) {
        //     $query->where('user_id', $user->id);
        // }

        return view('exports.asm-program', [
            'rows' => $query
        ]);
    }
}

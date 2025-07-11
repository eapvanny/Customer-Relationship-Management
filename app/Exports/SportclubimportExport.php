<?php

namespace App\Exports;

use App\Http\Helpers\AppHelper;
use App\Models\Report;
use App\Models\School;
use App\Models\School_import;
use App\Models\Sport_club_import;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\Auth;

class SportclubimportExport implements FromView
{
    /**
    * @return \Illuminate\Contracts\View\View
    */
    public function view(): View
    {
        $user = Auth::user();

        $query = Sport_club_import::with('user');

        if ($user->role_id === AppHelper::USER_MANAGER) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('manager_id', $user->id);
            });
        } elseif ($user->role_id !== AppHelper::USER_SUPER_ADMIN && $user->role_id !== AppHelper::USER_ADMIN) {
            $query->where('user_id', $user->id);
        }

        return view('exports.sportclubimportExport', [
            'rows' => $query->get()
        ]);
    }
}

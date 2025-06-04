<?php

namespace App\Exports;

use App\Http\Helpers\AppHelper;
use App\Models\Retail;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\Auth;

class RetailExport implements FromView
{
    /**
    * @return \Illuminate\Contracts\View\View
    */
    public function view(): View
    {
        $user = Auth::user();
        $query = Retail::with('user')->orderBy('id', 'desc');

        if ($user->role_id === AppHelper::USER_MANAGER) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('manager_id', $user->id);
            });
        } elseif ($user->role_id !== AppHelper::USER_SUPER_ADMIN && $user->role_id !== AppHelper::USER_ADMIN) {
            $query->where('user_id', $user->id);
        }

        return view('exports.retail', [
            'rows' => $query->get()
        ]);
    }
}

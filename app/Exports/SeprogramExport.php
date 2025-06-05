<?php

namespace App\Exports;

use App\Models\Se_program;
use App\Http\Helpers\AppHelper;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;

class SeprogramExport implements FromView
{
    /**
    * @return \Illuminate\Contracts\View\View
    */
    protected $date1;
    protected $date2;
    protected $fullname;

    public function __construct($date1, $date2, $fullname)
    {
        $this->date1 = $date1;
        $this->date2 = $date2;
        $this->fullname = $fullname;
    }

    public function view(): View
    {
        $user = Auth::user();
        $query = Se_program::with('user', 'customer')->orderBy('id', 'desc');

        // Format dates
        $date1 = $this->date1 ? date('Y-m-d', strtotime($this->date1)) : null;
        $date2 = $this->date2 ? date('Y-m-d', strtotime($this->date2)) : null;

        // Apply date range filter
        if ($date1 && $date2) {
            $query->whereBetween('created_at', [$date1, $date2]);
        }

        // Apply user filter
        if ($this->fullname) {
            $query->where('user_id', $this->fullname);
        }

        // Uncomment the following logic if you want to apply role-based filtering
        if ($user->role_id === AppHelper::USER_SE_MANAGER) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('manager_id', $user->id);
            });
        } elseif ($user->role_id !== AppHelper::USER_SUPER_ADMIN && $user->role_id !== AppHelper::USER_ADMIN) {
            $query->where('user_id', $user->id);
        }

        return view('exports.asm-program', [
            'rows' => $query->get()
        ]);
    }
}

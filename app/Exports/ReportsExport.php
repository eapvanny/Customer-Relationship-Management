<?php

namespace App\Exports;

use App\Http\Helpers\AppHelper;
use App\Models\Report;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\Auth;

class ReportsExport implements FromView
{
    protected $date1;
    protected $date2;
    protected $fullname;

    public function __construct($date1, $date2, $fullname)
    {
        $this->date1 = $date1;
        $this->date2 = $date2;
        $this->fullname = $fullname;
    }
    /**
    * @return \Illuminate\Contracts\View\View
    */
    public function view(): View
    {
        $user = Auth::user();
        
        $query = Report::with('user','customer')->orderBy('id', 'desc');
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

        
        if ($user->role_id === AppHelper::USER_MANAGER) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('manager_id', $user->id);
            });
        } elseif ($user->role_id !== AppHelper::USER_SUPER_ADMIN && $user->role_id !== AppHelper::USER_ADMIN) {
            $query->where('user_id', $user->id);
        }

        return view('exports.reports', [
            'rows' => $query->get()
        ]);
    }
}

<?php

namespace App\Exports;

use App\Http\Helpers\AppHelper;
use App\Models\Report;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;

class ReportsExport implements FromView
{
    protected $date1;
    protected $date2;
    protected $user_id;
    protected $area_id;

    public function __construct($date1, $date2, $user_id, $area_id)
    {
        $this->date1 = $date1;
        $this->user_id = $user_id;
        $this->date2 = $date2;
        $this->area_id = $area_id;
    }

    public function view(): View
    {
        $user = Auth::check() ? Auth::user() : null;
        $query = Report::with(['user', 'customer'])->orderBy('id', 'desc');

        // Apply date range filter
        if ($this->date1 && $this->date2) {
            $startDate = \Carbon\Carbon::parse($this->date1)->startOfDay();
            $endDate = \Carbon\Carbon::parse($this->date2)->endOfDay();
            $query->whereBetween('date', [$startDate, $endDate]);
        }
        // Apply user_id filter
        if ($this->user_id) {
            $query->where('user_id', $this->user_id);
        }

        // Apply area_id filter
        if ($this->area_id) {
            $query->where('area_id', $this->area_id);
        }

        return view('exports.reports', [
            'rows' => $query->get()
        ]);
    }
}
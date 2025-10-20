<?php

namespace App\Imports;

use App\Models\Wholesale;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class WholesaleImport implements ToCollection, WithHeadingRow
{
    protected $employee_id;

    public function __construct($employee_id)
    {
        $this->employee_id = $employee_id;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            Wholesale::create([
                'region'            => $row['region'],
                'location'          => $row['location'],
                'sm_name'           => $row['sm_name'],
                'rsm_name'          => $row['rsm_name'],
                'asm_name'          => $row['asm_name'],
                'se_name'           => $row['se_name'],
                'se_code'           => $row['se_code'],
                'customer_code'     => $row['customer_code'],
                'depo_contact'      => $row['depo_contact'],
                'depo_name'         => $row['depo_name'],
                'wholesale_name'    => $row['wholesale_name'],
                'wholesale_contact' => $row['wholesale_contact'],
                'business_type'     => $row['business_type'],
                'sale_kpi'          => $row['sale_kpi'],
                'display_qty'       => $row['display_qty'],
                'foc_qty'           => $row['foc_qty'],
                'remark'            => $row['remark'],
                'apply_user'        => $this->employee_id,
                'creater'           => Auth::id(),
            ]);
        }
    }
}

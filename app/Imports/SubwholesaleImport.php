<?php

namespace App\Imports;

use App\Models\Display_subwholesale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SubwholesaleImport implements ToCollection, WithHeadingRow
{
    protected $employee_id;

    public function __construct($employee_id)
    {
        $this->employee_id = $employee_id;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            Display_subwholesale::create([
                'region' => $row['region'] ?? null,
                'province' => $row['province'] ?? null,
                'district' => $row['district'] ?? null,
                'commune' => $row['commune'] ?? null,
                'sm_name' => $row['sm_name'] ?? null,
                'rsm_name' => $row['rsm_name'] ?? null,
                'asm_name' => $row['asm_name'] ?? null,
                'se_name' => $row['se_name'] ?? null,
                'se_code' => $row['se_code'] ?? null,
                'customer_code' => $row['customer_code'] ?? null,
                'depot_contact' => $row['depot_contact'] ?? null,
                'depot_name' => $row['depot_name'] ?? null,
                'sub_ws_name' => $row['sub_ws_name'] ?? null,
                'sub_ws_contact' => $row['sub_ws_contact'] ?? null,
                'outlet_type' => $row['outlet_type'] ?? null,
                'sale_kpi' => $row['sale_kpi'] ?? null,
                'display_qty' => $row['display_qty'] ?? null,
                'sku' => $row['sku'] ?? null,
                'incentive' => $row['incentive'] ?? null,
                'remark' => $row['remark'] ?? null,
                'apply_user' => $this->employee_id,
            ]);
        }
    }
}

<?php

namespace App\Imports;

use App\Models\Retail;
use App\Models\Retail_import;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RetailImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {
            Retail::create([
                'region' => $row["region"],
                'asm_name' => $row["asmname"],
                'sup_name' => $row["supname"],
                'se_name' => $row["sename"],
                'customer_name' => $row["customername"],
                'contact_number' => $row["contactnumber"],
                'business_type' => $row["businesstype"],
                'ams' => $row["ams"],
                'display_parasol' => $row["displayparasol"],
                'foc' => $row["foc600ml"],
                'installation' => $row["installation"],
                'user_id' => Auth::id(),
            ]);
        }
    }
}

<?php

namespace App\Imports;

use App\Models\Se_program;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SeprogramImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            Se_program::create([
                // 'name' => $row[0],
                'area' => $row['area'],
                'outlet' => $row['outlet'],
                'customer' => $row['customer'],
                'customer_type' => $row['customer_type'],
                '250_ml' => $row['250ml'],
                '350_ml' => $row['350ml'],
                '600_ml' => $row['600ml'],     
                '1500_ml' => $row['1500ml'],
                'date' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date'])->format('Y-m-d'),
                'phone' => $row['phone'],
                'other' => $row['other'],
                'latitude' => $row['latitude'],
                'longitude' => $row['longitude'],
                'city' => $row['city'],
            ]);
        }
    }
}

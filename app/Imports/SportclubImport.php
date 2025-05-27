<?php

namespace App\Imports;

use App\Models\Retail_import;
use App\Models\School_import;
use App\Models\Sport_club_import;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class  SportclubImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {
            Sport_club_import::create([
                // 'name' => $row[0],
                'area_id' => $row['area'],
                'outlet_id' => $row['outlet'],
                'customer_id' => $row['customer'],
                'customer_type' => $row['customer_type'],
                '250_ml' => $row['250ml'],
                '350_ml' => $row['350ml'],
                '600_ml' => $row['600ml'],
                '1500_ml' => $row['1500ml'],
                // 'date' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date'])->format('Y-m-d'),
                // 'date' => !empty($row['date']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date'])->format('Y-m-d') : null,
                'date' => $row['date'],
                'phone' => $row['phone'],
                'other' => $row['other'],
                'latitude' => $row['latitude'],
                'longitude' => $row['longitude'],
                'city' => $row['city'],
                'user_id' => Auth::user()->id,
            ]);
        }
    }
}

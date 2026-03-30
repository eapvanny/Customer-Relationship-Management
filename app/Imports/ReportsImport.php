<?php

namespace App\Imports;

use App\Models\Report;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Auth;

class ReportsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    protected $date;
    public function __construct($date)
    {
        $this->date = $date;
    }
    public function model(array $row)
    {

     $prefix = $row['area'];

    // First create WITHOUT so_number
    $report = Report::create([
        'area' => $row['area'] ?? null,
        'customer_name' => $row['customer_name'] ?? null,
        'outlet_name' => $row['outlet_name'] ?? null,
        'driver_id' => $row['driver_id'] ?? null,
        'driver_name' => $row['driver_name'] ?? null,
        'sup_id' => $row['sup_id'] ?? null,
        'sup_name' => $row['sup_name'] ?? null,
        'rsm_name' => $row['rsm_name'] ?? null,
        'ssp_id' => $row['ssp_id'] ?? null,
        'ssp_name' => $row['ssp_name'] ?? null,
        'cus_type' => $row['cus_type'] ?? null,
        'date' => $this->date->toDateString(),

        '250_ml' => $row['250_ml'] ?? null,
        '350_ml' => $row['350_ml'] ?? null,
        '600_ml' => $row['600_ml'] ?? null,
        '1500_ml' => $row['1500_ml'] ?? null,

        'latitude' => $row['latitude'] ?? null,
        'longitude' => $row['longitude'] ?? null,
        'address' => $row['address'] ?? null,

        'qty' => $row['qty'] ?? null,
        'posm_name1' => $row['posm_name1'] ?? null,
        'qty2' => $row['qty2'] ?? null,
        'posm_name2' => $row['posm_name2'] ?? null,
        'qty3' => $row['qty3'] ?? null,
        'posm_name3' => $row['posm_name3'] ?? null,
        'status' => 'import',

        // 'user_id' => Auth::id(),
    ]);

    // Then update SO number (same as store)
    $report->update([
        'so_number' => $prefix . '-' . str_pad($report->id, 7, '0', STR_PAD_LEFT),
    ]);

    return $report;
    }
    
    /**
     * Define validation rules
     */
    public function rules(): array
    {
        return [
            'area' => 'required|string|max:255',
            'customer_name' => 'required|string|max:255',
            'outlet_name' => 'required|string|max:255',
            'ssp_name' => 'required|string|max:255',
            'ssp_id' => 'nullable',
            'driver_id' => 'nullable',
            'driver_name' => 'nullable|string|max:255',
            'sup_id' => 'nullable',
            'cus_type' => 'nullable|string|max:255',
            '250_ml' => 'nullable|integer|min:0',
            '350_ml' => 'nullable|integer|min:0',
            '600_ml' => 'nullable|integer|min:0',
            '1500_ml' => 'nullable|integer|min:0',
            // 'latitude' => 'nullable|numeric|between:-90,90',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            // 'longitude' => 'nullable|numeric|between:-180,180',
            'address' => 'nullable|string|max:500',
            'qty' => 'nullable|integer|min:0',
            'posm_name1' => 'nullable|string|max:255',
            'qty2' => 'nullable|integer|min:0',
            'posm_name2' => 'nullable|string|max:255',
            'qty3' => 'nullable|integer|min:0',
            'posm_name3' => 'nullable|string|max:255',
        ];
    }
}
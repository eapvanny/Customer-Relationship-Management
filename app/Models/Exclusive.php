<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exclusive extends Model
{
    use HasFactory;

    protected $table = 'exclusives';

    protected $fillable = [
        'area_id',
        'outlet_id',
        'customer_id',
        'customer_type',
        'date',
        'other',
        '250_ml',
        '350_ml',
        '600_ml',
        '1500_ml',
        'phone',
        'latitude',
        'longitude',
        'city',
        'country',
        'user_id',
        'photo',
        'customer',
        'customer_type',
        'phone',
        'photo_foc',
        'foc_250_qty',
        'foc_350_qty',
        'foc_600_qty',
        'foc_1500_qty',
        'foc_other',
        'foc_other_qty',
        'posm_1',
        'posm_1_qty',
        'posm_2',
        'posm_2_qty',
        'posm_3',
        'posm_3_qty',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function CustomerProvince()
    {
        return $this->belongsTo(CustomerProvince::class, 'customer_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'area_id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    public function posm1()
    {
        return $this->belongsTo(Posm::class, 'posm_1');
    }

    public function posm2()
    {
        return $this->belongsTo(Posm::class, 'posm_2');
    }

    public function posm3()
    {
        return $this->belongsTo(Posm::class, 'posm_3');
    }
}

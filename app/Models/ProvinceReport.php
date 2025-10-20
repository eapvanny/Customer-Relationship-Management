<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProvinceReport extends Model
{
    use HasFactory, SoftDeletes;

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
        'posm',
        'qty',
        'photo',
        'outlet_photo',
        'manager_id',
        'so_number',
    ];
    public function region()
    {
        return $this->belongsTo(Region::class, 'area_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function customer()
    {
        return $this->belongsTo(CustomerProvince::class, 'customer_id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }
    public function posm()
    {
        return $this->belongsTo(Posm::class, 'posm');
    }
}

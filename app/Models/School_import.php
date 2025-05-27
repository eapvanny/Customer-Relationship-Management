<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School_import extends Model
{
    use HasFactory;
    protected $table = 'school_imports';
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
        'customer',
        'customer_type',
        'phone',

        'photo_foc',
        'foc_qty',
        // 'manager_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retail extends Model
{
    use HasFactory;
    protected $table = 'retails';

    protected $fillable = [
        'area',
        'outlet',
        'customer',
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
        // 'manager_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

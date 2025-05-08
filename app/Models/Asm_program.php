<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asm_program extends Model
{
    use HasFactory;
    protected $table = 'asm_programs';

    protected $fillable = [
        'area',
        'outlet',
        'customer',
        'customer_type',
        '250_ml',
        '350_ml',
        '600_ml',
        '1500_ml',
        'date',
        'other',
        'phone',
        'latitude',
        'longitude',
        'city',
        // 'country',
        // 'user_id',
        // 'posm',
        // 'qty',
        'photo',
        // 'customer',
        // 'customer_type',
        // 'phone',
        // 'manager_id'
    ];

    // protected $hidden = [
    //     'created_at',
    //     'updated_at',
    // ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

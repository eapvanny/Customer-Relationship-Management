<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'area',
        'outlet',
        'date',
        'other',
        '250_ml',
        '350_ml',
        '600_ml',
        '1500_ml',
        'latitude',
        'longitude',
        'city',
        'country',
        'user_id',
        'posm',
        'qty',
        'photo',
        'manager_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}


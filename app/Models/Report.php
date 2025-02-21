<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'area',
        'depot_stock',
        'date',
        '250_ml',
        '350_ml',
        '600_ml',
        '1500_ml',
        'other',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}


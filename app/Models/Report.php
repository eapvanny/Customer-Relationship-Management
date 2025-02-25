<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'area', 'depot_stock', 'date', 'other', '250_ml', '350_ml', '600_ml', '1500_ml', 
        'latitude', 'longitude', 'city', 'country','user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use HasFactory;

    protected $table = 'outlets';
    protected $fillable = [
        'name',
        'phone',
        'area_id',
        'outlet_photo',
        'latitude',
        'longitude',
        'city',
        'country',
        'code',
        'customer_type',
        'user_type',
        'user_id',
        'active_status',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function region(){
        return $this->belongsTo(Region::class, 'area_id');
    }
}

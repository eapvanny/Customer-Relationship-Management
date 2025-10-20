<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use HasFactory;

    protected $table = 'outlets';
    protected $fillable = [
        'area_id',
        'user_id',
        'user_type',
        'name',
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

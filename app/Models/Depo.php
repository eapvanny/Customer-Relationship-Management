<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Depo extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'area_id',
        'user_id',
        'user_type',
        'name',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
}

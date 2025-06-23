<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depo extends Model
{
    use HasFactory;
    protected $fillable = [
        'area_id',
        'user_id',
        'name',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
}

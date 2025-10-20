<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WholesalePhoto extends Model
{
    use HasFactory;
    protected $table = 'wholesale_photos';
    protected $fillable = [
        'wholesale_id',
        'photo'
    ];
}

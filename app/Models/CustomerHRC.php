<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerHRC extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customer_hrc';
    protected $fillable = [
        'area',
        'user_id',
        'code',
        'name',
        'phone',
        'customer_type',
        'outlet_photo',
        'latitude',
        'longitude',
        'city',
        'country',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

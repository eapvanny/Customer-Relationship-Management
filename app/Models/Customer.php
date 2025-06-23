<?php

namespace App\Models;

use App\Http\Helpers\AppHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'area_id',
        'depo_id',
        'outlet_photo',
        'latitude',
        'longitude',
        'city',
        'country',
        'code',
        'customer_type',
        'ef
        ',
        'user_id',
    ];

    public static function getAreas()
    {
        return AppHelper::getAreas();
    }

    public function report()
    {
        return $this->hasMany(Report::class, 'customer_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function depo()
    {
        return $this->belongsTo(Depo::class, 'depo_id');
    }
}
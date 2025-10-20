<?php

namespace App\Models;

use App\Http\Helpers\AppHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerProvince extends Model
{
    use HasFactory;
    protected $table = 'customer_provinces';
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
        'user_type',
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
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'depo_id');
    }
    public function region(){
        return $this->belongsTo(Region::class, 'area_id');
    }
}

<?php

namespace App\Models;

use App\Http\Helpers\AppHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MCustomer extends Model
{
    use HasFactory;
    protected $table = 'm_customers';
    protected $fillable = ['name', 'phone', 'area_id', 'outlet', 'created_by'];

    public static function getAreas()
    {
        return AppHelper::getAreas();
    }

    public function report()
    {
        return $this->hasMany(Report::class, 'customer_id');
    }

    public function sub_wholesale()
    {
        return $this->hasMany(Sub_wholesale::class, 'customer_id');
    }

     public function retail()
    {
        return $this->hasMany(Retail::class, 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}

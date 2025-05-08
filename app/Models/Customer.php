<?php

namespace App\Models;

use App\Http\Helpers\AppHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name','phone', 'area_id'];

    public static function getAreas()
    {
        return AppHelper::getAreas();
    }

    public function report()
    {
        return $this->hasMany(Report::class, 'customer_id');
    }
}
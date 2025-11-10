<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubwholesalePicture extends Model
{
    use HasFactory;

    protected $table = 'subwholesale_pictures';

    protected $fillable = [
        'subwholesale_id',
        'picture',
        'latitude',
        'longitude',
        'city',
        'country'
    ];

    public function retail()
    {
        return $this->belongsTo(Sub_wholesale::class, 'subwholesale_id');
    }
}

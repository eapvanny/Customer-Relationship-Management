<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetailPictur extends Model
{
    use HasFactory;
    protected $table = 'retail_picturs';
    protected $fillable = [
        'retail_id',
        'picture',
    ];

    public function retail()
    {
        return $this->belongsTo(Retail::class, 'retail_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $table = 'regions';
    protected $fillable = [
        'region_name',
        'sd_name',
        'sm_name',
        'rg_manager_kh',
        'rg_manager_en',
        'province',
        'se_code',
        'active_status',
        'created_by',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'created_by');
    }
}

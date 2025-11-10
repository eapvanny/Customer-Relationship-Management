<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Display_subwholesale extends Model
{
    use HasFactory;
    protected $table = 'display_subwholesales';

    protected $fillable = [
        'region',
        'province',
        'district',
        'commune',
        'sm_name',
        'rsm_name',
        'asm_name',
        'se_name',
        'se_code',
        'customer_code',
        'depot_contact',
        'depot_name',
        'sub_ws_name',
        'sub_ws_contact',
        'outlet_type',
        'sale_kpi',
        'display_qty',
        'sku',
        'incentive',
        'remark',
        'apply_user',
        'latitude',
        'longitude',
        'city',
        'country'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'apply_user');
    }
}

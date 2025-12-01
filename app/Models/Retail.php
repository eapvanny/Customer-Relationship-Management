<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retail extends Model
{
    use HasFactory;
    protected $table = 'retails';

    protected $fillable = [
        'region',
        'province',
        'district',
        'commune',
        'sm_name',
        'rsm_name',
        'asm_name',
        'sup_name',
        'se_name',
        'se_code',
        'customer_code',
        'depot_contact',
        'depot_name',
        'retail_name',
        'retail_contact',
        'outlet_type',
        'sale_kpi',
        'display_qty',
        'sku',
        'incentive',
        'remark',
        'apply_user',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'apply_user');
    }
}

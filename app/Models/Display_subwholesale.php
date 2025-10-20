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
        'sm_name',
        'rsm_name',
        'asm_name',
        'sup_name',
        'se_name',
        'se_code',
        'customer_code',
        'depo_contact',
        'creater',
        'depo_name',
        'subwholesale_name',
        'subwholesale_contact',
        'business_type',
        'sale_kpi',
        'display_qty',
        'foc_qty',
        'remark',
        'apply_user',
        'location',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'apply_user');
    }
}

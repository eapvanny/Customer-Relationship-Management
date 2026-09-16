<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportExportCache extends Model
{
    use HasFactory;

    protected $table = 'report_export_caches';

    protected $fillable = [
        'report_id',
        'source_updated_at',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
        'source_updated_at' => 'datetime',
    ];
}
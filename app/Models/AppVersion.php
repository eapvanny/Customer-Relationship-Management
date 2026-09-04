<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppVersion extends Model
{
    protected $fillable = [
        'latest_version',
        'can_update',
        'force_update',
        'update_version_android',
        'update_url_android',
        'update_version_ios',
        'update_url_ios',
        'release_notes',
    ];

    protected $casts = [
        'can_update' => 'boolean',
        'force_update' => 'boolean',
        'update_version_android' => 'boolean',
        'update_version_ios' => 'boolean',
    ];
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posm extends Model
{
    use HasFactory;

    protected $table = 'posms';

    protected $fillable = [
        'name_kh',
        'name_en',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    // ✅ Each POSM belongs to one user (creator)

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ✅ Each POSM belongs to one user (updater)
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ✅ Each POSM belongs to one user (deleter)
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}

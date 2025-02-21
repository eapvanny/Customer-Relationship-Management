<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = ['item', 'text', 'locale'];

    // Optional: if you want to force the translation to be unique per locale and item
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (self::where('item', $model->item)->where('locale', $model->locale)->exists()) {
                throw new \Exception("The translation for this item already exists for the selected locale.");
            }
        });
    }
}


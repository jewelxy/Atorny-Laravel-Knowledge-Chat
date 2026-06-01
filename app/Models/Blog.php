<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'image',
        'status',
        'published_at',
    ];

    protected $casts = [
        'status' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function translations()
    {
        return $this->hasMany(
            BlogTranslation::class
        );
    }

    public function translation()
    {
        return $this->hasOne(
            BlogTranslation::class
        )->where(
            'locale',
            app()->getLocale()
        );
    }
}

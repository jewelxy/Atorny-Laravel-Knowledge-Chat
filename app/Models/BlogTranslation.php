<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogTranslation extends Model
{
    protected $fillable = [
        'locale',
        'title',
        'slug',
        'description',
        'meta_tag',
        'meta_description',
    ];

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }
}

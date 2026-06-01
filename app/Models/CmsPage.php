<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Minimal page registry for the headless CMS.
 *
 * A page is a container of ordered sections. It does not hold marketing copy —
 * that lives in section translations as JSONB documents keyed by locale.
 */
class CmsPage extends Model
{
    protected $fillable = [
        'key',
        'status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /** All sections belonging to this page (including inactive). */
    public function sections(): HasMany
    {
        return $this->hasMany(CmsSection::class)->orderBy('sort_order');
    }

    /**
     * Sections exposed to public APIs: active only, ordered for page-builder rendering.
     * Eager-load `translation` (or `translations`) before serializing for a locale.
     */
    public function activeSections(): HasMany
    {
        return $this->hasMany(CmsSection::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }
}

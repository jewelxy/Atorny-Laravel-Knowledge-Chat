<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Per-locale flexible content document for a section.
 *
 * The `data` JSONB column holds ALL translatable fields (headlines, FAQ items,
 * pricing tiers, media references, SEO snippets, etc.). Never add static columns —
 * extend the JSON schema in application code and admin forms instead.
 */
class CmsSectionTranslation extends Model
{
    protected $fillable = [
        'cms_section_id',
        'locale',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    protected $attributes = [
        'data' => '[]',
    ];

    public function cmsSection(): BelongsTo
    {
        return $this->belongsTo(CmsSection::class);
    }
}

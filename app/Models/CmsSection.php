<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Dynamic page-builder section.
 *
 * section_type → frontend component map (React/Next.js/Vue/mobile).
 * Layout and styling are handled in the frontend per section_type.
 * Translatable content → cms_section_translations.data (JSONB).
 */
class CmsSection extends Model
{
    protected $fillable = [
        'cms_page_id',
        'section_key',
        'section_type',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function cmsPage(): BelongsTo
    {
        return $this->belongsTo(CmsPage::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(CmsSectionTranslation::class);
    }

    public function translation(): HasOne
    {
        return $this->hasOne(CmsSectionTranslation::class)
            ->where('locale', app()->getLocale());
    }

    public function scopeWithLocale(Builder $query, ?string $locale = null): Builder
    {
        $locale = $locale ?? app()->getLocale();
        $fallback = config('localization.fallback_locale', 'en');

        return $query->with([
            'translations' => fn ($q) => $q->whereIn('locale', array_unique([$locale, $fallback])),
        ]);
    }

    public function resolveData(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $fallback = config('localization.fallback_locale', 'en');

        if ($this->relationLoaded('translation') && $this->translation) {
            return $this->translation->data ?? [];
        }

        if ($this->relationLoaded('translations')) {
            $match = $this->translations->firstWhere('locale', $locale)
                ?? $this->translations->firstWhere('locale', $fallback);

            return $match?->data ?? [];
        }

        $row = $this->translations()
            ->whereIn('locale', [$locale, $fallback])
            ->orderByRaw('CASE WHEN locale = ? THEN 0 ELSE 1 END', [$locale])
            ->first();

        return $row?->data ?? [];
    }
}

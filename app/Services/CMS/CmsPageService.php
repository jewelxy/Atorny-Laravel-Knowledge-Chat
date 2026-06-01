<?php

namespace App\Services\CMS;

use App\Models\CmsPage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Loads published CMS pages with locale-aware section translations.
 *
 * Pipeline:
 * 1. Resolve page by key (status = published).
 * 2. Eager-load active sections ordered by sort_order.
 * 3. Attach translation for requested locale with fallback.
 * 4. Serialize via CmsPagePublicResource for React/Next.js/Vue/mobile consumers.
 */
class CmsPageService
{
    public function getPublishedPageByKey(string $key, ?string $locale = null): CmsPage
    {
        $locale = $locale ?? app()->getLocale();
        $fallback = config('localization.fallback_locale', 'en');

        $page = CmsPage::query()
            ->where('key', $key)
            ->where('status', true)
            ->with([
                'activeSections' => fn ($query) => $query->with([
                    'translations' => fn ($q) => $q->whereIn('locale', array_unique([$locale, $fallback])),
                ]),
            ])
            ->first();

        if (! $page) {
            throw (new ModelNotFoundException)->setModel(CmsPage::class, [$key]);
        }

        $page->activeSections->each(function ($section) use ($locale, $fallback): void {
            $translation = $section->translations->firstWhere('locale', $locale)
                ?? $section->translations->firstWhere('locale', $fallback);

            $section->setRelation('translation', $translation);
        });

        return $page;
    }
}

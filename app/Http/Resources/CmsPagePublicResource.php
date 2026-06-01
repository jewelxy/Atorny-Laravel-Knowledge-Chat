<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Public page response consumed by SPA / SSR / mobile clients.
 *
 * Example:
 * {
 *   "page": "home",
 *   "locale": "en",
 *   "sections": [
 *     { "section_key": "hero_main", "section_type": "hero", "data": {} }
 *   ]
 * }
 */
class CmsPagePublicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'page' => $this->key,
            'locale' => app()->getLocale(),
            'sections' => CmsSectionPublicResource::collection(
                $this->whenLoaded('activeSections', $this->activeSections, collect())
            ),
        ];
    }
}

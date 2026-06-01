<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Public section payload for headless frontends.
 *
 * Frontend rendering:
 *   const Component = SECTION_REGISTRY[section.section_type];
 *   return <Component key={section.section_key} data={section.data} />;
 */
class CmsSectionPublicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'section_key' => $this->section_key,
            'section_type' => $this->section_type,
            'sort_order' => $this->sort_order,
            'data' => $this->relationLoaded('translation') && $this->translation
                ? ($this->translation->data ?? [])
                : $this->resolveData($request->query('locale')),
        ];
    }
}

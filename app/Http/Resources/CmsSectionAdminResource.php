<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CmsSectionAdminResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cms_page_id' => $this->cms_page_id,
            'section_key' => $this->section_key,
            'section_type' => $this->section_type,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'translations' => CmsSectionTranslationResource::collection(
                $this->whenLoaded('translations')
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

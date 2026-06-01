<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    public function toArray($request): array
    {
        return [

            'id' => $this->id,

            'title' => $this->translation?->title,

            'slug' => $this->translation?->slug,

            'description' => $this->translation?->description,

            'meta_tag' => $this->translation?->meta_tag,

            'meta_description' => $this->translation?->meta_description,

            'image' => $this->image,

            'published_at' => $this->published_at,
        ];
    }
}

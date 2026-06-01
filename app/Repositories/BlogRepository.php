<?php
namespace App\Repositories;

use App\Models\Blog;

class BlogRepository
{
    public function findBySlug(string $slug)
    {
        return Blog::query()
            ->whereHas('translations', function ($query) use ($slug) {
                $query->where('slug', $slug)
                    ->where('locale', app()->getLocale());
            })
            ->with('translation')
            ->firstOrFail();
    }
}
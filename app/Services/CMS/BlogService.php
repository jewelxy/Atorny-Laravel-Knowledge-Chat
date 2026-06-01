<?php
namespace App\Services\CMS;

use App\Models\Blog;

class BlogService
{
    public function getPublishedBlogs()
    {
        return Blog::query()
            ->with('translation')
            ->where('status', true)
            ->whereHas('translations', fn ($query) => $query->where('locale', app()->getLocale()))
            ->latest()
            ->paginate(10);
    }
}
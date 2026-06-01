<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use App\Services\CMS\BlogService;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(BlogService $blogService, ?string $locale = null)
    {
        if ($locale) {
            app()->setLocale($locale);
        }

        return BlogResource::collection(
            $blogService->getPublishedBlogs()
        );
    }

    public function show(string $slug)
    {
        $blog = Blog::query()
            ->with('translation')
            ->where('status', true)
            ->whereHas('translations', function ($query) use ($slug) {
                $query->where('slug', $slug)
                    ->where('locale', app()->getLocale());
            })
            ->firstOrFail();

        return new BlogResource($blog);
    }
}

<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Blog;
use App\Models\BlogTranslation;
use Illuminate\Http\Request;

class BlogTranslationController extends ApiController
{
    public function index(Blog $blog)
    {
        $translations = $blog->translations;

        return $this->successResponse('Blog translations retrieved', [
            'blog_id' => $blog->id,
            'translations' => $translations,
        ]);
    }

    public function store(Request $request, Blog $blog)
    {
        $data = $request->validate([
            'locale' => 'required|string|max:5',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'description' => 'required|string',
            'meta_tag' => 'sometimes|string|max:255',
            'meta_description' => 'sometimes|string|max:255',
        ]);

        $translation = BlogTranslation::updateOrCreate(
            ['blog_id' => $blog->id, 'locale' => $data['locale']],
            $data
        );

        return $this->successResponse('Blog translation saved', [
            'translation' => $translation,
        ], 201);
    }

    public function show(Blog $blog, BlogTranslation $translation)
    {
        if ($translation->blog_id !== $blog->id) {
            return $this->errorResponse('Translation not found', 404);
        }

        return $this->successResponse('Blog translation loaded', [
            'translation' => $translation,
        ]);
    }

    public function update(Request $request, Blog $blog, BlogTranslation $translation)
    {
        if ($translation->blog_id !== $blog->id) {
            return $this->errorResponse('Translation not found', 404);
        }

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'meta_tag' => 'sometimes|string|max:255',
            'meta_description' => 'sometimes|string|max:255',
        ]);

        $translation->update($data);

        return $this->successResponse('Blog translation updated', [
            'translation' => $translation,
        ]);
    }

    public function destroy(Blog $blog, BlogTranslation $translation)
    {
        if ($translation->blog_id !== $blog->id) {
            return $this->errorResponse('Translation not found', 404);
        }

        $translation->delete();

        return $this->successResponse('Blog translation deleted');
    }
}

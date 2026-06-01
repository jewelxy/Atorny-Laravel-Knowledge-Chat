<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends ApiController
{
    public function index()
    {
        $blogs = Blog::orderByDesc('created_at')->get();

        return $this->successResponse('Admin blog list loaded', [
            'blogs' => $blogs,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:blogs,slug',
            'content' => 'sometimes|string',
            'image' => 'sometimes|string|url',
            'status' => 'sometimes|boolean',
            'published_at' => 'sometimes|date',
        ]);

        $data['status'] = $data['status'] ?? true;
        $data['published_at'] = $data['published_at'] ?? now();

        $blog = Blog::create($data);

        return $this->successResponse('Blog created', [
            'blog' => $blog,
        ], 201);
    }

    public function show(int $id)
    {
        $blog = Blog::findOrFail($id);

        return $this->successResponse('Admin blog loaded', [
            'blog' => $blog,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $blog = Blog::findOrFail($id);

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => "sometimes|string|max:255|unique:blogs,slug,$id",
            'content' => 'sometimes|string',
        ]);

        $blog->update($data);

        return $this->successResponse('Blog updated', [
            'blog' => $blog,
        ]);
    }

    public function destroy(int $id)
    {
        $blog = Blog::findOrFail($id);
        $blog->delete();

        return $this->successResponse('Blog deleted', [
            'blog' => ['id' => $id],
        ]);
    }
}

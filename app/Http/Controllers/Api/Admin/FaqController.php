<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class FaqController extends ApiController
{
    public function index()
    {
        return $this->successResponse('Admin FAQ list loaded', [
            'faqs' => [],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'slug' => 'required|string|max:255|unique:faqs,slug',
        ]);

        return $this->successResponse('FAQ created', [
            'faq' => $data,
        ], 201);
    }

    public function show(int $id)
    {
        return $this->successResponse('Admin FAQ loaded', [
            'faq' => ['id' => $id],
        ]);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'question' => 'sometimes|string|max:255',
            'answer' => 'sometimes|string',
            'slug' => 'sometimes|string|max:255',
        ]);

        return $this->successResponse('FAQ updated', [
            'faq' => array_merge(['id' => $id], $data),
        ]);
    }

    public function destroy(int $id)
    {
        return $this->successResponse('FAQ deleted', [
            'faq' => ['id' => $id],
        ]);
    }
}

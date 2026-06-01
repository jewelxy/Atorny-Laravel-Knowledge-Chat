<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Api\ApiController;

class FaqController extends ApiController
{
    public function index()
    {
        return $this->successResponse('FAQ list loaded', [
            'faqs' => [],
        ]);
    }

    public function show(string $slug)
    {
        return $this->successResponse('FAQ item loaded', [
            'faq' => [
                'slug' => $slug,
            ],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Api\ApiController;

class ServiceController extends ApiController
{
    public function index()
    {
        return $this->successResponse('Services retrieved successfully', [
            'services' => [],
        ]);
    }

    public function show(string $slug)
    {
        return $this->successResponse('Service details loaded', [
            'service' => [
                'slug' => $slug,
            ],
        ]);
    }
}

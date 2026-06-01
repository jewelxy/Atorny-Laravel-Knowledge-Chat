<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Api\ApiController;

class DynamicContentModuleController extends ApiController
{
    public function index(string $module)
    {
        return $this->successResponse('Dynamic module items loaded', [
            'module' => $module,
            'items' => [],
        ]);
    }

    public function show(string $module, string $slug)
    {
        return $this->successResponse('Dynamic module item loaded', [
            'module' => $module,
            'item' => [
                'slug' => $slug,
            ],
        ]);
    }
}

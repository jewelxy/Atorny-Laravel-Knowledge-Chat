<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class DynamicContentModuleController extends ApiController
{
    public function index()
    {
        return $this->successResponse('Dynamic content modules loaded', [
            'modules' => [],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'module_name' => 'required|string|max:255',
            'schema' => 'sometimes|array',
        ]);

        return $this->successResponse('Dynamic module created', [
            'module' => $data,
        ], 201);
    }

    public function show(int $id)
    {
        return $this->successResponse('Dynamic module loaded', [
            'module' => ['id' => $id],
        ]);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'module_name' => 'sometimes|string|max:255',
            'schema' => 'sometimes|array',
        ]);

        return $this->successResponse('Dynamic module updated', [
            'module' => array_merge(['id' => $id], $data),
        ]);
    }

    public function destroy(int $id)
    {
        return $this->successResponse('Dynamic module deleted', [
            'module' => ['id' => $id],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class ServiceController extends ApiController
{
    public function index()
    {
        return $this->successResponse('Admin services list loaded', [
            'services' => [],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'slug' => 'required|string|max:255|unique:services,slug',
        ]);

        return $this->successResponse('Service created', [
            'service' => $data,
        ], 201);
    }

    public function show(int $id)
    {
        return $this->successResponse('Admin service loaded', [
            'service' => ['id' => $id],
        ]);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'slug' => 'sometimes|string|max:255',
        ]);

        return $this->successResponse('Service updated', [
            'service' => array_merge(['id' => $id], $data),
        ]);
    }

    public function destroy(int $id)
    {
        return $this->successResponse('Service deleted', [
            'service' => ['id' => $id],
        ]);
    }
}

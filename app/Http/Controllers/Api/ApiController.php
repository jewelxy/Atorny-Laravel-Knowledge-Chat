<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    /**
     * Build a standardized success JSON response.
     */
    protected function successResponse(string $message, mixed $data = null, int $statusCode = 200): JsonResponse
    {
        // Start with the common response envelope used by all success endpoints.
        $payload = [
            'status' => true,
            'message' => $message,
        ];

        // Attach data only when provided to keep payloads minimal.
        if (! is_null($data)) {
            $payload['data'] = $data;
        }

        return response()->json($payload, $statusCode);
    }

    /**
     * Build a standardized error JSON response.
     */
    protected function errorResponse(string $message, int $statusCode = 422): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message,
        ], $statusCode);
    }
}

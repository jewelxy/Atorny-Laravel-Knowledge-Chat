<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends ApiController
{
    /**
     * Inject profile-related business logic service.
     */
    public function __construct(private readonly ProfileService $profileService)
    {
    }

    /**
     * Return the authenticated user's profile.
     */
    public function show(Request $request): JsonResponse
    {
        // Wrap profile data in UserResource to keep API output consistent.
        return $this->successResponse('Profile data', new UserResource($this->profileService->profile($request->user())));
    }
}
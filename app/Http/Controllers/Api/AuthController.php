<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\AuthTokenResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends ApiController
{
    /**
     * Inject authentication business logic service.
     */
    public function __construct(private readonly AuthService $authService)
    {
    }

    /**
     * Register a new user and return an API token with user details.
     */
    public function register(RegisterRequest $request)
    {
        // Pass validated form data and optional profile image to the service layer.
        $result = $this->authService->register(
            $request->validated(),
            $request->file('profile_image')
        );

        // Normalize registration response using a dedicated token resource shape.
        return $this->successResponse(
            'User registered successfully',
            new AuthTokenResource([
                'token' => $result['token'],
                'user' => $result['user'],
            ]),
            201
        );
    }

    /**
     * Authenticate credentials and return a token when successful.
     */
    public function login(LoginRequest $request)
    {
        // Attempt login using validated credentials from the form request.
        $result = $this->authService->login($request->email, $request->password);

        // Forward authentication failure details from the service.
        if (! $result['ok']) {
            return $this->errorResponse($result['message'], $result['status']);
        }

        // Return token + user payload in the same resource shape as register.
        return $this->successResponse($result['message'], new AuthTokenResource([
            'token' => $result['token'],
            'user' => $result['user'],
        ]));
    }

    /**
     * Re-issue a token for the currently authenticated user.
     */
    public function refreshToken(Request $request): JsonResponse
    {
        // Delegate token rotation logic to the service layer.
        $result = $this->authService->refreshToken($request->user());

        return $this->successResponse('Token re-issued', new AuthTokenResource([
            'token' => $result['token'],
            'user' => $result['user'],
        ]));
    }

    /**
     * Revoke active token/session for the authenticated user.
     */
    public function logout(Request $request): JsonResponse
    {
        // Remove or invalidate user access token through service abstraction.
        $this->authService->logout($request->user());

        return $this->successResponse('User logged out');
    }
}
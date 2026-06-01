<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    private const DEFAULT_GUARD = 'sanctum';

    /**
     * Inject user repository abstraction for auth-related persistence.
     */
    public function __construct(private readonly UserRepositoryInterface $users)
    {
    }

    /**
     * Register a user, optionally store profile image, and issue an access token.
     */
    public function register(array $attributes, ?UploadedFile $profileImage = null): array
    {
        // Persist profile image first and save returned path in user attributes.
        if ($profileImage) {
            $attributes['profile_image'] = $this->users->storeProfileImage($profileImage);
        }

        // Create the user record with final validated attributes.
        $user = $this->users->create($attributes);

        // Auto-assign default user role when present for the active API guard.
        if ($this->users->hasRoleByName('user', self::DEFAULT_GUARD)) {
            $this->users->assignRoleByName($user, 'user');
        }

        // Issue token after user creation (and optional role assignment).
        $token = $this->users->createToken($user);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Validate credentials and return auth payload or failure details.
     */
    public function login(string $email, string $password): array
    {
        // Lookup user once by email before password verification.
        $user = $this->users->findByEmail($email);

        // Reject when user not found or password hash does not match.
        if (! $user || ! Hash::check($password, $user->password)) {
            return [
                'ok' => false,
                'status' => 401,
                'message' => 'Invalid credentials',
            ];
        }

        // Return a fresh token for successful login.
        return [
            'ok' => true,
            'status' => 200,
            'message' => 'User logged in sucessfully',
            'user' => $user,
            'token' => $this->users->createToken($user),
        ];
    }

    /**
     * Rotate the current token and return a newly issued token.
     */
    public function refreshToken(User $user): array
    {
        // Invalidate current token to enforce one active token per session flow.
        $this->users->deleteCurrentAccessToken($user);

        return [
            'user' => $user,
            'token' => $this->users->createToken($user),
        ];
    }

    /**
     * Logout user by deleting the active token.
     */
    public function logout(User $user): void
    {
        $this->users->deleteCurrentAccessToken($user);
    }
}
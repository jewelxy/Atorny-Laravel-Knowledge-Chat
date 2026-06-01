<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

interface UserRepositoryInterface
{
    /**
     * Persist a new user.
     */
    public function create(array $attributes): User;

    /**
     * Find a user by email, or return null when not found.
     */
    public function findByEmail(string $email): ?User;

    /**
     * Store the uploaded profile image and return its storage path.
     */
    public function storeProfileImage(UploadedFile $file): string;

    /**
     * Create a new API token and return the plain text token value.
     */
    public function createToken(User $user, string $tokenName = 'myToken'): string;

    /**
     * Delete the currently authenticated access token.
     */
    public function deleteCurrentAccessToken(User $user): void;

    /**
     * Check if a role exists by role name and guard.
     */
    public function hasRoleByName(string $name, string $guardName): bool;

    /**
     * Assign a role to a user using role name.
     */
    public function assignRoleByName(User $user, string $name): void;

    /**
     * Return assigned role names for a user.
     */
    public function getRoleNames(User $user): Collection;

    /**
     * Return all effective permissions for a user.
     */
    public function getAllPermissions(User $user): Collection;

    /**
     * Replace the user's roles with the provided roles.
     */
    public function syncRoles(User $user, array $roles): void;

    /**
     * Check whether a user currently has the given role.
     */
    public function hasRole(User $user, Role $role): bool;

    /**
     * Remove a specific role from a user.
     */
    public function removeRole(User $user, Role $role): void;

    /**
     * Grant direct permissions to a user.
     */
    public function givePermissions(User $user, iterable $permissions): void;

    /**
     * Revoke one direct permission from a user.
     */
    public function revokePermission(User $user, Permission $permission): void;

    /**
     * Reload and return the latest persisted user state.
     */
    public function refresh(User $user): User;
}
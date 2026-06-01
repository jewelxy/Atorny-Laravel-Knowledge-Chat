<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Persist a new user record.
     */
    public function create(array $attributes): User
    {
        return User::create($attributes);
    }

    /**
     * Find the first user matching an email address.
     */
    public function findByEmail(string $email): ?User
    {
        return User::query()->where('email', $email)->first();
    }

    /**
     * Store profile image in the public users directory.
     */
    public function storeProfileImage(UploadedFile $file): string
    {
        return $file->store('users', 'public');
    }

    /**
     * Create and return a plain text API token for a user.
     */
    public function createToken(User $user, string $tokenName = 'myToken'): string
    {
        return $user->createToken($tokenName)->plainTextToken;
    }

    /**
     * Delete the currently active token if one exists.
     */
    public function deleteCurrentAccessToken(User $user): void
    {
        // Guard against null when request was not authenticated via token.
        if ($user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }
    }

    /**
     * Check whether a role exists by name under a specific guard.
     */
    public function hasRoleByName(string $name, string $guardName): bool
    {
        // Validate existence only; no model hydration needed.
        return Role::query()
            ->where('name', $name)
            ->where('guard_name', $guardName)
            ->exists();
    }

    /**
     * Assign a role to a user by role name.
     */
    public function assignRoleByName(User $user, string $name): void
    {
        $user->assignRole($name);
    }

    /**
     * Return normalized role names collection for API use.
     */
    public function getRoleNames(User $user): Collection
    {
        return $user->getRoleNames()->values();
    }

    /**
     * Return all inherited and direct permissions for the user.
     */
    public function getAllPermissions(User $user): Collection
    {
        return $user->getAllPermissions();
    }

    /**
     * Replace user's roles with the provided role set.
     */
    public function syncRoles(User $user, array $roles): void
    {
        $user->syncRoles($roles);
    }

    /**
     * Check role ownership by comparing role IDs.
     */
    public function hasRole(User $user, Role $role): bool
    {
        // Use loaded relation IDs for a simple membership test.
        return $user->roles->pluck('id')->contains((int) $role->id);
    }

    /**
     * Remove a specific role from a user.
     */
    public function removeRole(User $user, Role $role): void
    {
        $user->removeRole($role);
    }

    /**
     * Grant one or more direct permissions to a user.
     */
    public function givePermissions(User $user, iterable $permissions): void
    {
        $user->givePermissionTo($permissions);
    }

    /**
     * Revoke a direct permission from a user.
     */
    public function revokePermission(User $user, Permission $permission): void
    {
        $user->revokePermissionTo($permission);
    }

    /**
     * Reload and return the latest state from the database.
     */
    public function refresh(User $user): User
    {
        return $user->fresh();
    }
}
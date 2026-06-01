<?php

namespace App\Repositories;

use App\Repositories\Contracts\RolePermissionRepositoryInterface;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionRepository implements RolePermissionRepositoryInterface
{
    /**
     * Fetch roles with a lightweight permission projection for API responses.
     */
    public function getRolesWithPermissions(): Collection
    {
        // Eager-load only required permission columns to reduce payload and query cost.
        return Role::query()
            ->with('permissions:id,name')
            ->get(['id', 'name', 'guard_name']);
    }
    
    /**
     * Persist a new role under the provided guard.
     */
    public function createRole(string $name, string $guardName): Role
    {
        // Store guard_name explicitly to keep role scope deterministic.
        return Role::create([
            'name' => $name,
            'guard_name' => $guardName,
        ]);
    }

    /**
     * Fetch all permissions with fields commonly required by clients.
     */
    public function getPermissions(): Collection
    {
        // Return only ID, display name, and guard scope.
        return Permission::query()->get(['id', 'name', 'guard_name']);
    }

    /**
     * Persist a new permission under the provided guard.
     */
    public function createPermission(string $name, string $guardName): Permission
    {
        // Store guard_name explicitly to prevent cross-guard ambiguity.
        return Permission::create([
            'name' => $name,
            'guard_name' => $guardName,
        ]);
    }

    /**
     * Resolve permission names to models, creating missing permissions when needed.
     */
    public function findOrCreatePermissionsByNames(array $names, string $guardName): Collection
    {
        // Ensure every provided name maps to a permission in the target guard.
        return collect($names)
            ->map(fn (string $name) => Permission::findOrCreate($name, $guardName))
            ->values();
    }

    /**
     * Replace all role permissions with the provided permission set.
     */
    public function syncRolePermissions(Role $role, iterable $permissions): void
    {
        // syncPermissions removes stale links and keeps only the supplied ones.
        $role->syncPermissions($permissions);
    }

    /**
     * Locate a role by ID using Spatie's guard-aware resolver.
     */
    public function findRoleById(int $id): Role
    {
        return Role::findById($id);
    }

    /**
     * Fetch permissions by IDs with a minimal selected column set.
     */
    public function findPermissionsByIds(iterable $ids): Collection
    {
        // Normalize iterable IDs before using whereIn to avoid generator edge cases.
        return Permission::query()
            ->whereIn('id', collect($ids)->values()->all())
            ->get(['id', 'name', 'guard_name']);
    }

    /**
     * Locate a permission by ID using Spatie's guard-aware resolver.
     */
    public function findPermissionById(int $id): Permission
    {
        return Permission::findById($id);
    }
}
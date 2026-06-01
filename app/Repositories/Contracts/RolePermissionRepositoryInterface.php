<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

interface RolePermissionRepositoryInterface
{
    /**
     * Fetch all roles with their attached permissions.
     */
    public function getRolesWithPermissions(): Collection;

    /**
     * Create a role for the given guard.
     */
    public function createRole(string $name, string $guardName): Role;

    /**
     * Fetch all available permissions.
     */
    public function getPermissions(): Collection;

    /**
     * Create a permission for the given guard.
     */
    public function createPermission(string $name, string $guardName): Permission;

    /**
     * Resolve permission names to models, creating missing entries when needed.
     */
    public function findOrCreatePermissionsByNames(array $names, string $guardName): Collection;

    /**
     * Replace a role's permission set with the provided permissions.
     */
    public function syncRolePermissions(Role $role, iterable $permissions): void;

    /**
     * Find one role by ID.
     */
    public function findRoleById(int $id): Role;

    /**
     * Find permissions using a list of IDs.
     */
    public function findPermissionsByIds(iterable $ids): Collection;

    /**
     * Find one permission by ID.
     */
    public function findPermissionById(int $id): Permission;
}
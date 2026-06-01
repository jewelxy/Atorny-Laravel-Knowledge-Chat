<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\RolePermissionRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Collection;
use Spatie\Permission\Guard;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionService
{
    /**
     * Inject repository dependencies for role-permission and user operations.
     */
    public function __construct(
        private readonly RolePermissionRepositoryInterface $rolePermissions,
        private readonly UserRepositoryInterface $users
    ) {
    }

    /**
     * Fetch all roles with their attached permissions.
     */
    public function roles(): Collection
    {
        return $this->rolePermissions->getRolesWithPermissions();
    }

    /**
     * Create a role scoped to a specific guard.
     */
    public function createRole(string $name, string $guardName): Role
    {
        return $this->rolePermissions->createRole($name, $guardName);
    }

    /**
     * Fetch all available permissions.
     */
    public function permissions(): Collection
    {
        return $this->rolePermissions->getPermissions();
    }

    /**
     * Create a permission scoped to a specific guard.
     */
    public function createPermission(string $name, string $guardName): Permission
    {
        return $this->rolePermissions->createPermission($name, $guardName);
    }

    /**
     * Replace the role's permissions using the provided permission IDs.
     */
    public function syncRolePermissions(Role $role, array $permissionIds): array
    {
        // Normalize incoming IDs to integers for reliable set comparisons.
        $requestedIds = collect($permissionIds)->map(fn ($id) => (int) $id)->values();

        $permissions = $this->rolePermissions->findPermissionsByIds($requestedIds);

        // Keep role-permission sync within the same guard namespace.
        $guardMismatch = $permissions->first(
            fn (Permission $permission) => $permission->guard_name !== $role->guard_name
        );

        if ($guardMismatch) {
            return [
                'ok' => false,
                'status' => 422,
                'message' => "Permission guard mismatch. Expected {$role->guard_name}, got {$guardMismatch->guard_name}",
            ];
        }

        // Defensive check in case validation rules change in the future.
        $existingIds = $permissions->pluck('id')->map(fn ($id) => (int) $id)->values();
        $missingIds = $requestedIds->diff($existingIds)->values();

        if ($missingIds->isNotEmpty()) {
            return [
                'ok' => false,
                'status' => 422,
                'message' => 'One or more permission IDs are invalid',
            ];
        }

        // syncPermissions behavior: remove stale permissions and keep only requested ones.
        $this->rolePermissions->syncRolePermissions($role, $permissions);

        // Return a fresh snapshot so the response reflects the persisted state.
        return [
            'ok' => true,
            'role' => $role->name,
            'permissions' => $role->fresh()->getPermissionNames()->values()->all(),
        ];
    }

    /**
     * Assign one role to a user, replacing existing assigned roles.
     */
    public function assignRole(User $user, int $roleId): array
    {
        $role = $this->rolePermissions->findRoleById($roleId);
        $expectedGuard = Guard::getDefaultName($user);

        // Prevent cross-guard role assignment (e.g. web role to sanctum user).
        if ($role->guard_name !== $expectedGuard) {
            return [
                'ok' => false,
                'status' => 422,
                'message' => "Role guard mismatch. Expected {$expectedGuard}, got {$role->guard_name}",
            ];
        }

        // Keep previous role names for audit-friendly API responses.
        $previousRoles = $this->users->getRoleNames($user);

        // syncRoles enforces single-role assignment policy from this endpoint.
        $this->users->syncRoles($user, [$role]);

        return [
            'ok' => true,
            'status' => 200,
            'message' => 'Role assigned successfully',
            'data' => [
                'user_id' => $user->id,
                'previous_roles' => $previousRoles,
                'roles' => $this->users->getRoleNames($this->users->refresh($user)),
            ],
        ];
    }

    /**
     * Remove one role from a user after guard and assignment validation.
     */
    public function removeRole(User $user, int $roleId): array
    {
        $role = $this->rolePermissions->findRoleById($roleId);
        $expectedGuard = Guard::getDefaultName($user);

        // Block removing roles from a different guard to avoid data inconsistency.
        if ($role->guard_name !== $expectedGuard) {
            return [
                'ok' => false,
                'status' => 422,
                'message' => "Role guard mismatch. Expected {$expectedGuard}, got {$role->guard_name}",
            ];
        }

        // Return a clear message when the role is not currently assigned.
        if (! $this->users->hasRole($user, $role)) {
            return [
                'ok' => false,
                'status' => 422,
                'message' => 'Role is not assigned to this user',
            ];
        }

        $this->users->removeRole($user, $role);

        return [
            'ok' => true,
            'status' => 200,
            'message' => 'Role removed successfully',
            'data' => [
                'user_id' => $user->id,
                'roles' => $this->users->getRoleNames($this->users->refresh($user)),
            ],
        ];
    }

    /**
     * Assign direct permissions to a user using permission IDs.
     */
    public function assignPermissions(User $user, array $permissionIds): array
    {
        // Normalize incoming IDs to integers for predictable collection comparisons.
        $requestedIds = collect($permissionIds)->map(fn ($id) => (int) $id)->values();

        // Fetch and index existing permissions for fast lookups and set operations.
        $permissionsById = $this->rolePermissions
            ->findPermissionsByIds($requestedIds)
            ->keyBy('id');

        $expectedGuard = Guard::getDefaultName($user);

        // Detect first permission that violates the user's expected guard.
        $guardMismatch = $permissionsById->first(
            fn (Permission $permission) => $permission->guard_name !== $expectedGuard
        );

        if ($guardMismatch) {
            return [
                'ok' => false,
                'status' => 422,
                'message' => "Permission guard mismatch. Expected {$expectedGuard}, got {$guardMismatch->guard_name}",
            ];
        }

        // Compare requested IDs vs existing IDs to identify missing permissions.
        $existingIds = $permissionsById->keys()->map(fn ($id) => (int) $id)->values();
        $notFoundIds = $requestedIds->diff($existingIds)->values();

        // Build current permission set to avoid reassigning already granted entries.
        $currentPermissionIds = $this->users
            ->getAllPermissions($user)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values();

        // Split IDs into already-assigned and assignable groups.
        $alreadyAssignedIds = $existingIds->intersect($currentPermissionIds)->values();
        $assignableIds = $existingIds->diff($alreadyAssignedIds)->values();

        // Only persist new permission grants when there is something to add.
        if ($assignableIds->isNotEmpty()) {
            $this->users->givePermissions(
                $user,
                $permissionsById->only($assignableIds->all())->values()
            );
        }

        // Choose message that reflects whether new permissions were actually added.
        $message = match (true) {
            $assignableIds->isEmpty() && $alreadyAssignedIds->isNotEmpty() => 'Permission already assigned',
            $assignableIds->isEmpty() => 'No valid permissions found to assign',
            default => 'Permission assigned successfully',
        };

        // Return grouped assignment results for easier frontend handling.
        return [
            'ok' => true,
            'status' => 200,
            'message' => $message,
            'data' => [
                'user_id' => $user->id,
                'requested_permission_ids' => $requestedIds,
                'assigned_permissions' => $this->formatPermissions($permissionsById->only($assignableIds->all())->values()),
                'already_assigned_permissions' => $this->formatPermissions($permissionsById->only($alreadyAssignedIds->all())->values()),
                'not_found_permission_ids' => $notFoundIds,
                'permissions' => $this->formatPermissions($this->users->getAllPermissions($this->users->refresh($user))),
            ],
        ];
    }

    /**
     * Revoke one direct permission from a user.
     */
    public function removePermission(User $user, int $permissionId): array
    {
        $permission = $this->rolePermissions->findPermissionById($permissionId);

        $this->users->revokePermission($user, $permission);

        // Return updated permission names after revocation.
        return [
            'user_id' => $user->id,
            'permissions' => $this->users->getAllPermissions($this->users->refresh($user))->pluck('name')->values(),
        ];
    }

    /**
     * Return the caller-friendly snapshot of a user's access.
     */
    public function myAccess(User $user): array
    {
        return [
            'user_id' => $user->id,
            'roles' => $this->users->getRoleNames($user),
            'permissions' => $this->users->getAllPermissions($user)->pluck('name')->values(),
        ];
    }

    /**
     * Format permissions to a lightweight id-name response schema.
     */
    private function formatPermissions(iterable $permissions): array
    {
        return collect($permissions)
            ->map(fn (Permission $permission) => ['id' => $permission->id, 'name' => $permission->name])
            ->values()
            ->all();
    }
}
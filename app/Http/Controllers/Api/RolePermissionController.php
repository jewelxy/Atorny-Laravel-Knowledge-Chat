<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Services\RolePermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class RolePermissionController extends ApiController
{
    private const DEFAULT_GUARD = 'sanctum';

    /**
     * Inject role and permission business logic service.
     */
    public function __construct(private readonly RolePermissionService $rolePermissionService)
    {
    }

    /**
     * Return all roles with their related permissions.
     */
    public function roles(): JsonResponse
    {
        $roles = $this->rolePermissionService->roles();

        return $this->successResponse('Roles fetched successfully', $roles);
    }

    /**
     * Validate and create a new role for the selected guard.
     */
    public function createRole(Request $request): JsonResponse
    {
        // Use a safe default guard when the client does not provide one.
        $guardName = $request->input('guard_name', self::DEFAULT_GUARD);

        // Enforce role-name uniqueness within the same guard namespace.
        $data = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('roles', 'name')->where('guard_name', $guardName),
            ],
            'guard_name' => ['nullable', 'string', 'max:255'],
        ]);

        $role = $this->rolePermissionService->createRole($data['name'], $guardName);

        return $this->successResponse('Role created successfully', $role, 201);
    }

    /**
     * Return all permissions available in the system.
     */
    public function permissions(): JsonResponse
    {
        $permissions = $this->rolePermissionService->permissions();

        return $this->successResponse('Permissions fetched successfully', $permissions);
    }

    /**
     * Validate and create a permission for the selected guard.
     */
    public function createPermission(Request $request): JsonResponse
    {
        // Use the controller default guard if the request omits guard_name.
        $guardName = $request->input('guard_name', self::DEFAULT_GUARD);

        // Enforce permission-name uniqueness within the same guard.
        $data = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('permissions', 'name')->where('guard_name', $guardName),
            ],
            'guard_name' => ['nullable', 'string', 'max:255'],
        ]);

        $permission = $this->rolePermissionService->createPermission($data['name'], $guardName);

        return $this->successResponse('Permission created successfully', $permission, 201);
    }

    /**
     * Replace a role's permission set with the provided permission IDs.
     */
    public function syncRolePermissions(Request $request, Role $role): JsonResponse
    {
        // Accept a non-empty list of permission IDs to sync.
        $data = $request->validate([
            'permissions'   => ['required', 'array', 'min:1'],
            'permissions.*' => ['required', 'integer', 'distinct', 'exists:permissions,id'],
        ]);

        $result = $this->rolePermissionService->syncRolePermissions($role, $data['permissions']);

        if (! $result['ok']) {
            return $this->errorResponse($result['message'], $result['status']);
        }

        return $this->successResponse('Role permissions updated successfully', [
            'role' => $result['role'],
            'permissions' => $result['permissions'],
        ]);
    }

    /**
     * Assign a single role to a user by role ID.
     */
    public function assignRole(Request $request, User $user): JsonResponse
    {

        $data = $request->validate([
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ]);

        $result = $this->rolePermissionService->assignRole($user, (int) $data['role_id']);

        // Bubble service validation errors with the returned status code.
        if (! $result['ok']) {
            return $this->errorResponse($result['message'], $result['status']);
        }

        return $this->successResponse($result['message'], $result['data']);
    }

    /**
     * Remove a role assignment from a user by role ID.
     */
    public function removeRole(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ]);

        $result = $this->rolePermissionService->removeRole($user, (int) $data['role_id']);

        // Return service-level guard or state validation errors as-is.
        if (! $result['ok']) {
            return $this->errorResponse($result['message'], $result['status']);
        }

        return $this->successResponse($result['message'], $result['data']);
    }

    /**
     * Assign multiple direct permissions to a user by permission IDs.
     */
    public function assignPermission(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'permissions'   => ['required', 'array', 'min:1'],
            'permissions.*' => ['required', 'integer', 'distinct'],
        ]);

        $result = $this->rolePermissionService->assignPermissions($user, $data['permissions']);

        // Preserve detailed error messages from service-level checks.
        if (! $result['ok']) {
            return $this->errorResponse($result['message'], $result['status']);
        }

        return $this->successResponse($result['message'], $result['data']);
    }

    /**
     * Revoke a direct permission from a user.
     */
    public function removePermission(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'permission_id' => ['required', 'integer', 'exists:permissions,id'],
        ]);

        $result = $this->rolePermissionService->removePermission($user, (int) $data['permission_id']);

        return $this->successResponse('Permission removed successfully', [
            'user_id' => $result['user_id'],
            'permissions' => $result['permissions'],
        ]);
    }

    /**
     * Return current authenticated user's roles and permissions.
     */
    public function myAccess(Request $request): JsonResponse
    {
        return $this->successResponse('Access data', $this->rolePermissionService->myAccess($request->user()));
    }
}

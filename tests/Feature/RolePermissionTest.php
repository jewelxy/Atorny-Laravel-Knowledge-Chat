<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_admin_can_sync_role_permissions_and_replace_existing_ones(): void
    {
        $admin = $this->makeAdminUser();
        $role = Role::findOrCreate('manager', 'sanctum');

        Permission::findOrCreate('view reports', 'sanctum');
        $createReports = Permission::findOrCreate('create reports', 'sanctum');
        $role->givePermissionTo('view reports');

        $response = $this->actingAsAdmin($admin)->postJson("/api/roles/{$role->id}/permissions/sync", [
            'permissions' => [$createReports->id],
        ]);

        $response->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.role', 'manager');

        $role->refresh();

        $this->assertTrue($role->hasPermissionTo('create reports'));
        $this->assertFalse($role->hasPermissionTo('view reports'));
    }

    public function test_admin_can_assign_and_remove_direct_permissions_for_a_user(): void
    {
        $admin = $this->makeAdminUser();
        $targetUser = User::factory()->create();
        $permission = Permission::findOrCreate('export reports', 'sanctum');

        $this->actingAsAdmin($admin)->postJson("/api/users/{$targetUser->id}/permissions/assign", [
            'permissions' => [$permission->id],
        ])->assertOk();

        $targetUser->refresh();
        $this->assertTrue($targetUser->hasDirectPermission('export reports'));

        $this->actingAsAdmin($admin)->postJson("/api/users/{$targetUser->id}/permissions/remove", [
            'permission_id' => $permission->id,
        ])->assertOk();

        $targetUser->refresh();
        $this->assertFalse($targetUser->hasDirectPermission('export reports'));
    }

    public function test_admin_routes_return_json_403_for_non_admin_users(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $this->getJson('/api/roles')
            ->assertForbidden()
            ->assertJsonStructure(['status', 'message'])
            ->assertJsonPath('status', false);
    }

    public function test_unauthenticated_request_returns_json_401(): void
    {
        $this->getJson('/api/roles')
            ->assertUnauthorized()
            ->assertJsonPath('status', false)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function test_create_role_rejects_duplicate_name_for_same_guard(): void
    {
        $admin = $this->makeAdminUser();
        Role::findOrCreate('editor', 'sanctum');

        $this->actingAsAdmin($admin)->postJson('/api/roles', ['name' => 'editor'])
            ->assertStatus(422);
    }

    public function test_create_role_allows_same_name_for_different_guard(): void
    {
        $admin = $this->makeAdminUser();
        Role::findOrCreate('editor', 'web');

        // 'editor' exists for 'web' guard — creating for 'sanctum' (default) must succeed
        $this->actingAsAdmin($admin)->postJson('/api/roles', ['name' => 'editor'])
            ->assertStatus(201)
            ->assertJsonPath('status', true);
    }

    public function test_permission_assignment_rejects_cross_guard_permissions(): void
    {
        $admin = $this->makeAdminUser();
        $targetUser = User::factory()->create();

        $legacyPermission = Permission::create([
            'name' => 'legacy export',
            'guard_name' => 'web',
        ]);

        $response = $this->actingAsAdmin($admin)->postJson("/api/users/{$targetUser->id}/permissions/assign", [
            'permissions' => [$legacyPermission->id],
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('status', false);
    }

    private function makeAdminUser(): User
    {
        $user = User::factory()->create();

        Role::findOrCreate('admin', 'sanctum');
        $user->assignRole('admin');

        return $user;
    }

    private function actingAsAdmin(User $user)
    {
        Sanctum::actingAs($user, ['*']);

        return $this;
    }
}
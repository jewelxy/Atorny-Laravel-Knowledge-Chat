<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Seed initial roles and permissions for API auth with Sanctum guard.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'manage roles',
            'manage permissions',
            'assign roles',
            'assign permissions',
            'create blogs',
            'update blogs',
            'delete blogs',
            'use knowledge chat',
            'reindex knowledge base',
            'view knowledge chunks',
            'retrieve internal knowledge',
            'submit contact messages',
            'manage chat prompts',
        ];

        foreach ($permissions as $permissionName) {
            Permission::findOrCreate($permissionName, 'sanctum');
        }

        $adminRole = Role::findOrCreate('admin', 'sanctum');
        $userRole = Role::findOrCreate('user', 'sanctum');

        $adminRole->syncPermissions($permissions);

        $firstUser = User::query()->orderBy('id')->first();

        if ($firstUser) {
            $firstUser->assignRole($adminRole);
        }

        $allUsers = User::query()->get();

        foreach ($allUsers as $user) {
            if (! $user->hasRole('admin')) {
                $user->assignRole($userRole);
            }
        }
    }
}

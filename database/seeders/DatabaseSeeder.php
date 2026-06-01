<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'David Chung',
            'email' => 'david@example.com',
            'password' => '12345678',
        ]);

        $this->call([
            RolePermissionSeeder::class,
            BlogSeeder::class,
            CmsSectionSeeder::class,
            ChatPromptSeeder::class,
        ]);
    }
}

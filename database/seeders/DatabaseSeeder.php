<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $manager  = User::factory()->create([
            'name' => 'Manager',
            'email' => 'manager@test.com',
            'password' => 'pass1234',
        ]);

        $role_manager = Role::create(['name' => 'manager']);
        $role_admin = Role::create(['name' => 'admin']);
        $role_customer = Role::create(['name' => 'customer']);
        $role_visit = Role::create(['name' => 'visit']);

        $manager->assignRole($role_manager);
    }
}

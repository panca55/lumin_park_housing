<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'user']);

        // Optional: Create permissions if needed
        // Permission::firstOrCreate(['name' => 'manage products']);
        // Permission::firstOrCreate(['name' => 'view products']);
        // Permission::firstOrCreate(['name' => 'create orders']);

        // Optional: Assign permissions to roles
        // $adminRole = Role::findByName('admin');
        // $adminRole->givePermissionTo(Permission::all());

        // $userRole = Role::findByName('user');
        // $userRole->givePermissionTo(['view products', 'create orders']);
    }
}

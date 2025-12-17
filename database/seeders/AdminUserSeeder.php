<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::updateOrCreate([
            'email' => 'admin@luminpark.com',
        ], [
            'name' => 'Admin',
            'password' => Hash::make('admin123'),
        ]);
        $admin->assignRole('admin');

        // Create Regular User
        $user = User::updateOrCreate([
            'email' => 'user@luminpark.com',
        ], [
            'name' => 'User',
            'password' => Hash::make('user123'),
        ]);
        $user->assignRole('user');

        // Create another sample customer user
        $customer = User::updateOrCreate([
            'email' => 'customer@luminpark.com',
        ], [
            'name' => 'Customer',
            'password' => Hash::make('customer123'),
        ]);
        $customer->assignRole('user');
    }
}

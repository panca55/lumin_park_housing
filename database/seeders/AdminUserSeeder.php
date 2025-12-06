<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'admin@luminpark.com',
        ], [
            'name' => 'Admin',
            'password' => 'admin123', // will be hashed by User model cast
        ]);
    }
}

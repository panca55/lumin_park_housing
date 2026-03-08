<?php

namespace App\Observers;

use App\Models\User;
use Spatie\Permission\Models\Role;

class UserObserver
{
    /**
     * Handle the User "created" event.
     * Assign 'user' role automatically to new registered users.
     */
    public function created(User $user): void
    {
        // Pastikan role 'user' exists, jika tidak buat
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Berikan role 'user' kepada user baru
        if (!$user->hasRole('user') && !$user->hasRole('admin')) {
            $user->assignRole('user');
        }
    }
}

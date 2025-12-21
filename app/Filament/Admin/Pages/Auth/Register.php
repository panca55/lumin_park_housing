<?php

namespace App\Filament\Admin\Pages\Auth;

use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Register extends BaseRegister
{
    protected static ?string $title = 'Registrasi Akun';

    protected function getRedirectUrl(): string
    {
        return '/login';
    }

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')
                ->label('Nama Lengkap')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->email()
                ->required()
                ->unique(User::class),

            TextInput::make('password')
                ->password()
                ->required()
                ->minLength(8)
                ->same('passwordConfirmation'),

            TextInput::make('passwordConfirmation')
                ->password()
                ->required()
                ->label('Konfirmasi Password'),
        ]);
    }

    protected function handleRegistration(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Assign role 'user' automatically
        $user->assignRole('user');

        return $user;
    }
}

<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UsersForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Masukkan nama lengkap')
                    ->columnSpan(2),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->placeholder('user@example.com')
                    ->columnSpan(2),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required(fn(string $context): bool => $context === 'create')
                    ->dehydrateStateUsing(fn($state) => !empty($state) ? Hash::make($state) : null)
                    ->dehydrated(fn($state) => filled($state))
                    ->revealable()
                    ->maxLength(255)
                    ->placeholder('Masukkan password')
                    ->helperText('Minimal 8 karakter. Kosongkan jika tidak ingin mengubah password.')
                    ->columnSpan(1),

                TextInput::make('password_confirmation')
                    ->label('Konfirmasi Password')
                    ->password()
                    ->required(fn(string $context): bool => $context === 'create')
                    ->same('password')
                    ->dehydrated(false)
                    ->revealable()
                    ->maxLength(255)
                    ->placeholder('Ulangi password')
                    ->columnSpan(1),

                Select::make('roles')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->required()
                    ->helperText('Pilih role untuk user ini (admin/user)')
                    ->placeholder('Pilih role')
                    ->columnSpan(2),
            ])
            ->columns(2);
    }
}

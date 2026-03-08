<?php

namespace App\Filament\Resources\AppSettingResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Schema;
use App\Models\AppSetting;

class AppSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->label('Key')
                    ->required()
                    ->maxLength(255)
                    ->disabled(fn(?AppSetting $record) => $record !== null), // Tidak bisa edit key yang sudah ada

                TextInput::make('name')
                    ->label('Nama Setting')
                    ->required()
                    ->maxLength(255),

                Select::make('type')
                    ->label('Tipe Input')
                    ->options([
                        'text' => 'Text',
                        'textarea' => 'Textarea',
                        'number' => 'Number',
                        'email' => 'Email',
                        'tel' => 'Telephone',
                        'boolean' => 'Boolean (Ya/Tidak)',
                    ])
                    ->required()
                    ->live(),

                Group::make([
                    // Dynamic form field based on type
                    TextInput::make('value')
                        ->label('Nilai')
                        ->visible(fn($get) => in_array($get('type'), ['text', 'email', 'tel', 'number']))
                        ->type(fn($get) => match ($get('type')) {
                            'email' => 'email',
                            'tel' => 'tel',
                            'number' => 'number',
                            default => 'text'
                        }),

                    Textarea::make('value')
                        ->label('Nilai')
                        ->visible(fn($get) => $get('type') === 'textarea')
                        ->rows(4),

                    Toggle::make('value')
                        ->label('Nilai')
                        ->visible(fn($get) => $get('type') === 'boolean'),
                ]),

                Textarea::make('description')
                    ->label('Deskripsi')
                    ->helperText('Penjelasan tentang setting ini'),

                Toggle::make('is_public')
                    ->label('Publik')
                    ->helperText('Dapat diakses oleh user biasa (non-admin)')
            ]);
    }
}

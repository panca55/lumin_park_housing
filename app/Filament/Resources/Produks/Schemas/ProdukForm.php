<?php

namespace App\Filament\Resources\Produks\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckBox;  
use Filament\Schemas\Schema;

class ProdukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name'),
                Select::make('category')
                    ->options([
                        'properti' => 'Properti',
                        'rumah' => 'Rumah',
                    ])
                    ->required(),
                TextInput::make('type')
                    ->required()
                    ->label('Type')
                    ->helperText('Contoh: "32" untuk tipe rumah 32.')
                    ->numeric(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('Rp '),
                FileUpload::make('image')
                    ->label('Image')
                    ->helperText('Upload a product image.')
                    ->directory('images')
                    ->image(),
                FileUpload::make('model_3d')
                    ->label('3D Model')
                    ->helperText('Upload a 3D model file (e.g., .glb, .gltf).')
                    ->acceptedFileTypes(['model/gltf-binary', 'model/gltf+json', '.glb', '.gltf'])
                    ->directory('models')
                    ->maxSize(10240),
                CheckBox::make('is_available')
                    ->label('Available')
                    ->default(false),
            ]);
    }
}

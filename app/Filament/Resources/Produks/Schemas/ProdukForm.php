<?php

namespace App\Filament\Resources\Produks\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckBox;
use Filament\Forms\Components\Repeater;
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
                    ->required()
                    ->live(),
                TextInput::make('type')
                    ->label('Type')
                    ->helperText('Contoh: "32" untuk tipe rumah 32.')
                    ->numeric()
                    ->hidden(fn($get) => $get('category') === 'properti'),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('Rp '),
                FileUpload::make('image')
                    ->label('Image')
                    ->disk('public')
                    ->helperText('Upload a product image.')
                    ->directory('images')
                    ->image(),
                FileUpload::make('model_3d')
                    ->label('3D Model')
                    ->disk('public')
                    ->helperText('Upload a 3D model file (e.g., .glb, .gltf).')
                    ->acceptedFileTypes(['model/gltf-binary', 'model/gltf+json', '.glb', '.gltf'])
                    ->directory('models')
                    ->minSize(512)
                    ->maxSize(50000),
                FileUpload::make('denah')
                    ->label('Denah (Floor Plan)')
                    ->disk('public')
                    ->helperText('Upload floor plan image (only for rumah category).')
                    ->directory('images/denah')
                    ->image()
                    ->hidden(fn($get) => $get('category') !== 'rumah'),
                CheckBox::make('is_available')
                    ->label('Available')
                    ->default(false),

                Repeater::make('gambarProduks')
                    ->relationship()
                    ->label('Gambar Produk')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Gambar')
                            ->disk('public')
                            ->directory('images/produk')
                            ->image()
                            ->required(),
                    ])
                    ->columnSpanFull()
                    ->defaultItems(0)
                    ->addActionLabel('Tambah Gambar')
                    ->collapsible(),

                Repeater::make('panoramaProduks')
                    ->relationship()
                    ->label('Panorama Produk')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Panorama')
                            ->placeholder('Contoh: Ruang Tamu, Kamar Tidur, dll.')
                            ->maxLength(255),
                        FileUpload::make('image')
                            ->label('Panorama')
                            ->disk('public')
                            ->directory('images/panorama')
                            ->image()
                            ->required(),
                    ])
                    ->columnSpanFull()
                    ->defaultItems(0)
                    ->addActionLabel('Tambah Panorama')
                    ->collapsible(),
            ]);
    }
}

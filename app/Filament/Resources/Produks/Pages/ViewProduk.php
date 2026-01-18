<?php

namespace App\Filament\Resources\Produks\Pages;

use App\Filament\Resources\Produks\ProdukResource;
// use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Auth;

class ViewProduk extends ViewRecord
{
    protected static string $resource = ProdukResource::class;


    /**
     * 🔥 PAKSA FULL WIDTH
     */
    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    /**
     * 🔥 MATIKAN GRID DEFAULT FILAMENT
     */
    protected function getContentGrid(): ?array
    {
        return [
            'default' => 1,
            'xl' => 1,
        ];
    }

    /**
     * MAIN LAYOUT
     */
    public function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            View::make('filament.infolists.product-detail')
                ->viewData(fn($record) => [
                    'model_3d'     => filled($record->model_3d) ? \Storage::url($record->model_3d) : null,
                    'image'        => $record->image,
                    'name'         => $record->name,
                    'description'  => $record->description,
                    'price'        => $record->price,
                    'category'     => $record->category,
                    'type'         => $record->type,
                    'is_available' => $record->is_available,
                    'created_at'   => $record->created_at,
                    'updated_at'   => $record->updated_at,
                    'denah'        => $record->denah,
                    'gambar_produks' => $record->gambarProduks,
                    'panorama_produks' => $record->panoramaProduks,
                ])
                ->columnSpanFull(),
        ]);
    }
}

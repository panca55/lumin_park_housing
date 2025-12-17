<?php

namespace App\Filament\Resources\Produks\Pages;

use App\Filament\Resources\Produks\ProdukResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Auth;

class ViewProduk extends ViewRecord
{
    protected static string $resource = ProdukResource::class;

    protected function getHeaderActions(): array
    {
        return Auth::user()?->hasRole('admin')
            ? [EditAction::make()]
            : [];
    }

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
            // Wrapper Grid untuk kontrol penuh layout
            Grid::make([
                'default' => 1,
                'lg' => 2,
            ])
                ->schema([

                    // LEFT — 3D VIEWER
                    View::make('filament.infolists.3d-viewer')
                        ->viewData(fn($record) => [
                            'model_3d' => filled($record->model_3d)
                                ? \Storage::url($record->model_3d)
                                : null,
                        ])
                        ->columnSpan([
                            'default' => 1,
                            'lg' => 1,
                        ]),

                    // RIGHT — IMAGE + DETAILS
                    View::make('filament.infolists.product-right-panel')
                        ->viewData(fn($record) => [
                            'image'        => $record->image,
                            'name'         => $record->name,
                            'description'  => $record->description,
                            'price'        => $record->price,
                            'category'     => $record->category,
                            'type'         => $record->type,
                            'is_available' => $record->is_available,
                            'created_at'   => $record->created_at,
                            'updated_at'   => $record->updated_at,
                        ])
                        ->columnSpan([
                            'default' => 1,
                            'lg' => 1,
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }
}

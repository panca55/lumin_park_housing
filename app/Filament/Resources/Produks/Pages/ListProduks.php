<?php

namespace App\Filament\Resources\Produks\Pages;

use App\Filament\Resources\Produks\ProdukResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListProduks extends ListRecords
{
    protected static string $resource = ProdukResource::class;

    protected function getHeaderActions(): array
    {
        // Hanya admin yang bisa create
        if (Auth::user()?->hasRole('admin')) {
            return [
                CreateAction::make(),
            ];
        }
        return [];
    }

    // Force table layout (no grid)
    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 25, 50, 100];
    }

    // Force disable content grid
    public function getTableContentGrid(): ?array
    {
        return null;
    }
}

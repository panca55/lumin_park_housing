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

    // Grid 3x3 pagination options
    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [9, 18, 27, 36];
    }

    // Enable content grid for better product display
    public function getTableContentGrid(): ?array
    {
        return [
            'md' => 2,  // 2 kolom di medium screen
            'lg' => 3,  // 3 kolom di large screen (optimal untuk desktop)
            'xl' => 3,  // tetap 3 kolom di xl screen
        ];
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Produk;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseStatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalProducts = Produk::count();
        $availableProducts = Produk::where('is_available', true)->count();
        $pendingPayments = Payment::where('status', 'pending')->count();
        $totalRevenue = Payment::where('status', 'approved')->sum('amount');

        return [
            Stat::make('Total Products', $totalProducts)
                ->description('Total products in listing')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('success'),

            Stat::make('Available Products', $availableProducts)
                ->description('Products that are available for sale')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('warning'),
        ];
    }
}

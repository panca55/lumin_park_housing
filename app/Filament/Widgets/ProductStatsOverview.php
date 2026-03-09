<?php

namespace App\Filament\Widgets;

use App\Models\Produk;
use App\Models\MeetingRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ProductStatsOverview extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    protected function getStats(): array
    {
        $totalProducts = Produk::count();
        $availableProducts = Produk::where('is_available', true)->count();
        $soldProducts = Produk::where('is_sold', true)->count();
        $totalMeetings = MeetingRequest::count();
        $pendingMeetings = MeetingRequest::where('status', 'pending')->count();
        $approvedMeetings = MeetingRequest::where('status', 'approved')->count();

        $soldPercentage = $totalProducts > 0 ? round(($soldProducts / $totalProducts) * 100, 1) : 0;

        return [
            Stat::make('Total Produk', $totalProducts)
                ->description('Semua produk dalam sistem')
                ->descriptionIcon('heroicon-m-home')
                ->color('primary'),

            Stat::make('Produk Tersedia', $availableProducts)
                ->description('Masih dapat dijual')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Produk Terjual', $soldProducts)
                ->description("({$soldPercentage}% dari total)")
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($soldPercentage > 50 ? 'success' : 'warning'),

            Stat::make('Total Meeting', $totalMeetings)
                ->description('Semua permintaan meeting')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Meeting Pending', $pendingMeetings)
                ->description('Menunggu persetujuan')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Meeting Disetujui', $approvedMeetings)
                ->description('Sudah diapprove')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
        ];
    }
}

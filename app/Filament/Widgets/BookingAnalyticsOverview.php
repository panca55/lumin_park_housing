<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\MeetingRequest;
use App\Models\Produk;
use Illuminate\Support\Facades\Cache;

class BookingAnalyticsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Meeting Requests', $this->getTotalMeetingRequests())
                ->description('Semua waktu')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),

            Stat::make('Meeting Requests Bulan Ini', $this->getMonthlyMeetingRequests())
                ->description('Bulan ' . now()->format('F Y'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Pending Meetings', $this->getPendingMeetings())
                ->description('Menunggu konfirmasi')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Produk Terpopuler', $this->getMostPopularProduct())
                ->description('Berdasarkan booking count')
                ->descriptionIcon('heroicon-m-star')
                ->color('info'),
        ];
    }

    private function getTotalMeetingRequests(): int
    {
        return Cache::remember('stats.total_meetings', 1800, function () {
            return MeetingRequest::where('status', '!=', 'cancelled')->count();
        });
    }

    private function getMonthlyMeetingRequests(): int
    {
        return Cache::remember('stats.monthly_meetings', 1800, function () {
            return MeetingRequest::where('status', '!=', 'cancelled')
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count();
        });
    }

    private function getPendingMeetings(): int
    {
        return MeetingRequest::whereIn('status', ['pending', 'confirmed'])->count();
    }

    private function getMostPopularProduct(): string
    {
        return Cache::remember('stats.most_popular_product', 3600, function () {
            $mostBooked = Produk::getMostBookedProducts(1)->first();

            if ($mostBooked) {
                $count = $mostBooked->getBookingCount();
                return "{$mostBooked->name} ({$count}x)";
            }

            return 'Belum ada data';
        });
    }
}

<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\MeetingRequest;
use Illuminate\Support\Facades\Cache;

class BookingTrendChart extends ChartWidget
{
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public function getHeading(): string
    {
        return 'Trend Meeting Requests (30 Hari Terakhir)';
    }

    protected function getData(): array
    {
        $data = Cache::remember('chart.booking_trend', 1800, function () {
            $bookings = MeetingRequest::where('status', '!=', 'cancelled')
                ->where('created_at', '>=', now()->subDays(30))
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('count', 'date')
                ->toArray();

            // Fill missing dates with 0
            $labels = [];
            $data = [];

            for ($i = 29; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $dateKey = $date->format('Y-m-d');
                $labels[] = $date->format('d M');
                $data[] = $bookings[$dateKey] ?? 0;
            }

            return [
                'labels' => $labels,
                'data' => $data,
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Meeting Requests',
                    'data' => $data['data'],
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.3,
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

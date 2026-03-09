<?php

namespace App\Filament\Widgets;

use App\Models\Produk;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class ProductSalesChart extends ChartWidget
{
    protected static ?int $sort = 10;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        // Untuk sementara tampilkan untuk semua user yang login
        // Nanti bisa disesuaikan dengan logic role yang sudah fix
        return Auth::check();
    }

    public function getHeading(): ?string
    {
        return 'Produk Terjual Berdasarkan Tipe';
    }

    protected function getData(): array
    {
        // Get sold products grouped by type
        $soldProducts = Produk::where('is_sold', true)
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->pluck('count', 'type')
            ->toArray();

        // Prepare data for chart
        $types = array_keys($soldProducts);
        $counts = array_values($soldProducts);

        // Format labels dengan "Rumah Type {type}"
        $formattedLabels = array_map(function ($type) {
            return "Rumah Type {$type}";
        }, $types);

        // Generate colors for each bar
        $colors = [
            '#ef4444', // red-500
            '#f97316', // orange-500
            '#eab308', // yellow-500
            '#22c55e', // green-500
            '#3b82f6', // blue-500
            '#8b5cf6', // violet-500
            '#ec4899', // pink-500
            '#06b6d4', // cyan-500
            '#84cc16', // lime-500
            '#f59e0b', // amber-500
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Produk Terjual',
                    'data' => $counts,
                    'backgroundColor' => array_slice($colors, 0, count($types)),
                    'borderColor' => array_slice($colors, 0, count($types)),
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $formattedLabels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                        'precision' => 0,
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Jumlah Produk',
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Tipe Produk',
                    ],
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
            'interaction' => [
                'intersect' => false,
                'mode' => 'index',
            ],
        ];
    }

    public function getDescription(): ?string
    {
        $totalSold = Produk::where('is_sold', true)->count();
        $totalProducts = Produk::count();
        $percentage = $totalProducts > 0 ? round(($totalSold / $totalProducts) * 100, 1) : 0;

        return "Total {$totalSold} dari {$totalProducts} produk telah terjual ({$percentage}%)";
    }
}

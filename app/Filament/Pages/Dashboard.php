<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            \Filament\Widgets\AccountWidget::class,
            \App\Filament\Widgets\UserNotificationWidget::class,
            \App\Filament\Widgets\ProductStatsOverview::class,
            \App\Filament\Widgets\ProductSalesChart::class,
        ];
    }
}

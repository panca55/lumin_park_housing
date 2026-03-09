<?php

namespace App\Providers;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Resources\ProdukResource;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use App\Filament\Pages\Login;
use App\Filament\Pages\EditProfile;

class FilamentPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('dashboard')
            ->path('dashboard')
            ->login(Login::class)
            ->registration()
            ->colors([
                'primary' => Color::Blue,
            ])
            // Explicit registration untuk performa lebih baik
            ->resources($this->getResources())
            ->pages([
                Pages\Dashboard::class,
                EditProfile::class,
            ])
            // Load widgets hanya yang diperlukan
            ->widgets($this->getWidgets())
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label('Edit Profil')
                    ->icon('heroicon-o-user-circle')
                    ->url(fn() => EditProfile::getUrl()),

                'landing' => MenuItem::make()
                    ->label('Landing Page')
                    ->url('/')
                    ->icon('heroicon-o-home'),
            ])
            ->navigationItems([
                NavigationItem::make('Landing Page')
                    ->url('/', shouldOpenInNewTab: false)
                    ->icon('heroicon-o-home')
                    ->group('External')
                    ->sort(999),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authGuard('web')
            ->authMiddleware([
                Authenticate::class,
            ])
            ->brandName('Lumin Park Housing')  // Static untuk performa
            ->favicon(asset('favicon.ico'))
            ->viteTheme('resources/css/app.css'); // Load custom CSS with responsive improvements
    }

    /**
     * Get resources explicitly to avoid discovery overhead
     */
    private function getResources(): array
    {
        return [
            \App\Filament\Resources\Produks\ProdukResource::class,
            \App\Filament\Resources\AppSettingResource::class,
            // Add other resources as needed
        ];
    }

    /**
     * Get widgets based on context untuk performa optimal
     */
    private function getWidgets(): array
    {
        // Base widgets untuk semua user
        $widgets = [
            Widgets\AccountWidget::class,
        ];

        // Widget admin hanya load saat diperlukan via canView()
        if ($this->shouldLoadAdminWidgets()) {
            $widgets = array_merge($widgets, [
                \App\Filament\Widgets\BookingAnalyticsOverview::class,
                \App\Filament\Widgets\BookingTrendChart::class,
                \App\Filament\Widgets\MostBookedProductsTable::class,
            ]);
        }

        return $widgets;
    }

    /**
     * Check if should load admin widgets
     */
    private function shouldLoadAdminWidgets(): bool
    {
        // Hanya load untuk user yang sudah login sebagai admin
        return auth()->check() && auth()->user()->hasRole('admin');
    }
}

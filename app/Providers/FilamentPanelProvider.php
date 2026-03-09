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
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\ProdukResource;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use App\Filament\Pages\Login;
use App\Filament\Pages\EditProfile;
use App\Filament\Pages\Dashboard;

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
                Dashboard::class,
                EditProfile::class,
            ])
            -> discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
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
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->spa();
    }

    /**
     * Get resources explicitly to avoid discovery overhead
     */
    private function getResources(): array
    {
        return [
            \App\Filament\Resources\Users\UsersResource::class,
            \App\Filament\Resources\Produks\ProdukResource::class,
            \App\Filament\Resources\AppSettingResource::class,
            // Add other resources as needed
        ];
    }
}

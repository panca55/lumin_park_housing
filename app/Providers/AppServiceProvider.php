<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use App\Filament\Responses\LogoutResponse;
use App\Filament\Responses\CustomLoginResponse;
use App\Models\User;
use App\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            LogoutResponseContract::class,
            LogoutResponse::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register User Observer untuk auto-assign role
        User::observe(UserObserver::class);

        // Performance optimizations
        $this->configurePerformanceOptimizations();

        // Development query optimization
        if (app()->environment('local')) {
            $this->configureDevelopmentOptimizations();
        }
    }

    /**
     * Configure performance optimizations for production
     */
    private function configurePerformanceOptimizations(): void
    {
        // Prevent lazy loading violations in production
        Model::preventLazyLoading(!app()->isProduction());

        // Prevent accessing missing attributes
        Model::preventAccessingMissingAttributes(!app()->isProduction());

        // Enable query caching for common queries
        if (app()->isProduction()) {
            // Cache configuration queries for 1 hour
            Cache::remember('app_config', 3600, function () {
                return config()->all();
            });
        }
    }

    /**
     * Configure development-specific optimizations
     */
    private function configureDevelopmentOptimizations(): void
    {
        // Log slow queries in development
        DB::listen(function ($query) {
            if ($query->time > 1000) { // Log queries slower than 1 second
                logger()->warning('Slow query detected', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time . 'ms'
                ]);
            }
        });

        // Detect N+1 queries
        if (class_exists('\Barryvdh\Debugbar\ServiceProvider')) {
            Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
                logger()->warning('N+1 Query detected', [
                    'model' => get_class($model),
                    'relation' => $relation
                ]);
            });
        }
    }
}

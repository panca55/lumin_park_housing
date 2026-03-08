<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Models\Produk;

class PerformanceServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register performance-related services
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Cache view composers for heavy data
        $this->registerViewComposers();

        // Warm up common caches
        if (app()->isProduction()) {
            $this->warmUpCaches();
        }
    }

    /**
     * Register view composers to cache heavy data
     */
    private function registerViewComposers(): void
    {
        // Cache categories and types for forms
        View::composer(['filament.resources.produks.*'], function ($view) {
            $categories = Cache::remember('view.categories', 3600, function () {
                return Produk::getCategories()->toArray();
            });

            $types = Cache::remember('view.types', 3600, function () {
                return Produk::getTypes()->toArray();
            });

            $view->with(compact('categories', 'types'));
        });
    }

    /**
     * Warm up common caches on application boot
     */
    private function warmUpCaches(): void
    {
        // Warm up in background to avoid blocking requests
        dispatch(function () {
            Cache::rememberForever('app.stats', function () {
                return [
                    'total_products' => Produk::count(),
                    'available_products' => Produk::where('is_available', true)->count(),
                    'categories' => Produk::getCategories(),
                    'types' => Produk::getTypes(),
                    'last_updated' => now()->toISOString()
                ];
            });
        })->afterResponse();
    }
}

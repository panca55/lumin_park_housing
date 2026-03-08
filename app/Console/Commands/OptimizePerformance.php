<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class OptimizePerformance extends Command
{
    protected $signature = 'app:optimize-performance {--clear : Clear all caches first}';
    protected $description = 'Optimize application performance by caching routes, config, views, and more';

    public function handle(): int
    {
        $this->info('🚀 Starting Laravel Performance Optimization...');

        // Clear caches if requested
        if ($this->option('clear')) {
            $this->info('🧹 Clearing all caches...');
            $this->clearAllCaches();
        }

        // Optimize for production
        $this->info('⚡ Optimizing for production...');

        // Cache configurations
        $this->call('config:cache');
        $this->info('✅ Configuration cached');

        // Cache routes
        $this->call('route:cache');
        $this->info('✅ Routes cached');

        // Cache views
        $this->call('view:cache');
        $this->info('✅ Views cached');

        // Cache events
        $this->call('event:cache');
        $this->info('✅ Events cached');

        // Optimize composer autoloader
        $this->info('📦 Optimizing Composer autoloader...');
        exec('composer dump-autoload --optimize', $output);
        $this->info('✅ Composer autoloader optimized');

        // Build assets for production
        if (file_exists(base_path('package.json'))) {
            $this->info('🎨 Building production assets...');
            exec('npm run build', $output);
            $this->info('✅ Assets built for production');
        }

        // Warm up application caches
        $this->info('🔥 Warming up application caches...');
        $this->warmUpCaches();

        $this->info('');
        $this->info('🎉 Performance optimization completed successfully!');
        $this->info('');
        $this->info('📊 Performance Tips:');
        $this->warn('   • Consider using Redis for caching instead of database');
        $this->warn('   • Enable OPcache in your PHP configuration');
        $this->warn('   • Use CDN for static assets');
        $this->warn('   • Monitor slow queries with Laravel Telescope');

        return Command::SUCCESS;
    }

    private function clearAllCaches(): void
    {
        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('route:clear');
        $this->call('view:clear');
        $this->call('event:clear');

        // Clear opcache if available
        if (function_exists('opcache_reset')) {
            opcache_reset();
            $this->info('✅ OPcache cleared');
        }
    }

    private function warmUpCaches(): void
    {
        try {
            // Warm up database query caches
            Cache::remember('app.stats', 3600, function () {
                return [
                    'total_products' => \App\Models\Produk::count(),
                    'available_products' => \App\Models\Produk::where('is_available', true)->count(),
                    'categories' => \App\Models\Produk::getCategories(),
                    'types' => \App\Models\Produk::getTypes(),
                    'last_updated' => now()->toISOString()
                ];
            });

            // Cache available products
            \App\Models\Produk::getAvailable();

            $this->info('✅ Application caches warmed up');
        } catch (\Exception $e) {
            $this->warn("⚠️  Could not warm up some caches: {$e->getMessage()}");
        }
    }
}

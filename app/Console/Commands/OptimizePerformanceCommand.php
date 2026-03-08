<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class OptimizePerformanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:optimize {--clear : Clear all cashes before optimizing}';

    /**
     * The console command description.
     */
    protected $description = 'Optimize application performance for better login and page load speed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Optimizing Lumin Park Housing Application Performance');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        if ($this->option('clear')) {
            $this->clearAllCaches();
        }

        $this->optimizeConfiguration();
        $this->optimizeRoutes();
        $this->optimizeViews();
        $this->optimizeAutoloader();

        $this->newLine();
        $this->info('✅ Application optimization completed!');
        $this->info('🔥 Login and dashboard should now load much faster');

        $this->showPerformanceTips();

        return 0;
    }

    private function clearAllCaches(): void
    {
        $this->warn('🧹 Clearing all caches...');

        $commands = [
            'config:clear' => 'Configuration cache',
            'route:clear' => 'Route cache',
            'view:clear' => 'View cache',
            'cache:clear' => 'Application cache',
        ];

        foreach ($commands as $command => $description) {
            try {
                Artisan::call($command);
                $this->line("   ✓ Cleared {$description}");
            } catch (\Exception $e) {
                $this->error("   ✗ Failed to clear {$description}: " . $e->getMessage());
            }
        }
        $this->newLine();
    }

    private function optimizeConfiguration(): void
    {
        $this->info('⚙️ Optimizing configuration...');

        try {
            Artisan::call('config:cache');
            $this->line('   ✓ Configuration cached successfully');
        } catch (\Exception $e) {
            $this->error('   ✗ Failed to cache configuration: ' . $e->getMessage());
        }
    }

    private function optimizeRoutes(): void
    {
        $this->info('🛣️ Optimizing routes...');

        try {
            Artisan::call('route:cache');
            $this->line('   ✓ Routes cached successfully');
        } catch (\Exception $e) {
            $this->error('   ✗ Failed to cache routes: ' . $e->getMessage());
        }
    }

    private function optimizeViews(): void
    {
        $this->info('👁️ Optimizing views...');

        try {
            Artisan::call('view:cache');
            $this->line('   ✓ Views cached successfully');
        } catch (\Exception $e) {
            $this->error('   ✗ Failed to cache views: ' . $e->getMessage());
        }
    }

    private function optimizeAutoloader(): void
    {
        $this->info('🔄 Optimizing autoloader...');

        try {
            // Optimize composer autoloader
            exec('composer dump-autoload --optimize --no-dev 2>&1', $output, $returnCode);

            if ($returnCode === 0) {
                $this->line('   ✓ Composer autoloader optimized');
            } else {
                $this->warn('   ⚠ Composer autoloader optimization skipped (composer not found or error)');
            }
        } catch (\Exception $e) {
            $this->warn('   ⚠ Composer autoloader optimization failed: ' . $e->getMessage());
        }
    }

    private function showPerformanceTips(): void
    {
        $this->newLine();
        $this->info('💡 Performance Tips:');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━');

        $tips = [
            '🔸 Use browser cache headers in production',
            '🔸 Enable Gzip compression on web server',
            '🔸 Use Redis for session and cache in production',
            '🔸 Optimize database indexes for better query performance',
            '🔸 Use CDN for static assets in production',
            '🔸 Consider using OPcache in production environment',
            '🔸 Run this command periodically: php artisan app:optimize',
        ];

        foreach ($tips as $tip) {
            $this->line("   {$tip}");
        }

        $this->newLine();
        $this->comment('Note: Clear caches when deploying new code with --clear flag');
    }
}

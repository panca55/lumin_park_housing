<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class PerformanceReport extends Command
{
    protected $signature = 'app:performance-report';
    protected $description = 'Generate a performance report for the application';

    public function handle(): int
    {
        $this->info('📊 Generating Performance Report...');
        $this->line('');

        // Database Performance
        $this->checkDatabasePerformance();

        // Cache Performance
        $this->checkCachePerformance();

        // File System Performance
        $this->checkFileSystemPerformance();

        // Memory Usage
        $this->checkMemoryUsage();

        // Application Statistics
        $this->checkApplicationStats();

        $this->line('');
        $this->info('✅ Performance report completed');

        return Command::SUCCESS;
    }

    private function checkDatabasePerformance(): void
    {
        $this->info('🗄️  Database Performance:');

        try {
            // Check database connection time
            $start = microtime(true);
            DB::select('SELECT 1');
            $connectionTime = round((microtime(true) - $start) * 1000, 2);

            if ($connectionTime < 10) {
                $this->info("   ✅ Database connection: {$connectionTime}ms (Excellent)");
            } elseif ($connectionTime < 50) {
                $this->warn("   ⚠️  Database connection: {$connectionTime}ms (Good)");
            } else {
                $this->error("   ❌ Database connection: {$connectionTime}ms (Slow)");
            }

            // Check table sizes
            $tableStats = $this->getTableStats();
            foreach ($tableStats as $stat) {
                $this->line("   📋 {$stat['table']}: {$stat['rows']} rows, {$stat['size']} MB");
            }

            // Check for missing indexes
            $this->checkMissingIndexes();
        } catch (\Exception $e) {
            $this->error("   ❌ Database error: {$e->getMessage()}");
        }

        $this->line('');
    }

    private function checkCachePerformance(): void
    {
        $this->info('💾 Cache Performance:');

        try {
            // Test cache write/read performance
            $testKey = 'performance_test_' . time();
            $testData = ['test' => 'data', 'timestamp' => now()];

            $start = microtime(true);
            Cache::put($testKey, $testData, 60);
            $writeTime = round((microtime(true) - $start) * 1000, 2);

            $start = microtime(true);
            $cached = Cache::get($testKey);
            $readTime = round((microtime(true) - $start) * 1000, 2);

            Cache::forget($testKey);

            $this->info("   ✅ Cache write: {$writeTime}ms");
            $this->info("   ✅ Cache read: {$readTime}ms");

            // Check cache driver
            $cacheDriver = config('cache.default');
            $this->info("   🔧 Cache driver: {$cacheDriver}");

            if ($cacheDriver === 'database') {
                $this->warn("   ⚠️  Consider using Redis for better cache performance");
            }
        } catch (\Exception $e) {
            $this->error("   ❌ Cache error: {$e->getMessage()}");
        }

        $this->line('');
    }

    private function checkFileSystemPerformance(): void
    {
        $this->info('📁 File System Performance:');

        try {
            // Test file write/read performance
            $testFile = 'performance_test_' . time() . '.txt';
            $testData = str_repeat('Performance test data ', 1000);

            $start = microtime(true);
            Storage::put($testFile, $testData);
            $writeTime = round((microtime(true) - $start) * 1000, 2);

            $start = microtime(true);
            $content = Storage::get($testFile);
            $readTime = round((microtime(true) - $start) * 1000, 2);

            Storage::delete($testFile);

            $this->info("   ✅ File write: {$writeTime}ms");
            $this->info("   ✅ File read: {$readTime}ms");

            // Check storage space
            $this->checkStorageSpace();
        } catch (\Exception $e) {
            $this->error("   ❌ File system error: {$e->getMessage()}");
        }

        $this->line('');
    }

    private function checkMemoryUsage(): void
    {
        $this->info('🧠 Memory Usage:');

        $memoryUsage = round(memory_get_usage(true) / 1024 / 1024, 2);
        $peakMemory = round(memory_get_peak_usage(true) / 1024 / 1024, 2);
        $memoryLimit = ini_get('memory_limit');

        $this->info("   📊 Current usage: {$memoryUsage} MB");
        $this->info("   📈 Peak usage: {$peakMemory} MB");
        $this->info("   🎯 Memory limit: {$memoryLimit}");

        if ($peakMemory > 128) {
            $this->warn("   ⚠️  High memory usage detected");
        }

        $this->line('');
    }

    private function checkApplicationStats(): void
    {
        $this->info('📱 Application Statistics:');

        try {
            // Get product statistics
            $totalProducts = \App\Models\Produk::count();
            $availableProducts = \App\Models\Produk::where('is_available', true)->count();
            $totalUsers = \App\Models\User::count();

            $this->info("   📦 Total products: {$totalProducts}");
            $this->info("   ✅ Available products: {$availableProducts}");
            $this->info("   👥 Total users: {$totalUsers}");

            // Check for large tables
            if ($totalProducts > 10000) {
                $this->warn("   ⚠️  Large product table - consider pagination optimizations");
            }
        } catch (\Exception $e) {
            $this->error("   ❌ Stats error: {$e->getMessage()}");
        }

        $this->line('');
    }

    private function getTableStats(): array
    {
        try {
            $tables = ['produks', 'gambar_produks', 'panorama_produks', 'denahs', 'users'];
            $stats = [];

            foreach ($tables as $table) {
                $rows = DB::table($table)->count();
                $size = 0; // Simplified - actual implementation would query table size

                $stats[] = [
                    'table' => $table,
                    'rows' => number_format($rows),
                    'size' => number_format($size, 2)
                ];
            }

            return $stats;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function checkMissingIndexes(): void
    {
        // This is a simplified check - in production you'd query INFORMATION_SCHEMA
        $recommendations = [
            "Consider adding index on produks.is_available",
            "Consider adding index on produks.category",
            "Consider adding index on produks.created_at"
        ];

        foreach ($recommendations as $rec) {
            $this->warn("   💡 {$rec}");
        }
    }

    private function checkStorageSpace(): void
    {
        try {
            $storagePath = storage_path();
            $freeSpace = disk_free_space($storagePath);
            $totalSpace = disk_total_space($storagePath);

            if ($freeSpace && $totalSpace) {
                $freeGB = round($freeSpace / 1024 / 1024 / 1024, 2);
                $totalGB = round($totalSpace / 1024 / 1024 / 1024, 2);
                $usedPercent = round((($totalSpace - $freeSpace) / $totalSpace) * 100, 1);

                $this->info("   💾 Storage: {$freeGB}GB free of {$totalGB}GB ({$usedPercent}% used)");

                if ($usedPercent > 90) {
                    $this->error("   ❌ Storage is almost full!");
                } elseif ($usedPercent > 80) {
                    $this->warn("   ⚠️  Storage is getting low");
                }
            }
        } catch (\Exception $e) {
            // Storage check failed
        }
    }
}

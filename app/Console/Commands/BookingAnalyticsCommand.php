<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Produk;
use App\Models\MeetingRequest;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class BookingAnalyticsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'booking:analytics 
                            {--refresh-cache : Refresh all analytics cache}
                            {--top-products=5 : Show top N most booked products}
                            {--product-id= : Show analytics for specific product}';

    /**
     * The console command description.
     */
    protected $description = 'Display booking analytics and manage cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🏠 Lumin Park Housing - Booking Analytics');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        if ($this->option('refresh-cache')) {
            $this->refreshAllCache();
        }

        if ($productId = $this->option('product-id')) {
            $this->showProductAnalytics($productId);
        } else {
            $this->showOverallAnalytics();
            $this->showTopProducts();
        }

        return 0;
    }

    private function refreshAllCache(): void
    {
        $this->info('🔄 Refreshing analytics cache...');

        // Clear all booking-related cache
        Cache::forget('stats.total_meetings');
        Cache::forget('stats.monthly_meetings');
        Cache::forget('stats.most_popular_product');
        Cache::forget('chart.booking_trend');
        Cache::forget('products.most_booked');

        // Refresh cache for all products
        Produk::chunk(20, function ($products) {
            foreach ($products as $product) {
                $product->refreshBookingCountCache();
            }
        });

        $this->info('✅ Cache refreshed successfully!');
        $this->newLine();
    }

    private function showOverallAnalytics(): void
    {
        $totalMeetings = MeetingRequest::where('status', '!=', 'cancelled')->count();
        $monthlyMeetings = MeetingRequest::where('status', '!=', 'cancelled')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
        $pendingMeetings = MeetingRequest::whereIn('status', ['pending', 'confirmed'])->count();

        $this->info('📊 Overall Statistics:');
        $this->line("   Total Meeting Requests: {$totalMeetings}");
        $this->line("   This Month: {$monthlyMeetings}");
        $this->line("   Pending: {$pendingMeetings}");
        $this->newLine();
    }

    private function showTopProducts(): void
    {
        $limit = $this->option('top-products');
        $this->info("🌟 Top {$limit} Most Booked Products:");

        $topProducts = Produk::getMostBookedProducts($limit);

        if ($topProducts->isEmpty()) {
            $this->warn('   No products with bookings found.');
            return;
        }

        $headers = ['Rank', 'Product ID', 'Name', 'Category', 'Total Bookings', 'Pending', 'Popularity Score'];
        $rows = [];

        foreach ($topProducts as $index => $product) {
            $analytics = $product->getBookingAnalytics();
            $rows[] = [
                $index + 1,
                $product->id,
                \Str::limit($product->name, 30),
                $product->category,
                $analytics['total_bookings'],
                $analytics['pending_bookings'],
                $analytics['popularity_score']
            ];
        }

        $this->table($headers, $rows);
        $this->newLine();
    }

    private function showProductAnalytics(int $productId): void
    {
        $product = Produk::find($productId);

        if (!$product) {
            $this->error("Product with ID {$productId} not found.");
            return;
        }

        $this->info("🏠 Analytics for: {$product->name}");
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        $analytics = $product->getBookingAnalytics();

        $this->info('📈 Booking Statistics:');
        $this->line("   Total Bookings: {$analytics['total_bookings']}");
        $this->line("   Pending: {$analytics['pending_bookings']}");
        $this->line("   Completed: {$analytics['completed_bookings']}");
        $this->line("   Average per Day: {$analytics['average_per_day']}");
        $this->line("   Popularity Score: {$analytics['popularity_score']}");
        $this->newLine();

        $this->info('📅 Recent Trend (Last 7 days):');
        $trend = $analytics['recent_trend'];
        $dates = array_keys($trend);
        $counts = array_values($trend);

        $this->table(
            array_map(fn($date) => \Carbon\Carbon::parse($date)->format('M d'), $dates),
            [$counts]
        );
        $this->newLine();

        // Show recent meeting requests
        $recentMeetings = MeetingRequest::where('status', '!=', 'cancelled')
            ->where(function ($query) use ($product) {
                $query->whereJsonContains('produk_ids', $product->id);
            })
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();

        if ($recentMeetings->isNotEmpty()) {
            $this->info('📋 Recent Meeting Requests:');
            $rows = [];
            foreach ($recentMeetings as $meeting) {
                $rows[] = [
                    $meeting->created_at->format('Y-m-d H:i'),
                    $meeting->user->name ?? 'N/A',
                    $meeting->status,
                    $meeting->tanggal_meeting?->format('Y-m-d') ?? 'N/A',
                ];
            }
            $this->table(['Date Created', 'Customer', 'Status', 'Meeting Date'], $rows);
        }
    }
}

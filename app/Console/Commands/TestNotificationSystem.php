<?php

namespace App\Console\Commands;

use App\Models\Produk;
use App\Models\MeetingRequest;
use App\Models\User;
use Illuminate\Console\Command;

class TestNotificationSystem extends Command
{
    protected $signature = 'app:test-notifications';
    protected $description = 'Test the product sold notification system';

    public function handle(): int
    {
        $this->info('🧪 Testing Product Sold Notification System...');
        $this->line('');

        // Check if we have test data
        $this->checkTestData();

        // Show current meeting requests
        $this->showMeetingRequests();

        // Test notification trigger
        if ($this->confirm('Do you want to test notification by marking a product as sold?')) {
            $this->testNotificationTrigger();
        }

        $this->line('');
        $this->info('✅ Test completed!');
        return Command::SUCCESS;
    }

    private function checkTestData(): void
    {
        $this->info('📊 Current System Data:');

        $usersCount = User::count();
        $produksCount = Produk::count();
        $meetingsCount = MeetingRequest::count();

        $this->line("👥 Users: {$usersCount}");
        $this->line("🏠 Products: {$produksCount}");
        $this->line("📅 Meeting Requests: {$meetingsCount}");

        if ($meetingsCount === 0) {
            $this->warn('⚠️  No meeting requests found. Create some by using WhatsApp meeting feature in the app.');
        }

        $this->line('');
    }

    private function showMeetingRequests(): void
    {
        $this->info('📅 Recent Meeting Requests:');

        $meetings = MeetingRequest::with(['user'])
            ->latest()
            ->take(5)
            ->get();

        if ($meetings->isEmpty()) {
            $this->warn('No meeting requests found.');
            return;
        }

        foreach ($meetings as $meeting) {
            $produkNames = Produk::whereIn('id', $meeting->produk_ids)->pluck('name')->join(', ');
            $status = $meeting->notified_if_sold ? '🔔 Notified' : '⏳ Not notified';

            $this->line("• {$meeting->user->name} - {$produkNames}");
            $this->line("  📅 {$meeting->tanggal_meeting->format('d/m/Y')} at {$meeting->jam_meeting->format('H:i')}");
            $this->line("  📊 Status: {$meeting->status} | {$status}");
            $this->line('');
        }
    }

    private function testNotificationTrigger(): void
    {
        // Get available products that have meeting requests
        $availableProducts = Produk::where('is_available', true)->get();

        if ($availableProducts->isEmpty()) {
            $this->error('No available products found to test with.');
            return;
        }

        // Show products and let user choose
        $this->info('Available Products:');
        foreach ($availableProducts as $index => $product) {
            $meetingCount = MeetingRequest::where('produk_ids', 'like', '%"' . $product->id . '"%')
                ->where('notified_if_sold', false)
                ->count();

            $this->line($index + 1 . ". {$product->name} (Price: " . number_format($product->price) . ") - {$meetingCount} pending meetings");
        }

        $choice = $this->ask('Enter product number to mark as sold (or 0 to cancel)');

        if ($choice == 0 || !is_numeric($choice) || $choice > $availableProducts->count()) {
            $this->info('Test cancelled.');
            return;
        }

        $selectedProduct = $availableProducts[$choice - 1];

        $this->info("🏠 Selected: {$selectedProduct->name}");

        // Check how many customers will be notified
        $meetingRequests = MeetingRequest::getCustomersForProducts([$selectedProduct->id]);

        $this->line("📧 Will notify {$meetingRequests->count()} customers:");
        foreach ($meetingRequests as $meeting) {
            $this->line("   • {$meeting->user->name} ({$meeting->user->email})");
        }

        if ($this->confirm('Proceed to mark this product as sold and send notifications?')) {
            // Mark product as sold (not available)
            $selectedProduct->update(['is_available' => false]);

            $this->info('✅ Product marked as sold!');
            $this->info('📧 Notifications have been queued and will be sent.');
            $this->line('');
            $this->warn('💡 Check your mail logs or queue:work to see the actual emails being sent.');
        }
    }
}

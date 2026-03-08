<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AppSetting;

class TestSettingsCommand extends Command
{
    protected $signature = 'test:settings {--set-whatsapp=}';

    protected $description = 'Test app settings functionality';

    public function handle()
    {
        $this->info('🔧 Testing App Settings');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━');

        // Test get methods
        $this->info('📱 Admin WhatsApp: ' . AppSetting::getAdminWhatsApp());
        $this->info('🏢 Company Name: ' . AppSetting::getAppName());

        // Test setting WhatsApp if provided
        if ($newWhatsApp = $this->option('set-whatsapp')) {
            AppSetting::set('admin_whatsapp_number', $newWhatsApp);
            $this->info("✅ WhatsApp number updated to: {$newWhatsApp}");
            $this->info('📱 New WhatsApp: ' . AppSetting::getAdminWhatsApp());
        }

        // Show all settings
        $this->newLine();
        $this->info('📋 All Settings:');
        $settings = AppSetting::all();

        if ($settings->count() > 0) {
            $headers = ['Key', 'Name', 'Value', 'Type'];
            $rows = [];

            foreach ($settings as $setting) {
                $rows[] = [
                    $setting->key,
                    $setting->name,
                    \Str::limit($setting->value, 30),
                    $setting->type
                ];
            }

            $this->table($headers, $rows);
        } else {
            $this->warn('No settings found. Run: php artisan db:seed --class=AppSettingsSeeder');
        }

        return 0;
    }
}

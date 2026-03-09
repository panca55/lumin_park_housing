<?php

namespace App\Filament\Resources\AppSettingResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;
use App\Models\AppSetting;

class AppSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('setting_key')
                    ->label('Pilih Setting')
                    ->options(self::getAvailableSettings())
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set, ?AppSetting $record) {
                        // Only set defaults for new records
                        if (!$record && $state) {
                            $settingConfig = self::getSettingConfig($state);
                            $set('key', $state);
                            $set('name', $settingConfig['name']);
                            $set('type', $settingConfig['type']);
                            // Clear value field when changing setting type
                            $set('value', null);
                        }
                    }),

                Hidden::make('key')
                    ->required()
                    ->default(fn($get) => $get('setting_key')),

                Hidden::make('type')
                    ->required()
                    ->default(fn($get) => $get('setting_key') ? self::getSettingConfig($get('setting_key'))['type'] : 'text'),

                TextInput::make('name')
                    ->label('Nama Setting')
                    ->required()
                    ->maxLength(255)
                    ->readOnly()
                    ->helperText('Nama setting sudah ditentukan berdasarkan pilihan'),

                // Dynamic form field based on type
                TextInput::make('value')
                    ->label('Nilai')
                    ->visible(fn($get) => $get('setting_key') && in_array(self::getSettingConfig($get('setting_key'))['type'] ?? 'text', ['text', 'email', 'tel', 'number']))
                    ->required()
                    ->type(fn($get) => $get('setting_key') ? match (self::getSettingConfig($get('setting_key'))['type'] ?? 'text') {
                        'email' => 'email',
                        'tel' => 'tel',
                        'number' => 'number',
                        default => 'text'
                    } : 'text')
                    ->default(null)
                    ->placeholder(fn($get) => $get('setting_key') ? self::getPlaceholder($get('setting_key')) : 'Isi nilai setting...')
                    ->helperText(fn($get) => $get('setting_key') ? (self::getSettingConfig($get('setting_key'))['helper'] ?? null) : null),

                Textarea::make('value')
                    ->label('Nilai')
                    ->visible(fn($get) => $get('setting_key') && (self::getSettingConfig($get('setting_key'))['type'] ?? 'text') === 'textarea')
                    ->required()
                    ->rows(4)
                    ->default(null)
                    ->placeholder(fn($get) => $get('setting_key') ? self::getPlaceholder($get('setting_key')) : 'Isi nilai setting...')
                    ->helperText(fn($get) => $get('setting_key') ? (self::getSettingConfig($get('setting_key'))['helper'] ?? null) : null),

                Toggle::make('value')
                    ->label('Nilai')
                    ->visible(fn($get) => $get('setting_key') && (self::getSettingConfig($get('setting_key'))['type'] ?? 'text') === 'boolean')
                    ->default(false),

                Textarea::make('description')
                    ->label('Deskripsi')
                    ->required()
                    ->helperText('Penjelasan tentang setting ini'),

                // Hide is_public - all settings are applied to system
                Hidden::make('is_public')
                    ->default(true)
            ]);
    }

    /**
     * Get available settings that can be created
     */
    private static function getAvailableSettings(): array
    {
        $allSettings = [
            // WhatsApp & Meeting Settings (Priority)
            'admin_whatsapp_number' => '📱 Nomor WhatsApp Admin',
            'whatsapp_message_template' => '💬 Template Pesan WhatsApp',

            // Company & Website Settings
            'company_name' => '🏢 Nama Perusahaan',
            'site_title' => '🌐 Judul Website',
            'app_name' => '📱 Nama Aplikasi',

            // Contact Settings
            'admin_email' => '📧 Email Admin',
            'contact_email' => '📧 Email Kontak',
            'support_email' => '📧 Email Support',
            'admin_phone_backup' => '📞 Telepon Admin Cadangan',
            'admin_contact_phone' => '📞 Telepon Kontak Admin',

            // System Settings
            'max_products_per_page' => '📊 Maksimal Produk Per Halaman',
            'max_booking_limit' => '📋 Batas Maksimal Booking',
            'session_timeout' => '⏱️ Timeout Session (menit)',

            // Feature Toggles
            'enable_notifications' => '🔔 Aktifkan Notifikasi',
            'enable_email_alerts' => '📨 Aktifkan Alert Email',
            'maintenance_mode' => '🔧 Mode Maintenance',

            // Templates & Content
            'email_template_meeting' => '📧 Template Email Meeting',
            'notification_template' => '🔔 Template Notifikasi',
        ];

        // Filter out settings that already exist
        $availableSettings = [];
        foreach ($allSettings as $key => $name) {
            if (!AppSetting::where('key', $key)->exists()) {
                $availableSettings[$key] = $name;
            }
        }

        return $availableSettings;
    }

    /**
     * Get configuration for a specific setting
     */
    private static function getSettingConfig(string $key): array
    {
        $configs = [
            // WhatsApp & Meeting Settings
            'admin_whatsapp_number' => [
                'name' => 'Nomor WhatsApp Admin',
                'type' => 'tel',
                'helper' => 'Format: 628xxxxxxxxx (dimulai dengan 628) - Digunakan untuk fitur Atur Meeting'
            ],
            'whatsapp_message_template' => [
                'name' => 'Template Pesan WhatsApp',
                'type' => 'textarea',
                'helper' => 'Template pesan untuk fitur Atur Meeting. Variabel tersedia: {company_name}, {product_list}, {meeting_date}, {meeting_time}, {customer_name}, {customer_email}'
            ],

            // Company & Website Settings
            'company_name' => [
                'name' => 'Nama Perusahaan',
                'type' => 'text',
                'helper' => 'Nama perusahaan yang akan ditampilkan di aplikasi'
            ],
            'site_title' => [
                'name' => 'Judul Website',
                'type' => 'text',
                'helper' => 'Judul yang akan ditampilkan di browser'
            ],
            'app_name' => [
                'name' => 'Nama Aplikasi',
                'type' => 'text',
                'helper' => 'Nama aplikasi untuk branding'
            ],

            // Contact Settings
            'admin_email' => [
                'name' => 'Email Admin',
                'type' => 'email',
                'helper' => 'Email utama administrator'
            ],
            'contact_email' => [
                'name' => 'Email Kontak',
                'type' => 'email',
                'helper' => 'Email untuk kontak customer'
            ],
            'support_email' => [
                'name' => 'Email Support',
                'type' => 'email',
                'helper' => 'Email untuk support teknis'
            ],
            'admin_phone_backup' => [
                'name' => 'Telepon Admin Cadangan',
                'type' => 'tel',
                'helper' => 'Nomor telepon cadangan admin'
            ],
            'admin_contact_phone' => [
                'name' => 'Telepon Kontak Admin',
                'type' => 'tel',
                'helper' => 'Nomor telepon kontak admin'
            ],

            // System Settings
            'max_products_per_page' => [
                'name' => 'Maksimal Produk Per Halaman',
                'type' => 'number',
                'helper' => 'Jumlah maksimal produk yang ditampilkan per halaman (default: 9 untuk grid 3x3)'
            ],
            'max_booking_limit' => [
                'name' => 'Batas Maksimal Booking',
                'type' => 'number',
                'helper' => 'Batas maksimal booking per customer'
            ],
            'session_timeout' => [
                'name' => 'Timeout Session',
                'type' => 'number',
                'helper' => 'Durasi timeout session dalam menit'
            ],

            // Feature Toggles
            'enable_notifications' => [
                'name' => 'Aktifkan Notifikasi',
                'type' => 'boolean',
                'helper' => 'Mengaktifkan sistem notifikasi'
            ],
            'enable_email_alerts' => [
                'name' => 'Aktifkan Alert Email',
                'type' => 'boolean',
                'helper' => 'Mengaktifkan alert via email'
            ],
            'maintenance_mode' => [
                'name' => 'Mode Maintenance',
                'type' => 'boolean',
                'helper' => 'Mengaktifkan mode maintenance website'
            ],

            // Templates & Content
            'email_template_meeting' => [
                'name' => 'Template Email Meeting',
                'type' => 'textarea',
                'helper' => 'Template email untuk meeting request'
            ],
            'notification_template' => [
                'name' => 'Template Notifikasi',
                'type' => 'textarea',
                'helper' => 'Template untuk notifikasi sistem'
            ],
        ];

        return $configs[$key] ?? [
            'name' => 'Setting Umum',
            'type' => 'text',
            'helper' => null
        ];
    }

    /**
     * Get placeholder for a specific setting
     */
    private static function getPlaceholder(string $key): string
    {
        $placeholders = [
            // WhatsApp & Meeting Settings
            'admin_whatsapp_number' => '628123456789',
            'whatsapp_message_template' => 'Halo {customer_name}, terima kasih telah mengatur meeting untuk {product_list} pada {meeting_date} pukul {meeting_time}. Kami dari {company_name} akan menghubungi Anda segera.',

            // Company & Website Settings
            'company_name' => 'Lumin Park Housing',
            'site_title' => 'Lumin Park Housing - Properti Impian Anda',
            'app_name' => 'Lumin Park Housing',

            // Contact Settings
            'admin_email' => 'admin@luminpark.com',
            'contact_email' => 'contact@luminpark.com',
            'support_email' => 'support@luminpark.com',
            'admin_phone_backup' => '628987654321',
            'admin_contact_phone' => '628111222333',

            // System Settings
            'max_products_per_page' => '9',
            'max_booking_limit' => '3',
            'session_timeout' => '120',

            // Templates & Content
            'email_template_meeting' => 'Subject: Meeting Request Confirmation\n\nDear {customer_name},\n\nThank you for your interest in {product_list}.\nWe have scheduled your meeting on {meeting_date} at {meeting_time}.\n\nBest regards,\n{company_name}',
            'notification_template' => 'Pemberitahuan: {message}',
        ];

        return $placeholders[$key] ?? 'Masukkan nilai untuk setting ini...';
    }
}

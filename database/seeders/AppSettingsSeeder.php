<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AppSetting;

class AppSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'admin_whatsapp_number',
                'name' => 'Nomor WhatsApp Admin',
                'value' => '6281234567890',
                'description' => 'Nomor WhatsApp yang akan dihubungi customer untuk meeting request',
                'type' => 'tel',
                'is_public' => false
            ],
            [
                'key' => 'company_name',
                'name' => 'Nama Perusahaan',
                'value' => 'Lumin Park Housing',
                'description' => 'Nama perusahaan yang ditampilkan di aplikasi',
                'type' => 'text',
                'is_public' => true
            ],
            [
                'key' => 'company_address',
                'name' => 'Alamat Perusahaan',
                'value' => 'Jl. Example No. 123, Jakarta',
                'description' => 'Alamat lengkap perusahaan',
                'type' => 'textarea',
                'is_public' => true
            ],
            [
                'key' => 'company_email',
                'name' => 'Email Perusahaan',
                'value' => 'info@luminpark.com',
                'description' => 'Email resmi perusahaan untuk kontak',
                'type' => 'email',
                'is_public' => true
            ],
            [
                'key' => 'max_products_per_page',
                'name' => 'Max Produk Per Halaman',
                'value' => '9',
                'description' => 'Jumlah maksimal produk yang ditampilkan per halaman pada grid 3x3',
                'type' => 'number',
                'options' => ['min' => 3, 'max' => 36, 'step' => 3],
                'is_public' => false
            ],
            [
                'key' => 'whatsapp_message_template',
                'name' => 'Template Pesan WhatsApp',
                'value' => 'Halo Admin {company_name} 👋\n\nSaya ingin mengatur jadwal meeting untuk produk berikut:\n\n{product_list}\n\n📅 Jadwal Meeting: {meeting_date} pada {meeting_time}\n\n👤 Informasi Customer:\nNama: {customer_name}\nEmail: {customer_email}\n\nMohon konfirmasi ketersediaan jadwal meeting.\nTerima kasih 🙏',
                'description' => 'Template pesan WhatsApp untuk meeting request. Available variables: {company_name}, {product_list}, {meeting_date}, {meeting_time}, {customer_name}, {customer_email}',
                'type' => 'textarea',
                'is_public' => false
            ]
        ];

        foreach ($settings as $setting) {
            AppSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('App settings seeded successfully!');
    }
}

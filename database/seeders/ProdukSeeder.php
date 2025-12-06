<?php

namespace Database\Seeders;

use App\Models\Produk;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Produk::create([
            'name' => 'Rumah Minimalis 2 Lantai',
            'description' => 'Rumah minimalis modern dengan 3 kamar tidur, 2 kamar mandi dan halaman.',
            'price' => 1250000000,
            'category' => 'rumah',
            'type' => 'Tipe 72', // contoh tipe rumah
            'image' => 'images/rumah1.jpg',
            'model_3d' => 'models/rumah type 36.glb',
            'is_available' => true,
        ]);

        Produk::create([
            'name' => 'Rumah Kompak Sederhana',
            'description' => 'Rumah kompak cocok untuk keluarga kecil, 2 kamar tidur dan ruang tamu minimalis.',
            'price' => 850000000,
            'category' => 'rumah',
            'type' => 'Tipe 36', // tipe rumah kecil
            'image' => 'images/rumah2.jpg',
            'model_3d' => 'models/rumah type 36.glb',
            'is_available' => true,
        ]);

        Produk::create([
            'name' => 'Sofa Kulit Modern',
            'description' => 'Sofa 3-seater berbahan kulit sintetis, warna abu-abu.',
            'price' => 4500000,
            'category' => 'properti',
            'type' => null, // bukan rumah → null
            'image' => 'images/sofa.jpg',
            'model_3d' => 'models/sofa.glb',
            'is_available' => true,
        ]);
    }
}

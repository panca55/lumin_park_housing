<?php

namespace App\Http\Controllers;

// use App\Models\Katalog;
use App\Models\Produk;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get available properties with related images and panoramas
        $katalogs = Produk::with(['gambarProduks', 'panoramaProduks'])
            ->where('is_available', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('welcome', compact('katalogs'));
    }
}

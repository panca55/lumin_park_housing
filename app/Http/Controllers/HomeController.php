<?php

namespace App\Http\Controllers;

// use App\Models\Katalog;
use App\Models\Produk;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get available properties (not sold and not marked as sold)
        $katalogs = Produk::where('is_available', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('welcome', compact('katalogs'));
    }
}

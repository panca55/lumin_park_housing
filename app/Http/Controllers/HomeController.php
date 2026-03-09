<?php

namespace App\Http\Controllers;

// use App\Models\Katalog;
use App\Models\Produk;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get available properties with related images and panoramas
        // Pagination: 9 items per page (3x3 grid)
        $katalogs = Produk::with(['gambarProduks', 'panoramaProduks', 'denahProduks'])
            ->where('is_available', true)
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        // Get WhatsApp number and app name from settings (force fresh from DB)
        \Illuminate\Support\Facades\Cache::forget('app_setting.admin_whatsapp_number');
        \Illuminate\Support\Facades\Cache::forget('app_setting.company_name');

        $adminWhatsApp = AppSetting::getAdminWhatsApp();
        $appName = AppSetting::getAppName();

        return view('welcome', compact('katalogs', 'adminWhatsApp', 'appName'));
    }
}

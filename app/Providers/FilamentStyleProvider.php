<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Provider ini sudah tidak diperlukan - dihapus dari bootstrap/providers.php
class FilamentStyleProvider extends ServiceProvider
{
    public function register(): void
    {
        // Tidak diperlukan lagi
    }

    public function boot(): void
    {
        // Asset sudah di-handle langsung di FilamentPanelProvider
    }
}

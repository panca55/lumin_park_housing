<?php

return [

    'broadcasting' => [
        // ...
    ],

    'default_filesystem_disk' => env('FILESYSTEM_DISK', 'public'),

    'assets_path' => null,

    'cache_path' => base_path('bootstrap/cache/filament'),

    'livewire_loading_delay' => 'default',

    'file_generation' => [
        'flags' => [],
    ],

    'system_route_prefix' => 'filament',

    /*
    |--------------------------------------------------------------------------
    | Filament Authentication Settings
    |--------------------------------------------------------------------------
    */
    'auth' => [
        'guard' => 'web', // gunakan guard default Laravel
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Authentication Pages
    |--------------------------------------------------------------------------
    | Gunakan halaman login custom: /admin/login
    */
    'login_url' => '/admin/login',

    /*
    |--------------------------------------------------------------------------
    | Redirect setelah berhasil login/logout
    |--------------------------------------------------------------------------
    */
    'home_url' => fn() => route('filament.admin.pages.dashboard'),
];

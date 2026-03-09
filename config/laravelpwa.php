<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cargo Trans Progressive Web App
    |--------------------------------------------------------------------------
    */

    'name' => config('app.name', 'Cargo Trans'),

    'manifest' => [
        'name' => config('app.name', 'Cargo Trans'),
        'short_name' => config('app.name', 'Cargo Trans'),
        'start_url' => '/dashboard',
        'background_color' => '#0d6efd',
        'theme_color' => '#0d6efd',
        'display' => 'standalone',
        'orientation' => 'portrait',
        'status_bar' => 'black',

        // === Иконки приложения ===
        'icons' => [
            '72x72'   => ['path' => '/images/icons/cargo-logo.png', 'purpose' => 'any'],
            '96x96'   => ['path' => '/images/icons/cargo-logo.png', 'purpose' => 'any'],
            '128x128' => ['path' => '/images/icons/cargo-logo.png', 'purpose' => 'any'],
            '144x144' => ['path' => '/images/icons/cargo-logo.png', 'purpose' => 'any'],
            '152x152' => ['path' => '/images/icons/cargo-logo.png', 'purpose' => 'any'],
            '192x192' => ['path' => '/images/icons/cargo-logo.png', 'purpose' => 'any'],
            '384x384' => ['path' => '/images/icons/cargo-logo.png', 'purpose' => 'any'],
            '512x512' => ['path' => '/images/icons/cargo-logo.png', 'purpose' => 'any'],
        ],

        // === Splash-экраны (iOS) ===
        'splash' => [
            '640x1136'  => '/images/icons/cargo-logo.png',
            '750x1334'  => '/images/icons/cargo-logo.png',
            '828x1792'  => '/images/icons/cargo-logo.png',
            '1125x2436' => '/images/icons/cargo-logo.png',
            '1242x2208' => '/images/icons/cargo-logo.png',
            '1242x2688' => '/images/icons/cargo-logo.png',
            '1536x2048' => '/images/icons/cargo-logo.png',
            '1668x2224' => '/images/icons/cargo-logo.png',
            '1668x2388' => '/images/icons/cargo-logo.png',
            '2048x2732' => '/images/icons/cargo-logo.png',
        ],

        // === Быстрые ссылки (иконки на домашнем экране) ===
        'shortcuts' => [
            [
                'name' => 'Dashboard',
                'description' => 'View ' . config('app.name', 'Cargo Trans') . ' dashboard',
                'url' => '/dashboard',
                'icons' => [
                    'src' => '/images/icons/cargo-logo.png',
                    'purpose' => 'any',
                ],
            ],
            [
                'name' => 'Drivers',
                'description' => 'Manage drivers',
                'url' => '/drivers',
            ],
            [
                'name' => 'Trips',
                'description' => 'View and manage trips',
                'url' => '/trips',
            ],
        ],

        'custom' => [],
    ],
];

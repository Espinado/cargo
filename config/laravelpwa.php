<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cargo TMS Progressive Web App
    |--------------------------------------------------------------------------
    */

    'name' => config('app.name', 'Cargo TMS'),

    'manifest' => [
        'name' => config('app.name', 'Cargo TMS'),
        'short_name' => config('app.name', 'Cargo TMS'),
        'start_url' => '/dashboard',
        'background_color' => '#0d6efd',
        'theme_color' => '#0d6efd',
        'display' => 'standalone',
        'orientation' => 'portrait',
        'status_bar' => 'black',

        // === Иконки приложения ===
        'icons' => [
            '72x72'   => ['path' => '/' . config('app.logo.path'), 'purpose' => 'any'],
            '96x96'   => ['path' => '/' . config('app.logo.path'), 'purpose' => 'any'],
            '128x128' => ['path' => '/' . config('app.logo.path'), 'purpose' => 'any'],
            '144x144' => ['path' => '/' . config('app.logo.path'), 'purpose' => 'any'],
            '152x152' => ['path' => '/' . config('app.logo.path'), 'purpose' => 'any'],
            '192x192' => ['path' => '/' . config('app.logo.path_192'), 'purpose' => 'any'],
            '384x384' => ['path' => '/' . config('app.logo.path_512'), 'purpose' => 'any'],
            '512x512' => ['path' => '/' . config('app.logo.path_512'), 'purpose' => 'any'],
        ],

        // === Splash-экраны (iOS) ===
        'splash' => [
            '640x1136'  => '/' . config('app.logo.path'),
            '750x1334'  => '/' . config('app.logo.path'),
            '828x1792'  => '/' . config('app.logo.path'),
            '1125x2436' => '/' . config('app.logo.path'),
            '1242x2208' => '/' . config('app.logo.path'),
            '1242x2688' => '/' . config('app.logo.path'),
            '1536x2048' => '/' . config('app.logo.path'),
            '1668x2224' => '/' . config('app.logo.path'),
            '1668x2388' => '/' . config('app.logo.path'),
            '2048x2732' => '/' . config('app.logo.path'),
        ],

        // === Быстрые ссылки (иконки на домашнем экране) ===
        'shortcuts' => [
            [
                'name' => 'Dashboard',
                'description' => 'View ' . config('app.name', 'Cargo TMS') . ' dashboard',
                'url' => '/dashboard',
                'icons' => [
                    'src' => '/' . config('app.logo.path'),
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

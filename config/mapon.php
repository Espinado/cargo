<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Base Mapon API URL
    |--------------------------------------------------------------------------
    */
    'base_url' => env('MAPON_API_BASE_URL', 'https://mapon.com/api/v1'),

    /*
    |--------------------------------------------------------------------------
    | API keys
    |--------------------------------------------------------------------------
    |
    | key         – fallback (старый вариант, оставляем для совместимости)
    | keys[ID]    – ключи по компаниям (trucks.company)
    |
    */

    // 🔙 старый вариант (НЕ УДАЛЯЕМ)
    'key' => env('MAPON_API_KEY', ''),

    // ✅ ключи по компаниям (company_id => API key)
    'keys' => [
        1 => env('MAPON_API_KEY', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | CAN / odometer freshness
    |--------------------------------------------------------------------------
    */
    'can_stale_days'    => env('MAPON_CAN_STALE_DAYS', 2),
    'can_stale_minutes' => env('MAPON_CAN_STALE_MINUTES', 30),

    /*
    |--------------------------------------------------------------------------
    | Leaflet (карты) — для страницы /map
    |--------------------------------------------------------------------------
    */
    'use_local_leaflet'   => env('MAPON_LEAFLET_LOCAL', false),
    'leaflet_js_url'      => env('MAPON_LEAFLET_JS_URL', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'),
    'leaflet_css_url'     => env('MAPON_LEAFLET_CSS_URL', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'),

    'tile_layer_url'      => env('MAPON_TILE_LAYER_URL', ''),
    'tile_attribution'    => env('MAPON_TILE_ATTRIBUTION', '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'),
    'tile_use_proxy'      => env('MAPON_TILE_USE_PROXY', true),

    'map_provider' => env('MAP_PROVIDER', 'google'), // Google Maps JavaScript API

];

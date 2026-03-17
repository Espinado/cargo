<?php

return [
    'api_key' => env('OPENROUTESERVICE_API_KEY', ''),
    'base_url' => env('OPENROUTESERVICE_BASE_URL', 'https://api.openrouteservice.org'),
    'timeout' => (int) env('OPENROUTESERVICE_TIMEOUT', 15),
    'profile' => env('OPENROUTESERVICE_DIRECTIONS_PROFILE', 'driving-hgv'), // driving-hgv for trucks
];

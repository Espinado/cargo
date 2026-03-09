<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="driver-app-root">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

    <link rel="manifest" href="/driver/manifest.webmanifest">
    <link rel="icon" href="/images/icons/cargo-logo-192.png">
    <link rel="apple-touch-icon" href="/images/icons/cargo-logo-512.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#0066ff">

    @vite(['resources/driver/css/app.css', 'resources/driver/js/app.js'])
    <title>{{ $title ?? config('app.name', 'Cargo Trans') . ' — ' . __('app.driver.login.title') }}</title>
</head>

<body class="driver-app bg-gray-100 text-gray-900 w-full max-w-full">

    <div class="min-h-screen flex items-center justify-center p-4 w-full max-w-full">
        {{ $slot }}
    </div>

</body>
</html>

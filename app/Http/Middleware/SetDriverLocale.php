<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetDriverLocale
{
    /**
     * Handle an incoming request.
     *
     * Авто-выбор языка для DriverApp:
     * - пытаемся определить по Accept-Language (lv, ru, hu)
     * - по умолчанию — lv
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supported = ['lv', 'ru', 'hu'];

        $locale = $request->getPreferredLanguage($supported) ?: 'lv';

        app()->setLocale($locale);

        return $next($request);
    }
}


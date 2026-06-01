<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocalizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = ['en', 'es'];
        $defaultLocale = 'en';
        $fallbackLocale = 'en';

        $locale = $request->header('Accept-Language');

        if ($locale) {
            // Extract primary language tag (e.g., 'en' from 'en-US')
            $locale = explode('-', $locale)[0];
        }

        if (!in_array($locale, $supportedLocales)) {
            $locale = $defaultLocale;
        }

        app()->setLocale($locale);

        return $next($request);
    }
}

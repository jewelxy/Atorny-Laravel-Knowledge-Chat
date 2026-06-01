<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $supportedLocales = config('localization.supported_locales');

        $locale = $request->header('Accept-Language', config('localization.default_locale', 'en'));

        if ($locale) {
            $locale = strtolower(explode(',', $locale)[0]);
            $locale = explode('-', $locale)[0];
        }

        if (! in_array($locale, $supportedLocales)) {
            $locale = config('localization.default_locale');
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
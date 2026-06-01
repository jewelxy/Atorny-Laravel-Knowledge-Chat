<?php

namespace App\Http\Middleware;

use App\Services\Cache\PublicResponseCacheService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ResponseCacheMiddleware
{
    public function __construct(
        private readonly PublicResponseCacheService $publicResponseCache
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('get') && !$request->user()) {
            $key = 'response:'.$this->publicResponseCache->version().':'.md5($request->fullUrl().app()->getLocale());

            if (Cache::has($key)) {
                return response(Cache::get($key));
            }

            $response = $next($request);

            Cache::put($key, $response->getContent(), 3600);

            return $response;
        }

        return $next($request);
    }
}
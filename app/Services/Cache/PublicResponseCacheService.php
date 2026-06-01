<?php

namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;

/**
 * Versioned busting for ResponseCacheMiddleware entries (key prefix: response:).
 * Incrementing the version invalidates all cached public GET responses without scanning Redis.
 */
class PublicResponseCacheService
{
    private const VERSION_KEY = 'public_response_cache_version';

    public function version(): int
    {
        return (int) Cache::get(self::VERSION_KEY, 1);
    }

    public function bump(): void
    {
        Cache::forever(self::VERSION_KEY, $this->version() + 1);
    }
}

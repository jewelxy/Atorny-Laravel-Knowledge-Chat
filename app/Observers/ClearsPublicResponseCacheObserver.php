<?php

namespace App\Observers;

use App\Services\Cache\PublicResponseCacheService;

class ClearsPublicResponseCacheObserver
{
    public function __construct(
        private readonly PublicResponseCacheService $publicResponseCache
    ) {}

    public function saved(): void
    {
        $this->publicResponseCache->bump();
    }

    public function deleted(): void
    {
        $this->publicResponseCache->bump();
    }
}

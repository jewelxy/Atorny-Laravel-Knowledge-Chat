<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware(['force.json', 'set.locale'])
    ->group(function () {
        require __DIR__ . '/api/v1/auth.php';
        require __DIR__ . '/api/v1/public.php';
        require __DIR__ . '/api/v1/admin.php';
        require __DIR__ . '/api/v1/cms.php';
        require __DIR__ . '/api/v1/localization.php';
    });


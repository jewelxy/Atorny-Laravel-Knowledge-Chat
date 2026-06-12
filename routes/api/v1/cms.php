<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\CmsMediaController;
use App\Http\Controllers\Api\Admin\CmsPageController;
use App\Http\Controllers\Api\Admin\CmsSectionController;
use App\Http\Controllers\Api\Admin\CmsSectionTranslationController;

Route::middleware(['auth:sanctum', 'role:admin'])
    ->prefix('cms')
    ->group(function () {
        Route::post('media/upload', [CmsMediaController::class, 'upload']);

        Route::apiResource('pages', CmsPageController::class);
        Route::apiResource('sections', CmsSectionController::class);
        Route::apiResource('sections.translations', CmsSectionTranslationController::class);
    });

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Public\BlogController as PublicBlogController;
use App\Http\Controllers\Api\Public\CmsPageController as PublicCmsPageController;
use App\Http\Controllers\Api\Public\ServiceController as PublicServiceController;
use App\Http\Controllers\Api\Public\FaqController as PublicFaqController;
use App\Http\Controllers\Api\Public\ContactController as PublicContactController;
use App\Http\Controllers\Api\Public\DynamicContentModuleController as PublicDynamicContentModuleController;
use App\Http\Controllers\Api\Public\KnowledgeChatController as PublicKnowledgeChatController;

Route::prefix('public')->group(function () {
    Route::middleware('response.cache')->group(function () {
        $supportedLocales = config('localization.supported_locales', ['en']);

        Route::get('blogs', [PublicBlogController::class, 'index']);
        Route::get('blogs/{locale}', [PublicBlogController::class, 'index'])
            ->whereIn('locale', $supportedLocales);
        Route::get('blogs/{slug}', [PublicBlogController::class, 'show']);

        Route::get('pages/{key}', [PublicCmsPageController::class, 'show']);

        Route::get('services', [PublicServiceController::class, 'index']);
        Route::get('services/{slug}', [PublicServiceController::class, 'show']);

        Route::get('faqs', [PublicFaqController::class, 'index']);
        Route::get('faqs/{slug}', [PublicFaqController::class, 'show']);

        Route::get('contact', [PublicContactController::class, 'info']);

        Route::get('modules/{module}', [PublicDynamicContentModuleController::class, 'index']);
        Route::get('modules/{module}/{slug}', [PublicDynamicContentModuleController::class, 'show']);
    });

    Route::post('contact', [PublicContactController::class, 'submit']);

    Route::post('chat', [PublicKnowledgeChatController::class, 'store'])
        ->middleware('throttle:20,1');
});

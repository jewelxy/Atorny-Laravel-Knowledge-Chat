<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LocalizationController;

Route::middleware('response.cache')->group(function () {
    Route::get('locales', [LocalizationController::class, 'index']);
    Route::get('locales/{locale}', [LocalizationController::class, 'show']);

    Route::get('lang/{locale}', [LocalizationController::class, 'show']);
});

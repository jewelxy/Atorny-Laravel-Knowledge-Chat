<?php

use App\Services\Localization\LocalizationService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/lang/{locale}', function (string $locale, LocalizationService $localizationService) {
    $supportedLocales = config('localization.supported_locales', ['en']);

    if (! in_array($locale, $supportedLocales)) {
        abort(404, 'Locale not supported');
    }

    $translations = $localizationService->getTranslations($locale);

    if ($translations === []) {
        abort(404, 'Translations not found for this locale');
    }

    return response()->json([
        'locale' => $locale,
        'translations' => $translations,
    ]);
})->whereIn('locale', config('localization.supported_locales', ['en']));

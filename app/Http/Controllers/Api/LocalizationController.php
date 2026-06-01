<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Services\Localization\LocalizationService;

class LocalizationController extends ApiController
{
    public function index()
    {
        $locales = config('localization.supported_locales', ['en']);

        return $this->successResponse('Supported locales loaded', [
            'locales' => $locales,
        ]);
    }

    public function show(string $locale, LocalizationService $localizationService)
    {
        $supportedLocales = config('localization.supported_locales', ['en']);

        if (! in_array($locale, $supportedLocales)) {
            return $this->errorResponse('Locale not supported', 404);
        }

        $translations = $localizationService->getTranslations($locale);

        if ($translations === []) {
            return $this->errorResponse('Translations not found for this locale', 404);
        }

        return $this->successResponse('Translations loaded', [
            'locale' => $locale,
            'translations' => $translations,
        ]);
    }
}

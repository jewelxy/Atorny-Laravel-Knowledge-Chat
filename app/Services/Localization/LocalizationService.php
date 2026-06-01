<?php

namespace App\Services\Localization;

use Illuminate\Support\Facades\File;

class LocalizationService
{
    public function getLocale(): string
    {
        return app()->getLocale();
    }

    public function getTranslations(string $locale): array
    {
        $translations = [];

        $localePath = lang_path($locale);

        if (File::isDirectory($localePath)) {
            foreach (File::files($localePath) as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                $group = $file->getFilenameWithoutExtension();
                $translations[$group] = require $file->getPathname();
            }
        }

        $jsonPath = lang_path("{$locale}.json");

        if (File::exists($jsonPath)) {
            $jsonTranslations = json_decode(File::get($jsonPath), true);

            if (is_array($jsonTranslations)) {
                $translations = array_merge($translations, $jsonTranslations);
            }
        }

        return $translations;
    }
}

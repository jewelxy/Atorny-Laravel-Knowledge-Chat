<?php

namespace App\Support;

use InvalidArgumentException;

class ChatSpecialist
{
    /**
     * @return array<int, string>
     */
    public static function keys(): array
    {
        return array_keys(config('chat.specialists', []));
    }

    public static function normalize(?string $specialist): string
    {
        $specialist = strtolower(trim((string) $specialist));
        $keys = self::keys();

        if ($specialist === '' || ! in_array($specialist, $keys, true)) {
            return config('chat.default_specialist', 'marco');
        }

        return $specialist;
    }

    public static function displayName(string $specialist): string
    {
        $specialist = self::normalize($specialist);

        return config("chat.specialists.{$specialist}.display_name", ucfirst($specialist));
    }

    /**
     * @return array{key: string, display_name: string, title: string, firm: string}
     */
    public static function meta(?string $specialist): array
    {
        $key = self::normalize($specialist);

        return [
            'key' => $key,
            'display_name' => self::displayName($key),
            'title' => config("chat.specialists.{$key}.title", 'David assistant'),
            'firm' => config('chat.firm_name', 'David assistant of Bankruptcy Custom Solution'),
        ];
    }

    public static function introAnswer(string $specialist, string $locale): string
    {
        $specialist = self::normalize($specialist);
        $name = self::displayName($specialist);
        $firm = config('chat.firm_name');
        $options = config("chat.situation_options.{$locale}")
            ?? config('chat.situation_options.en', []);

        $list = self::formatOptionList($options, $locale);

        if ($locale === 'es') {
            return "Soy {$name}, asistente David de Bankruptcy Custom Solution. Para comenzar, ¿cuál de estas opciones describe mejor su situación? {$list}";
        }

        return "I'm {$name}, a {$firm}. To get us started, which of these best describes your situation? {$list}";
    }

    /**
     * @param  array<int, string>  $options
     */
    private static function formatOptionList(array $options, string $locale): string
    {
        if ($options === []) {
            throw new InvalidArgumentException('Situation options are not configured.');
        }

        if (count($options) === 1) {
            return $options[0];
        }

        $last = array_pop($options);
        $joiner = $locale === 'es' ? ' u ' : ', or ';

        return implode(', ', $options).$joiner.$last.'?';
    }
}

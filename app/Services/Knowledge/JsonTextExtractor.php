<?php

namespace App\Services\Knowledge;

class JsonTextExtractor
{
    /**
     * Flatten nested JSON/arrays into searchable plain text.
     */
    public function extract(mixed $value): string
    {
        $parts = [];
        $this->walk($value, $parts);

        return trim(implode("\n", array_unique(array_filter($parts))));
    }

    private function walk(mixed $value, array &$parts): void
    {
        if (is_string($value)) {
            $clean = trim(strip_tags($value));

            if ($clean !== '' && ! $this->looksLikeUrl($clean)) {
                $parts[] = $clean;
            }

            return;
        }

        if (is_numeric($value)) {
            $parts[] = (string) $value;

            return;
        }

        if (! is_array($value)) {
            return;
        }

        foreach ($value as $key => $item) {
            if (is_string($key) && in_array($key, ['src', 'href', 'url', 'image', 'icon', 'variant', 'type'], true)) {
                if (is_string($item) && $this->looksLikeUrl($item)) {
                    continue;
                }
            }

            $this->walk($item, $parts);
        }
    }

    private function looksLikeUrl(string $value): bool
    {
        return (bool) preg_match('#^(https?://|/assets/|/storage/)#i', $value);
    }
}

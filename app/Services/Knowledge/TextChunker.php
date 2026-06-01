<?php

namespace App\Services\Knowledge;

class TextChunker
{
    /**
     * @return array<int, string>
     */
    public function chunk(string $text): array
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text) ?? '');

        if ($text === '') {
            return [];
        }

        $maxChars = max(200, (int) config('openai.chunking.max_chars', 1200));
        $overlap = max(0, (int) config('openai.chunking.overlap_chars', 200));

        if (mb_strlen($text) <= $maxChars) {
            return [$text];
        }

        $chunks = [];
        $offset = 0;
        $length = mb_strlen($text);

        while ($offset < $length) {
            $slice = mb_substr($text, $offset, $maxChars);

            if ($offset + $maxChars < $length) {
                $breakAt = max(
                    (int) mb_strrpos($slice, '. '),
                    (int) mb_strrpos($slice, "\n"),
                    (int) mb_strrpos($slice, ' ')
                );

                if ($breakAt > (int) ($maxChars * 0.5)) {
                    $slice = mb_substr($slice, 0, $breakAt + 1);
                }
            }

            $slice = trim($slice);

            if ($slice !== '') {
                $chunks[] = $slice;
            }

            if ($slice === '' || mb_strlen($slice) === 0) {
                break;
            }

            $step = max(1, mb_strlen($slice) - $overlap);
            $offset += $step;
        }

        return $chunks === [] ? [$text] : $chunks;
    }
}

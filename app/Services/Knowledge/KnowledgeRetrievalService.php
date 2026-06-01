<?php

namespace App\Services\Knowledge;

use App\Models\KnowledgeChunk;
use App\Models\User;
use App\Services\AI\OpenRouterClient;
use App\Services\Chat\ChatAccessPolicy;

class KnowledgeRetrievalService
{
    public function __construct(
        private readonly OpenRouterClient $openRouter,
        private readonly ChatAccessPolicy $accessPolicy,
    ) {}

    /**
     * @return array<int, array{chunk: KnowledgeChunk, score: float}>
     */
    public function retrieve(string $query, string $locale, ?User $user = null, ?string $specialist = null): array
    {
        $queryVector = $this->openRouter->embed($this->expandQuery($query));
        $minScore = (float) config('openai.retrieval.min_similarity', 0.2);
        $topK = max(1, (int) config('openai.retrieval.top_k', 6));

        $chunks = KnowledgeChunk::query()
            ->where('locale', $locale)
            ->when(
                ! $this->accessPolicy->includeNonPublic($user),
                fn ($q) => $q->where(function ($inner): void {
                    $inner->where('is_public', true)
                        ->orWhere('source_type', KnowledgeChunk::SOURCE_CHAT_PROMPT);
                })
            )
            ->when($specialist, function ($q, string $specialist): void {
                $q->where(function ($inner) use ($specialist): void {
                    $inner->where('source_type', '!=', KnowledgeChunk::SOURCE_CHAT_PROMPT)
                        ->orWhere('specialist', $specialist)
                        ->orWhereNull('specialist');
                });
            })
            ->whereIn('source_type', $this->accessPolicy->allowedSourceTypes($user))
            ->get();

        $scored = [];

        foreach ($chunks as $chunk) {
            $score = $this->cosineSimilarity($queryVector, $chunk->embedding ?? []);

            if ($score >= $minScore) {
                $scored[] = ['chunk' => $chunk, 'score' => $score];
            }
        }

        usort($scored, fn (array $a, array $b) => $b['score'] <=> $a['score']);

        return array_slice($scored, 0, $topK);
    }

    /**
     * Enrich short visitor queries so semantic search matches CMS pricing/services content.
     */
    private function expandQuery(string $query): string
    {
        $normalized = strtolower($query);

        if (preg_match('/\b(services?|pricing|plans?|packages?|fees?|cost)\b/', $normalized)) {
            return trim($query.' legal service plans pricing essential full representation consultation filing');
        }

        return $query;
    }

    /**
     * @param  array<int, float>  $a
     * @param  array<int, float>  $b
     */
    private function cosineSimilarity(array $a, array $b): float
    {
        if ($a === [] || $b === [] || count($a) !== count($b)) {
            return 0.0;
        }

        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($a as $i => $value) {
            $other = $b[$i] ?? 0.0;
            $dot += $value * $other;
            $normA += $value * $value;
            $normB += $other * $other;
        }

        if ($normA <= 0.0 || $normB <= 0.0) {
            return 0.0;
        }

        return $dot / (sqrt($normA) * sqrt($normB));
    }
}

<?php

namespace App\Services\Knowledge;

use App\Models\User;
use App\Services\AI\OpenRouterClient;
use App\Services\Chat\ChatAccessPolicy;
use App\Support\ChatSpecialist;
use Illuminate\Support\Str;
use RuntimeException;

class KnowledgeChatService
{
    public function __construct(
        private readonly KnowledgeRetrievalService $retrieval,
        private readonly OpenRouterClient $openRouter,
        private readonly ChatAccessPolicy $accessPolicy,
    ) {}

    /**
     * @param  array<int, array{role: string, content: string}>|null  $history
     * @return array{
     *   answer: string,
     *   locale: string,
     *   disclaimer: string,
     *   citations: array<int, array<string, mixed>>,
     *   retrieval: array{chunks_used: int, allowed_tools: array<int, string>},
     *   specialist: array{key: string, display_name: string, title: string, firm: string},
     *   grounded: bool
     * }
     */
    public function chat(string $message, string $locale, ?User $user = null, ?array $history = null, ?string $specialist = null): array
    {
        if (! $this->accessPolicy->canUseChat($user)) {
            throw new RuntimeException('You do not have permission to use knowledge chat.', 403);
        }

        $locale = $this->normalizeLocale($locale);
        $specialist = ChatSpecialist::normalize($specialist);
        $retrieved = $this->retrieval->retrieve($message, $locale, $user, $specialist);

        if ($retrieved === []) {
            return [
                'answer' => $this->noContextAnswer($locale),
                'locale' => $locale,
                'specialist' => ChatSpecialist::meta($specialist),
                'disclaimer' => $this->disclaimer($locale),
                'citations' => [],
                'retrieval' => [
                    'chunks_used' => 0,
                    'allowed_tools' => $this->accessPolicy->allowedTools($user),
                ],
                'grounded' => false,
            ];
        }

        $context = $this->buildContextBlock($retrieved);
        $citations = $this->buildCitations($retrieved);
        $messages = $this->buildMessages($message, $locale, $context, $history, count($retrieved));
        $answer = $this->openRouter->chat($messages);

        return [
            'answer' => $answer,
            'locale' => $locale,
            'specialist' => ChatSpecialist::meta($specialist),
            'disclaimer' => $this->disclaimer($locale),
            'citations' => $citations,
            'retrieval' => [
                'chunks_used' => count($retrieved),
                'allowed_tools' => $this->accessPolicy->allowedTools($user),
            ],
            'grounded' => true,
        ];
    }

    private function normalizeLocale(string $locale): string
    {
        $locale = strtolower(Str::before($locale, '-'));
        $supported = config('localization.supported_locales', ['en']);

        return in_array($locale, $supported, true)
            ? $locale
            : config('localization.default_locale', 'en');
    }

    /**
     * @param  array<int, array{chunk: \App\Models\KnowledgeChunk, score: float}>  $retrieved
     */
    private function buildContextBlock(array $retrieved): string
    {
        $blocks = [];

        foreach ($retrieved as $index => $item) {
            $chunk = $item['chunk'];
            $ref = $index + 1;
            $urlLine = $chunk->publicUrl() ? "URL: {$chunk->publicUrl()}\n" : '';
            $blocks[] = "[{$ref}] {$chunk->title}\n{$urlLine}{$chunk->content}";
        }

        return implode("\n\n---\n\n", $blocks);
    }

    /**
     * @param  array<int, array{chunk: \App\Models\KnowledgeChunk, score: float}>  $retrieved
     * @return array<int, array<string, mixed>>
     */
    private function buildCitations(array $retrieved): array
    {
        $seen = [];
        $citations = [];

        foreach ($retrieved as $item) {
            $chunk = $item['chunk'];
            $key = $chunk->source_type.'|'.($chunk->public_path ?: $chunk->source_id);

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $citation = [
                'title' => $chunk->title,
                'slug' => $chunk->slug,
                'source_type' => $chunk->source_type,
                'locale' => $chunk->locale,
                'relevance' => round($item['score'], 4),
                'chat_only' => $chunk->isChatOnly(),
            ];

            if ($chunk->publicUrl()) {
                $citation['url'] = $chunk->publicUrl();
                $citation['path'] = '/'.ltrim($chunk->public_path, '/');
            }

            $citations[] = $citation;
        }

        return $citations;
    }

    /**
     * @param  array<int, array{role: string, content: string}>|null  $history
     * @return array<int, array{role: string, content: string}>
     */
    private function buildMessages(
        string $message,
        string $locale,
        string $context,
        ?array $history,
        int $chunksUsed = 0,
    ): array {
        $language = $locale === 'es' ? 'Spanish' : 'English';

        $contextRules = $chunksUsed > 0
            ? <<<RULES
- The CONTEXT below is the source of truth for this answer.
- If CONTEXT contains information relevant to the user's question, you MUST answer from it, including plan names, prices, and features.
- Do not claim information is missing when CONTEXT already answers the question.
- Prior assistant messages in this conversation may be wrong; always prefer CONTEXT over earlier replies.
RULES
            : <<<RULES
- If the context does not contain enough information, say you do not have that information on the website and suggest contacting the firm.
RULES;

        $system = <<<PROMPT
You are a friendly and helpful website assistant for a bankruptcy law firm.
Use ONLY the CONTEXT below from this firm's website (FAQs, services, blog posts, CMS sections).
Preserve the latest conversation exchange when provided in history.
If the user says hello or asks how you are, reply politely and briefly but do not invent outside information.
If there is no relevant information in the context, say you could not find related information and suggest contacting the firm.
Do not use outside knowledge about bankruptcy law.

Rules:
- Respond in {$language}.
{$contextRules}
- Never provide legal advice, case strategy, or eligibility determinations.
- Reference sources using [n] markers matching the context blocks when helpful.
- Keep answers concise, human, and helpful.

CONTEXT:
{$context}
PROMPT;

        $messages = [
            ['role' => 'system', 'content' => $system],
        ];

        if ($history) {
            foreach ($history as $turn) {
                if (! isset($turn['role'], $turn['content'])) {
                    continue;
                }

                if (! in_array($turn['role'], ['user', 'assistant'], true)) {
                    continue;
                }

                $messages[] = [
                    'role' => $turn['role'],
                    'content' => (string) $turn['content'],
                ];
            }
        }

        $messages[] = ['role' => 'user', 'content' => $message];

        return $messages;
    }

    private function disclaimer(string $locale): string
    {
        return config("openai.disclaimer.{$locale}")
            ?? config('openai.disclaimer.en');
    }

    private function noContextAnswer(string $locale): string
    {
        return $locale === 'es'
            ? 'No encontré información relacionada en el contenido publicado de este sitio. Para ayuda personalizada, utilice el formulario de contacto o programe una consulta con el despacho.'
            : 'I could not find related information in this website\'s published content. For personalized help, please use the contact form or schedule a consultation with the firm.';
    }
}

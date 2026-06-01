<?php

namespace App\Services\AI;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenRouterClient
{
    public function embed(string $text): array
    {
        $response = $this->request('embeddings', [
            'model' => config('openai.embedding_model'),
            'input' => $text,
        ]);

        $embedding = $response['data'][0]['embedding'] ?? null;

        if (! is_array($embedding) || $embedding === []) {
            throw new RuntimeException('Embedding API returned an empty vector.');
        }

        return $embedding;
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     */
    public function chat(array $messages, float $temperature = 0.2): string
    {
        $response = $this->request('chat/completions', [
            'model' => config('openai.chat_model'),
            'messages' => $messages,
            'temperature' => $temperature,
        ]);

        $content = $response['choices'][0]['message']['content'] ?? null;

        if (! is_string($content) || trim($content) === '') {
            throw new RuntimeException('Chat API returned an empty response.');
        }

        return trim($content);
    }

    private function request(string $path, array $payload): array
    {
        $apiKey = config('openai.api_key');

        if (! $apiKey) {
            throw new RuntimeException('OPENAI_ROUTER_API_KEY is not configured.');
        }

        try {
            $response = Http::baseUrl(rtrim(config('openai.base_url'), '/'))
                ->withToken($apiKey)
                ->acceptJson()
                ->timeout(90)
                ->post('/'.$path, $payload)
                ->throw();
        } catch (RequestException $exception) {
            $body = $exception->response?->json('error.message')
                ?? $exception->response?->body()
                ?? $exception->getMessage();

            throw new RuntimeException('OpenRouter request failed: '.$body, 0, $exception);
        }

        return $response->json() ?? [];
    }
}

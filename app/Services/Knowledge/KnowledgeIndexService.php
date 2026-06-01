<?php

namespace App\Services\Knowledge;

use App\Models\Blog;
use App\Models\ChatPrompt;
use App\Models\CmsPage;
use App\Models\CmsSection;
use App\Models\KnowledgeChunk;
use App\Services\AI\OpenRouterClient;
use Illuminate\Support\Facades\DB;

class KnowledgeIndexService
{
    public function __construct(
        private readonly OpenRouterClient $openRouter,
        private readonly TextChunker $chunker,
        private readonly JsonTextExtractor $jsonExtractor,
    ) {}

    /**
     * @return array{indexed: int, sources: array<string, int>}
     */
    public function reindexAll(): array
    {
        $counts = [
            KnowledgeChunk::SOURCE_BLOG => 0,
            KnowledgeChunk::SOURCE_CMS_SECTION => 0,
            KnowledgeChunk::SOURCE_CHAT_PROMPT => 0,
        ];

        DB::transaction(function () use (&$counts): void {
            KnowledgeChunk::query()->delete();

            $counts[KnowledgeChunk::SOURCE_BLOG] = $this->indexBlogs();
            $counts[KnowledgeChunk::SOURCE_CMS_SECTION] = $this->indexCmsSections();
            $counts[KnowledgeChunk::SOURCE_CHAT_PROMPT] = $this->indexChatPrompts();
        });

        return [
            'indexed' => array_sum($counts),
            'sources' => $counts,
        ];
    }

    private function indexBlogs(): int
    {
        $indexed = 0;

        Blog::query()
            ->where('status', true)
            ->with('translations')
            ->orderBy('id')
            ->each(function (Blog $blog) use (&$indexed): void {
                foreach ($blog->translations as $translation) {
                    $body = trim(implode("\n\n", array_filter([
                        $translation->title,
                        $translation->description,
                        $blog->content,
                    ])));

                    if ($body === '') {
                        continue;
                    }

                    $slug = $translation->slug ?: $blog->slug;
                    $path = 'blogs/'.$slug;

                    $indexed += $this->storeChunks(
                        sourceType: KnowledgeChunk::SOURCE_BLOG,
                        sourceId: (string) $blog->id.':'.$translation->locale,
                        locale: $translation->locale,
                        title: $translation->title ?: $blog->title,
                        slug: $slug,
                        publicPath: $path,
                        text: $body,
                    );
                }
            });

        return $indexed;
    }

    private function indexCmsSections(): int
    {
        $indexed = 0;

        CmsPage::query()
            ->where('status', true)
            ->with([
                'activeSections.translations',
                'activeSections.cmsPage',
            ])
            ->orderBy('id')
            ->each(function (CmsPage $page) use (&$indexed): void {
                foreach ($page->activeSections as $section) {
                    foreach ($section->translations as $translation) {
                        $text = $this->jsonExtractor->extract($translation->data ?? []);

                        if ($text === '') {
                            continue;
                        }

                        $title = $page->key.' / '.$section->section_key;
                        $path = 'pages/'.$page->key.'#'.$section->section_key;

                        $indexed += $this->storeChunks(
                            sourceType: KnowledgeChunk::SOURCE_CMS_SECTION,
                            sourceId: (string) $section->id.':'.$translation->locale,
                            locale: $translation->locale,
                            title: $title,
                            slug: $section->section_key,
                            publicPath: $path,
                            text: $text,
                        );
                    }
                }
            });

        return $indexed;
    }

    private function indexChatPrompts(): int
    {
        $indexed = 0;

        ChatPrompt::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->each(function (ChatPrompt $prompt) use (&$indexed): void {
                $text = $prompt->indexingText();

                if ($text === '') {
                    return;
                }

                $indexed += $this->storeChunks(
                    sourceType: KnowledgeChunk::SOURCE_CHAT_PROMPT,
                    sourceId: (string) $prompt->id.':'.$prompt->locale,
                    locale: $prompt->locale,
                    title: $prompt->question,
                    slug: $prompt->specialist,
                    publicPath: '',
                    text: $text,
                    isPublic: false,
                    specialist: $prompt->specialist,
                );
            });

        return $indexed;
    }

    private function storeChunks(
        string $sourceType,
        string $sourceId,
        string $locale,
        string $title,
        ?string $slug,
        string $publicPath,
        string $text,
        bool $isPublic = true,
        ?string $specialist = null,
    ): int {
        $chunks = $this->chunker->chunk($text);
        $stored = 0;

        foreach ($chunks as $index => $chunkText) {
            $hash = hash('sha256', $chunkText);
            $embedding = $this->openRouter->embed($chunkText);

            KnowledgeChunk::updateOrCreate(
                [
                    'source_type' => $sourceType,
                    'source_id' => $sourceId,
                    'locale' => $locale,
                    'chunk_index' => $index,
                ],
                [
                    'title' => $title,
                    'slug' => $slug,
                    'specialist' => $specialist,
                    'public_path' => $publicPath,
                    'content' => $chunkText,
                    'content_hash' => $hash,
                    'embedding' => $embedding,
                    'is_public' => $isPublic,
                ]
            );

            $stored++;
        }

        return $stored;
    }
}

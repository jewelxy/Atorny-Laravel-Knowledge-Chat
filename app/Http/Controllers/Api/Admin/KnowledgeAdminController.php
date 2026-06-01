<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\KnowledgeChunk;
use App\Services\Chat\ChatAccessPolicy;
use App\Services\Knowledge\KnowledgeIndexService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;
use Throwable;

class KnowledgeAdminController extends ApiController
{
    public function __construct(
        private readonly KnowledgeIndexService $indexService,
        private readonly ChatAccessPolicy $accessPolicy,
    ) {}

    public function reindex(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $this->accessPolicy->canReindex($user)) {
            return $this->errorResponse('Forbidden. Missing permission to reindex knowledge base.', 403);
        }

        try {
            $stats = $this->indexService->reindexAll();
        } catch (Throwable $exception) {
            return $this->errorResponse(
                'Knowledge indexing failed: '.$exception->getMessage(),
                503
            );
        }

        return $this->successResponse('Knowledge base reindexed', $stats);
    }

    public function chunks(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $this->accessPolicy->canViewChunks($user)) {
            return $this->errorResponse('Forbidden. Missing permission to view knowledge chunks.', 403);
        }

        $chunks = KnowledgeChunk::query()
            ->when($request->query('locale'), fn ($q, $locale) => $q->where('locale', $locale))
            ->when($request->query('source_type'), fn ($q, $type) => $q->where('source_type', $type))
            ->orderBy('source_type')
            ->orderBy('source_id')
            ->orderBy('chunk_index')
            ->paginate((int) $request->query('per_page', 25));

        return $this->successResponse('Knowledge chunks loaded', [
            'chunks' => $chunks->through(fn (KnowledgeChunk $chunk) => [
                'id' => $chunk->id,
                'locale' => $chunk->locale,
                'source_type' => $chunk->source_type,
                'source_id' => $chunk->source_id,
                'title' => $chunk->title,
                'slug' => $chunk->slug,
                'path' => $chunk->public_path ? '/'.ltrim($chunk->public_path, '/') : null,
                'url' => $chunk->publicUrl(),
                'chat_only' => $chunk->isChatOnly(),
                'chunk_index' => $chunk->chunk_index,
                'content_preview' => mb_substr($chunk->content, 0, 200),
                'is_public' => $chunk->is_public,
            ]),
            'meta' => [
                'current_page' => $chunks->currentPage(),
                'last_page' => $chunks->lastPage(),
                'per_page' => $chunks->perPage(),
                'total' => $chunks->total(),
            ],
        ]);
    }
}

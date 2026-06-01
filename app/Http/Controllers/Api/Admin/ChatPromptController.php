<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Chat\StoreChatPromptRequest;
use App\Http\Requests\Chat\UpdateChatPromptRequest;
use App\Http\Resources\ChatPromptResource;
use App\Models\ChatPrompt;
use App\Services\Chat\ChatAccessPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatPromptController extends ApiController
{
    public function __construct(
        private readonly ChatAccessPolicy $accessPolicy,
    ) {}

    public function index(Request $request): JsonResponse
    {
        if (! $request->user()?->can(ChatAccessPolicy::PERMISSION_MANAGE_CHAT_PROMPTS)) {
            return $this->errorResponse('Forbidden. Missing permission to manage chat prompts.', 403);
        }

        $prompts = ChatPrompt::query()
            ->when($request->query('locale'), fn ($q, $locale) => $q->where('locale', $locale))
            ->when($request->query('specialist'), fn ($q, $specialist) => $q->where('specialist', $specialist))
            ->when($request->has('is_active'), fn ($q) => $q->where('is_active', filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN)))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return $this->successResponse('Chat prompts loaded', [
            'prompts' => ChatPromptResource::collection($prompts),
        ]);
    }

    public function store(StoreChatPromptRequest $request): JsonResponse
    {
        if (! $request->user()?->can(ChatAccessPolicy::PERMISSION_MANAGE_CHAT_PROMPTS)) {
            return $this->errorResponse('Forbidden. Missing permission to manage chat prompts.', 403);
        }

        $prompt = ChatPrompt::create([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->integer('sort_order', 0),
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);

        return $this->successResponse('Chat prompt created', [
            'prompt' => new ChatPromptResource($prompt),
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        if (! $request->user()?->can(ChatAccessPolicy::PERMISSION_MANAGE_CHAT_PROMPTS)) {
            return $this->errorResponse('Forbidden. Missing permission to manage chat prompts.', 403);
        }

        $prompt = ChatPrompt::findOrFail($id);

        return $this->successResponse('Chat prompt loaded', [
            'prompt' => new ChatPromptResource($prompt),
        ]);
    }

    public function update(UpdateChatPromptRequest $request, int $id): JsonResponse
    {
        if (! $request->user()?->can(ChatAccessPolicy::PERMISSION_MANAGE_CHAT_PROMPTS)) {
            return $this->errorResponse('Forbidden. Missing permission to manage chat prompts.', 403);
        }

        $prompt = ChatPrompt::findOrFail($id);
        $prompt->update([
            ...$request->validated(),
            'updated_by' => $request->user()->id,
        ]);

        return $this->successResponse('Chat prompt updated', [
            'prompt' => new ChatPromptResource($prompt->fresh()),
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        if (! $request->user()?->can(ChatAccessPolicy::PERMISSION_MANAGE_CHAT_PROMPTS)) {
            return $this->errorResponse('Forbidden. Missing permission to manage chat prompts.', 403);
        }

        $prompt = ChatPrompt::findOrFail($id);
        $prompt->delete();

        return $this->successResponse('Chat prompt deleted', [
            'prompt' => ['id' => $id],
        ]);
    }
}

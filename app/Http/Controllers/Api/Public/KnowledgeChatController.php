<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Chat\KnowledgeChatRequest;
use App\Services\Knowledge\KnowledgeChatService;
use Illuminate\Http\JsonResponse;
use RuntimeException;
use Throwable;

class KnowledgeChatController extends ApiController
{
    public function __construct(
        private readonly KnowledgeChatService $chatService,
    ) {}

    public function store(KnowledgeChatRequest $request): JsonResponse
    {
        $locale = $request->input('locale', app()->getLocale());

        if ($locale) {
            app()->setLocale($locale);
        }

        try {
            $result = $this->chatService->chat(
                message: $request->string('message')->toString(),
                locale: $locale,
                user: null,
                history: $request->input('history'),
                specialist: $request->input('specialist'),
            );
        } catch (RuntimeException $exception) {
            $status = $exception->getCode() === 403 ? 403 : 503;

            return $this->errorResponse($exception->getMessage(), $status);
        } catch (Throwable) {
            return $this->errorResponse(
                'Knowledge chat is temporarily unavailable. Please try again later.',
                503
            );
        }

        return $this->successResponse('Knowledge chat response generated', $result);
    }
}

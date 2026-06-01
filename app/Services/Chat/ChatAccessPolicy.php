<?php

namespace App\Services\Chat;

use App\Models\KnowledgeChunk;
use App\Models\User;

/**
 * Permission-based access control for chat retrieval and tools.
 *
 * This is not chat personality — it gates which corpora and actions are available.
 */
class ChatAccessPolicy
{
    public const PERMISSION_USE_CHAT = 'use knowledge chat';

    public const PERMISSION_REINDEX = 'reindex knowledge base';

    public const PERMISSION_VIEW_CHUNKS = 'view knowledge chunks';

    public const PERMISSION_RETRIEVE_INTERNAL = 'retrieve internal knowledge';

    public const PERMISSION_MANAGE_CHAT_PROMPTS = 'manage chat prompts';

    public const TOOL_CONTACT_SUBMIT = 'contact.submit';

    /**
     * Source types visitors may retrieve (published public corpus only).
     *
     * @return array<int, string>
     */
    public function publicSourceTypes(): array
    {
        return [
            KnowledgeChunk::SOURCE_BLOG,
            KnowledgeChunk::SOURCE_CMS_SECTION,
            KnowledgeChunk::SOURCE_CHAT_PROMPT,
            KnowledgeChunk::SOURCE_FAQ,
            KnowledgeChunk::SOURCE_SERVICE,
            KnowledgeChunk::SOURCE_MODULE,
        ];
    }

    /**
     * Source types available to the current principal.
     *
     * @return array<int, string>
     */
    public function allowedSourceTypes(?User $user): array
    {
        if ($user?->can(self::PERMISSION_RETRIEVE_INTERNAL)) {
            return $this->publicSourceTypes();
        }

        return $this->publicSourceTypes();
    }

    public function includeNonPublic(?User $user): bool
    {
        return (bool) $user?->can(self::PERMISSION_RETRIEVE_INTERNAL);
    }

    /**
     * Tools the principal may invoke alongside chat.
     *
     * @return array<int, string>
     */
    public function allowedTools(?User $user): array
    {
        $tools = [];

        if ($user?->can('submit contact messages')) {
            $tools[] = self::TOOL_CONTACT_SUBMIT;
        }

        return $tools;
    }

    public function canUseChat(?User $user): bool
    {
        if ($user === null) {
            return true;
        }

        return $user->can(self::PERMISSION_USE_CHAT);
    }

    public function canReindex(?User $user): bool
    {
        return (bool) $user?->can(self::PERMISSION_REINDEX);
    }

    public function canViewChunks(?User $user): bool
    {
        return (bool) $user?->can(self::PERMISSION_VIEW_CHUNKS);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeChunk extends Model
{
    public const SOURCE_BLOG = 'blog';

    public const SOURCE_CMS_SECTION = 'cms_section';

    public const SOURCE_FAQ = 'faq';

    public const SOURCE_SERVICE = 'service';

    public const SOURCE_MODULE = 'module';

    /** Admin-only Q&A — indexed for chat, not shown on the website. */
    public const SOURCE_CHAT_PROMPT = 'chat_prompt';

    protected $fillable = [
        'locale',
        'specialist',
        'source_type',
        'source_id',
        'title',
        'slug',
        'public_path',
        'chunk_index',
        'content',
        'content_hash',
        'embedding',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'embedding' => 'array',
            'is_public' => 'boolean',
            'chunk_index' => 'integer',
        ];
    }

    public function isChatOnly(): bool
    {
        return $this->source_type === self::SOURCE_CHAT_PROMPT;
    }

    public function publicUrl(): ?string
    {
        if ($this->isChatOnly() || $this->public_path === '') {
            return null;
        }

        $base = config('openai.frontend_public_url');

        return $base.'/'.ltrim($this->public_path, '/');
    }
}

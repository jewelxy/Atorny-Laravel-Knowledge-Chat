<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Admin-only Q&A for the knowledge chatbot.
 *
 * Not exposed on the public website or FAQ APIs — indexed for RAG retrieval only.
 */
class ChatPrompt extends Model
{
    protected $fillable = [
        'locale',
        'specialist',
        'question',
        'answer',
        'is_active',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function indexingText(): string
    {
        return trim("Question: {$this->question}\nAnswer: {$this->answer}");
    }
}

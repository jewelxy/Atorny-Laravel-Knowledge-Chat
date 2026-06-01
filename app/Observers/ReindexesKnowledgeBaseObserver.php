<?php

namespace App\Observers;

use App\Jobs\ReindexKnowledgeBaseJob;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

/**
 * Queues a knowledge reindex when public content changes.
 */
class ReindexesKnowledgeBaseObserver implements ShouldHandleEventsAfterCommit
{
    public function saved(): void
    {
        ReindexKnowledgeBaseJob::dispatch()->afterCommit();
    }

    public function deleted(): void
    {
        ReindexKnowledgeBaseJob::dispatch()->afterCommit();
    }
}

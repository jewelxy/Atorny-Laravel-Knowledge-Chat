<?php

namespace App\Jobs;

use App\Services\Knowledge\KnowledgeIndexService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ReindexKnowledgeBaseJob implements ShouldQueue
{
    use Queueable;

    public function handle(KnowledgeIndexService $indexService): void
    {
        $indexService->reindexAll();
    }
}

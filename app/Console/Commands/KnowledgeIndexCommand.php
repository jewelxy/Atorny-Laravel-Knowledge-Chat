<?php

namespace App\Console\Commands;

use App\Services\Knowledge\KnowledgeIndexService;
use Illuminate\Console\Command;
use Throwable;

class KnowledgeIndexCommand extends Command
{
    protected $signature = 'knowledge:index';

    protected $description = 'Build locale-aware knowledge chunks and embeddings for RAG chat';

    public function handle(KnowledgeIndexService $indexService): int
    {
        $this->info('Indexing published blogs and CMS sections...');

        try {
            $stats = $indexService->reindexAll();
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Indexed '.$stats['indexed'].' chunks.');
        foreach ($stats['sources'] as $type => $count) {
            $this->line("  - {$type}: {$count}");
        }

        return self::SUCCESS;
    }
}

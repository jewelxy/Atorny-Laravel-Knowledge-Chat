<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('knowledge_chunks', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 8);
            $table->string('source_type', 32);
            $table->string('source_id', 64);
            $table->string('title');
            $table->string('slug')->nullable();
            $table->string('public_path');
            $table->unsignedSmallInteger('chunk_index')->default(0);
            $table->text('content');
            $table->string('content_hash', 64);
            $table->json('embedding');
            $table->boolean('is_public')->default(true);
            $table->timestamps();

            $table->unique(
                ['source_type', 'source_id', 'locale', 'chunk_index'],
                'knowledge_chunks_source_locale_chunk_unique'
            );
            $table->index(['locale', 'is_public']);
            $table->index('source_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_chunks');
    }
};

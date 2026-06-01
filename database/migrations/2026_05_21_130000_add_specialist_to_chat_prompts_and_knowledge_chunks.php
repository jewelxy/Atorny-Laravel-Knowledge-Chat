<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_prompts', function (Blueprint $table) {
            $table->string('specialist', 32)->nullable()->after('locale');
            $table->index(['specialist', 'locale', 'is_active']);
        });

        Schema::table('knowledge_chunks', function (Blueprint $table) {
            $table->string('specialist', 32)->nullable()->after('locale');
            $table->index(['specialist', 'locale', 'source_type']);
        });
    }

    public function down(): void
    {
        Schema::table('knowledge_chunks', function (Blueprint $table) {
            $table->dropIndex(['specialist', 'locale', 'source_type']);
            $table->dropColumn('specialist');
        });

        Schema::table('chat_prompts', function (Blueprint $table) {
            $table->dropIndex(['specialist', 'locale', 'is_active']);
            $table->dropColumn('specialist');
        });
    }
};

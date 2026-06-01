<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * cms_section_translations — per-locale flexible content (JSONB document store).
 *
 * WHY a single `data` JSONB column instead of title/subtitle/description columns:
 * - Each section_type has a different shape (hero vs faq vs pricing).
 * - New fields ship in application code + admin UI, not database migrations.
 * - Supports nested objects, arrays, rich text blocks, and component-specific props.
 *
 * PostgreSQL GIN index enables fast containment queries for admin search and previews.
 * Example: WHERE data @> '{"headline":"Welcome"}'::jsonb
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_section_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cms_section_id')
                ->constrained('cms_sections')
                ->cascadeOnDelete();

            $table->string('locale', 5);

            // All translatable section payload — never add static content columns again.
            $table->jsonb('data')->default('{}');

            $table->timestamps();

            $table->unique(['cms_section_id', 'locale'], 'cms_section_translations_section_locale_unique');

            $table->index('locale', 'cms_section_translations_locale_idx');
        });

        DB::statement(
            'CREATE INDEX cms_section_translations_data_gin
             ON cms_section_translations USING GIN (data)'
        );
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS cms_section_translations_data_gin');

        Schema::dropIfExists('cms_section_translations');
    }
};

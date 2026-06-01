<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Refactors the CMS from fixed translation columns to a JSONB-driven headless architecture.
 *
 * WHY JSONB (data column on cms_section_translations):
 * - Section schemas evolve per section_type (hero, faq, pricing, etc.) without ALTER TABLE migrations.
 * - PostgreSQL JSONB is binary-encoded, indexable (GIN), and supports nested objects and arrays.
 * - Same pattern used by Strapi, Contentful, Sanity: schema lives in the app; content lives in flexible JSON.
 *
 * WHY translations are separated from cms_sections:
 * - settings/styles on sections are locale-agnostic (layout, animation, grid).
 * - Translatable copy and rich content live in cms_section_translations per locale.
 * - One row per (section, locale) with a unique constraint — predictable upserts and caching keys.
 *
 * WHY sections are dynamic (section_type + section_key):
 * - Frontend maps section_type → React/Vue component (e.g. hero → <HeroSection />).
 * - section_key allows multiple instances of the same type on one page (hero_main, hero_secondary).
 * - Reordering via sort_order; toggling via is_active without deleting content.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── cms_sections: add styles JSONB + production indexes ─────────────────
        Schema::table('cms_sections', function (Blueprint $table) {
            if (! Schema::hasColumn('cms_sections', 'styles')) {
                // Locale-agnostic presentation: spacing, breakpoints, CSS variables, Tailwind tokens.
                $table->jsonb('styles')->nullable()->after('settings');
            }
        });

        Schema::table('cms_sections', function (Blueprint $table) {
            // Replace non-unique index from initial migration with a uniqueness guarantee.
            $table->dropIndex(['cms_page_id', 'section_key']);
        });

        Schema::table('cms_sections', function (Blueprint $table) {
            // Unique section_key per page — prevents duplicate component slots.
            $table->unique(['cms_page_id', 'section_key'], 'cms_sections_page_section_key_unique');

            // Fast page builder load: active sections in display order.
            $table->index(['cms_page_id', 'is_active', 'sort_order'], 'cms_sections_page_active_sort_idx');

            // Filter/report by component type across pages.
            $table->index('section_type', 'cms_sections_section_type_idx');
        });

        // ── cms_section_translations: migrate fixed columns → data JSONB ────────
        Schema::table('cms_section_translations', function (Blueprint $table) {
            if (! Schema::hasColumn('cms_section_translations', 'data')) {
                // All translatable fields (title, body, CTAs, FAQ items, pricing tiers, etc.)
                // MUST live inside this column — never add static columns again.
                $table->jsonb('data')->default('{}')->after('locale');
            }
        });

        $this->migrateLegacyTranslationColumnsToData();

        Schema::table('cms_section_translations', function (Blueprint $table) {
            $columnsToDrop = ['title', 'subtitle', 'description', 'content', 'meta_data'];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('cms_section_translations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // GIN index for JSONB containment / path queries (@>, ?, jsonb_path_ops optional).
        // Example: WHERE data @> '{"cta":{"label":"Get Started"}}'::jsonb
        DB::statement(
            'CREATE INDEX IF NOT EXISTS cms_section_translations_data_gin
             ON cms_section_translations USING GIN (data)'
        );
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS cms_section_translations_data_gin');

        Schema::table('cms_section_translations', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->jsonb('content')->nullable();
            $table->jsonb('meta_data')->nullable();
            $table->dropColumn('data');
        });

        Schema::table('cms_sections', function (Blueprint $table) {
            $table->dropUnique('cms_sections_page_section_key_unique');
            $table->dropIndex('cms_sections_page_active_sort_idx');
            $table->dropIndex('cms_sections_section_type_idx');
            $table->dropColumn('styles');
        });
    }

    /**
     * Merge legacy scalar/jsonb columns into the unified data document.
     */
    private function migrateLegacyTranslationColumnsToData(): void
    {
        if (! Schema::hasColumn('cms_section_translations', 'title')) {
            return;
        }

        DB::table('cms_section_translations')->orderBy('id')->lazy()->each(function (object $row): void {
            $data = array_filter([
                'title' => $row->title,
                'subtitle' => $row->subtitle,
                'description' => $row->description,
                'content' => $row->content ? json_decode($row->content, true) : null,
                'meta' => $row->meta_data ? json_decode($row->meta_data, true) : null,
            ], fn ($value) => $value !== null && $value !== '');

            DB::table('cms_section_translations')
                ->where('id', $row->id)
                ->update(['data' => json_encode($data ?: new \stdClass)]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * cms_sections — dynamic, reusable page-builder blocks.
 *
 * section_type drives frontend component resolution:
 *   const COMPONENTS = { hero: HeroSection, faq: FaqSection, pricing: PricingSection };
 *   sections.map(s => COMPONENTS[s.section_type]({ section_key, data }));
 *
 * Layout, styling, and variant config are handled in the frontend — not stored here.
 * Translatable copy/media belongs in cms_section_translations.data (JSONB).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_sections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cms_page_id')
                ->constrained('cms_pages')
                ->cascadeOnDelete();

            $table->string('section_key');

            $table->string('section_type');

            $table->unsignedInteger('sort_order')->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['cms_page_id', 'section_key'], 'cms_sections_page_section_key_unique');

            $table->index(['cms_page_id', 'is_active', 'sort_order'], 'cms_sections_page_active_sort_idx');

            $table->index('section_type', 'cms_sections_section_type_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_sections');
    }
};

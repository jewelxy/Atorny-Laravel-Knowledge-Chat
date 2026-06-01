<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * cms_pages — minimal page registry (headless CMS page shell).
 *
 * Pages do NOT store content. They are identifiers + publish state + audit trail.
 * All renderable content lives in cms_sections + cms_section_translations.
 *
 * Frontend flow: GET /api/v1/public/pages/{key}?locale=en
 *   → resolve page by key → load ordered active sections → merge locale translation data.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();

            // Stable API identifier consumed by React/Next.js routers (e.g. "home", "about").
            $table->string('key')->unique();

            $table->boolean('status')->default(true);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('status', 'cms_pages_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_pages');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Presentation and layout config are owned by the frontend (per section_type).
 * cms_sections only stores structure; translatable content lives in translations.data.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cms_sections', function (Blueprint $table) {
            if (Schema::hasColumn('cms_sections', 'settings')) {
                $table->dropColumn('settings');
            }
            if (Schema::hasColumn('cms_sections', 'styles')) {
                $table->dropColumn('styles');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cms_sections', function (Blueprint $table) {
            $table->jsonb('settings')->nullable()->after('is_active');
            $table->jsonb('styles')->nullable()->after('settings');
        });
    }
};

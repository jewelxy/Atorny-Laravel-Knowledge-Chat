<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
            Schema::create('blog_translations', function (Blueprint $table) {

                $table->id();

                $table->foreignId('blog_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->string('locale', 5);

                $table->string('title');

                $table->string('slug')->unique();

                $table->longText('description');

                $table->string('meta_tag')->nullable();

                $table->text('meta_description')->nullable();

                $table->timestamps();

                $table->unique([
                    'blog_id',
                    'locale'
                ]);

                $table->index([
                    'locale',
                    'slug'
                ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_translations');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('lesson_variants', 'chapter_variants');
        
        Schema::table('chapter_variants', function (Blueprint $table) {
            // Drop old foreign keys
            try {
                $table->dropForeign('lesson_variants_original_lesson_id_foreign');
            } catch (\Exception $e) {
                // Key might not exist
            }
            
            try {
                $table->dropForeign('lesson_variants_variant_lesson_id_foreign');
            } catch (\Exception $e) {
                // Key might not exist
            }
        });
        
        // Rename columns
        Schema::table('chapter_variants', function (Blueprint $table) {
            $table->renameColumn('original_lesson_id', 'original_chapter_id');
            $table->renameColumn('variant_lesson_id', 'variant_chapter_id');
        });
        
        // Re-add foreign keys
        Schema::table('chapter_variants', function (Blueprint $table) {
            $table->foreign('original_chapter_id')->references('id')->on('chapters')->cascadeOnDelete();
            $table->foreign('variant_chapter_id')->references('id')->on('chapters')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('chapter_variants', function (Blueprint $table) {
            try {
                $table->dropForeign('chapter_variants_original_chapter_id_foreign');
                $table->dropForeign('chapter_variants_variant_chapter_id_foreign');
            } catch (\Exception $e) {
                // Keys might not exist
            }
        });
        
        Schema::table('chapter_variants', function (Blueprint $table) {
            $table->renameColumn('original_chapter_id', 'original_lesson_id');
            $table->renameColumn('variant_chapter_id', 'variant_lesson_id');
        });
        
        Schema::table('chapter_variants', function (Blueprint $table) {
            $table->foreign('original_lesson_id')->references('id')->on('lessons')->cascadeOnDelete();
            $table->foreign('variant_lesson_id')->references('id')->on('lessons')->cascadeOnDelete();
        });
        
        Schema::rename('chapter_variants', 'lesson_variants');
    }
};

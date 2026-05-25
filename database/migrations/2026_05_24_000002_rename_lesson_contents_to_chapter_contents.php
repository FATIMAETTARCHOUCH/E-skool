<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Check if already migrated
        if (Schema::hasTable('chapter_contents')) {
            return;
        }

        // First, drop the foreign key before renaming
        if (Schema::hasTable('lesson_contents')) {
            Schema::table('lesson_contents', function (Blueprint $table) {
                // Get all foreign keys for this table
                $fks = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME='lesson_contents' AND COLUMN_NAME='lesson_id' AND REFERENCED_TABLE_NAME IS NOT NULL");
                if (!empty($fks)) {
                    $table->dropForeign($fks[0]->CONSTRAINT_NAME);
                }
            });

            // Rename the table
            Schema::rename('lesson_contents', 'chapter_contents');
        }
        
        // Rename the column if needed
        if (Schema::hasColumn('chapter_contents', 'lesson_id')) {
            Schema::table('chapter_contents', function (Blueprint $table) {
                $table->renameColumn('lesson_id', 'chapter_id');
            });
        }
        
        // Re-add foreign key with new column name if it doesn't exist
        Schema::table('chapter_contents', function (Blueprint $table) {
            $fks = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME='chapter_contents' AND COLUMN_NAME='chapter_id' AND REFERENCED_TABLE_NAME='chapters'");
            if (empty($fks)) {
                $table->foreign('chapter_id')->references('id')->on('chapters')->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('chapter_contents')) {
            return;
        }

        // Drop foreign key
        Schema::table('chapter_contents', function (Blueprint $table) {
            $fks = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME='chapter_contents' AND COLUMN_NAME='chapter_id' AND REFERENCED_TABLE_NAME IS NOT NULL");
            if (!empty($fks)) {
                $table->dropForeign($fks[0]->CONSTRAINT_NAME);
            }
        });
        
        // Rename column back
        if (Schema::hasColumn('chapter_contents', 'chapter_id')) {
            Schema::table('chapter_contents', function (Blueprint $table) {
                $table->renameColumn('chapter_id', 'lesson_id');
            });
        }
        
        // Rename table back
        Schema::rename('chapter_contents', 'lesson_contents');
        
        // Re-add old foreign key
        Schema::table('lesson_contents', function (Blueprint $table) {
            $table->foreign('lesson_id')->references('id')->on('lessons')->cascadeOnDelete();
        });
    }
};

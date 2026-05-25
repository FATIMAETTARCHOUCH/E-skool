<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_progress', function (Blueprint $table) {
            // Add chapter_id column
            if (!Schema::hasColumn('student_progress', 'chapter_id')) {
                $table->foreignId('chapter_id')->nullable()->after('lesson_id')->constrained('chapters')->cascadeOnDelete();
            }
        });

        // Migrate data: copy lesson_id values to chapter_id (they now reference the renamed table)
        DB::statement('UPDATE student_progress SET chapter_id = lesson_id WHERE lesson_id IS NOT NULL AND chapter_id IS NULL');

        Schema::table('student_progress', function (Blueprint $table) {
            // Drop old foreign key constraint
            if (Schema::hasColumn('student_progress', 'lesson_id')) {
                $fks = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME='student_progress' AND COLUMN_NAME='lesson_id' AND REFERENCED_TABLE_NAME IS NOT NULL");
                if (!empty($fks)) {
                    $table->dropForeign($fks[0]->CONSTRAINT_NAME);
                }
                
                // Drop unique constraint if it exists
                $indices = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME='student_progress' AND CONSTRAINT_TYPE='UNIQUE'");
                foreach ($indices as $index) {
                    if (strpos($index->CONSTRAINT_NAME, 'lesson') !== false) {
                        try {
                            $table->dropUnique($index->CONSTRAINT_NAME);
                        } catch (\Exception $e) {
                            // Constraint might not exist
                        }
                    }
                }
                
                $table->dropColumn('lesson_id');
            }
        });

        // Add new unique constraint on (user_id, chapter_id)
        Schema::table('student_progress', function (Blueprint $table) {
            $indices = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME='student_progress' AND CONSTRAINT_TYPE='UNIQUE' AND CONSTRAINT_NAME LIKE '%chapter%'");
            if (empty($indices)) {
                $table->unique(['user_id', 'chapter_id']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_progress', function (Blueprint $table) {
            // Drop unique constraint
            $indices = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME='student_progress' AND CONSTRAINT_TYPE='UNIQUE' AND CONSTRAINT_NAME LIKE '%chapter%'");
            if (!empty($indices)) {
                $table->dropUnique($indices[0]->CONSTRAINT_NAME);
            }
        });

        Schema::table('student_progress', function (Blueprint $table) {
            // Re-add lesson_id column
            if (!Schema::hasColumn('student_progress', 'lesson_id')) {
                $table->foreignId('lesson_id')->nullable()->after('user_id')->constrained('lessons')->cascadeOnDelete();
            }
        });

        // Migrate data back
        DB::statement('UPDATE student_progress SET lesson_id = chapter_id WHERE chapter_id IS NOT NULL AND lesson_id IS NULL');

        Schema::table('student_progress', function (Blueprint $table) {
            // Add back old unique constraint
            $indices = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME='student_progress' AND CONSTRAINT_TYPE='UNIQUE' AND CONSTRAINT_NAME LIKE '%lesson%'");
            if (empty($indices)) {
                $table->unique(['user_id', 'lesson_id']);
            }
            
            // Drop chapter_id
            if (Schema::hasColumn('student_progress', 'chapter_id')) {
                $fks = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME='student_progress' AND COLUMN_NAME='chapter_id' AND REFERENCED_TABLE_NAME IS NOT NULL");
                if (!empty($fks)) {
                    $table->dropForeign($fks[0]->CONSTRAINT_NAME);
                }
                $table->dropColumn('chapter_id');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            // Add chapter_id column if it doesn't exist
            if (!Schema::hasColumn('quizzes', 'chapter_id')) {
                $table->foreignId('chapter_id')->nullable()->after('lesson_id')->constrained('chapters')->cascadeOnDelete();
            }
        });

        // Migrate data: copy lesson_id values to chapter_id (they now reference the renamed table)
        DB::statement('UPDATE quizzes SET chapter_id = lesson_id WHERE lesson_id IS NOT NULL AND chapter_id IS NULL');

        Schema::table('quizzes', function (Blueprint $table) {
            // Drop old lesson_id column and foreign key
            if (Schema::hasColumn('quizzes', 'lesson_id')) {
                $fks = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME='quizzes' AND COLUMN_NAME='lesson_id' AND REFERENCED_TABLE_NAME IS NOT NULL");
                if (!empty($fks)) {
                    $table->dropForeign($fks[0]->CONSTRAINT_NAME);
                }
                $table->dropColumn('lesson_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            // Re-add lesson_id column
            if (!Schema::hasColumn('quizzes', 'lesson_id')) {
                $table->foreignId('lesson_id')->nullable()->after('id')->constrained('lessons')->cascadeOnDelete();
            }
        });

        // Migrate data back
        DB::statement('UPDATE quizzes SET lesson_id = chapter_id WHERE chapter_id IS NOT NULL AND lesson_id IS NULL');

        Schema::table('quizzes', function (Blueprint $table) {
            // Drop chapter_id
            if (Schema::hasColumn('quizzes', 'chapter_id')) {
                $fks = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME='quizzes' AND COLUMN_NAME='chapter_id' AND REFERENCED_TABLE_NAME IS NOT NULL");
                if (!empty($fks)) {
                    $table->dropForeign($fks[0]->CONSTRAINT_NAME);
                }
                $table->dropColumn('chapter_id');
            }
        });
    }
};

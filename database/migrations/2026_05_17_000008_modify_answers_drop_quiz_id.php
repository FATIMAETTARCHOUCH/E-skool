<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('answers', function (Blueprint $table) {
            if (Schema::hasColumn('answers', 'quiz_id')) {
                $table->dropForeign(['quiz_id']);
                $table->dropColumn('quiz_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('answers', function (Blueprint $table) {
            if (! Schema::hasColumn('answers', 'quiz_id')) {
                $table->foreignId('quiz_id')->constrained('quizzes')->after('question_id');
            }
        });
    }
};

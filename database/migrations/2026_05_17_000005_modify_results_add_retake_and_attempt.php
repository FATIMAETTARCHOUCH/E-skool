<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->foreignId('quiz_retake_id')->nullable()->after('quiz_id')->constrained('quiz_retakes')->nullOnDelete();
            $table->unsignedTinyInteger('attempt_number')->default(1)->after('quiz_retake_id');
            if (Schema::hasColumn('results', 'total_questions')) {
                $table->dropColumn('total_questions');
            }
        });
    }

    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            if (Schema::hasColumn('results', 'attempt_number')) {
                $table->dropColumn('attempt_number');
            }
            if (Schema::hasColumn('results', 'quiz_retake_id')) {
                $table->dropForeign(['quiz_retake_id']);
                $table->dropColumn('quiz_retake_id');
            }
            if (! Schema::hasColumn('results', 'total_questions')) {
                $table->unsignedInteger('total_questions')->default(0)->after('score');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('answers', function (Blueprint $table) {
            $table->foreignId('quiz_retake_id')->nullable()->after('option_id')->constrained('quiz_retakes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('answers', function (Blueprint $table) {
            if (Schema::hasColumn('answers', 'quiz_retake_id')) {
                $table->dropForeign(['quiz_retake_id']);
                $table->dropColumn('quiz_retake_id');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_retakes', function (Blueprint $table) {
            $table->unsignedTinyInteger('attempt_number')->default(1)->after('id');
            $table->timestamp('started_at')->nullable()->after('attempt_number');
            $table->timestamp('completed_at')->nullable()->after('started_at');
            $table->enum('status', ['in_progress', 'completed'])->default('in_progress')->after('completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('quiz_retakes', function (Blueprint $table) {
            if (Schema::hasColumn('quiz_retakes', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('quiz_retakes', 'completed_at')) {
                $table->dropColumn('completed_at');
            }
            if (Schema::hasColumn('quiz_retakes', 'started_at')) {
                $table->dropColumn('started_at');
            }
            if (Schema::hasColumn('quiz_retakes', 'attempt_number')) {
                $table->dropColumn('attempt_number');
            }
        });
    }
};

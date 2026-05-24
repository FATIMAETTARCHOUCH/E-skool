<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_progress', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable()->after('updated_at');
            $table->unsignedInteger('time_spent_seconds')->default(0)->after('completed_at');
            if (Schema::hasColumn('student_progress', 'is_completed')) {
                $table->dropColumn('is_completed');
            }
            $table->enum('status', ['locked', 'unlocked', 'in_progress', 'in_remediation', 'passed', 'passed_with_help', 'stuck'])->default('locked')->after('time_spent_seconds');
            $table->index('lesson_id');
            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('student_progress', function (Blueprint $table) {
            if (Schema::hasColumn('student_progress', 'status')) {
                $table->dropIndex(['status']);
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('student_progress', 'time_spent_seconds')) {
                $table->dropColumn('time_spent_seconds');
            }
            if (Schema::hasColumn('student_progress', 'completed_at')) {
                $table->dropColumn('completed_at');
            }
            if (! Schema::hasColumn('student_progress', 'is_completed')) {
                $table->boolean('is_completed')->default(false)->after('updated_at');
            }
            if (Schema::hasColumn('student_progress', 'lesson_id')) {
                $table->dropIndex(['lesson_id']);
            }
            if (Schema::hasColumn('student_progress', 'user_id')) {
                $table->dropIndex(['user_id']);
            }
        });
    }
};

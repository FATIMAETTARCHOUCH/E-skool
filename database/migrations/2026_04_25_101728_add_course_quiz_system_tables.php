i
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create courses table
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->string('level')->nullable();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 2. Create course_group pivot table
        Schema::create('course_group', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // 3. Update lessons table (acting as course_parts)
        Schema::table('lessons', function (Blueprint $table) {
            // Add new columns
            $table->foreignId('course_id')->nullable()->constrained()->cascadeOnDelete();
            $table->integer('order')->default(1);

            // Drop old group_id (if sqlite, might need separate drop foreign)
            // It's MySQL based on previous checks.
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
        });

        // 4. Update quizzes table
        Schema::table('quizzes', function (Blueprint $table) {
            $table->integer('passing_score')->default(80); // percentage
        });

        // 5. Create student_progress table
        Schema::create('student_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete(); // lesson = course_part
            $table->boolean('is_completed')->default(false);
            $table->timestamp('unlocked_at')->nullable();
            $table->timestamps();
        });

        // 6. Update results table
        Schema::table('results', function (Blueprint $table) {
            $table->boolean('is_passed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn('is_passed');
        });

        Schema::dropIfExists('student_progress');

        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn('passing_score');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->foreignId('group_id')->nullable()->constrained()->cascadeOnDelete();
            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');
            $table->dropColumn('order');
        });

        Schema::dropIfExists('course_group');
        Schema::dropIfExists('courses');
    }
};

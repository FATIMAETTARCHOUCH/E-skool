<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            if (Schema::hasColumn('lessons', 'parent_lesson_id')) {
                $table->dropForeign(['parent_lesson_id']);
                $table->dropColumn('parent_lesson_id');
            }
            if (Schema::hasColumn('lessons', 'content_text')) {
                $table->dropColumn('content_text');
            }
            if (Schema::hasColumn('lessons', 'pdf_path')) {
                $table->dropColumn('pdf_path');
            }
            if (Schema::hasColumn('lessons', 'video_path')) {
                $table->dropColumn('video_path');
            }
            if (Schema::hasColumn('lessons', 'image_path')) {
                $table->dropColumn('image_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            if (! Schema::hasColumn('lessons', 'parent_lesson_id')) {
                $table->unsignedBigInteger('parent_lesson_id')->nullable()->after('id');
                $table->foreign('parent_lesson_id')->references('id')->on('lessons')->nullOnDelete();
            }
            if (! Schema::hasColumn('lessons', 'content_text')) {
                $table->text('content_text')->nullable()->after('parent_lesson_id');
            }
            if (! Schema::hasColumn('lessons', 'pdf_path')) {
                $table->string('pdf_path')->nullable()->after('content_text');
            }
            if (! Schema::hasColumn('lessons', 'video_path')) {
                $table->string('video_path')->nullable()->after('pdf_path');
            }
            if (! Schema::hasColumn('lessons', 'image_path')) {
                $table->string('image_path')->nullable()->after('video_path');
            }
        });
    }
};

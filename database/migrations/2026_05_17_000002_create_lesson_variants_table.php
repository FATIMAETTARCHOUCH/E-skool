<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('original_lesson_id')->constrained('lessons')->cascadeOnDelete();
            $table->foreignId('variant_lesson_id')->constrained('lessons')->cascadeOnDelete();
            $table->string('trigger')->default('quiz_failed');
            $table->timestamps();
            $table->unique(['original_lesson_id', 'trigger']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_variants');
    }
};

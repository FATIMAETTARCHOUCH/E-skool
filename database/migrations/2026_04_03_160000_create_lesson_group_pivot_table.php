<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_group', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $blueprint->foreignId('group_id')->constrained()->onDelete('cascade');
            $blueprint->timestamps();
        });

        // We can keep group_id in lessons for now but make it nullable 
        // to avoid breaking existing data during transition
        Schema::table('lessons', function (Blueprint $table) {
            $table->foreignId('group_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_group');
    }
};

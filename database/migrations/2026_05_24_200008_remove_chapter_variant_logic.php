<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('chapter_variants');
    }

    public function down(): void
    {
        Schema::create('chapter_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_chapter_id');
            $table->unsignedBigInteger('variant_chapter_id');
            $table->string('trigger');
            $table->timestamps();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('lessons', 'chapters');
        
        Schema::table('chapters', function (Blueprint $table) {
            // Add new description column for chapter intros
            if (!Schema::hasColumn('chapters', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
        });
    }

    public function down(): void
    {
        Schema::table('chapters', function (Blueprint $table) {
            if (Schema::hasColumn('chapters', 'description')) {
                $table->dropColumn('description');
            }
        });
        
        Schema::rename('chapters', 'lessons');
    }
};

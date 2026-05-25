<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\ChapterContent;
use App\Models\ChapterResource;

return new class extends Migration
{
    public function up(): void
    {
        $contents = DB::table('chapter_contents')->get();
        foreach ($contents as $content) {
            DB::table('chapter_resources')->insert([
                'chapter_id' => $content->chapter_id,
                'is_remedial' => false,
                'type' => $content->type,
                'value' => $content->value,
                'order' => $content->order,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('chapter_resources')->truncate();
    }
};

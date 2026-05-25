<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChapterVariant extends Model
{
    protected $fillable = ['original_chapter_id', 'variant_chapter_id', 'trigger'];

    public function originalChapter()
    {
        return $this->belongsTo(Chapter::class, 'original_chapter_id');
    }

    public function variantChapter()
    {
        return $this->belongsTo(Chapter::class, 'variant_chapter_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonVariant extends Model
{
    protected $fillable = ['original_lesson_id', 'variant_lesson_id', 'trigger'];

    public function originalLesson()
    {
        return $this->belongsTo(Lesson::class, 'original_lesson_id');
    }

    public function variantLesson()
    {
        return $this->belongsTo(Lesson::class, 'variant_lesson_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = ['course_id', 'order', 'title', 'tag'];

    public function course() { return $this->belongsTo(Course::class); }
    public function quizzes() { return $this->hasMany(Quiz::class); }
    public function studentProgress() { return $this->hasMany(StudentProgress::class); }

    // New relationship: lesson contents (text, pdf, video, image blocks)
    public function contents() { return $this->hasMany(LessonContent::class)->orderBy('order'); }

    // New relationship: variants mapping where this lesson is the original
    public function variants() { return $this->hasMany(LessonVariant::class, 'original_lesson_id'); }

    // New relationship: cases where this lesson is used as a variant of another
    public function asVariantOf() { return $this->hasMany(LessonVariant::class, 'variant_lesson_id'); }

    // Returns the remedial variant Lesson (if any)
    public function getRemediationVariant()
    {
        $variant = $this->variants()->where('trigger', 'quiz_failed')->first();
        return $variant?->variantLesson;
    }
}

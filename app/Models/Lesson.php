<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = ['course_id', 'order', 'title', 'content_text', 'pdf_path', 'video_path', 'image_path', 'tag'];

    public function course() { return $this->belongsTo(Course::class); }
    public function quizzes() { return $this->hasMany(Quiz::class); }
    public function studentProgress() { return $this->hasMany(StudentProgress::class); }
}

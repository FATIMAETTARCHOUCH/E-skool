<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonPDF extends Model
{
    protected $table = 'lesson_pdfs';
    protected $fillable = ['lesson_id', 'title', 'pdf_path', 'order'];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    // Get full URL for PDF
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->pdf_path);
    }
}

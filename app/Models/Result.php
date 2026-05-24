<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = ['user_id', 'quiz_id', 'score', 'is_passed', 'quiz_retake_id', 'attempt_number'];

    protected $casts = [
        'is_passed' => 'boolean',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function quiz() { return $this->belongsTo(Quiz::class); }

    public function retake() { return $this->belongsTo(QuizRetake::class, 'quiz_retake_id'); }

    public function getTotalQuestionsAttribute()
    {
        return $this->quiz?->questions()->count() ?? 0;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = ['user_id', 'question_id', 'option_id', 'quiz_retake_id'];

    public function user() { return $this->belongsTo(User::class); }
    public function question() { return $this->belongsTo(Question::class); }
    public function option() { return $this->belongsTo(Option::class); }
    public function retake() { return $this->belongsTo(QuizRetake::class, 'quiz_retake_id'); }

    // Accessor to preserve `quiz_id` attribute compatibility
    public function getQuizIdAttribute()
    {
        return $this->question?->quiz_id;
    }
}

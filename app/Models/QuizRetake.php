<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizRetake extends Model
{
    protected $fillable = ['quiz_id', 'user_id', 'attempt_number', 'started_at', 'completed_at', 'status'];

    public function quiz() { return $this->belongsTo(Quiz::class); }
    public function user() { return $this->belongsTo(User::class); }

    public function answers() { return $this->hasMany(Answer::class, 'quiz_retake_id'); }
    public function result() { return $this->hasOne(Result::class, 'quiz_retake_id'); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = ['lesson_id', 'title', 'scheduled_at', 'is_active', 'passing_score'];
    
    protected function casts(): array {
        return [
            'scheduled_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function lesson() { return $this->belongsTo(Lesson::class); }
    public function questions() { return $this->hasMany(Question::class); }
    public function results() { return $this->hasMany(Result::class); }
    public function retakes() { return $this->hasMany(QuizRetake::class); }
}

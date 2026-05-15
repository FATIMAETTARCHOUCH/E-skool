<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = ['user_id', 'quiz_id', 'score', 'total_questions', 'is_passed'];

    protected $casts = [
        'is_passed' => 'boolean',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function quiz() { return $this->belongsTo(Quiz::class); }
}

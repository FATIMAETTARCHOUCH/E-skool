<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $fillable = ['question_id', 'content_text', 'is_correct'];

    protected function casts(): array {
        return ['is_correct' => 'boolean'];
    }

    public function question() { return $this->belongsTo(Question::class); }
}

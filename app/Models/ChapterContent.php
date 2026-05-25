<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChapterContent extends Model
{
    protected $fillable = ['chapter_id', 'type', 'value', 'order'];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}

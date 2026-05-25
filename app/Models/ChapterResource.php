<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChapterResource extends Model
{
    protected $fillable = ['chapter_id', 'is_remedial', 'type', 'value', 'order'];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    protected $fillable = ['course_id', 'order', 'title', 'description', 'tag'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function resources()
    {
        return $this->hasMany(ChapterResource::class)->orderBy('order');
    }

    public function primaryResources()
    {
        return $this->resources()->where('is_remedial', false);
    }

    public function remedialResources()
    {
        return $this->resources()->where('is_remedial', true);
    }

    public function quiz()
    {
        return $this->hasOne(Quiz::class);
    }

    public function studentProgress()
    {
        return $this->hasMany(StudentProgress::class);
    }
}

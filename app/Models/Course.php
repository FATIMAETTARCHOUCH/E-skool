<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'level',
        'teacher_id'
    ];

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'course_group');
    }

    public function lessons() // Acting as CourseParts
    {
        return $this->hasMany(Lesson::class)->orderBy('order', 'asc');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['branch_id', 'academic_year_id', 'name'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function lessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_group');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_group');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\StudentProgressStatus;

class StudentProgress extends Model
{
    use HasFactory;

    protected $table = 'student_progress';

    protected $fillable = [
        'user_id',
        'lesson_id', // acting as course_part
        'status',
        'completed_at',
        'time_spent_seconds',
        'unlocked_at'
    ];

    protected $casts = [
        'unlocked_at' => 'datetime',
        'completed_at' => 'datetime',
        'time_spent_seconds' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function isStuck()
    {
        return $this->status === StudentProgressStatus::STUCK->value;
    }

    public function passedWithHelp()
    {
        return $this->status === StudentProgressStatus::PASSED_WITH_HELP->value;
    }
}

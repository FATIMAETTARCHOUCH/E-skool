<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\StudentProgressStatus;
use Carbon\Carbon;

class StudentProgress extends Model
{
    public const QUIZ_BLOCK_HOURS = 34;
    use HasFactory;

    protected $table = 'student_progress';

    protected $fillable = [
        'user_id',
        'chapter_id',
        'status',
        'completed_at',
        'time_spent_seconds',
        'unlocked_at',
        'needs_remediation',
        'quiz_blocked_until',
    ];

    protected $casts = [
        'unlocked_at' => 'datetime',
        'completed_at' => 'datetime',
        'time_spent_seconds' => 'integer',
        'needs_remediation' => 'boolean',
        'quiz_blocked_until' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function isStuck()
    {
        return $this->status === StudentProgressStatus::STUCK->value;
    }

    public function passedWithHelp(): bool
    {
        return $this->status === StudentProgressStatus::PASSED_WITH_HELP->value;
    }

    public function hasPassedChapter(): bool
    {
        return in_array($this->status, [
            StudentProgressStatus::PASSED->value,
            StudentProgressStatus::PASSED_WITH_HELP->value,
        ], true);
    }

    public function isQuizBlocked(): bool
    {
        return $this->quiz_blocked_until !== null
            && $this->quiz_blocked_until->isFuture();
    }

    public function quizBlockedRemainingHours(): int
    {
        if (! $this->isQuizBlocked()) {
            return 0;
        }

        return (int) max(1, ceil(now()->diffInSeconds($this->quiz_blocked_until) / 3600));
    }

    public static function completedStatuses(): array
    {
        return [
            StudentProgressStatus::PASSED->value,
            StudentProgressStatus::PASSED_WITH_HELP->value,
        ];
    }
}

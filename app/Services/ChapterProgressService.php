<?php

namespace App\Services;

use App\Enums\StudentProgressStatus;
use App\Models\Answer;
use App\Models\Chapter;
use App\Models\Quiz;
use App\Models\QuizRetake;
use App\Models\Result;
use App\Models\StudentProgress;
use App\Models\User;
use App\Notifications\StudentStuckNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ChapterProgressService
{
    public function startChapter(User $student, Chapter $chapter): StudentProgress
    {
        $progress = StudentProgress::firstOrCreate([
            'user_id' => $student->id,
            'chapter_id' => $chapter->id,
        ], [
            'status' => StudentProgressStatus::IN_PROGRESS->value,
            'unlocked_at' => Carbon::now(),
        ]);

        if ($progress->hasPassedChapter() || $progress->isStuck()) {
            return $progress;
        }

        if ($progress->status === StudentProgressStatus::LOCKED->value) {
            $progress->status = StudentProgressStatus::IN_PROGRESS->value;
            $progress->unlocked_at = $progress->unlocked_at ?? Carbon::now();
            $progress->save();
        }

        return $progress;
    }

    public function submitQuizAttempt(User $student, Quiz $quiz, array $answers, ?QuizRetake $retake = null): array
    {
        return DB::transaction(function () use ($student, $quiz, $answers, $retake) {
            // Determine attempt / retake
            if ($retake) {
                $retakeModel = $retake;
            } else {
                $lastAttempt = QuizRetake::where('quiz_id', $quiz->id)->where('user_id', $student->id)->max('attempt_number');
                $attemptNumber = $lastAttempt ? $lastAttempt + 1 : 1;
                $retakeModel = QuizRetake::create([
                    'quiz_id' => $quiz->id,
                    'user_id' => $student->id,
                    'attempt_number' => $attemptNumber,
                    'started_at' => Carbon::now(),
                    'status' => 'in_progress',
                ]);
            }

            // Remove any previous answers for this retake (idempotency)
            Answer::where('quiz_retake_id', $retakeModel->id)->delete();

            // Normalize answers: support both [q => option] and [[question_id, option_id],...]
            $normalized = [];
            if ($this->isAssoc($answers)) {
                foreach ($answers as $q => $opt) {
                    $normalized[] = ['question_id' => (int)$q, 'option_id' => (int)$opt];
                }
            } else {
                foreach ($answers as $a) {
                    if (isset($a['question_id']) && isset($a['option_id'])) {
                        $normalized[] = ['question_id' => (int)$a['question_id'], 'option_id' => (int)$a['option_id']];
                    }
                }
            }

            $correct = 0;
            $total = $quiz->questions()->count() ?: count($normalized);

            foreach ($normalized as $row) {
                $question = $quiz->questions()->where('id', $row['question_id'])->first();
                if (! $question) continue;
                $option = $question->options()->where('id', $row['option_id'])->first();
                $isCorrect = $option?->is_correct ? true : false;
                if ($isCorrect) $correct++;

                Answer::create([
                    'user_id' => $student->id,
                    'question_id' => $row['question_id'],
                    'option_id' => $row['option_id'],
                    'quiz_retake_id' => $retakeModel->id,
                ]);
            }

            $percentage = $total > 0 ? (int) floor(($correct / $total) * 100) : 0;
            $passed = $percentage >= ($quiz->passing_score ?? 50);

            // finalize retake
            $retakeModel->completed_at = Carbon::now();
            $retakeModel->status = 'completed';
            $retakeModel->save();

            // create result
            $result = Result::create([
                'user_id' => $student->id,
                'quiz_id' => $quiz->id,
                'score' => $percentage,
                'is_passed' => $passed,
                'quiz_retake_id' => $retakeModel->id,
                'attempt_number' => $retakeModel->attempt_number,
            ]);

            // Update student progress
            $chapter = $quiz->chapter;
            $progress = StudentProgress::firstOrCreate([
                'user_id' => $student->id,
                'chapter_id' => $chapter->id,
            ], [
                'status' => StudentProgressStatus::UNLOCKED->value,
                'unlocked_at' => Carbon::now(),
            ]);

            if ($passed) {
                if ($retakeModel->attempt_number <= 1) {
                    $progress->status = StudentProgressStatus::PASSED->value;
                } else {
                    $progress->status = StudentProgressStatus::PASSED_WITH_HELP->value;
                }
                $progress->needs_remediation = false;
                $progress->quiz_blocked_until = null;
                $progress->completed_at = Carbon::now();
                $progress->save();

                return ['passed' => true, 'score' => $percentage, 'retake' => $retakeModel, 'result' => $result];
            }

            // Not passed
            if ($retakeModel->attempt_number <= 1) {
                // Mark progress to indicate remediation is needed
                $progress->needs_remediation = true;
                $progress->status = StudentProgressStatus::IN_REMEDIATION->value;
                $progress->save();

                return ['passed' => false, 'score' => $percentage, 'retake' => $retakeModel];
            }

            // attempt 2+ failed => mark stuck and block quiz for 34 hours
            $this->markAsStuck($student, $chapter);
            $progress->status = StudentProgressStatus::STUCK->value;
            $progress->quiz_blocked_until = Carbon::now()->addHours(StudentProgress::QUIZ_BLOCK_HOURS);
            $progress->save();

            return ['passed' => false, 'score' => $percentage, 'retake' => $retakeModel, 'blocked_until' => $progress->quiz_blocked_until];
        });
    }

    public function getChapterProgress(User $student, Chapter $chapter): ?StudentProgress
    {
        return StudentProgress::where('user_id', $student->id)
            ->where('chapter_id', $chapter->id)
            ->first();
    }

    public function canTakeQuiz(User $student, Quiz $quiz): array
    {
        $progress = $this->getChapterProgress($student, $quiz->chapter);

        if ($progress?->hasPassedChapter()) {
            return ['allowed' => false, 'reason' => 'passed'];
        }

        if ($progress?->isQuizBlocked()) {
            return [
                'allowed' => false,
                'reason' => 'blocked',
                'blocked_until' => $progress->quiz_blocked_until,
                'hours_remaining' => $progress->quizBlockedRemainingHours(),
            ];
        }

        return ['allowed' => true, 'reason' => null];
    }

    private function markAsStuck(User $student, Chapter $chapter): void
    {
        $progress = StudentProgress::firstOrCreate([
            'user_id' => $student->id,
            'chapter_id' => $chapter->id,
        ]);
        $progress->status = StudentProgressStatus::STUCK->value;
        $progress->quiz_blocked_until = Carbon::now()->addHours(StudentProgress::QUIZ_BLOCK_HOURS);
        $progress->save();

        // notify the course teacher if exists
        $teacher = $chapter->course?->teacher;
        if ($teacher) {
            Notification::send($teacher, new StudentStuckNotification($student, $chapter));
        }
    }

    private function isAssoc(array $arr): bool
    {
        if ([] === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}

<?php

namespace App\Services;

use App\Enums\StudentProgressStatus;
use App\Models\Answer;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizRetake;
use App\Models\Result;
use App\Models\StudentProgress;
use App\Models\User;
use App\Notifications\StudentStuckNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class LessonProgressService
{
    public function startLesson(User $student, Lesson $lesson): StudentProgress
    {
        $progress = StudentProgress::firstOrCreate([
            'user_id' => $student->id,
            'lesson_id' => $lesson->id,
        ], [
            'status' => StudentProgressStatus::IN_PROGRESS->value,
            'unlocked_at' => Carbon::now(),
        ]);

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
            $lesson = $quiz->lesson;
            $progress = StudentProgress::firstOrCreate([
                'user_id' => $student->id,
                'lesson_id' => $lesson->id,
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
                $progress->completed_at = Carbon::now();
                $progress->save();
                return ['passed' => true, 'score' => $percentage, 'retake' => $retakeModel, 'result' => $result];
            }

            // Not passed
            if ($retakeModel->attempt_number <= 1) {
                $variant = $this->redirectToVariant($student, $lesson);
                return ['passed' => false, 'score' => $percentage, 'retake' => $retakeModel, 'variant' => $variant];
            }

            // attempt 2+ failed => mark stuck
            $this->markAsStuck($student, $lesson);
            $progress->status = StudentProgressStatus::STUCK->value;
            $progress->save();

            return ['passed' => false, 'score' => $percentage, 'retake' => $retakeModel, 'variant' => null];
        });
    }

    public function redirectToVariant(User $student, Lesson $lesson): ?Lesson
    {
        $variant = $lesson->getRemediationVariant();
        // update progress to in_remediation
        $progress = StudentProgress::firstOrCreate([
            'user_id' => $student->id,
            'lesson_id' => $lesson->id,
        ]);
        $progress->status = StudentProgressStatus::IN_REMEDIATION->value;
        $progress->save();

        return $variant;
    }

    public function startRemediation(User $student, Lesson $originalLesson): void
    {
        $variant = $originalLesson->getRemediationVariant();
        if (! $variant) return;

        // mark original as in_remediation
        $origProgress = StudentProgress::firstOrCreate([
            'user_id' => $student->id,
            'lesson_id' => $originalLesson->id,
        ]);
        $origProgress->status = StudentProgressStatus::IN_REMEDIATION->value;
        $origProgress->save();

        // ensure variant progress exists and mark as in_progress
        $variantProgress = StudentProgress::firstOrCreate([
            'user_id' => $student->id,
            'lesson_id' => $variant->id,
        ], [
            'status' => StudentProgressStatus::IN_PROGRESS->value,
            'unlocked_at' => Carbon::now(),
        ]);

        if ($variantProgress->status === StudentProgressStatus::UNLOCKED->value) {
            $variantProgress->status = StudentProgressStatus::IN_PROGRESS->value;
            $variantProgress->save();
        }
    }

    private function markAsStuck(User $student, Lesson $lesson): void
    {
        $progress = StudentProgress::firstOrCreate([
            'user_id' => $student->id,
            'lesson_id' => $lesson->id,
        ]);
        $progress->status = StudentProgressStatus::STUCK->value;
        $progress->save();

        // notify the course teacher if exists
        $teacher = $lesson->course?->teacher;
        if ($teacher) {
            Notification::send($teacher, new StudentStuckNotification($student, $lesson));
        }
    }

    private function isAssoc(array $arr): bool
    {
        if ([] === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}

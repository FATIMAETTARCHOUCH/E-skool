<?php

namespace App\Http\Controllers\Student;

use App\Enums\StudentProgressStatus;
use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\StudentProgress;
use App\Services\LessonProgressService;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    public function show(Lesson $lesson, LessonProgressService $progressService)
    {
        $user = Auth::user();

        $lesson->load(['course.teacher', 'contents' => fn ($query) => $query->orderBy('order'), 'quizzes.questions.options']);
        $this->ensurePreviousLessonCompleted($user->id, $lesson);
        $progressService->startLesson($user, $lesson);

        return view('student.lessons.show', [
            'lesson' => $lesson,
            'course' => $lesson->course,
            'quiz' => $lesson->quizzes->first(),
        ]);
    }

    public function showVariant(Lesson $lesson, LessonProgressService $progressService)
    {
        $user = Auth::user();
        $variant = $lesson->getRemediationVariant();

        if (! $variant) {
            return redirect()->route('student.lesson', $lesson)->with('error', 'Aucune version simplifiée n\'est disponible pour cette leçon.');
        }

        $progressService->startRemediation($user, $lesson);

        $variant->load(['course.teacher', 'contents' => fn ($query) => $query->orderBy('order'), 'quizzes.questions.options']);

        return view('student.lessons.variant', [
            'lesson' => $lesson,
            'variantLesson' => $variant,
            'course' => $lesson->course,
            'quiz' => $variant->quizzes->first(),
        ]);
    }

    private function ensurePreviousLessonCompleted(int $userId, Lesson $lesson): void
    {
        if ($lesson->order <= 1) {
            return;
        }

        $previousLesson = Lesson::where('course_id', $lesson->course_id)
            ->where('order', '<', $lesson->order)
            ->orderBy('order', 'desc')
            ->first();

        if (! $previousLesson) {
            return;
        }

        $prevProgress = StudentProgress::where('user_id', $userId)
            ->where('lesson_id', $previousLesson->id)
            ->first();

        $completedStatuses = [
            StudentProgressStatus::PASSED->value,
            StudentProgressStatus::PASSED_WITH_HELP->value,
        ];

        if (! $prevProgress || ! in_array($prevProgress->status, $completedStatuses, true)) {
            abort(403, "Vous devez valider la partie précédente avant d'accéder à celle-ci.");
        }
    }
}

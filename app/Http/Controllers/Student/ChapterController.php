<?php

namespace App\Http\Controllers\Student;

use App\Enums\StudentProgressStatus;
use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\StudentProgress;
use App\Services\ChapterProgressService;
use Illuminate\Support\Facades\Auth;

class ChapterController extends Controller
{
    public function show(Chapter $chapter, ChapterProgressService $progressService)
    {
        $user = Auth::user();

        $chapter->load(['course.teacher', 'resources' => fn ($query) => $query->orderBy('order'), 'quiz.questions.options']);

        $this->ensurePreviousChapterCompleted($user->id, $chapter);
        $progressService->startChapter($user, $chapter);

        $progress = StudentProgress::where('user_id', $user->id)
            ->where('chapter_id', $chapter->id)
            ->first();

        $hasPassed = $progress?->hasPassedChapter() ?? false;
        $isQuizBlocked = $progress?->isQuizBlocked() ?? false;

        $needsRemediation = ! $hasPassed && $progress && (
            $progress->needs_remediation
            || $progress->status === StudentProgressStatus::IN_REMEDIATION->value
        );

        $remedialResources = $chapter->resources->where('is_remedial', true)->values();
        $primaryResources = $chapter->resources->where('is_remedial', false)->values();

        $usingRemedialFallback = false;
        if ($needsRemediation && $remedialResources->isNotEmpty()) {
            $resources = $remedialResources;
        } elseif ($needsRemediation) {
            $usingRemedialFallback = true;
            $resources = $primaryResources;
        } else {
            $resources = $primaryResources;
        }

        $canTakeQuiz = $chapter->quiz && ! $hasPassed && ! $isQuizBlocked;
        if ($chapter->quiz && ! $hasPassed && ! $isQuizBlocked) {
            $access = $progressService->canTakeQuiz($user, $chapter->quiz);
            $canTakeQuiz = $access['allowed'];
        }

        return view('student.chapters.show', [
            'chapter' => $chapter,
            'course' => $chapter->course,
            'quiz' => $chapter->quiz,
            'progress' => $progress,
            'hasPassed' => $hasPassed,
            'isQuizBlocked' => $isQuizBlocked,
            'canTakeQuiz' => $canTakeQuiz,
            'needsRemediation' => $needsRemediation,
            'resources' => $resources,
            'usingRemedialFallback' => $usingRemedialFallback,
            'quizResult' => session('quiz_result'),
            'blockedUntil' => $progress?->quiz_blocked_until,
        ]);
    }

    private function ensurePreviousChapterCompleted(int $userId, Chapter $chapter): void
    {
        if ($chapter->order <= 1) {
            return;
        }

        $previousChapter = Chapter::where('course_id', $chapter->course_id)
            ->where('order', '<', $chapter->order)
            ->orderBy('order', 'desc')
            ->first();

        if (! $previousChapter) {
            return;
        }

        $prevProgress = StudentProgress::where('user_id', $userId)
            ->where('chapter_id', $previousChapter->id)
            ->first();

        if (! $prevProgress || ! $prevProgress->hasPassedChapter()) {
            abort(403, "Vous devez valider la partie précédente avant d'accéder à celle-ci.");
        }
    }
}

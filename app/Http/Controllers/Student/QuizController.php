<?php

namespace App\Http\Controllers\Student;

use App\Enums\StudentProgressStatus;
use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Quiz;
use App\Models\QuizRetake;
use App\Models\StudentProgress;
use App\Services\ChapterProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function show(Quiz $quiz, ChapterProgressService $progressService)
    {
        $quiz->load(['chapter.course', 'questions.options']);
        $user = Auth::user();

        $access = $progressService->canTakeQuiz($user, $quiz);
        if (! $access['allowed']) {
            return $this->denyQuizAccess($quiz, $access);
        }

        $latestRetake = QuizRetake::with('answers')
            ->where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->orderByDesc('attempt_number')
            ->first();

        $answers = $latestRetake
            ? $latestRetake->answers->pluck('option_id', 'question_id')
            : collect([]);

        return view('student.quizzes.show', [
            'quiz' => $quiz,
            'answers' => $answers,
            'retake' => $latestRetake,
        ]);
    }

    public function submit(Request $request, Quiz $quiz, ChapterProgressService $progressService)
    {
        $quiz->load('chapter.course', 'questions.options');
        $user = Auth::user();

        $access = $progressService->canTakeQuiz($user, $quiz);
        if (! $access['allowed']) {
            return $this->denyQuizAccess($quiz, $access);
        }

        $validated = $request->validate([
            'answers' => ['required', 'array'],
        ]);

        $payload = [];
        foreach ($validated['answers'] as $questionId => $optionId) {
            $payload[] = [
                'question_id' => (int) $questionId,
                'option_id' => (int) $optionId,
            ];
        }

        $attempt = $progressService->submitQuizAttempt($user, $quiz, $payload);

        if ($attempt['passed']) {
            $status = $attempt['retake']->attempt_number <= 1 ? 'passed' : 'passed_with_help';

            $nextChapter = Chapter::where('course_id', $quiz->chapter->course_id)
                ->where('order', '>', $quiz->chapter->order)
                ->orderBy('order')
                ->first();

            return redirect()->route('student.quiz.result', $quiz)->with([
                'success' => $nextChapter
                    ? 'Quiz réussi ! Vous pouvez passer à la partie suivante.'
                    : 'Quiz réussi ! Vous avez terminé ce cours.',
                'quiz_result' => $attempt,
                'quiz_status' => $status,
                'next_chapter_id' => $nextChapter?->id,
            ]);
        }

        if ($attempt['retake']->attempt_number <= 1) {
            return redirect()->route('student.chapter', $quiz->chapter)->with([
                'error' => 'Vous devez consulter les ressources de remédiation avant de retenter le quiz.',
                'quiz_result' => $attempt,
            ]);
        }

        return redirect()->route('student.quiz.result', $quiz)->with([
            'error' => 'Deuxième échec : vous ne pouvez pas repasser le quiz avant '.StudentProgress::QUIZ_BLOCK_HOURS.' heures. Votre enseignant a été notifié.',
            'quiz_result' => $attempt,
            'quiz_status' => 'stuck',
            'blocked_until' => $attempt['blocked_until'] ?? null,
        ]);
    }

    public function result(Quiz $quiz)
    {
        $quiz->load(['chapter.course', 'questions.options']);
        $progress = app(ChapterProgressService::class)->getChapterProgress(Auth::user(), $quiz->chapter);

        $latestRetake = QuizRetake::with('answers')
            ->where('quiz_id', $quiz->id)
            ->where('user_id', Auth::id())
            ->orderByDesc('attempt_number')
            ->first();

        $latestResult = $latestRetake?->result
            ?? $quiz->results()->where('user_id', Auth::id())->latest()->first();

        $nextChapter = Chapter::where('course_id', $quiz->chapter->course_id)
            ->where('order', '>', $quiz->chapter->order)
            ->orderBy('order')
            ->first();

        $quizStatus = session('quiz_status');
        if (! $quizStatus && $progress) {
            if ($progress->hasPassedChapter()) {
                $quizStatus = $progress->passedWithHelp() ? 'passed_with_help' : 'passed';
            } elseif ($progress->isStuck()) {
                $quizStatus = $progress->isQuizBlocked() ? 'stuck' : 'stuck_unlocked';
            } elseif ($progress->status === StudentProgressStatus::IN_REMEDIATION->value) {
                $quizStatus = 'in_remediation';
            }
        }

        return view('student.quizzes.result', [
            'quiz' => $quiz,
            'result' => $latestResult,
            'retake' => $latestRetake,
            'progress' => $progress,
            'quizResult' => session('quiz_result'),
            'quizStatus' => $quizStatus ?? ($latestResult?->is_passed ? 'passed' : 'failed'),
            'nextChapter' => session('next_chapter_id')
                ? Chapter::find(session('next_chapter_id'))
                : $nextChapter,
            'blockedUntil' => session('blocked_until') ?? $progress?->quiz_blocked_until,
        ]);
    }

    private function denyQuizAccess(Quiz $quiz, array $access)
    {
        if ($access['reason'] === 'passed') {
            return redirect()->route('student.quiz.result', $quiz)->with([
                'info' => 'Vous avez déjà réussi ce quiz.',
                'quiz_status' => 'passed',
            ]);
        }

        return redirect()->route('student.chapter', $quiz->chapter_id)->with([
            'error' => 'Quiz temporairement bloqué. Réessayez dans environ '.$access['hours_remaining'].' heure(s).',
            'blocked_until' => $access['blocked_until'],
        ]);
    }
}

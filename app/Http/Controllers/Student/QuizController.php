<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizRetake;
use App\Services\LessonProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function show(Quiz $quiz)
    {
        $quiz->load(['lesson.course', 'questions.options']);
        $user = Auth::user();

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

    public function submit(Request $request, Quiz $quiz, LessonProgressService $progressService)
    {
        $quiz->load('lesson.course', 'questions.options');

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

        $attempt = $progressService->submitQuizAttempt(Auth::user(), $quiz, $payload);

        if ($attempt['passed']) {
            $nextLesson = Lesson::where('course_id', $quiz->lesson->course_id)
                ->where('order', '>', $quiz->lesson->order)
                ->orderBy('order')
                ->first();

            if ($nextLesson) {
                return redirect()->route('student.lesson', $nextLesson)->with([
                    'success' => 'Quizz réussi. La prochaine leçon est maintenant disponible.',
                    'quiz_result' => $attempt,
                ]);
            }

            return redirect()->route('student.course', $quiz->lesson->course_id)->with([
                'success' => 'Quizz réussi. Vous avez terminé ce cours.',
                'quiz_result' => $attempt,
            ]);
        }

        if (! empty($attempt['variant'])) {
            return redirect()->route('student.lesson.variant', $quiz->lesson)->with([
                'error' => 'Vous devez consulter la version simplifiée avant de retenter le quiz.',
                'quiz_result' => $attempt,
            ]);
        }

        return redirect()->route('student.quiz.result', $quiz)->with([
            'error' => 'Deuxième échec: votre enseignant a été notifié.',
            'quiz_result' => $attempt,
            'quiz_status' => 'stuck',
        ]);
    }

    public function result(Quiz $quiz)
    {
        $quiz->load(['lesson.course', 'questions.options']);
        $latestRetake = QuizRetake::with('answers')->where('quiz_id', $quiz->id)->where('user_id', Auth::id())->orderByDesc('attempt_number')->first();
        $latestResult = $latestRetake?->result ?? $quiz->results()->where('user_id', Auth::id())->latest()->first();
        $nextLesson = Lesson::where('course_id', $quiz->lesson->course_id)
            ->where('order', '>', $quiz->lesson->order)
            ->orderBy('order')
            ->first();

        return view('student.quizzes.result', [
            'quiz' => $quiz,
            'result' => $latestResult,
            'retake' => $latestRetake,
            'quizResult' => session('quiz_result'),
            'quizStatus' => session('quiz_status', $latestResult?->is_passed ? 'passed' : 'stuck'),
            'nextLesson' => $nextLesson,
        ]);
    }
}

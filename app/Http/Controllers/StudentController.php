<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Result;
use App\Models\Answer;
use App\Models\StudentProgress;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        if ($user->role !== 'student') abort(403);

        $group = $user->group;
        $courses = $group ? $group->courses : collect([]);

        return view('student.dashboard', compact('user', 'group', 'courses'));
    }

    public function course($id)
    {
        $course = Course::with(['lessons' => function($q) {
            $q->orderBy('order', 'asc');
        }])->findOrFail($id);

        $user = auth()->user();
        $progress = StudentProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->get()->keyBy('lesson_id');

        return view('student.course', compact('course', 'progress'));
    }

    public function lesson($id)
    {
        $lesson = Lesson::with(['quizzes' => function($q) {
            $q->where('is_active', true);
        }])->findOrFail($id);
        
        $course = $lesson->course;
        $user = auth()->user();

        // Ensure previous part was completed (if order > 1)
        if ($lesson->order > 1) {
            $previousLesson = Lesson::where('course_id', $course->id)
                                    ->where('order', '<', $lesson->order)
                                    ->orderBy('order', 'desc')
                                    ->first();
            
            if ($previousLesson) {
                $prevProgress = StudentProgress::where('user_id', $user->id)
                                               ->where('lesson_id', $previousLesson->id)
                                               ->first();
                
                if (!$prevProgress || !$prevProgress->is_completed) {
                    return redirect()->route('student.course', $course->id)
                                     ->with('error', 'Vous devez valider la partie précédente avant d\'accéder à celle-ci.');
                }
            }
        }

        return view('student.lesson', compact('lesson', 'course'));
    }

    public function quiz($id)
    {
        $user = auth()->user();
        $quiz = Quiz::with(['questions.options'])->findOrFail($id);
        
        $result = Result::where('user_id', $user->id)->where('quiz_id', $id)->first();
        $answers = collect([]);
        $wrongQuestionIds = collect([]);
        
        if ($result && !$result->is_passed) {
            // Fetch answers from previous attempt
            $answers = Answer::where('user_id', $user->id)->where('quiz_id', $id)->get();
            
            foreach($quiz->questions as $question) {
                $ans = $answers->where('question_id', $question->id)->first();
                $isCorrect = false;
                if ($ans) {
                    $opt = $question->options->where('id', $ans->option_id)->first();
                    if ($opt && $opt->is_correct) {
                        $isCorrect = true;
                    }
                }
                if (!$isCorrect) {
                    $wrongQuestionIds->push($question->id);
                }
            }
            
            // Only keep wrong questions for retake
            $quiz->setRelation('questions', $quiz->questions->whereIn('id', $wrongQuestionIds));
            
            // Re-pluck answers to just populate what was previously selected
            $answers = $answers->pluck('option_id', 'question_id');
        }

        return view('student.quiz', compact('quiz', 'result', 'answers'));
    }

    public function submitQuiz(Request $request, $id)
    {
        $user = auth()->user();
        $quiz = Quiz::with(['questions.options'])->findOrFail($id);
        
        $result = Result::where('user_id', $user->id)->where('quiz_id', $id)->first();
        
        $score = $result ? $result->score : 0; // Carry over correct scores from previous attempt if any
        $total = $quiz->questions->count(); // Original total

        $wrongQuestionIds = collect([]);
        if ($result && !$result->is_passed) {
            $answers = Answer::where('user_id', $user->id)->where('quiz_id', $id)->get();
            foreach($quiz->questions as $question) {
                $ans = $answers->where('question_id', $question->id)->first();
                $isCorrect = false;
                if ($ans) {
                    $opt = $question->options->where('id', $ans->option_id)->first();
                    if ($opt && $opt->is_correct) {
                        $isCorrect = true;
                    }
                }
                if (!$isCorrect) {
                    $wrongQuestionIds->push($question->id);
                }
            }
            // Clear only wrong answers from db
            Answer::where('user_id', $user->id)->where('quiz_id', $id)->whereIn('question_id', $wrongQuestionIds)->delete();
        } else {
            // First attempt, clear all answers just in case
            Answer::where('user_id', $user->id)->where('quiz_id', $id)->delete();
            $wrongQuestionIds = $quiz->questions->pluck('id');
            $score = 0; // Reset score on first attempt
        }

        foreach ($quiz->questions->whereIn('id', $wrongQuestionIds) as $question) {
            $selectedOptionId = $request->input('q_' . $question->id);
            
            if ($selectedOptionId) {
                Answer::create([
                    'user_id' => $user->id,
                    'quiz_id' => $quiz->id,
                    'question_id' => $question->id,
                    'option_id' => $selectedOptionId
                ]);

                $correctOption = $question->options->where('is_correct', true)->first();
                if ($correctOption && $correctOption->id == $selectedOptionId) {
                    $score++;
                }
            }
        }

        $passingScorePercentage = $quiz->passing_score;
        $currentPercentage = $total > 0 ? ($score / $total) * 100 : 0;
        $isPassed = $currentPercentage >= $passingScorePercentage;

        $result = Result::updateOrCreate(
            ['user_id' => $user->id, 'quiz_id' => $quiz->id],
            ['score' => $score, 'total_questions' => $total, 'is_passed' => $isPassed]
        );

        if ($isPassed) {
            StudentProgress::updateOrCreate(
                ['user_id' => $user->id, 'lesson_id' => $quiz->lesson_id],
                ['is_completed' => true, 'unlocked_at' => now()]
            );

            return redirect()->route('student.course', $quiz->lesson->course_id)->with([
                'success' => 'Quizz réussi ! Vous avez validé cette partie.',
                'quiz_result' => [
                    'score' => $score,
                    'total' => $total,
                    'percentage' => $currentPercentage
                ]
            ]);
        } else {
            return redirect()->route('student.lesson', $quiz->lesson_id)
                ->with('error', "Vous avez obtenu $currentPercentage%. Le seuil est de $passingScorePercentage%. Veuillez réviser et réessayer.")
                ->with('quiz_result', [
                    'score' => $score,
                    'total' => $total,
                    'percentage' => $currentPercentage
                ]);
        }
    }

    public function analytics()
    {
        $user = auth()->user();
        $results = Result::where('user_id', $user->id)->with('quiz.lesson.course')->get();
        
        $averageScore = $results->count() > 0 ? $results->avg(function($r) {
            return $r->total_questions > 0 ? ($r->score / $r->total_questions) * 100 : 0;
        }) : 0;

        return view('student.analytics', compact('results', 'averageScore'));
    }
}

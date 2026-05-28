<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Group;
use App\Models\Chapter;
use App\Models\Quiz;
use App\Models\Result;
use App\Models\StudentProgress;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function students(Request $request)
    {
        $query = User::where('role', 'student')->with('group.branch.school');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('massar_code', 'like', "%$search%");
            });
        }

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'last_login_at')) {
            $students = $query->orderBy('last_login_at', 'desc')->get();
        } else {
            $students = $query->get();
        }

        $groups = Group::all();

        return view('admin.analytics.students', compact('students', 'groups'));
    }

    public function studentProfile($id)
    {
        $student = User::with(['group.courses.chapters', 'results.quiz.chapter.course'])->findOrFail($id);
        
        $assignedCourses = $student->group ? $student->group->courses : collect([]);
        $assignedChapters = collect([]);
        foreach($assignedCourses as $course) {
            $assignedChapters = $assignedChapters->merge($course->chapters);
        }

        $progresses = StudentProgress::where('user_id', $student->id)->with('chapter.quiz')->get()->keyBy('chapter_id');
        $completedChapterIds = $progresses
            ->filter(fn ($p) => in_array($p->status, \App\Models\StudentProgress::completedStatuses(), true))
            ->keys()
            ->toArray();
        
        $chaptersNotRead = $assignedChapters->filter(fn($l) => !in_array($l->id, $completedChapterIds));
        
        // Quiz Stats
        $quizzesTakenIds = $student->results->pluck('quiz_id')->toArray();
        $allPossibleQuizzes = Quiz::whereIn('chapter_id', $assignedChapters->pluck('id'))->get();
        $quizzesNotTaken = $allPossibleQuizzes->filter(fn($q) => !in_array($q->id, $quizzesTakenIds));

        $stuckProgresses = $progresses->filter(function($p) {
            return $p->status === \App\Enums\StudentProgressStatus::STUCK->value
                || ($p->quiz_blocked_until && $p->quiz_blocked_until->isFuture());
        });

        return view('admin.analytics.student_profile', compact('student', 'assignedCourses', 'chaptersNotRead', 'quizzesNotTaken', 'progresses', 'stuckProgresses'));
    }

    public function resetQuizzes($id)
    {
        $student = User::where('role', 'student')->findOrFail($id);

        // Delete all results
        Result::where('user_id', $student->id)->delete();
        
        // Delete all student progress (chapter completion)
        StudentProgress::where('user_id', $student->id)->delete();

        // Optional: Delete answers if you have an Answer model linked to user
        if (class_exists('App\Models\Answer')) {
            \App\Models\Answer::where('user_id', $student->id)->delete();
        }

        return redirect()->back()->with('success', "Tout le parcours de l'étudiant a été réinitialisé.");
    }

    public function deleteResult($id)
    {
        $result = Result::findOrFail($id);
        $userId = $result->user_id;
        $quizId = $result->quiz_id;

        // Delete associated answers if they exist
        if (class_exists('App\Models\Answer')) {
            \App\Models\Answer::where('user_id', $userId)->where('quiz_id', $quizId)->delete();
        }

        // Delete the result itself
        $result->delete();

        // Optional: Remove progress for the chapter linked to this quiz
        $quiz = Quiz::find($quizId);
        if ($quiz) {
            StudentProgress::where('user_id', $userId)->where('chapter_id', $quiz->chapter_id)->delete();
        }

        return redirect()->back()->with('success', "Le résultat de l'examen a été supprimé.");
    }
}

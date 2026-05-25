<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\StudentProgress;
use App\Models\User;

class StudentProgressController extends Controller
{
    public function index(Group $group)
    {
        $group->load('courses.chapters.quizzes');

        $students = User::where('role', 'student')
            ->where('group_id', $group->id)
            ->with(['results.quiz.chapter', 'results.retake', 'group'])
            ->orderBy('first_name')
            ->get();

        $chapters = $group->courses
            ->flatMap(fn ($course) => $course->chapters)
            ->sortBy('order')
            ->values();

        $progresses = StudentProgress::whereIn('user_id', $students->pluck('id'))
            ->whereIn('chapter_id', $chapters->pluck('id'))
            ->get()
            ->groupBy('user_id');

        return view('admin.progress.index', [
            'group' => $group,
            'students' => $students,
            'chapters' => $chapters,
            'progresses' => $progresses,
        ]);
    }

    public function show(User $student)
    {
        $student->load(['group.courses.chapters.quizzes', 'results.quiz.chapter', 'results.retake', 'group.branch.school']);

        $chapters = $student->group
            ? $student->group->courses->flatMap(fn ($course) => $course->chapters)->sortBy('order')->values()
            : collect();

        $progresses = StudentProgress::where('user_id', $student->id)->get()->keyBy('chapter_id');

        $attemptsByChapter = $student->results
            ->sortBy(fn ($result) => [$result->quiz?->chapter_id ?? 0, $result->attempt_number])
            ->groupBy(fn ($result) => $result->quiz?->chapter_id);

        return view('admin.progress.show', [
            'student' => $student,
            'chapters' => $chapters,
            'progresses' => $progresses,
            'attemptsByChapter' => $attemptsByChapter,
        ]);
    }
}

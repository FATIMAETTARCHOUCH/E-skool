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
        $group->load('courses.lessons.quizzes');

        $students = User::where('role', 'student')
            ->where('group_id', $group->id)
            ->with(['results.quiz.lesson', 'results.retake', 'group'])
            ->orderBy('first_name')
            ->get();

        $lessons = $group->courses
            ->flatMap(fn ($course) => $course->lessons)
            ->sortBy('order')
            ->values();

        $progresses = StudentProgress::whereIn('user_id', $students->pluck('id'))
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->get()
            ->groupBy('user_id');

        return view('admin.progress.index', [
            'group' => $group,
            'students' => $students,
            'lessons' => $lessons,
            'progresses' => $progresses,
        ]);
    }

    public function show(User $student)
    {
        $student->load(['group.courses.lessons.quizzes', 'results.quiz.lesson', 'results.retake', 'group.branch.school']);

        $lessons = $student->group
            ? $student->group->courses->flatMap(fn ($course) => $course->lessons)->sortBy('order')->values()
            : collect();

        $progresses = StudentProgress::where('user_id', $student->id)->get()->keyBy('lesson_id');

        $attemptsByLesson = $student->results
            ->sortBy(fn ($result) => [$result->quiz?->lesson_id ?? 0, $result->attempt_number])
            ->groupBy(fn ($result) => $result->quiz?->lesson_id);

        return view('admin.progress.show', [
            'student' => $student,
            'lessons' => $lessons,
            'progresses' => $progresses,
            'attemptsByLesson' => $attemptsByLesson,
        ]);
    }
}

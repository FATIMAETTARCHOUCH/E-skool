<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = \App\Models\Quiz::with('lesson.course.groups.branch.school')->orderBy('created_at', 'desc')->get();
        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        $lessons = \App\Models\Lesson::with('course.groups')->get();
        return view('admin.quizzes.create', compact('lessons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'lesson_id' => 'required|exists:lessons,id',
            'passing_score' => 'required|integer|min:0|max:100',
            'scheduled_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $quiz = \App\Models\Quiz::create([
            'title' => $request->title,
            'lesson_id' => $request->lesson_id,
            'passing_score' => $request->passing_score,
            'scheduled_at' => $request->scheduled_at,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.quizzes.questions.index', $quiz->id)->with('success', 'Quiz created. Now add some questions!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'lesson_id' => 'required|exists:lessons,id',
            'passing_score' => 'required|integer|min:0|max:100',
            'scheduled_at' => 'nullable|date',
        ]);
        $quiz = \App\Models\Quiz::findOrFail($id);
        
        $data = $request->only(['title', 'lesson_id', 'scheduled_at', 'passing_score']);
        $data['is_active'] = $request->has('is_active');
        
        $quiz->update($data);
        return redirect()->back()->with('success', 'Quiz settings updated.');
    }

    public function destroy($id)
    {
        \App\Models\Quiz::findOrFail($id)->delete();
        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz deleted.');
    }
}

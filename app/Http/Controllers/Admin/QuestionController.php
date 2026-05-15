<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index($quizId)
    {
        $quiz = Quiz::with('questions.options')->findOrFail($quizId);
        return view('admin.quizzes.questions', compact('quiz'));
    }

    public function store(Request $request, $quizId)
    {
        $request->validate([
            'content_text' => 'required|string',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string',
            'correct_option' => 'required|integer',
        ]);

        $question = Question::create([
            'quiz_id' => $quizId,
            'content_text' => $request->content_text,
        ]);

        foreach ($request->options as $index => $optionText) {
            Option::create([
                'question_id' => $question->id,
                'content_text' => $optionText,
                'is_correct' => ($index == $request->correct_option),
            ]);
        }

        return redirect()->back()->with('success', 'Question added to quiz.');
    }

    public function destroy($id)
    {
        Question::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Question removed.');
    }
}

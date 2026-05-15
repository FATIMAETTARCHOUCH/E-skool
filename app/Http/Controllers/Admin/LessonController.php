<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function index()
    {
        // This index isn't used much anymore since we manage lessons from the Course show page
        // But let's keep it functional.
        $lessons = Lesson::with('course')->orderBy('created_at', 'desc')->get();
        return view('admin.lessons.index', compact('lessons'));
    }

    public function create(Request $request)
    {
        $courses = Course::all();
        $course_id = $request->query('course_id');
        return view('admin.lessons.create', compact('courses', 'course_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'order' => 'required|integer|min:1',
            'content_text' => 'nullable|string',
            'pdf_file' => 'nullable|file|mimes:pdf|max:20480',
            'video_file' => 'nullable|file|mimes:mp4,mov,avi,wmv|max:102400',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'tag' => 'nullable|string',
        ]);

        $data = $request->only(['course_id', 'title', 'order', 'content_text', 'tag']);

        if ($request->hasFile('pdf_file')) {
            $data['pdf_path'] = $request->file('pdf_file')->store('lessons/pdfs', 'public');
        }
        if ($request->hasFile('video_file')) {
            $data['video_path'] = $request->file('video_file')->store('lessons/videos', 'public');
        }
        if ($request->hasFile('image_file')) {
            $data['image_path'] = $request->file('image_file')->store('lessons/images', 'public');
        }

        Lesson::create($data);

        return redirect()->route('admin.courses.show', $request->course_id)->with('success', 'Partie créée avec succès.');
    }

    public function edit($id)
    {
        $lesson = Lesson::findOrFail($id);
        $courses = Course::all();
        return view('admin.lessons.edit', compact('lesson', 'courses'));
    }

    public function update(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'order' => 'required|integer|min:1',
            'content_text' => 'nullable|string',
            'pdf_file' => 'nullable|file|mimes:pdf|max:20480',
            'video_file' => 'nullable|file|mimes:mp4,mov,avi,wmv|max:102400',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'tag' => 'nullable|string',
        ]);

        $data = $request->only(['course_id', 'title', 'order', 'content_text', 'tag']);

        if ($request->hasFile('pdf_file')) {
            if ($lesson->pdf_path) \Illuminate\Support\Facades\Storage::disk('public')->delete($lesson->pdf_path);
            $data['pdf_path'] = $request->file('pdf_file')->store('lessons/pdfs', 'public');
        }
        if ($request->hasFile('video_file')) {
            if ($lesson->video_path) \Illuminate\Support\Facades\Storage::disk('public')->delete($lesson->video_path);
            $data['video_path'] = $request->file('video_file')->store('lessons/videos', 'public');
        }
        if ($request->hasFile('image_file')) {
            if ($lesson->image_path) \Illuminate\Support\Facades\Storage::disk('public')->delete($lesson->image_path);
            $data['image_path'] = $request->file('image_file')->store('lessons/images', 'public');
        }

        $lesson->update($data);

        return redirect()->route('admin.courses.show', $request->course_id)->with('success', 'Partie mise à jour avec succès.');
    }

    public function destroy($id)
    {
        $lesson = Lesson::findOrFail($id);
        $course_id = $lesson->course_id;
        
        if ($lesson->pdf_path) \Illuminate\Support\Facades\Storage::disk('public')->delete($lesson->pdf_path);
        if ($lesson->video_path) \Illuminate\Support\Facades\Storage::disk('public')->delete($lesson->video_path);
        if ($lesson->image_path) \Illuminate\Support\Facades\Storage::disk('public')->delete($lesson->image_path);
        
        $lesson->delete();
        
        return redirect()->route('admin.courses.show', $course_id)->with('success', 'Partie supprimée.');
    }
}

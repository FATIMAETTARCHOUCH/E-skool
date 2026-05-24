<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonContent;
use App\Models\LessonVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'content_text' => 'nullable|string',
            'order' => 'required|integer|min:1',
            'parent_lesson_id' => 'nullable|exists:lessons,id',
            'pdf_file' => 'nullable|file|mimes:pdf|max:20480',
            'video_file' => 'nullable|file|mimes:mp4,mov,avi,wmv|max:102400',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'tag' => 'nullable|string',
            'pdf_files.*' => 'nullable|file|mimes:pdf|max:20480',
            'pdf_titles.*' => 'nullable|string|max:255',
        ]);

        $lesson = Lesson::create($request->only(['course_id', 'title', 'order', 'tag']));

        $this->syncLessonContents($lesson, $request);

        if ($request->filled('parent_lesson_id')) {
            LessonVariant::updateOrCreate(
                [
                    'original_lesson_id' => (int) $request->input('parent_lesson_id'),
                    'trigger' => 'quiz_failed',
                ],
                [
                    'variant_lesson_id' => $lesson->id,
                ]
            );
        }

        return redirect()->route('admin.courses.show', $request->course_id)->with('success', 'Partie créée avec succès.');
    }

    public function edit($id)
    {
        $lesson = Lesson::with('contents', 'asVariantOf')->findOrFail($id);
        $courses = Course::all();
        $textContent = $lesson->contents->firstWhere('type', 'text')?->value;
        $parentLessonId = $lesson->asVariantOf->firstWhere('trigger', 'quiz_failed')?->original_lesson_id;

        return view('admin.lessons.edit', compact('lesson', 'courses', 'textContent', 'parentLessonId'));
    }

    public function update(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'content_text' => 'nullable|string',
            'order' => 'required|integer|min:1',
            'parent_lesson_id' => 'nullable|exists:lessons,id',
            'pdf_file' => 'nullable|file|mimes:pdf|max:20480',
            'video_file' => 'nullable|file|mimes:mp4,mov,avi,wmv|max:102400',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'tag' => 'nullable|string',
            'pdf_files.*' => 'nullable|file|mimes:pdf|max:20480',
            'pdf_titles.*' => 'nullable|string|max:255',
        ]);

        $lesson->update($request->only(['course_id', 'title', 'order', 'tag']));

        $this->deleteStoredLessonFiles($lesson);
        $lesson->contents()->delete();
        $this->syncLessonContents($lesson, $request);

        LessonVariant::where('variant_lesson_id', $lesson->id)
            ->where('trigger', 'quiz_failed')
            ->delete();

        if ($request->filled('parent_lesson_id')) {
            LessonVariant::updateOrCreate(
                [
                    'original_lesson_id' => (int) $request->input('parent_lesson_id'),
                    'trigger' => 'quiz_failed',
                ],
                [
                    'variant_lesson_id' => $lesson->id,
                ]
            );
        }

        return redirect()->route('admin.courses.show', $request->course_id)->with('success', 'Partie mise à jour avec succès.');
    }

    public function destroy($id)
    {
        $lesson = Lesson::findOrFail($id);
        $course_id = $lesson->course_id;

        $this->deleteStoredLessonFiles($lesson);
        
        $lesson->delete();
        
        return redirect()->route('admin.courses.show', $course_id)->with('success', 'Partie supprimée.');
    }

    /**
     * Delete a specific PDF from a lesson
     */
    public function deletePdf($pdfId)
    {
        return redirect()->back()->with('error', 'La suppression de PDF individuel est désactivée avec le nouveau modèle de contenu.');
    }

    /**
     * Reorder PDFs within a lesson
     */
    public function reorderPdfs(Request $request, $lessonId)
    {
        return response()->json(['success' => false, 'message' => 'PDF reorder is no longer supported with lesson_contents.'], 410);
    }

    /**
     * Get all lessons from a course (for parent lesson selection in variants)
     * Used by AJAX to populate parent lesson dropdown
     */
    public function getLessonsForParent($courseId)
    {
        $lessons = Lesson::where('course_id', $courseId)
            ->orderBy('order')
            ->get(['id', 'order', 'title']);
        
        return response()->json($lessons);
    }

    private function syncLessonContents(Lesson $lesson, Request $request): void
    {
        $contentOrder = 0;

        if ($request->filled('content_text')) {
            LessonContent::create([
                'lesson_id' => $lesson->id,
                'type' => 'text',
                'value' => $request->input('content_text'),
                'order' => ++$contentOrder,
            ]);
        }

        if ($request->hasFile('pdf_file')) {
            LessonContent::create([
                'lesson_id' => $lesson->id,
                'type' => 'pdf',
                'value' => $request->file('pdf_file')->store('lessons/pdfs', 'public'),
                'order' => ++$contentOrder,
            ]);
        }

        if ($request->hasFile('video_file')) {
            LessonContent::create([
                'lesson_id' => $lesson->id,
                'type' => 'video',
                'value' => $request->file('video_file')->store('lessons/videos', 'public'),
                'order' => ++$contentOrder,
            ]);
        }

        if ($request->hasFile('image_file')) {
            LessonContent::create([
                'lesson_id' => $lesson->id,
                'type' => 'image',
                'value' => $request->file('image_file')->store('lessons/images', 'public'),
                'order' => ++$contentOrder,
            ]);
        }

        if ($request->hasFile('pdf_files')) {
            foreach ($request->file('pdf_files') as $pdf) {
                if (! $pdf) {
                    continue;
                }
                LessonContent::create([
                    'lesson_id' => $lesson->id,
                    'type' => 'pdf',
                    'value' => $pdf->store('lessons/pdfs', 'public'),
                    'order' => ++$contentOrder,
                ]);
            }
        }
    }

    private function deleteStoredLessonFiles(Lesson $lesson): void
    {
        foreach ($lesson->contents as $content) {
            if (in_array($content->type, ['pdf', 'video', 'image'], true) && $content->value) {
                Storage::disk('public')->delete($content->value);
            }
        }
    }
}

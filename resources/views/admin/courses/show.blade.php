@extends('layouts.admin')

@section('header', $course->title)

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h3 class="text-xl font-bold text-gray-900">lessons (Lesson Parts)</h3>
        <a href="{{ route('admin.lessons.create', ['course_id' => $course->id]) }}" class="px-3 py-2.5 rounded-lg bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 transition-colors">Add Lesson (lessons)</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Lessons List (Left Sidebar) -->
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-lg border border-gray-200">
                <h4 class="font-bold text-gray-900 mb-4">lessons.order & lessons.title</h4>
                <div class="space-y-2">
                    @forelse($course->lessons as $lesson)
                    <button onclick="selectLesson({{ $lesson->id }})" class="lesson-tab w-full text-left p-3 rounded-lg hover:bg-gray-50 border-2 border-transparent transition-all" data-lesson-id="{{ $lesson->id }}">
                        <div class="text-sm font-bold text-gray-900">[{{ $lesson->order }}] {{ $lesson->title }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ $lesson->contents->count() }} content blocks</div>
                    </button>
                    @empty
                    <div class="py-8 text-center text-gray-400 font-bold italic">No lessons added</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Lesson Details & PDF Viewer (Main Content) -->
        <div class="lg:col-span-3 space-y-6">
            @forelse($course->lessons as $lesson)
            <div class="lesson-content bg-white rounded-lg border border-gray-200 hidden" data-lesson-id="{{ $lesson->id }}">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="text-lg font-bold text-gray-900 mb-2">lessons.id={{ $lesson->id }}, lessons.order={{ $lesson->order }}, lessons.title={{ $lesson->title }}</h4>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.lessons.edit', $lesson) }}" class="px-3 py-2 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 text-xs font-bold transition">Edit (lessons)</a>
                            <form action="{{ route('admin.lessons.destroy', $lesson) }}" method="POST" onsubmit="return confirm('Delete this lesson?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-3 py-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 text-xs font-bold transition">Delete (lessons)</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-gray-50 space-y-4">
                    @forelse($lesson->contents as $content)
                        @if($content->type === 'text')
                            <div class="p-4 rounded-lg bg-white border border-gray-200">
                                <p class="text-xs text-gray-500 uppercase tracking-widest mb-2">Text</p>
                                <div class="prose prose-sm max-w-none text-gray-700">{!! nl2br(e($content->value)) !!}</div>
                            </div>
                        @elseif($content->type === 'pdf')
                            <div class="p-4 rounded-lg bg-white border border-gray-200">
                                <p class="text-xs text-gray-500 uppercase tracking-widest mb-2">PDF</p>
                                <iframe src="{{ asset('storage/' . $content->value) }}" class="w-full h-[480px] rounded border border-gray-200"></iframe>
                            </div>
                        @elseif($content->type === 'video')
                            <div class="p-4 rounded-lg bg-white border border-gray-200">
                                <p class="text-xs text-gray-500 uppercase tracking-widest mb-2">Video</p>
                                <video width="100%" controls class="rounded border border-gray-200">
                                    <source src="{{ asset('storage/' . $content->value) }}" type="video/mp4">
                                </video>
                            </div>
                        @elseif($content->type === 'image')
                            <div class="p-4 rounded-lg bg-white border border-gray-200 text-center">
                                <p class="text-xs text-gray-500 uppercase tracking-widest mb-2 text-left">Image</p>
                                <img src="{{ asset('storage/' . $content->value) }}" alt="{{ $lesson->title }}" class="max-w-full max-h-[480px] mx-auto rounded border border-gray-200">
                            </div>
                        @endif
                    @empty
                        <div class="p-6 text-center text-gray-500 italic">
                            No lesson content uploaded.
                        </div>
                    @endforelse
                </div>

                <!-- Quizzes Section (quizzes.lesson_id) -->
                <div class="p-6 bg-indigo-50 border-t-2 border-indigo-200 flex items-center justify-between">
                    <div>
                        <h5 class="font-bold text-indigo-900 mb-1">quizzes (Linked to lessons.id)</h5>
                        <p class="text-xs text-indigo-700">Manage quizzes for this lesson via quizzes.lesson_id</p>
                    </div>
                    <a href="{{ route('admin.quizzes.index', ['lesson_id' => $lesson->id]) }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition">Manage Quizzes →</a>
                </div>
            </div>
            @empty
            <div class="col-span-full py-12 text-center text-gray-400 font-bold italic">No lessons added to this course.</div>
            @endforelse
        </div>
    </div>
</div>

<script>
    function selectLesson(lessonId) {
        // Hide all lesson contents
        document.querySelectorAll('.lesson-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.lesson-tab').forEach(el => el.classList.remove('border-indigo-600', 'border-2'));
        
        // Show selected lesson content
        document.querySelector(`[data-lesson-id="${lessonId}"].lesson-content`).classList.remove('hidden');
        document.querySelector(`[data-lesson-id="${lessonId}"].lesson-tab`).classList.add('border-indigo-600', 'border-2');
    }

    // Select first lesson on page load
    window.addEventListener('DOMContentLoaded', () => {
        const firstLesson = document.querySelector('.lesson-tab');
        if (firstLesson) {
            firstLesson.click();
        }
    });
</script>
@endsection

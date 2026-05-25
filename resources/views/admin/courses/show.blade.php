@extends('layouts.admin')

@section('header', $course->title)

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h3 class="text-xl font-bold text-gray-900">chapters (Chapter Parts)</h3>
        <a href="{{ route('admin.chapters.create', ['course_id' => $course->id]) }}" class="px-3 py-2.5 rounded-lg bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 transition-colors">Add Chapter (chapters)</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Chapters List (Left Sidebar) -->
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-lg border border-gray-200">
                <h4 class="font-bold text-gray-900 mb-4">chapters.order & chapters.title</h4>
                <div class="space-y-2">
                    @forelse($course->chapters as $chapter)
                    <button onclick="selectChapter({{ $chapter->id }})" class="chapter-tab w-full text-left p-3 rounded-lg hover:bg-gray-50 border-2 border-transparent transition-all" data-chapter-id="{{ $chapter->id }}">
                        <div class="text-sm font-bold text-gray-900">[{{ $chapter->order }}] {{ $chapter->title }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ $chapter->resources->count() }} resource blocks</div>
                    </button>
                    @empty
                    <div class="py-8 text-center text-gray-400 font-bold italic">No chapters added</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Chapter Details & PDF Viewer (Main Content) -->
        <div class="lg:col-span-3 space-y-6">
            @forelse($course->chapters as $chapter)
            <div class="chapter-content bg-white rounded-lg border border-gray-200 hidden" data-chapter-id="{{ $chapter->id }}">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="text-lg font-bold text-gray-900 mb-2">chapters.id={{ $chapter->id }}, chapters.order={{ $chapter->order }}, chapters.title={{ $chapter->title }}</h4>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.chapters.edit', $chapter) }}" class="px-3 py-2 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 text-xs font-bold transition">Edit (chapters)</a>
                            <form action="{{ route('admin.chapters.destroy', $chapter) }}" method="POST" onsubmit="return confirm('Delete this chapter?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-3 py-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 text-xs font-bold transition">Delete (chapters)</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-gray-50 space-y-4">
                    @forelse($chapter->resources as $resource)
                        @if($resource->type === 'text')
                            <div class="p-4 rounded-lg bg-white border border-gray-200">
                                <p class="text-xs text-gray-500 uppercase tracking-widest mb-2">Text</p>
                                <div class="prose prose-sm max-w-none text-gray-700">{!! nl2br(e($resource->value)) !!}</div>
                            </div>
                        @elseif($resource->type === 'pdf')
                            <div class="p-4 rounded-lg bg-white border border-gray-200">
                                <p class="text-xs text-gray-500 uppercase tracking-widest mb-2">PDF</p>
                                <iframe src="{{ asset('storage/' . $resource->value) }}" class="w-full h-[480px] rounded border border-gray-200"></iframe>
                            </div>
                        @elseif($resource->type === 'video')
                            <div class="p-4 rounded-lg bg-white border border-gray-200">
                                <p class="text-xs text-gray-500 uppercase tracking-widest mb-2">Video</p>
                                <video width="100%" controls class="rounded border border-gray-200">
                                    <source src="{{ asset('storage/' . $resource->value) }}" type="video/mp4">
                                </video>
                            </div>
                        @elseif($resource->type === 'image')
                            <div class="p-4 rounded-lg bg-white border border-gray-200 text-center">
                                <p class="text-xs text-gray-500 uppercase tracking-widest mb-2 text-left">Image</p>
                                <img src="{{ asset('storage/' . $resource->value) }}" alt="{{ $chapter->title }}" class="max-w-full max-h-[480px] mx-auto rounded border border-gray-200">
                            </div>
                        @endif
                    @empty
                        <div class="p-6 text-center text-gray-500 italic">
                            No chapter content uploaded.
                        </div>
                    @endforelse
                </div>

                <!-- Quizzes Section (quizzes.chapter_id) -->
                <div class="p-6 bg-indigo-50 border-t-2 border-indigo-200 flex items-center justify-between">
                    <div>
                        <h5 class="font-bold text-indigo-900 mb-1">quizzes (Linked to chapters.id)</h5>
                        <p class="text-xs text-indigo-700">Manage quizzes for this chapter via quizzes.chapter_id</p>
                    </div>
                    <a href="{{ route('admin.quizzes.index', ['chapter_id' => $chapter->id]) }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition">Manage Quizzes →</a>
                </div>
            </div>
            @empty
            <div class="col-span-full py-12 text-center text-gray-400 font-bold italic">No chapters added to this course.</div>
            @endforelse
        </div>
    </div>
</div>

<script>
    function selectChapter(chapterId) {
        // Hide all chapter contents
        document.querySelectorAll('.chapter-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.chapter-tab').forEach(el => el.classList.remove('border-indigo-600', 'border-2'));
        
        // Show selected chapter content
        document.querySelector(`[data-chapter-id="${chapterId}"].chapter-content`).classList.remove('hidden');
        document.querySelector(`[data-chapter-id="${chapterId}"].chapter-tab`).classList.add('border-indigo-600', 'border-2');
    }

    // Select first chapter on page load
    window.addEventListener('DOMContentLoaded', () => {
        const firstChapter = document.querySelector('.chapter-tab');
        if (firstChapter) {
            firstChapter.click();
        }
    });
</script>
@endsection

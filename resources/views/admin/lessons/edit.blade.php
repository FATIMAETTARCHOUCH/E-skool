@extends('layouts.admin')

@section('header', 'Modifier la Partie')

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white rounded shadow mt-8">
    <form action="{{ route('admin.lessons.update', $lesson->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <div>
                <label class="block font-medium text-sm text-gray-700">courses.id</label>
                <select name="course_id" id="course_id" class="mt-1 block w-full border rounded px-3 py-2" required>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ $lesson->course_id == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-medium text-sm text-gray-700">lessons.title</label>
                <input type="text" name="title" class="mt-1 block w-full border rounded px-3 py-2" value="{{ $lesson->title }}" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-sm text-gray-700">lessons.order</label>
                    <input type="number" name="order" class="mt-1 block w-full border rounded px-3 py-2" value="{{ $lesson->order }}" min="1" required>
                </div>
                <div></div>
            </div>

            <div>
                <label class="block font-medium text-sm text-gray-700">lessons.tag (optional)</label>
                <input type="text" name="tag" class="mt-1 block w-full border rounded px-3 py-2" value="{{ $lesson->tag }}">
            </div>

            <div>
                <label class="block font-medium text-sm text-gray-700">lessons.parent_lesson_id (variant)</label>
                <select name="parent_lesson_id" id="parent_lesson_id" class="mt-1 block w-full border rounded px-3 py-2">
                    <option value="">-- None --</option>
                </select>
            </div>

            <div>
                <label class="block font-medium text-sm text-gray-700">lessons.pdf_path</label>
                <input type="file" name="pdf_file" class="mt-1 block w-full">
                @if($lesson->pdf_path)
                    <p class="text-xs text-gray-500 mt-1">Current: {{ $lesson->pdf_path }}</p>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-sm text-gray-700">lessons.video_path</label>
                    <input type="file" name="video_file" class="mt-1 block w-full">
                </div>
                <div>
                    <label class="block font-medium text-sm text-gray-700">lessons.image_path</label>
                    <input type="file" name="image_file" class="mt-1 block w-full">
                </div>
            </div>

            <div>
                <label class="block font-medium text-sm text-gray-700">lessons.content_text</label>
                <textarea name="content_text" rows="6" class="mt-1 block w-full border rounded px-3 py-2">{{ $textContent }}</textarea>
            </div>

            <div class="flex justify-between">
                <a href="{{ route('admin.courses.show', $lesson->course_id) }}" class="inline-block px-4 py-2 bg-gray-200 rounded">Cancel</a>
                <button type="submit" class="inline-block px-4 py-2 bg-blue-600 text-white rounded">Update (lessons)</button>
            </div>
        </div>
    </form>
</div>

<script>
// Minimal JS: populate parent lessons when a course is selected
document.addEventListener('DOMContentLoaded', function() {
    const courseSelect = document.getElementById('course_id');
    const parentSelect = document.getElementById('parent_lesson_id');
    const currentParent = {{ $parentLessonId ?? 'null' }};

    function loadParents(courseId) {
        parentSelect.innerHTML = '<option value="">-- Aucune --</option>';
        if (!courseId) return;
        fetch(`/admin/courses/${courseId}/lessons-for-parent`)
            .then(r => r.json())
            .then(list => {
                list.forEach(l => {
                    const opt = document.createElement('option');
                    opt.value = l.id;
                    opt.textContent = `[${l.order}] ${l.title}`;
                    parentSelect.appendChild(opt);
                });
                if (currentParent) parentSelect.value = currentParent;
            })
            .catch(() => {});
    }

    courseSelect.addEventListener('change', () => loadParents(courseSelect.value));
    loadParents(courseSelect.value);
});
</script>

@endsection

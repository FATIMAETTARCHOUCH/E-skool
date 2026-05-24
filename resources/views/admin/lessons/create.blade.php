@extends('layouts.admin')

@section('header', 'Ajouter une Partie au Cours')

@section('content')
<div class="max-w-4xl mx-auto p-10 rounded-[2.5rem] shadow-neumorphic bg-neu-base mb-12">
    <form action="{{ route('admin.lessons.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-6">
                <div>
                    <x-input-label value="courses.id - Course" />
                    <select name="course_id" id="course_id" class="block mt-1 w-full rounded-xl border-slate-200" required>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ $course_id == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label value="lessons.title - Lesson Title" />
                    <x-text-input class="block mt-1 w-full" type="text" name="title" required />
                </div>

                <div>
                    <x-input-label value="lessons.order - Order Number" />
                    <x-text-input class="block mt-1 w-full" type="number" name="order" value="1" min="1" required />
                </div>

                <div>
                    <x-input-label value="lessons.tag - Remediation Tag" />
                    <x-text-input class="block mt-1 w-full" type="text" name="tag" />
                </div>
                <div>
                    <x-input-label value="lessons.type - Type" />
                    <select name="type" class="block mt-1 w-full rounded-xl border-slate-200">
                        <option value="normal" {{ old('type', 'normal') == 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="simplest" {{ old('type') == 'simplest' ? 'selected' : '' }}>Simplest (Remediation)</option>
                    </select>
                </div>
                
                <div>
                    <x-input-label value="lessons.parent_lesson_id - Parent Lesson (Variant)" />
                    <select name="parent_lesson_id" id="parent_lesson_id" class="block mt-1 w-full rounded-xl border-slate-200">
                        <option value="">-- None --</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-2">Select parent if this is a remediation variant</p>
                </div>
            </div>
        </div>

        <div class="pt-6 border-t border-gray-200">
            <h4 class="text-lg font-bold text-gray-700 mb-6">Fichiers de la Partie (Optionnels)</h4>
            <div class="space-y-4" id="pdf-uploads">
                <div class="pdf-upload-group p-6 rounded-2xl shadow-neumorphic-inset space-y-3 border-2 border-dashed border-brand-200">
                    <div>
                        <x-input-label value="Titre de la Partie PDF (ex: Part 1: Introduction)" class="mb-2" />
                        <x-text-input type="text" name="pdf_titles[]" placeholder="Part 1" class="w-full" />
                    </div>
                    <div>
                        <x-input-label value="Fichier PDF" class="mb-2" />
                        <input type="file" name="pdf_files[]" accept=".pdf" class="text-xs text-gray-500 w-full">
                    </div>
                    <button type="button" onclick="removePdfGroup(this)" class="text-sm text-red-500 hover:text-red-700 font-bold">- Supprimer cette partie</button>
                </div>
            </div>
            <button type="button" onclick="addPdfUpload()" class="mt-4 px-6 py-3 rounded-xl bg-brand-50 text-brand-600 hover:bg-brand-100 font-bold transition">
                + Ajouter une partie PDF
            </button>
        </div>

        <div class="pt-6 border-t border-gray-200">
            <h4 class="text-lg font-bold text-gray-700 mb-6">lessons.pdf_path, video_path, image_path (Optional)</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 rounded-2xl shadow-neumorphic-inset">
                    <x-input-label value="lessons.pdf_path" class="mb-3" />
                    <input type="file" name="pdf_file" class="text-xs text-gray-500">
                </div>
                <div class="p-6 rounded-2xl shadow-neumorphic-inset">
                    <x-input-label value="lessons.video_path" class="mb-3" />
                    <input type="file" name="video_file" class="text-xs text-gray-500">
                </div>
                <div class="p-6 rounded-2xl shadow-neumorphic-inset">
                    <x-input-label value="lessons.image_path" class="mb-3" />
                    <input type="file" name="image_file" class="text-xs text-gray-500">
                </div>
            </div>
        </div>

        <div>
            <x-input-label value="lessons.content_text - Content" />
            <textarea name="content_text" rows="6" class="block mt-1 w-full bg-gray-50 border border-gray-200 rounded-lg py-3 px-4"></textarea>
        </div>

        <div class="flex gap-4 pt-6">
            <a href="{{ $course_id ? route('admin.courses.show', $course_id) : route('admin.courses.index') }}" class="flex-1 py-4 text-center rounded-2xl shadow-neumorphic-btn font-bold text-gray-500 transition hover:shadow-neumorphic-btn-active">Cancel</a>
            <button type="submit" class="flex-1 py-4 rounded-2xl bg-brand-600 text-white font-bold shadow-glow hover:bg-brand-500">Create Lesson (lessons)</button>
        </div>
    </form>
</div>

<script>
    function addPdfUpload() {
        const container = document.getElementById('pdf-uploads');
        const count = container.children.length + 1;
        const newGroup = document.createElement('div');
        newGroup.className = 'pdf-upload-group p-6 rounded-2xl shadow-neumorphic-inset space-y-3 border-2 border-dashed border-brand-200';
        newGroup.innerHTML = `
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Titre de la Partie (ex: Part ${count}: ...)</label>
                <input type="text" name="pdf_titles[]" placeholder="Part ${count}" class="block w-full border-slate-200 rounded-xl py-2 px-3">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Fichier PDF</label>
                <input type="file" name="pdf_files[]" accept=".pdf" class="text-xs text-gray-500 w-full" required>
            </div>
            <button type="button" onclick="removePdfGroup(this)" class="text-sm text-red-500 hover:text-red-700 font-bold">- Supprimer cette partie</button>
        `;
        container.appendChild(newGroup);
    }

    function removePdfGroup(button) {
        const container = document.getElementById('pdf-uploads');
        if (container.children.length > 1) {
            button.closest('.pdf-upload-group').remove();
        } else {
            alert('Vous devez avoir au moins une partie PDF');
        }
    }

    // Load parent lessons based on selected course
    document.getElementById('course_id').addEventListener('change', function() {
        const courseId = this.value;
        const parentSelect = document.getElementById('parent_lesson_id');
        parentSelect.innerHTML = '<option value="">-- Aucune (Normal) --</option>';
        
        if (!courseId) return;
        
        // Fetch lessons from this course via AJAX
        fetch(`/admin/courses/${courseId}/lessons-for-parent`)
            .then(response => response.json())
            .then(lessons => {
                lessons.forEach(lesson => {
                    const option = document.createElement('option');
                    option.value = lesson.id;
                    option.textContent = `[${lesson.order}] ${lesson.title}`;
                    parentSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error loading lessons:', error));
    });

    // Trigger on page load if course is pre-selected
    document.addEventListener('DOMContentLoaded', function() {
        const courseSelect = document.getElementById('course_id');
        if (courseSelect.value) {
            courseSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection

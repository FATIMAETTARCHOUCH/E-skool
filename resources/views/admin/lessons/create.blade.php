@extends('layouts.admin')

@section('header', 'Ajouter une Partie au Cours')

@section('content')
<div class="max-w-4xl mx-auto p-10 rounded-[2.5rem] shadow-neumorphic bg-neu-base mb-12">
    <form action="{{ route('admin.lessons.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-6">
                <div>
                    <x-input-label value="Cours" />
                    <select name="course_id" class="block mt-1 w-full rounded-xl border-slate-200" required>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ $course_id == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label value="Titre de la Partie (Leçon)" />
                    <x-text-input class="block mt-1 w-full" type="text" name="title" required />
                </div>

                <div>
                    <x-input-label value="Ordre (Numéro de la partie)" />
                    <x-text-input class="block mt-1 w-full" type="number" name="order" value="1" min="1" required />
                </div>

                <div>
                    <x-input-label value="Tag de Remédiation" />
                    <x-text-input class="block mt-1 w-full" type="text" name="tag" />
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <x-input-label value="Contenu de la Partie (Rich Text)" />
                    <textarea name="content_text" id="content_text" rows="8" class="block w-full mt-1 bg-white border border-slate-200 rounded-xl py-3 px-4 text-gray-700"></textarea>
                </div>
            </div>
        </div>

        <div class="pt-6 border-t border-gray-200">
            <h4 class="text-lg font-bold text-gray-700 mb-6">Fichiers joints (Optionnels)</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 rounded-2xl shadow-neumorphic-inset">
                    <x-input-label value="Document PDF" class="mb-3" />
                    <input type="file" name="pdf_file" class="text-xs text-gray-500">
                </div>
                <div class="p-6 rounded-2xl shadow-neumorphic-inset">
                    <x-input-label value="Vidéo" class="mb-3" />
                    <input type="file" name="video_file" class="text-xs text-gray-500">
                </div>
                <div class="p-6 rounded-2xl shadow-neumorphic-inset">
                    <x-input-label value="Image" class="mb-3" />
                    <input type="file" name="image_file" class="text-xs text-gray-500">
                </div>
            </div>
        </div>

        <div class="flex gap-4 pt-6">
            <a href="{{ $course_id ? route('admin.courses.show', $course_id) : route('admin.courses.index') }}" class="flex-1 py-4 text-center rounded-2xl shadow-neumorphic-btn font-bold text-gray-500 transition hover:shadow-neumorphic-btn-active">Annuler</a>
            <button type="submit" class="flex-1 py-4 rounded-2xl bg-brand-600 text-white font-bold shadow-glow hover:bg-brand-500">Sauvegarder la Partie</button>
        </div>
    </form>
</div>

<!-- TinyMCE 5 Pro Setup (Free & No API Key) -->
<script src="https://cdn.jsdelivr.net/npm/tinymce@5.10.7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#content_text',
        height: 500,
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table paste code help wordcount'
        ],
        toolbar: 'undo redo | formatselect | bold italic backcolor | \
                  alignleft aligncenter alignright alignjustify | \
                  bullist numlist outdent indent | removeformat | link image media | code preview fullscreen',
        content_style: 'body { font-family:Inter,Helvetica,Arial,sans-serif; font-size:16px }',
        branding: false,
        menubar: 'edit insert view format table help',
        image_title: true,
        automatic_uploads: true,
        file_picker_types: 'image media',
        media_live_embeds: true,
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
        }
    });
</script>
@endsection

@extends('layouts.admin')

@section('header', $course->title)

@section('content')
<div class="space-y-8">
    <div class="flex justify-between items-center">
        <h3 class="text-2xl font-black text-slate-800">Parties du Cours (Leçons)</h3>
        <a href="{{ route('admin.lessons.create', ['course_id' => $course->id]) }}" class="px-6 py-3 rounded-2xl bg-brand-600 text-white font-bold shadow-glow hover:bg-brand-500 transition-colors">Ajouter une Partie</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($course->lessons as $lesson)
        <div class="glass p-8 rounded-3xl border border-white/60 shadow-glass">
            <h4 class="text-xl font-bold text-slate-800 mb-2">Partie {{ $lesson->order }}: {{ $lesson->title }}</h4>
            <p class="text-sm text-slate-500 mb-6 truncate">{{ $lesson->content_text }}</p>
            
            <div class="flex justify-between items-center">
                <a href="{{ route('admin.lessons.edit', $lesson) }}" class="text-indigo-600 font-bold text-sm hover:underline">Modifier la partie</a>
                
                <form action="{{ route('admin.lessons.destroy', $lesson) }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="text-red-500 font-bold text-sm hover:underline">Supprimer</button>
                </form>
            </div>
            
            <div class="mt-4 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.quizzes.index', ['lesson_id' => $lesson->id]) }}" class="text-brand-600 font-bold text-sm hover:underline">Gérer les Quizz liés</a>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center text-slate-400 font-bold italic">Aucune partie n'a été ajoutée à ce cours.</div>
        @endforelse
    </div>
</div>
@endsection

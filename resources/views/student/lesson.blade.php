@extends('layouts.app')

@section('header')
    <div class="flex items-center gap-6">
        <a href="{{ route('student.course', $course->id) }}" class="w-14 h-14 rounded-2xl glass flex items-center justify-center text-slate-500 hover:text-brand-600 transition-all border border-white/40 shadow-sm">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <p class="text-[10px] font-black text-brand-600 uppercase tracking-widest">{{ $course->title }}</p>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Partie {{ $lesson->order }} : {{ $lesson->title }}</h2>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto px-4 pb-24">
    
    @if(session('error'))
    <div class="mb-8 p-6 rounded-3xl bg-red-50 border border-red-100 text-red-600 font-bold flex items-center gap-4">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        {{ session('error') }}
    </div>
    @endif

    <div class="glass p-10 md:p-14 rounded-[3rem] border border-white/60 shadow-glass relative overflow-hidden">
        
        <!-- Content -->
        <div class="prose prose-slate prose-lg max-w-none prose-headings:font-black prose-a:text-brand-600">
            {!! $lesson->content_text !!}
        </div>

        <!-- Attachments -->
        @if($lesson->pdf_path || $lesson->video_path)
        <div class="mt-12 pt-8 border-t border-slate-200/50">
            <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Fichiers joints</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @if($lesson->pdf_path)
                <a href="{{ Storage::url($lesson->pdf_path) }}" target="_blank" class="flex items-center gap-4 p-4 rounded-2xl bg-white/50 hover:bg-white border border-white/60 transition-colors group">
                    <div class="w-12 h-12 rounded-xl bg-red-50 text-red-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>
                    </div>
                    <div>
                        <p class="font-bold text-slate-700">Document PDF</p>
                        <p class="text-[10px] text-slate-400 uppercase font-black">Ouvrir</p>
                    </div>
                </a>
                @endif
                
                @if($lesson->video_path)
                <a href="{{ Storage::url($lesson->video_path) }}" target="_blank" class="flex items-center gap-4 p-4 rounded-2xl bg-white/50 hover:bg-white border border-white/60 transition-colors group">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"></path></svg>
                    </div>
                    <div>
                        <p class="font-bold text-slate-700">Vidéo</p>
                        <p class="text-[10px] text-slate-400 uppercase font-black">Regarder</p>
                    </div>
                </a>
                @endif
            </div>
        </div>
        @endif

        <!-- Action Area -->
        <div class="mt-12 pt-8 border-t border-slate-200/50 flex justify-center">
            @if($lesson->quizzes->count() > 0)
                @php
                    $quiz = $lesson->quizzes->first();
                @endphp
                <a href="{{ route('student.quiz', $quiz->id) }}" class="px-10 py-5 rounded-2xl bg-brand-600 text-white font-black uppercase tracking-widest shadow-glow hover:bg-brand-500 transition-all hover:-translate-y-1">
                    Passer le Quizz pour valider
                </a>
            @else
                <div class="px-8 py-4 rounded-2xl bg-slate-100 text-slate-400 font-bold uppercase text-xs italic">
                    Aucun quizz requis pour cette partie
                </div>
            @endif
        </div>

    </div>
</div>
@endsection

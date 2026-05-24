@extends('layouts.app')

@section('header')
    <div class="max-w-[95%] mx-auto flex items-center gap-6">
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
<div class="w-full max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8 pb-24">
    <div class="glass p-6 md:p-8 rounded-[3rem] border border-white/60 shadow-glass relative overflow-hidden">
        <div class="space-y-8">
            @foreach($lesson->contents as $content)
                @switch($content->type)
                    @case('text')
                        <div class="prose prose-slate max-w-none">
                            {!! nl2br(e($content->value)) !!}
                        </div>
                        @break
                    @case('pdf')
                        <div class="rounded-2xl overflow-hidden border border-slate-200 bg-slate-50">
                            <iframe src="{{ \Illuminate\Support\Facades\Storage::url($content->value) }}" class="w-full h-[75vh]"></iframe>
                        </div>
                        @break
                    @case('video')
                        <video controls class="w-full rounded-3xl border border-slate-200 bg-black">
                            <source src="{{ \Illuminate\Support\Facades\Storage::url($content->value) }}">
                        </video>
                        @break
                    @case('image')
                        <div class="rounded-3xl overflow-hidden border border-slate-200 bg-slate-50 text-center">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($content->value) }}" alt="{{ $lesson->title }}" class="mx-auto max-w-full h-auto">
                        </div>
                        @break
                @endswitch
            @endforeach
        </div>

        <div class="mt-12 pt-8 border-t border-slate-200/50 flex justify-center">
            @if($quiz)
                <a href="{{ route('student.quiz', $quiz->id) }}" class="px-10 py-5 rounded-2xl bg-brand-600 text-white font-black uppercase tracking-widest shadow-glow hover:bg-brand-500 transition-all hover:-translate-y-1">
                    Start quiz
                </a>
            @else
                <div class="px-8 py-4 rounded-2xl bg-slate-100 text-slate-400 font-bold uppercase text-xs italic">
                    No quiz available for this lesson
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

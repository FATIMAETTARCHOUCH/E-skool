@extends('layouts.app')

@section('header')
    <div class="max-w-[95%] mx-auto flex items-center gap-6">
        <a href="{{ route('student.course', $course->id) }}" class="w-14 h-14 rounded-2xl glass flex items-center justify-center text-slate-500 hover:text-brand-600 transition-all border border-white/40 shadow-sm">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <p class="text-[10px] font-black text-brand-600 uppercase tracking-widest">{{ $course->title }}</p>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Partie {{ $chapter->order }} : {{ $chapter->title }}</h2>
        </div>
    </div>
@endsection

@section('content')
<div class="w-full max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8 pb-24">
    @if(session('error'))
        <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-sm font-bold">
            {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold">
            {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div class="mb-6 p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 text-sm font-bold">
            {{ session('info') }}
        </div>
    @endif

    @if($hasPassed)
        <div class="mb-6 p-5 rounded-2xl bg-emerald-50 border border-emerald-200">
            <p class="text-sm font-black text-emerald-900 uppercase tracking-widest">Quiz réussi</p>
            <p class="text-emerald-800 mt-1 text-sm">
                @if($progress?->passedWithHelp())
                    Vous avez validé cette partie après remédiation.
                @else
                    Vous avez validé cette partie du premier coup.
                @endif
                @if($progress?->completed_at)
                    — {{ $progress->completed_at->format('d/m/Y H:i') }}
                @endif
            </p>
            @if($quiz)
                <a href="{{ route('student.quiz.result', $quiz->id) }}" class="inline-block mt-3 text-xs font-bold text-emerald-700 underline uppercase tracking-widest">Voir le résultat</a>
            @endif
        </div>
    @elseif($isQuizBlocked)
        <div class="mb-6 p-5 rounded-2xl bg-red-50 border border-red-200">
            <p class="text-sm font-black text-red-900 uppercase tracking-widest">Quiz bloqué</p>
            <p class="text-red-800 mt-1 text-sm">
                Après deux échecs, vous devez attendre {{ \App\Models\StudentProgress::QUIZ_BLOCK_HOURS }} heures avant de retenter le quiz.
                @if($blockedUntil)
                    Disponible à partir du {{ $blockedUntil->format('d/m/Y H:i') }}.
                @endif
            </p>
        </div>
    @elseif($needsRemediation)
        <div class="mb-6 p-5 rounded-2xl bg-amber-50 border border-amber-200">
            <p class="text-sm font-black text-amber-900 uppercase tracking-widest">Remédiation</p>
            @if(isset($quizResult['score']))
                <p class="text-amber-800 mt-1 text-sm">Score : {{ $quizResult['score'] }}% — consultez le contenu ci-dessous puis réessayez le quiz.</p>
            @else
                <p class="text-amber-800 mt-1 text-sm">Consultez le contenu de remédiation ci-dessous puis réessayez le quiz.</p>
            @endif
            @if($usingRemedialFallback ?? false)
                <p class="text-amber-700/80 mt-2 text-xs">Aucune ressource de remédiation dédiée — le contenu principal est affiché pour révision.</p>
            @endif
        </div>
    @endif

    <div class="glass p-6 md:p-8 rounded-[3rem] border border-white/60 shadow-glass relative overflow-hidden">
        <div class="space-y-8">
            @forelse($resources as $content)
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
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($content->value) }}" alt="{{ $chapter->title }}" class="mx-auto max-w-full h-auto">
                        </div>
                        @break
                @endswitch
            @empty
                <div class="py-12 text-center text-slate-400 font-bold uppercase text-sm tracking-widest">
                    Aucun contenu disponible pour cette partie.
                </div>
            @endforelse
        </div>

        <div class="mt-12 pt-8 border-t border-slate-200/50 flex justify-center">
            @if($hasPassed)
                <div class="px-10 py-5 rounded-2xl bg-emerald-100 text-emerald-800 font-black uppercase tracking-widest text-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    Partie validée
                </div>
            @elseif($isQuizBlocked)
                <div class="px-10 py-5 rounded-2xl bg-red-100 text-red-700 font-black uppercase tracking-widest text-sm text-center">
                    Quiz indisponible ({{ \App\Models\StudentProgress::QUIZ_BLOCK_HOURS }}h)
                </div>
            @elseif($quiz && $canTakeQuiz)
                <a href="{{ route('student.quiz', $quiz->id) }}" class="px-10 py-5 rounded-2xl bg-brand-600 text-white font-black uppercase tracking-widest shadow-glow hover:bg-brand-500 transition-all hover:-translate-y-1">
                    {{ $needsRemediation ? 'Réessayer le quiz' : 'Commencer le quiz' }}
                </a>
            @elseif($quiz)
                <div class="px-8 py-4 rounded-2xl bg-slate-100 text-slate-400 font-bold uppercase text-xs italic">
                    Quiz non disponible
                </div>
            @else
                <div class="px-8 py-4 rounded-2xl bg-slate-100 text-slate-400 font-bold uppercase text-xs italic">
                    Aucun quiz pour cette partie
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('header')
    <div class="max-w-[95%] mx-auto flex items-center gap-6">
        <a href="{{ route('student.course', $quiz->chapter->course_id) }}" class="w-12 h-12 rounded-2xl glass flex items-center justify-center text-slate-500 hover:text-brand-600 transition-all border border-white/40 shadow-sm">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <p class="text-[10px] font-black text-brand-600 uppercase tracking-widest">Résultat du quiz</p>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">{{ $quiz->title }}</h2>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-24 space-y-6">
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-sm font-bold">{{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 text-sm font-bold">{{ session('info') }}</div>
    @endif

    @php
        $score = data_get($quizResult, 'score', $result?->score ?? 0);
        $status = $quizStatus;
    @endphp

    <div class="glass p-10 rounded-[3rem] border border-white/60 shadow-glass">
        <div class="flex items-start justify-between gap-6">
            <div>
                <p class="text-xs font-black uppercase tracking-widest text-slate-400">Statut</p>
                <h3 class="text-3xl font-black text-slate-900 mt-2">
                    @if(in_array($status, ['passed', 'passed_with_help'], true))
                        <span class="text-emerald-600">Quiz réussi</span>
                    @elseif($status === 'stuck')
                        <span class="text-red-600">Bloqué {{ \App\Models\StudentProgress::QUIZ_BLOCK_HOURS }}h</span>
                    @elseif($status === 'in_remediation')
                        <span class="text-amber-600">Remédiation</span>
                    @else
                        Résultat
                    @endif
                </h3>
                <p class="mt-3 text-slate-600">Score : <strong>{{ $score }}%</strong></p>
                @if($status === 'passed_with_help')
                    <p class="mt-2 text-sm text-slate-500">Validé après une tentative de remédiation.</p>
                @endif
            </div>
            <div class="w-16 h-16 rounded-2xl {{ in_array($status, ['passed', 'passed_with_help'], true) ? 'bg-emerald-600/10 text-emerald-600 border-emerald-200' : 'bg-brand-600/10 text-brand-600 border-brand-200' }} flex items-center justify-center font-black text-2xl border">
                {{ $score }}
            </div>
        </div>

        <div class="mt-8 flex flex-wrap gap-3">
            @if(in_array($status, ['passed', 'passed_with_help'], true))
                <div class="px-4 py-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold w-full">
                    Cette partie est validée. Vous ne pouvez plus repasser ce quiz.
                </div>
                @if($nextChapter)
                    <a href="{{ route('student.chapter', $nextChapter->id) }}" class="px-6 py-3 rounded-2xl bg-brand-600 text-white font-black uppercase tracking-widest shadow-glow">Partie suivante</a>
                @endif
                <a href="{{ route('student.course', $quiz->chapter->course_id) }}" class="px-6 py-3 rounded-2xl bg-slate-100 text-slate-700 font-black uppercase tracking-widest">Retour au cours</a>
            @endif

            @if($status === 'stuck')
                <div class="px-4 py-3 rounded-2xl bg-red-50 border border-red-200 text-red-700 font-bold w-full">
                    Votre enseignant a été notifié.
                    @if($blockedUntil)
                        Vous pourrez retenter le quiz à partir du {{ $blockedUntil->format('d/m/Y à H:i') }}.
                    @else
                        Attendez {{ \App\Models\StudentProgress::QUIZ_BLOCK_HOURS }} heures avant de retenter.
                    @endif
                </div>
                <a href="{{ route('student.chapter', $quiz->chapter->id) }}" class="px-6 py-3 rounded-2xl bg-slate-100 text-slate-700 font-black uppercase tracking-widest">Retour à la partie</a>
            @endif

            @if($status === 'in_remediation')
                <a href="{{ route('student.chapter', $quiz->chapter->id) }}" class="px-6 py-3 rounded-2xl bg-amber-600 text-white font-black uppercase tracking-widest shadow-glow">Voir la remédiation</a>
            @endif
        </div>
    </div>

    @if($retake)
        <div class="glass p-8 rounded-[2.5rem] border border-white/60 shadow-glass">
            <p class="text-xs uppercase tracking-widest text-slate-400 font-black">Tentative #{{ $retake->attempt_number }}</p>
            <p class="mt-2 text-slate-700">Terminée le {{ optional($retake->completed_at)->format('d/m/Y H:i') ?? '—' }}</p>
        </div>
    @endif
</div>
@endsection

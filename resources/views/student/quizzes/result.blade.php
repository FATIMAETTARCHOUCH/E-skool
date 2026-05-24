@extends('layouts.app')

@section('header')
    <div class="max-w-[95%] mx-auto flex items-center gap-6">
        <a href="{{ route('student.lesson', $quiz->lesson_id) }}" class="w-12 h-12 rounded-2xl glass flex items-center justify-center text-slate-500 hover:text-brand-600 transition-all border border-white/40 shadow-sm">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <p class="text-[10px] font-black text-brand-600 uppercase tracking-widest">Quiz result</p>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">{{ $quiz->title }}</h2>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-24 space-y-6">
    @php
        $score = data_get($quizResult, 'score', $result?->score ?? 0);
        $status = $quizStatus;
        $percentage = $result && $result->total_questions ? round(($result->score / $result->total_questions) * 100, 2) : data_get($quizResult, 'score', 0);
    @endphp

    <div class="glass p-10 rounded-[3rem] border border-white/60 shadow-glass">
        <div class="flex items-start justify-between gap-6">
            <div>
                <p class="text-xs font-black uppercase tracking-widest text-slate-400">Status</p>
                <h3 class="text-3xl font-black text-slate-900 mt-2">
                    @if($status === 'passed') Passed
                    @elseif($status === 'passed_with_help') Passed with help
                    @elseif($status === 'stuck') Stuck
                    @elseif($status === 'in_remediation') In remediation
                    @else Result
                    @endif
                </h3>
                <p class="mt-3 text-slate-600">Score: <strong>{{ $score }}</strong></p>
            </div>
            <div class="w-16 h-16 rounded-2xl bg-brand-600/10 text-brand-600 flex items-center justify-center font-black text-2xl border border-brand-200">
                {{ is_numeric($percentage) ? round($percentage) : $percentage }}
            </div>
        </div>

        <div class="mt-8 flex flex-wrap gap-3">
            @if($status === 'stuck')
                <div class="px-4 py-3 rounded-2xl bg-red-50 border border-red-200 text-red-700 font-bold">Your teacher has been notified.</div>
            @endif

            @if(in_array($status, ['passed', 'passed_with_help'], true) && $nextLesson)
                <a href="{{ route('student.lesson', $nextLesson->id) }}" class="px-6 py-3 rounded-2xl bg-brand-600 text-white font-black uppercase tracking-widest shadow-glow">Next lesson</a>
            @endif

            @if(in_array($status, ['in_remediation', 'stuck'], true) && $quiz->lesson)
                <a href="{{ route('student.lesson.variant', $quiz->lesson->id) }}" class="px-6 py-3 rounded-2xl bg-amber-600 text-white font-black uppercase tracking-widest shadow-glow">View simplified lesson</a>
            @endif
        </div>
    </div>

    @if($retake)
        <div class="glass p-8 rounded-[2.5rem] border border-white/60 shadow-glass">
            <p class="text-xs uppercase tracking-widest text-slate-400 font-black">Attempt #{{ $retake->attempt_number }}</p>
            <p class="mt-2 text-slate-700">Completed at {{ optional($retake->completed_at)->format('Y-m-d H:i') ?? '—' }}</p>
        </div>
    @endif
</div>
@endsection

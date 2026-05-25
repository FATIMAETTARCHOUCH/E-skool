@extends('layouts.app')

@section('header')
    <div class="flex items-center gap-6">
        <a href="{{ route('student.dashboard') }}" class="w-14 h-14 rounded-2xl glass flex items-center justify-center text-slate-500 hover:text-brand-600 transition-all border border-white/40 shadow-sm">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h2 class="text-4xl font-black text-slate-900 tracking-tight">{{ $course->title }}</h2>
            <p class="text-sm font-bold text-slate-500 mt-1 uppercase tracking-widest">{{ $course->description }}</p>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto px-4 pb-20">
    <div class="space-y-8">
        @php
            $isLocked = false;
            $completedStatuses = \App\Models\StudentProgress::completedStatuses();
        @endphp

        @forelse($course->chapters as $index => $chapter)
            @php
                $chapterProgress = $progress[$chapter->id] ?? null;
                $isCompleted = $chapterProgress && in_array($chapterProgress->status, $completedStatuses, true);
                $isStuck = $chapterProgress && $chapterProgress->status === \App\Enums\StudentProgressStatus::STUCK->value;
                $isBlocked = $chapterProgress && $chapterProgress->isQuizBlocked();
                $inRemediation = $chapterProgress && $chapterProgress->status === \App\Enums\StudentProgressStatus::IN_REMEDIATION->value;

                if ($index > 0) {
                    $prevChapter = $course->chapters[$index - 1];
                    $prevProgress = $progress[$prevChapter->id] ?? null;
                    $prevCompleted = $prevProgress && in_array($prevProgress->status, $completedStatuses, true);
                    if (! $prevCompleted) {
                        $isLocked = true;
                    }
                }
            @endphp

            <div class="glass p-8 rounded-[3rem] border {{ $isLocked ? 'border-slate-200/50 opacity-60' : 'border-white/60 shadow-glass' }} relative overflow-hidden transition-all duration-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-brand-600 uppercase tracking-widest mb-2">Partie {{ $chapter->order }}</p>
                        <h4 class="text-2xl font-black text-slate-800">{{ $chapter->title }}</h4>

                        @if($isCompleted)
                            <div class="mt-4 flex items-center gap-2 text-emerald-600 text-xs font-bold uppercase">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                Quiz réussi
                            </div>
                        @elseif($isBlocked)
                            <div class="mt-4 flex items-center gap-2 text-red-600 text-xs font-bold uppercase">
                                Bloqué ({{ \App\Models\StudentProgress::QUIZ_BLOCK_HOURS }}h)
                            </div>
                        @elseif($isStuck)
                            <div class="mt-4 flex items-center gap-2 text-red-500 text-xs font-bold uppercase">
                                En attente — enseignant notifié
                            </div>
                        @elseif($inRemediation)
                            <div class="mt-4 flex items-center gap-2 text-amber-600 text-xs font-bold uppercase">
                                Remédiation en cours
                            </div>
                        @elseif($isLocked)
                            <div class="mt-4 flex items-center gap-2 text-slate-400 text-xs font-bold uppercase">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                Verrouillé
                            </div>
                        @else
                            <div class="mt-4 flex items-center gap-2 text-amber-500 text-xs font-bold uppercase">
                                Disponible
                            </div>
                        @endif
                    </div>

                    @if(!$isLocked)
                        <a href="{{ route('student.chapter', $chapter->id) }}" class="px-8 py-4 rounded-2xl {{ $isCompleted ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-brand-600 text-white shadow-glow hover:bg-brand-500' }} font-black text-xs uppercase tracking-widest transition-all">
                            {{ $isCompleted ? 'Réviser' : ($inRemediation || $isBlocked ? 'Continuer' : 'Commencer') }}
                        </a>
                    @else
                        <button disabled class="px-8 py-4 rounded-2xl bg-slate-100 text-slate-400 font-black text-xs uppercase tracking-widest cursor-not-allowed">
                            Bloqué
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="py-20 text-center glass rounded-[3rem] border border-white/60">
                <p class="text-slate-400 font-bold uppercase tracking-widest text-sm">Ce cours ne contient aucune partie pour le moment.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection

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
        @endphp

        @forelse($course->lessons as $index => $lesson)
            @php
                $isCompleted = isset($progress[$lesson->id]) && $progress[$lesson->id]->is_completed;
                
                // Si ce n'est pas la première partie et que la précédente n'est pas complétée, on la bloque.
                if ($index > 0) {
                    $prevLesson = $course->lessons[$index - 1];
                    $prevCompleted = isset($progress[$prevLesson->id]) && $progress[$prevLesson->id]->is_completed;
                    if (!$prevCompleted) {
                        $isLocked = true;
                    }
                }
            @endphp

            <div class="glass p-8 rounded-[3rem] border {{ $isLocked ? 'border-slate-200/50 opacity-60' : 'border-white/60 shadow-glass' }} relative overflow-hidden transition-all duration-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-brand-600 uppercase tracking-widest mb-2">Partie {{ $lesson->order }}</p>
                        <h4 class="text-2xl font-black text-slate-800">{{ $lesson->title }}</h4>
                        
                        @if($isCompleted)
                            <div class="mt-4 flex items-center gap-2 text-emerald-600 text-xs font-bold uppercase">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                Complété
                            </div>
                        @elseif($isLocked)
                            <div class="mt-4 flex items-center gap-2 text-slate-400 text-xs font-bold uppercase">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                Verrouillé (Terminez la partie précédente)
                            </div>
                        @else
                            <div class="mt-4 flex items-center gap-2 text-amber-500 text-xs font-bold uppercase">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Disponible
                            </div>
                        @endif
                    </div>

                    @if(!$isLocked)
                        <a href="{{ route('student.lesson', $lesson->id) }}" class="px-8 py-4 rounded-2xl {{ $isCompleted ? 'bg-slate-100 text-slate-500 hover:bg-slate-200' : 'bg-brand-600 text-white shadow-glow hover:bg-brand-500' }} font-black text-xs uppercase tracking-widest transition-all">
                            {{ $isCompleted ? 'Réviser' : 'Commencer' }}
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

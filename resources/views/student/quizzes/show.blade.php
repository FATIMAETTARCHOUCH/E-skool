@extends('layouts.app')

@section('header')
    <div class="flex items-center justify-between gap-6 max-w-[95%] mx-auto">
        <div class="flex items-center gap-6">
            <a href="{{ route('student.lesson', $quiz->lesson_id) }}" class="w-12 h-12 rounded-2xl glass flex items-center justify-center text-slate-500 hover:text-brand-600 transition-all border border-white/40 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12"></path></svg>
            </a>
            <div>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">{{ $quiz->title }}</h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="px-2 py-0.5 rounded-md bg-brand-600 text-white text-[10px] font-black uppercase tracking-tighter shadow-glow italic">Quiz</span>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">{{ $quiz->questions->count() }} questions</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-32">
    @if($retake)
        <div class="mb-12 glass p-8 rounded-[2.5rem] border border-amber-200 bg-amber-50/30">
            <h4 class="text-xl font-black text-slate-900 uppercase tracking-tight italic">Retake in progress</h4>
            <p class="text-xs text-amber-600 font-bold uppercase tracking-widest mt-1">Attempt #{{ $retake->attempt_number }}</p>
        </div>
    @endif

    <form action="{{ route('student.quiz.submit', $quiz->id) }}" method="POST" class="space-y-12">
        @csrf

        @foreach($quiz->questions as $index => $question)
            @php($selectedOptionId = $answers[$question->id] ?? null)
            <div class="glass p-12 rounded-[3.5rem] border border-white/60 shadow-glass relative overflow-hidden group question-block">
                <div class="flex items-start gap-8 mb-12 relative">
                    <div class="w-16 h-16 rounded-[1.5rem] bg-brand-50 text-brand-600 flex items-center justify-center font-black text-2xl shadow-inner border border-brand-100 flex-shrink-0 status-indicator">
                        {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                    </div>
                    <div class="pt-2">
                        <h3 class="text-3xl font-black text-slate-900 leading-tight tracking-tight">{{ $question->content_text }}</h3>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative">
                    @foreach($question->options as $option)
                        <label class="block cursor-pointer group/opt">
                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" {{ $selectedOptionId == $option->id ? 'checked' : '' }} class="peer hidden quiz-option" required>
                            <div class="p-8 rounded-[2rem] bg-white/40 border-2 border-slate-100 peer-checked:border-brand-600 peer-checked:bg-white peer-checked:shadow-glow transition-all duration-300 group-hover/opt:border-brand-200">
                                <div class="flex items-center gap-5">
                                    <div class="w-4 h-4 rounded-full bg-brand-600 opacity-0 transition-opacity duration-300 peer-checked:opacity-100"></div>
                                    <span class="text-xl font-bold text-slate-600 transition-colors peer-checked:text-slate-900">{{ $option->content_text }}</span>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="pt-10 flex flex-col items-center gap-4">
            <button type="submit" class="group relative px-16 py-8 rounded-[2.5rem] bg-slate-900 text-white overflow-hidden shadow-2xl transition-all hover:scale-[1.02] active:scale-95">
                <div class="absolute inset-0 bg-brand-600 translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                <span class="relative z-10 text-2xl font-black uppercase tracking-[0.3em]">Submit quiz</span>
            </button>
        </div>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('header')
    <div class="flex items-center justify-between gap-6">
        <div class="flex items-center gap-6">
            <a href="{{ route('student.chapter', $quiz->chapter_id) }}" class="w-12 h-12 rounded-2xl glass flex items-center justify-center text-slate-500 hover:text-brand-600 transition-all border border-white/40 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12"></path></svg>
            </a>
            <div>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">{{ $quiz->title }}</h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="px-2 py-0.5 rounded-md bg-brand-600 text-white text-[10px] font-black uppercase tracking-tighter shadow-glow italic">Examination Active</span>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">
                        <span id="answered-count">0</span> / {{ count($quiz->questions) }} Questions Answered
                    </p>
                </div>
            </div>
        </div>
        <div id="timer" class="px-8 py-4 rounded-2xl bg-slate-900 text-white font-mono text-2xl font-black shadow-xl ring-4 ring-brand-500/20">
            00:00
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-32">
    @php
        $isReviewMode = $result && $result->is_passed;
        $isRetakeMode = $result && !$result->is_passed;
    @endphp

    @if($isReviewMode)
    <div class="mb-12 glass p-8 rounded-[2.5rem] border border-brand-200 bg-brand-50/30 flex items-center justify-between">
        <div>
            <h4 class="text-xl font-black text-slate-900 uppercase tracking-tight italic">Review Mode Active</h4>
            <p class="text-xs text-brand-600 font-bold uppercase tracking-widest mt-1">Final Score: {{ $result->score }}/{{ $result->total_questions }}</p>
        </div>
        <div class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center text-brand-600 shadow-sm border border-brand-100">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
    </div>
    @elseif($isRetakeMode)
    <div class="mb-12 glass p-8 rounded-[2.5rem] border border-amber-200 bg-amber-50/30 flex items-center justify-between">
        <div>
            <h4 class="text-xl font-black text-slate-900 uppercase tracking-tight italic">Retake Mode Active</h4>
            <p class="text-xs text-amber-600 font-bold uppercase tracking-widest mt-1">Only incorrect questions are shown. Previous score: {{ $result->score }}/{{ $result->total_questions }}</p>
        </div>
        <div class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center text-amber-600 shadow-sm border border-amber-100">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
        </div>
    </div>
    @endif

    <form action="{{ route('student.quiz.submit', $quiz->id) }}" method="POST" id="quiz-form" class="space-y-12" onsubmit="return validateForm()">
        @csrf
        
        @foreach($quiz->questions as $index => $question)
        @php 
            $studentAnswerId = $answers[$question->id] ?? null;
        @endphp
        <div class="glass p-12 rounded-[3.5rem] border border-white/60 shadow-glass relative overflow-hidden group question-block">
            <div class="absolute top-0 right-0 w-64 h-64 bg-brand-600/5 rounded-full -mr-32 -mt-32 blur-3xl transition-all group-hover:bg-brand-600/10"></div>
            
            <div class="flex items-start gap-8 mb-12 relative">
                <div class="w-16 h-16 rounded-[1.5rem] bg-brand-50 text-brand-600 flex items-center justify-center font-black text-2xl shadow-inner border border-brand-100 flex-shrink-0 status-indicator">
                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                </div>
                <div class="pt-2">
                    <h3 class="text-3xl font-black text-slate-900 leading-tight tracking-tight">
                        {{ $question->content_text }}
                    </h3>
                    @if(!$isReviewMode)
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-4 italic status-text">Awaiting Selection</p>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative">
                @foreach($question->options as $option)
                @php
                    $isCorrect = $option->is_correct;
                    $isSelected = $studentAnswerId == $option->id;
                    
                    $bgColorClass = 'bg-white/40';
                    $borderColorClass = 'border-slate-100';
                    $textColorClass = 'text-slate-600';
                    
                    if ($isReviewMode) {
                        if ($isCorrect) {
                            $bgColorClass = 'bg-emerald-50';
                            $borderColorClass = 'border-emerald-500 ring-4 ring-emerald-500/10';
                            $textColorClass = 'text-emerald-700';
                        } elseif ($isSelected && !$isCorrect) {
                            $bgColorClass = 'bg-red-50';
                            $borderColorClass = 'border-red-500 ring-4 ring-red-500/10';
                            $textColorClass = 'text-red-700';
                        }
                    }
                @endphp
                <label class="block {{ $isReviewMode ? 'cursor-default' : 'cursor-pointer' }} group/opt">
                    <input type="radio" name="q_{{ $question->id }}" value="{{ $option->id }}" 
                        {{ $isSelected ? 'checked' : '' }}
                        {{ $isReviewMode ? 'disabled' : '' }}
                        class="peer hidden quiz-option" required onchange="updateProgress()">
                    <div class="p-8 rounded-[2rem] {{ $bgColorClass }} border-2 {{ $borderColorClass }} peer-checked:border-brand-600 peer-checked:bg-white peer-checked:shadow-glow transition-all duration-300 {{ !$isReviewMode ? 'group-hover/opt:border-brand-200' : '' }}">
                        <div class="flex items-center gap-5">
                            <div class="w-8 h-8 rounded-full border-2 {{ $isReviewMode && ($isCorrect || ($isSelected && !$isCorrect)) ? 'border-transparent' : 'border-slate-200' }} flex items-center justify-center transition-all bg-white shadow-inner">
                                @if($isReviewMode)
                                    @if($isCorrect)
                                        <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                    @elseif($isSelected && !$isCorrect)
                                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                    @else
                                        <div class="w-2 h-2 rounded-full bg-slate-100"></div>
                                    @endif
                                @else
                                    <div class="w-4 h-4 rounded-full bg-brand-600 opacity-0 transition-opacity duration-300 peer-checked:opacity-100"></div>
                                @endif
                            </div>
                            <span class="text-xl font-bold {{ $textColorClass }} transition-colors peer-checked:text-slate-900">
                                {{ $option->content_text }}
                            </span>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>
        </div>
        @endforeach

        <div class="pt-10 flex flex-col items-center gap-4">
            @if(!$isReviewMode)
            <button type="submit" id="submit-btn" disabled class="group relative px-16 py-8 rounded-[2.5rem] bg-slate-900 text-white overflow-hidden shadow-2xl transition-all hover:scale-[1.02] active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:scale-100">
                <div class="absolute inset-0 bg-brand-600 translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                <span class="relative z-10 text-2xl font-black uppercase tracking-[0.3em]">Finalize & Submit</span>
            </button>
            <p id="validation-msg" class="text-xs font-black text-red-500 uppercase tracking-widest animate-pulse">Complete all modules to unlock submission</p>
            @else
            <a href="{{ route('student.course', $quiz->chapter->course_id) }}" class="px-16 py-8 rounded-[2.5rem] glass border border-white/60 text-slate-900 font-black uppercase tracking-[0.3em] text-xl hover:bg-white transition-all shadow-glass">
                Back to Course
            </a>
            @endif
        </div>
    </form>
</div>

<style>
    /* Custom Radio Injection */
    input[type="radio"]:checked + div {
        border-color: #6366f1 !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 25px rgba(99, 102, 241, 0.2) !important;
    }
    input[type="radio"]:checked + div .w-4 {
        opacity: 1 !important;
    }
    input[type="radio"]:checked + div span {
        color: #0f172a !important;
    }
</style>

<script>
    // Robust Quiz Logic
    const isReviewMode = {{ $isReviewMode ? 'true' : 'false' }};
    
    function updateProgress() {
        if (isReviewMode) return;

        // 1. Get all unique question names present in the DOM
        const allQuestions = new Set();
        document.querySelectorAll('.quiz-option').forEach(opt => allQuestions.add(opt.name));
        const totalToAnswer = allQuestions.size;

        // 2. Get all currently checked unique question names
        const answeredQuestions = new Set();
        document.querySelectorAll('.quiz-option:checked').forEach(radio => {
            answeredQuestions.add(radio.name);
            
            // Visual feedback for answered blocks
            const block = radio.closest('.question-block');
            if (block) {
                const indicator = block.querySelector('.status-indicator');
                const statusText = block.querySelector('.status-text');
                if (indicator) {
                    indicator.classList.remove('bg-brand-50', 'text-brand-600');
                    indicator.classList.add('bg-brand-600', 'text-white', 'shadow-glow');
                }
                if (statusText) {
                    statusText.innerText = 'Response Locked';
                    statusText.classList.remove('text-slate-400');
                    statusText.classList.add('text-brand-600');
                }
            }
        });

        // 3. Update UI counts
        const count = answeredQuestions.size;
        const countDisplay = document.getElementById('answered-count');
        if (countDisplay) {
            countDisplay.innerText = count;
            // Force update the total text in the UI to match reality
            const parent = countDisplay.parentElement;
            if (parent) {
                parent.innerHTML = `<span id="answered-count">${count}</span> / ${totalToAnswer} Questions Answered`;
            }
        }

        // 4. Enable/Disable Submit Button
        const submitBtn = document.getElementById('submit-btn');
        const validationMsg = document.getElementById('validation-msg');

        if (submitBtn) {
            if (count >= totalToAnswer && totalToAnswer > 0) {
                submitBtn.disabled = false;
                if(validationMsg) validationMsg.classList.add('hidden');
            } else {
                submitBtn.disabled = true;
                if(validationMsg) validationMsg.classList.remove('hidden');
            }
        }
    }

    function validateForm() {
        const allQuestions = new Set();
        document.querySelectorAll('.quiz-option').forEach(opt => allQuestions.add(opt.name));
        const answeredQuestions = new Set();
        document.querySelectorAll('.quiz-option:checked').forEach(radio => answeredQuestions.add(radio.name));
        
        if (answeredQuestions.size < allQuestions.size) {
            alert('Veuillez répondre à toutes les questions affichées.');
            return false;
        }
        return true;
    }

    // Initialize on load
    document.addEventListener('DOMContentLoaded', () => {
        if (!isReviewMode) {
            updateProgress();
        }
        
        // Timer Logic
        let seconds = 0;
        const timerElement = document.getElementById('timer');
        if (timerElement) {
            setInterval(() => {
                seconds++;
                const mins = Math.floor(seconds / 60).toString().padStart(2, '0');
                const secs = (seconds % 60).toString().padStart(2, '0');
                timerElement.innerText = `${mins}:${secs}`;
            }, 1000);
        }
    });
</script>
@endsection

@extends('layouts.admin')

@section('header')
    <div class="flex items-center justify-between w-full">
        <div class="flex items-center gap-6">
            <a href="{{ route('admin.quizzes.index') }}" class="w-12 h-12 rounded-2xl glass flex items-center justify-center text-slate-500 hover:text-brand-600 transition-all shadow-sm border border-white/40">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">Quiz Content <span class="text-brand-600 italic">Builder</span></h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="px-2 py-0.5 rounded-md bg-brand-50 text-brand-600 text-[10px] font-black uppercase tracking-tighter border border-brand-100 italic">Assessment Mode</span>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">{{ $quiz->title }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start pb-24">
    
    <!-- Left: Fixed Sidebar for adding Questions -->
    <div class="lg:col-span-5 space-y-8 sticky top-8">
        <div class="glass p-10 rounded-[3rem] border border-white/60 shadow-glass relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-brand-600/5 rounded-full -mr-16 -mt-16 blur-3xl"></div>
            
            <h3 class="text-2xl font-black text-slate-800 mb-10 flex items-center gap-3">
                <span class="w-2 h-8 bg-brand-600 rounded-full shadow-glow"></span>
                Draft Question
            </h3>
            
            <form action="{{ route('admin.quizzes.questions.store', $quiz->id) }}" method="POST" class="space-y-10">
                @csrf
                
                <!-- Question Input -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Question Description</label>
                    <textarea name="content_text" required rows="3" class="block w-full bg-white/40 backdrop-blur-sm border border-slate-200 focus:border-brand-500 focus:ring-8 focus:ring-brand-500/5 rounded-3xl py-5 px-6 text-slate-700 placeholder-slate-300 transition-all duration-300 focus:outline-none font-bold text-lg leading-snug" placeholder="Ask something impactful..."></textarea>
                </div>

                <!-- Choices Container -->
                <div class="space-y-6">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Configure Choices</label>
                    
                    <div id="dynamic-fields" class="space-y-4">
                        <!-- Option 1 -->
                        <div class="group relative">
                            <div class="flex items-center gap-4 p-2 rounded-2xl hover:bg-brand-50/50 transition-colors">
                                <label class="cursor-pointer">
                                    <input type="radio" name="correct_option" value="0" checked class="peer hidden">
                                    <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-center peer-checked:!bg-emerald-500 peer-checked:!border-emerald-500 peer-checked:!text-white transition-all text-slate-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                </label>
                                <input type="text" name="options[]" required placeholder="Type Choice 1..." class="flex-1 bg-transparent border-b-2 border-slate-100 focus:border-brand-500 py-3 px-2 text-slate-700 font-bold transition-all outline-none">
                            </div>
                        </div>

                        <!-- Option 2 -->
                        <div class="group relative">
                            <div class="flex items-center gap-4 p-2 rounded-2xl hover:bg-brand-50/50 transition-colors">
                                <label class="cursor-pointer">
                                    <input type="radio" name="correct_option" value="1" class="peer hidden">
                                    <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-center peer-checked:!bg-emerald-500 peer-checked:!border-emerald-500 peer-checked:!text-white transition-all text-slate-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                </label>
                                <input type="text" name="options[]" required placeholder="Type Choice 2..." class="flex-1 bg-transparent border-b-2 border-slate-100 focus:border-brand-500 py-3 px-2 text-slate-700 font-bold transition-all outline-none">
                            </div>
                        </div>
                    </div>

                    <button type="button" onclick="addOptionField()" class="w-full py-4 rounded-2xl border-2 border-dashed border-slate-200 text-slate-400 font-black text-[10px] uppercase tracking-widest hover:border-brand-300 hover:text-brand-600 hover:bg-white transition-all">
                        + ADD NEW CHOICE
                    </button>
                </div>

                <x-primary-button class="w-full justify-center py-6 text-base tracking-[0.2em] shadow-glow transform active:scale-95">
                    PUBLISH TO QUIZ
                </x-primary-button>
            </form>
        </div>
    </div>

    <!-- Right: Questions Feed -->
    <div class="lg:col-span-7 space-y-10">
        <div class="flex items-center justify-between px-6">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em]">Structure & Content ({{ $quiz->questions->count() }})</h3>
            <div class="h-px flex-1 mx-10 bg-slate-200/50"></div>
        </div>

        <div class="space-y-10">
            @forelse($quiz->questions as $idx => $question)
            <div class="glass p-12 rounded-[3.5rem] border border-white/60 hover:border-brand-200 transition-all duration-700 group relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8">
                    <form action="{{ route('admin.questions.destroy', $question->id) }}" method="POST" onsubmit="return confirm('Archive question?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-12 h-12 rounded-2xl bg-red-50 text-red-400 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </div>

                <div class="flex items-start gap-8 mb-12">
                    <div class="w-16 h-16 rounded-[1.5rem] bg-brand-600 text-white flex items-center justify-center font-black text-2xl shadow-glow flex-shrink-0">
                        {{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}
                    </div>
                    <div class="pt-2 max-w-[80%]">
                        <h4 class="text-3xl font-black text-slate-900 leading-tight tracking-tight group-hover:text-brand-600 transition-colors">{{ $question->content_text }}</h4>
                        <div class="flex items-center gap-4 mt-4">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest bg-slate-100 px-3 py-1 rounded-full">Objective Assessment</span>
                            <span class="text-[9px] font-black text-emerald-600 uppercase tracking-widest bg-emerald-50 px-3 py-1 rounded-full italic">Ready for Students</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 ml-4">
                    @foreach($question->options as $opt)
                    <div class="p-6 rounded-3xl border-2 transition-all duration-300 {{ $opt->is_correct ? 'bg-emerald-50 border-emerald-200 shadow-lg shadow-emerald-500/10' : 'bg-white/40 border-slate-100 hover:border-brand-100 shadow-sm' }} flex items-center gap-5">
                        @if($opt->is_correct)
                            <div class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center text-white shadow-glow">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="4"><path d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        @else
                            <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 font-black text-[10px]">
                                {{ chr(65 + $loop->index) }}
                            </div>
                        @endif
                        <span class="{{ $opt->is_correct ? 'font-black text-emerald-800 text-lg' : 'font-bold text-slate-600' }}">{{ $opt->content_text }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <div class="py-40 text-center glass rounded-[4rem] border-2 border-dashed border-slate-200">
                <div class="w-24 h-24 bg-slate-50 rounded-full mx-auto flex items-center justify-center mb-8">
                    <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                </div>
                <h4 class="text-3xl font-black text-slate-300 italic tracking-tighter uppercase">No Questions Published</h4>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-[0.2em] mt-2">Use the drafting panel to create your content.</p>
            </div>
            @endforelse
        </div>
    </div>

</div>

<script>
    let currentOptions = 2;
    function addOptionField() {
        const container = document.getElementById('dynamic-fields');
        const div = document.createElement('div');
        div.className = 'group relative animate-in fade-in slide-in-from-left duration-500';
        div.innerHTML = `
            <div class="flex items-center gap-4 p-2 rounded-2xl hover:bg-brand-50/50 transition-colors">
                <label class="cursor-pointer">
                    <input type="radio" name="correct_option" value="${currentOptions}" class="peer hidden">
                    <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-center peer-checked:!bg-emerald-500 peer-checked:!border-emerald-500 peer-checked:!text-white transition-all text-slate-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M5 13l4 4L19 7"></path></svg>
                    </div>
                </label>
                <input type="text" name="options[]" required placeholder="Type Choice ${currentOptions + 1}..." class="flex-1 bg-transparent border-b-2 border-slate-100 focus:border-brand-500 py-3 px-2 text-slate-700 font-bold transition-all outline-none">
            </div>
        `;
        container.appendChild(div);
        currentOptions++;
    }
</script>
@endsection

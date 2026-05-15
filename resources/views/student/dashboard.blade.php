@extends('layouts.app')

@section('header')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-6">
            <div class="w-20 h-20 rounded-3xl glass flex items-center justify-center text-brand-600 shadow-glass border border-white/60">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M12 14l9-5-9-5-9 5 9 5z"></path><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.083 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
            </div>
            <div>
                <h2 class="text-4xl font-black text-slate-900 tracking-tight">Bienvenue, {{ $user->first_name }}</h2>
                <div class="flex items-center gap-3 mt-2">
                    <span class="px-3 py-1 rounded-full bg-brand-50 text-brand-600 text-[10px] font-black uppercase tracking-widest border border-brand-100">Massar: {{ $user->massar_code }}</span>
                    <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-500 text-[10px] font-black uppercase tracking-widest">{{ $group ? $group->name : 'Non assigné' }}</span>
                </div>
            </div>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('student.analytics') }}" class="px-8 py-4 rounded-2xl bg-white/60 backdrop-blur-md border border-white/40 shadow-glass font-black text-xs text-slate-600 hover:text-brand-600 transition-all uppercase tracking-widest">
                VOIR LES ANALYSES
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">

    <!-- Results Celebration Modal -->
    @if(session('quiz_result'))
    <div id="result-modal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-2xl bg-slate-900/60">
        <div class="glass p-12 rounded-[4rem] shadow-2xl max-w-lg w-full border border-white/60 text-center animate-in zoom-in duration-500 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-emerald-400 via-brand-500 to-indigo-500"></div>
            
            <div class="w-24 h-24 bg-emerald-50 text-emerald-500 rounded-full mx-auto flex items-center justify-center mb-8 shadow-glow ring-8 ring-emerald-500/5">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M5 13l4 4L19 7"></path></svg>
            </div>

            <h4 class="text-4xl font-black text-slate-900 mb-2 tracking-tight">Succès de l'Évaluation !</h4>
            <p class="text-xs font-black text-slate-400 uppercase tracking-[0.3em] mb-10 italic">Performance Synchronisée</p>

            <div class="grid grid-cols-2 gap-6 mb-10">
                <div class="p-6 rounded-[2rem] bg-white/40 border border-white shadow-inner">
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-1">Score Brut</p>
                    <div class="text-3xl font-black text-slate-800">{{ session('quiz_result')['score'] }}<span class="text-slate-300">/{{ session('quiz_result')['total'] }}</span></div>
                </div>
                <div class="p-6 rounded-[2rem] bg-brand-600 text-white shadow-glow">
                    <p class="text-[10px] font-black text-brand-100 uppercase mb-1">Pourcentage</p>
                    <div class="text-3xl font-black">{{ number_format(session('quiz_result')['percentage']) }}%</div>
                </div>
            </div>

            <button onclick="document.getElementById('result-modal').remove()" class="w-full py-6 rounded-[2rem] bg-slate-900 text-white font-black uppercase tracking-[0.2em] text-sm hover:bg-brand-600 transition-all shadow-xl">
                CONTINUER LE PARCOURS
            </button>
        </div>
    </div>
    @endif

    <div class="flex items-center justify-between mb-12">
        <h3 class="text-sm font-black text-slate-400 uppercase tracking-[0.3em]">Flux du Programme</h3>
        <div class="h-px flex-1 mx-10 bg-slate-200/50"></div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
        @forelse($courses as $course)
        <div class="glass p-10 rounded-[3rem] border border-white/60 shadow-glass hover:border-brand-300 transition-all duration-500 group flex flex-col h-full relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-brand-600/5 rounded-full -mr-16 -mt-16 blur-3xl"></div>
            
            <div class="w-16 h-16 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 mb-8 shadow-inner group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            
            <h4 class="text-2xl font-black text-slate-900 mb-4 leading-tight group-hover:text-brand-600 transition-colors">{{ $course->title }}</h4>
            <p class="text-slate-500 font-medium leading-relaxed mb-10 flex-1 italic text-sm">
                {{ Str::limit($course->description, 100) }}
            </p>
            
            <div class="flex items-center gap-4 mt-auto">
                <a href="{{ route('student.course', $course->id) }}" class="flex-1 py-5 rounded-2xl bg-brand-600 text-white font-black text-center shadow-glow hover:bg-brand-500 transition-all uppercase tracking-widest text-[10px]">
                    Entrer dans le Cours
                </a>
                <div class="w-14 h-14 rounded-2xl glass flex items-center justify-center text-slate-400 group-hover:text-brand-600 transition-colors border border-white/40 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-40 text-center glass rounded-[4rem] border-2 border-dashed border-slate-200">
            <div class="w-24 h-24 bg-slate-50 rounded-full mx-auto flex items-center justify-center mb-8">
                <svg class="w-12 h-12 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h4 class="text-3xl font-black text-slate-300 italic tracking-tighter uppercase">Aucune Mission Trouvée</h4>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-[0.2em] mt-2">Votre flux éducatif est actuellement vide.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection

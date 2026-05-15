@extends('layouts.admin')

@section('header', 'Aperçu du système')

@section('content')
<div class="space-y-8">
    
    <!-- Top Stats Row (Bento Grid) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Students -->
        <div class="p-8 rounded-4xl bg-white/60 backdrop-blur-md border border-white/40 shadow-glass group hover:bg-brand-600 transition-all duration-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest group-hover:text-brand-100 transition-colors">Étudiants</p>
                    <h3 class="text-4xl font-black text-slate-900 mt-2 group-hover:text-white transition-colors">{{ number_format($stats['total_students']) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 group-hover:bg-white/20 group-hover:text-white transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
            </div>
            <p class="text-[10px] text-slate-400 mt-4 font-bold group-hover:text-brand-200 uppercase">Apprenants actifs</p>
        </div>

        <!-- Lessons -->
        <div class="p-8 rounded-4xl bg-white/60 backdrop-blur-md border border-white/40 shadow-glass group hover:bg-indigo-600 transition-all duration-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest group-hover:text-indigo-100">Leçons</p>
                    <h3 class="text-4xl font-black text-slate-900 mt-2 group-hover:text-white">{{ number_format($stats['total_lessons']) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 group-hover:bg-white/20 group-hover:text-white transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
            </div>
            <p class="text-[10px] text-slate-400 mt-4 font-bold group-hover:text-indigo-200 uppercase">Modules de cours</p>
        </div>

        <!-- Average Score -->
        <div class="p-8 rounded-4xl bg-white/60 backdrop-blur-md border border-white/40 shadow-glass group hover:bg-emerald-600 transition-all duration-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest group-hover:text-emerald-100">Performance Moyenne</p>
                    <h3 class="text-4xl font-black text-slate-900 mt-2 group-hover:text-white">{{ number_format($stats['average_score'], 1) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 group-hover:bg-white/20 group-hover:text-white transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
            </div>
            <p class="text-[10px] text-slate-400 mt-4 font-bold group-hover:text-emerald-200 uppercase">Dans tout le système</p>
        </div>

        <!-- Groups -->
        <div class="p-8 rounded-4xl bg-white/60 backdrop-blur-md border border-white/40 shadow-glass group hover:bg-orange-600 transition-all duration-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest group-hover:text-orange-100">Groupes</p>
                    <h3 class="text-4xl font-black text-slate-900 mt-2 group-hover:text-white">{{ $stats['total_groups'] }}</h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-orange-50 flex items-center justify-center text-orange-600 group-hover:bg-white/20 group-hover:text-white transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
            <p class="text-[10px] text-slate-400 mt-4 font-bold group-hover:text-orange-200 uppercase">Cohortes actives</p>
        </div>
    </div>

    <!-- Main Bento Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Group Performance (Left 2/3) -->
        <div class="lg:col-span-2 p-10 rounded-4xl bg-white/60 backdrop-blur-md border border-white/40 shadow-glass">
            <h4 class="text-xl font-black text-slate-800 mb-8 uppercase tracking-widest italic">Performance des Groupes</h4>
            <div class="space-y-8">
                @forelse($groupPerformance as $group)
                <div class="space-y-3">
                    <div class="flex justify-between items-end">
                        <span class="font-black text-slate-700">{{ $group['name'] }}</span>
                        <span class="text-brand-600 font-black">{{ $group['avg'] }}%</span>
                    </div>
                    <div class="w-full h-4 bg-slate-100 rounded-full overflow-hidden shadow-inner">
                        <div class="h-full bg-brand-600 shadow-glow rounded-full transition-all duration-1000" style="width: {{ $group['avg'] }}%"></div>
                    </div>
                    <p class="text-[10px] text-slate-400 font-bold uppercase">{{ $group['count'] }} soumissions au total</p>
                </div>
                @empty
                <div class="py-12 text-center text-slate-400 italic font-bold">Aucune donnée de performance disponible.</div>
                @endforelse
            </div>
        </div>

        <!-- Side Bento Column -->
        <div class="space-y-8">
            <!-- Maintenance Toggle -->
            <div class="p-8 rounded-4xl {{ $maintenance ? 'bg-red-600 text-white shadow-red-200' : 'bg-white/60 backdrop-blur-md border border-white/40 shadow-glass' }} transition-colors duration-500">
                <h4 class="text-sm font-black uppercase tracking-widest mb-4 italic {{ $maintenance ? 'text-red-100' : 'text-slate-400' }}">Garde du Système</h4>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-black text-lg">Maintenance</p>
                        <p class="text-[10px] opacity-70 font-bold uppercase tracking-tighter">{{ $maintenance ? 'Accès Restreint' : 'Accès Global' }}</p>
                    </div>
                    <form action="{{ route('admin.maintenance.toggle') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-14 h-14 rounded-2xl flex items-center justify-center transition-all active:scale-90 {{ $maintenance ? 'bg-white text-red-600 shadow-xl' : 'bg-slate-100 text-slate-400 shadow-inner' }}">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path></svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="p-8 rounded-4xl bg-slate-900 text-white shadow-xl">
                <h4 class="text-xs font-black uppercase tracking-widest text-slate-500 mb-6 italic">Actions Rapides</h4>
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('admin.courses.create') }}" class="p-4 rounded-2xl bg-white/10 hover:bg-white/20 transition-all text-center">
                        <div class="text-xl font-black mb-1">Plus</div>
                        <div class="text-[10px] font-bold uppercase text-slate-400">Cours</div>
                    </a>
                    <a href="{{ route('admin.quizzes.create') }}" class="p-4 rounded-2xl bg-brand-600 hover:bg-brand-500 transition-all text-center shadow-glow">
                        <div class="text-xl font-black mb-1">Nouveau</div>
                        <div class="text-[10px] font-bold uppercase text-brand-100">Quizz</div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Row -->
    <div class="p-10 rounded-4xl bg-white/60 backdrop-blur-md border border-white/40 shadow-glass overflow-hidden">
        <div class="flex justify-between items-center mb-10">
            <h4 class="text-xl font-black text-slate-800 uppercase tracking-widest italic text-center">Activité Récente</h4>
            <div class="h-px flex-1 mx-8 bg-slate-100/50"></div>
            <a href="/admin/students" class="text-xs font-black text-brand-600 hover:underline tracking-widest">VOIR TOUT</a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($recentResults as $result)
            <div class="p-6 rounded-3xl bg-white/40 border border-white shadow-sm flex items-center gap-4 group hover:border-brand-200 transition-all">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center font-black text-brand-600 group-hover:bg-brand-50 transition-colors">
                    {{ substr($result->user->first_name, 0, 1) }}
                </div>
                <div class="flex-1 overflow-hidden">
                    <p class="font-black text-slate-800 truncate">{{ $result->user->first_name }} {{ $result->user->last_name }}</p>
                    <p class="text-[10px] text-slate-400 font-bold uppercase truncate">{{ $result->quiz->title }}</p>
                </div>
                <div class="text-right">
                    <div class="font-black text-emerald-600 text-lg">{{ $result->score }}/{{ $result->total_questions }}</div>
                    <p class="text-[8px] text-slate-300 font-black uppercase">{{ $result->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <div class="col-span-full py-10 text-center text-slate-400 italic">Aucune activité récente détectée.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection

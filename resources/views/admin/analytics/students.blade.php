@extends('layouts.admin')

@section('header', 'Analyse des Élèves')

@section('content')
<div class="space-y-6">
    
    <!-- Search & Filter Bento -->
    <div class="bg-white p-6 rounded-[2rem] border border-gray-200 shadow-sm">
        <form action="{{ route('admin.analytics.students') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
            <div class="md:col-span-6 space-y-2">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Rechercher un élève</label>
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, Prénom ou Code Massar..." class="block w-full bg-gray-50 border border-gray-200 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 rounded-xl py-2.5 pl-12 pr-4 text-gray-700 text-sm transition-all outline-none">
                </div>
            </div>
            
            <div class="md:col-span-4 space-y-2">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Filtrer par Groupe</label>
                <select name="group_id" class="block w-full bg-gray-50 border border-gray-200 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 rounded-xl py-2.5 px-4 text-gray-700 text-sm outline-none cursor-pointer">
                    <option value="">Tous les groupes</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <button type="submit" class="w-full justify-center py-2.5 px-4 rounded-xl bg-indigo-600 text-white font-medium text-sm hover:bg-indigo-700 transition-colors">
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Students Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($students as $student)
        @php
            $totalQuizzes = $student->results->unique('quiz_id')->count();
            $firstAttemptPassed = $student->results->where('attempt_number', 1)->where('is_passed', true)->unique('quiz_id')->count();
            $secondAttemptPassed = $student->results->where('attempt_number', 2)->where('is_passed', true)->unique('quiz_id')->count();
            $blockedCount = $student->progresses->filter(fn($p) => $p->isQuizBlocked())->count();
            $stuckCount = $student->progresses->filter(fn($p) => $p->isStuck())->count();
        @endphp
        <a href="{{ route('admin.analytics.student_profile', $student->id) }}" class="bg-white p-6 rounded-[2rem] border border-gray-200 hover:border-indigo-300 hover:shadow-lg transition-all duration-300 group block relative">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-lg border border-indigo-100 group-hover:scale-105 transition-transform">
                    {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                </div>
                <div>
                    <h4 class="text-base font-bold text-gray-900 group-hover:text-indigo-600 transition-colors leading-tight">{{ $student->first_name }} {{ $student->last_name }}</h4>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-0.5">{{ $student->massar_code }}</p>
                </div>
            </div>

            <!-- Group & Location -->
            <div class="mb-4 text-xs flex justify-between items-center text-gray-500 border-b border-gray-100 pb-3">
                <span class="font-bold text-slate-700 bg-slate-100 px-2.5 py-0.5 rounded-lg">{{ $student->group ? $student->group->name : 'N/A' }}</span>
                <span class="text-[10px] truncate max-w-[150px] font-semibold">{{ $student->group ? $student->group->branch->school->name : '—' }}</span>
            </div>

            <!-- Important Metrics Grid -->
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex flex-col justify-between">
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Quiz Tentés</span>
                    <span class="text-sm font-black text-slate-700 mt-0.5">{{ $totalQuizzes }}</span>
                </div>
                <div class="p-2.5 rounded-xl bg-amber-50/40 border border-amber-100/60 flex flex-col justify-between">
                    <span class="text-[9px] font-bold text-amber-500 uppercase tracking-wider">1er Essai Réussi</span>
                    <span class="text-sm font-black text-amber-600 mt-0.5">{{ $firstAttemptPassed }}</span>
                </div>
                <div class="p-2.5 rounded-xl bg-emerald-50/40 border border-emerald-100/60 flex flex-col justify-between">
                    <span class="text-[9px] font-bold text-emerald-500 uppercase tracking-wider">2e Essai Réussi</span>
                    <span class="text-sm font-black text-emerald-600 mt-0.5">{{ $secondAttemptPassed }}</span>
                </div>
                <div class="p-2.5 rounded-xl {{ $blockedCount > 0 ? 'bg-red-50 border-red-100 text-red-600' : 'bg-slate-50 border-slate-100 text-slate-700' }} flex flex-col justify-between">
                    <span class="text-[9px] font-bold {{ $blockedCount > 0 ? 'text-red-500' : 'text-slate-400' }} uppercase tracking-wider">Quiz Bloqués</span>
                    <span class="text-sm font-black mt-0.5">{{ $blockedCount }}</span>
                </div>
            </div>

            @if($stuckCount > 0)
            <div class="mt-3 p-2.5 rounded-xl bg-rose-50 border border-rose-100 flex items-center gap-2 text-rose-700">
                <svg class="w-4 h-4 shrink-0 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span class="text-[10px] font-black uppercase tracking-wider">{{ $stuckCount }} cours en difficulté</span>
            </div>
            @endif

            <div class="mt-4 flex items-center justify-between text-[10px] font-bold text-gray-400 uppercase tracking-wider border-t border-gray-50 pt-3">
                <span>Dernière connexion</span>
                <span class="text-gray-600">{{ $student->last_login_at ? $student->last_login_at->diffForHumans() : 'Jamais' }}</span>
            </div>

            <div class="mt-4 flex items-center justify-center gap-2 text-indigo-600 font-semibold text-[10px] uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-opacity">
                Voir le profil complet
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </div>
        </a>
        @empty
        <div class="col-span-full py-20 text-center bg-gray-50 rounded-[2rem] border-2 border-dashed border-gray-200">
            <h4 class="text-xl font-bold text-gray-400 italic">Aucun Élève Trouvé</h4>
            <p class="text-sm text-gray-500 mt-2">Aucun élève ne correspond à vos filtres actuels.</p>
        </div>
        @endforelse
    </div>

</div>
@endsection

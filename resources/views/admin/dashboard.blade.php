@extends('layouts.admin')

@section('header', 'Aperçu du système')

@section('content')
<div class="space-y-8">
    
    <!-- Top Stats Row (Bento Grid) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Students -->
        <div class="p-6 bg-white border border-gray-200 rounded-lg">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-medium text-gray-600 uppercase tracking-widest">Élèves</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_students']) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-md bg-indigo-50 flex items-center justify-center text-indigo-600 border border-indigo-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-4 font-medium uppercase">Apprenants actifs</p>
        </div>

        <!-- Chapters -->
        <div class="p-6 bg-white border border-gray-200 rounded-lg">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-medium text-gray-600 uppercase tracking-widest">Chapitres</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_chapters']) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-md bg-indigo-50 flex items-center justify-center text-indigo-600 border border-indigo-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-4 font-medium uppercase">Modules de cours</p>
        </div>

        <!-- Average Score -->
        <div class="p-6 bg-white border border-gray-200 rounded-lg">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-medium text-gray-600 uppercase tracking-widest">Performance Moyenne</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['average_score'], 1) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-md bg-indigo-50 flex items-center justify-center text-indigo-600 border border-indigo-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-4 font-medium uppercase">Dans tout le système</p>
        </div>

        <!-- Groups -->
        <div class="p-6 bg-white border border-gray-200 rounded-lg">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-medium text-gray-600 uppercase tracking-widest">Groupes</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_groups'] }}</h3>
                </div>
                <div class="w-12 h-12 rounded-md bg-indigo-50 flex items-center justify-center text-indigo-600 border border-indigo-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-4 font-medium uppercase">Cohortes actives</p>
        </div>
    </div>

    <!-- Blocked Students Section (Help Required) -->
    <div class="bg-white border border-gray-200 rounded-lg p-6" x-data="{ activeGroup: null }">
        <div class="flex items-center justify-between mb-6">
            <h4 class="text-lg font-bold text-gray-900 uppercase">Apprenants Bloqués par Groupe</h4>
            <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold uppercase tracking-wider">
                Aide requise
            </span>
        </div>

        @if($blockedGroups->isEmpty())
            <div class="py-8 text-center bg-emerald-50/50 border border-dashed border-emerald-200 rounded-lg">
                <svg class="w-12 h-12 text-emerald-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-sm font-bold text-emerald-800">Aucun blocage détecté !</p>
                <p class="text-xs text-emerald-600 mt-1">Tous les apprenants progressent normalement sur leurs quiz.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                @foreach($blockedGroups as $group)
                    <div class="border border-gray-200 rounded-lg p-4 cursor-pointer hover:border-red-300 hover:shadow-sm transition-all"
                         :class="activeGroup === {{ $group['id'] }} ? 'border-red-500 ring-1 ring-red-500 bg-red-50/20' : 'bg-gray-50/50'"
                         @click="activeGroup = (activeGroup === {{ $group['id'] }} ? null : {{ $group['id'] }})">
                        <div class="flex justify-between items-center">
                            <div>
                                <h5 class="font-bold text-gray-900 text-base">{{ $group['name'] }}</h5>
                                <p class="text-xs text-gray-500 mt-1 uppercase font-medium">{{ $group['count'] }} {{ Str::plural('élève', $group['count']) }} en difficulté</p>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-red-100 text-red-700 flex items-center justify-center font-black">
                                {{ $group['count'] }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Group Details Panel -->
            @foreach($blockedGroups as $group)
                <div x-show="activeGroup === {{ $group['id'] }}" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     class="border border-red-200 bg-red-50/10 rounded-lg p-5 mt-4"
                     style="display: none;">
                    <div class="flex items-center justify-between border-b border-red-100 pb-3 mb-4">
                        <h5 class="font-bold text-red-900 text-sm uppercase">Détails des Blocages - {{ $group['name'] }}</h5>
                        <button @click="activeGroup = null" class="text-xs font-bold text-gray-500 hover:text-gray-700 uppercase">Fermer</button>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="text-red-800 text-xs uppercase font-bold tracking-wide border-b border-red-100">
                                    <th class="py-2">Élève</th>
                                    <th class="py-2">Quiz / Chapitre Bloqué</th>
                                    <th class="py-2">Temps Restant</th>
                                    <th class="py-2 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-red-50/50">
                                @foreach($group['progresses'] as $progress)
                                    <tr>
                                        <td class="py-3">
                                            <div class="font-bold text-gray-900">{{ $progress->user->first_name }} {{ $progress->user->last_name }}</div>
                                            <div class="text-[10px] text-gray-500 uppercase mt-0.5">{{ $progress->user->massar_code }}</div>
                                        </td>
                                        <td class="py-3">
                                            <div class="font-bold text-gray-800">{{ $progress->chapter->quiz->title ?? 'Quiz du Chapitre' }}</div>
                                            <div class="text-[10px] text-indigo-600 uppercase mt-0.5">Partie {{ $progress->chapter->order }} : {{ $progress->chapter->title }}</div>
                                        </td>
                                        <td class="py-3">
                                            @if($progress->isQuizBlocked())
                                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-red-100 text-red-800 text-xs font-bold">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    Bloqué ({{ $progress->quizBlockedRemainingHours() }}h restants)
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-amber-100 text-amber-800 text-xs font-bold">
                                                    En difficulté
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-right">
                                            <form action="{{ route('admin.progress.unlock', $progress) }}" method="POST" class="inline" onsubmit="return confirm('Voulez-vous vraiment débloquer cet élève ?');">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-bold uppercase transition shadow-sm">
                                                    Débloquer
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <!-- Main Bento Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Group Performance (Left 2/3) -->
        <div class="lg:col-span-2 p-6 bg-white border border-gray-200 rounded-lg">
            <h4 class="text-lg font-bold text-gray-900 mb-8 uppercase">Performance des Groupes</h4>
            <div class="space-y-8">
                @forelse($groupPerformance as $group)
                <div class="space-y-3">
                    <div class="flex justify-between items-end">
                        <span class="font-bold text-gray-900">{{ $group['name'] }}</span>
                        <span class="text-indigo-600 font-bold">{{ $group['avg'] }}%</span>
                    </div>
                    <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-indigo-600 rounded-full transition-all duration-1000" style="width: {{ $group['avg'] }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 font-medium uppercase">{{ $group['count'] }} soumissions au total</p>
                </div>
                @empty
                <div class="py-12 text-center text-gray-400 text-sm italic">Aucune donnée de performance disponible.</div>
                @endforelse
            </div>
        </div>

        <!-- Side Bento Column -->
        <div class="space-y-8">
            <!-- Maintenance Toggle -->
            <div class="p-6 {{ $maintenance ? 'bg-red-50 border-red-200' : 'bg-white border-gray-200' }} border rounded-lg transition-colors duration-500">
                <h4 class="text-sm font-medium uppercase tracking-widest mb-4 {{ $maintenance ? 'text-red-700' : 'text-gray-700' }}">Maintenance</h4>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-bold text-lg">{{ $maintenance ? 'Accès Restreint' : 'Accès Global' }}</p>
                    </div>
                    <form action="{{ route('admin.maintenance.toggle') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-10 h-10 rounded-md flex items-center justify-center transition-all {{ $maintenance ? 'bg-red-200 text-red-700' : 'bg-gray-100 text-gray-500' }}">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path></svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="p-6 bg-gray-900 text-white rounded-lg">
                <h4 class="text-xs font-medium uppercase tracking-widest text-gray-400 mb-4">Actions Rapides</h4>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('admin.courses.create') }}" class="p-3 rounded-md bg-gray-800 hover:bg-gray-700 transition-all text-center">
                        <div class="text-sm font-bold mb-1">Nouveau</div>
                        <div class="text-[8px] font-medium uppercase text-gray-400">Cours</div>
                    </a>
                    <a href="{{ route('admin.quizzes.create') }}" class="p-3 rounded-md bg-indigo-600 hover:bg-indigo-700 transition-all text-center">
                        <div class="text-sm font-bold mb-1">Nouveau</div>
                        <div class="text-[8px] font-medium uppercase text-indigo-100">Quizz</div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    

    <!-- Top Students (First Attempt Success) -->
    <div class="p-6 bg-white border border-gray-200 rounded-lg overflow-hidden mt-8 relative">
        <div class="absolute top-0 right-0 w-32 h-32 bg-amber-500/5 rounded-full -mr-16 -mt-16 blur-3xl"></div>
        <div class="flex justify-between items-center mb-10">
            <div class="flex items-center gap-2">
                <span class="text-xl">🏆</span>
                <h4 class="text-lg font-bold text-gray-900 uppercase tracking-wide">Tableau d'Honneur (Réussite au 1er essai)</h4>
            </div>
            <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-black uppercase tracking-wider">
                Excellence
            </span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($topStudents as $result)
            <div class="p-5 bg-gradient-to-br from-amber-50/30 to-amber-100/10 border border-amber-200/60 rounded-xl flex items-center gap-4 group hover:border-amber-300 transition-all shadow-sm">
                <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center font-black border border-amber-200 shadow-inner shrink-0 relative">
                    <span class="absolute -top-1.5 -right-1 text-xs">👑</span>
                    {{ substr($result->user->first_name, 0, 1) }}{{ substr($result->user->last_name, 0, 1) }}
                </div>
                <div class="flex-1 overflow-hidden">
                    <div class="flex items-center gap-1.5">
                        <p class="font-bold text-gray-900 truncate">{{ $result->user->first_name }} {{ $result->user->last_name }}</p>
                    </div>
                    <p class="text-[9px] text-gray-500 font-bold uppercase truncate mt-0.5">{{ $result->quiz->title }}</p>
                    @if($result->user->group)
                        <span class="inline-block px-1.5 py-0.5 rounded text-[8px] font-black bg-indigo-50 text-indigo-700 uppercase tracking-wider mt-1 border border-indigo-100/50">
                            {{ $result->user->group->name }}
                        </span>
                    @endif
                </div>
                <div class="text-right shrink-0">
                    <div class="font-black text-amber-600 text-lg">{{ $result->score }}%</div>
                    <p class="text-[8px] text-amber-800 font-bold uppercase tracking-wider">1er essai</p>
                </div>
            </div>
            @empty
            <div class="col-span-full py-12 text-center text-gray-400 text-sm italic border border-dashed border-gray-200 rounded-lg">
                Aucun élève n'a encore validé de quiz dès le premier essai.
            </div>
            @endforelse
        </div>
    </div>

    <!-- Second Attempt Successes -->
    <div class="p-6 bg-white border border-gray-200 rounded-lg overflow-hidden mt-8 relative">
        <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/5 rounded-full -mr-16 -mt-16 blur-3xl"></div>
        <div class="flex justify-between items-center mb-10">
            <div class="flex items-center gap-2">
                <span class="text-xl">🎯</span>
                <h4 class="text-lg font-bold text-gray-900 uppercase tracking-wide">Réussite à la 2ème Tentative</h4>
            </div>
            <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-black uppercase tracking-wider">
                Persévérance
            </span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($secondAttemptStudents as $result)
            <div class="p-5 bg-gradient-to-br from-emerald-50/30 to-emerald-100/10 border border-emerald-200/60 rounded-xl flex items-center gap-4 group hover:border-emerald-300 transition-all shadow-sm">
                <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-black border border-emerald-200 shadow-inner shrink-0 relative">
                    <span class="absolute -top-1.5 -right-1 text-xs">⭐</span>
                    {{ substr($result->user->first_name, 0, 1) }}{{ substr($result->user->last_name, 0, 1) }}
                </div>
                <div class="flex-1 overflow-hidden">
                    <div class="flex items-center gap-1.5">
                        <p class="font-bold text-gray-900 truncate">{{ $result->user->first_name }} {{ $result->user->last_name }}</p>
                    </div>
                    <p class="text-[9px] text-gray-500 font-bold uppercase truncate mt-0.5">{{ $result->quiz->title }}</p>
                    @if($result->user->group)
                        <span class="inline-block px-1.5 py-0.5 rounded text-[8px] font-black bg-indigo-50 text-indigo-700 uppercase tracking-wider mt-1 border border-indigo-100/50">
                            {{ $result->user->group->name }}
                        </span>
                    @endif
                </div>
                <div class="text-right shrink-0">
                    <div class="font-black text-emerald-600 text-lg">{{ $result->score }}%</div>
                    <p class="text-[8px] text-emerald-800 font-bold uppercase tracking-wider">2ème essai</p>
                </div>
            </div>
            @empty
            <div class="col-span-full py-12 text-center text-gray-400 text-sm italic border border-dashed border-gray-200 rounded-lg">
                Aucun élève n'a encore validé de quiz à la deuxième tentative.
            </div>
            @endforelse
        </div>
    </div>

<!-- Recent Activity Row -->
    <div class="p-6 bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="flex justify-between items-center mb-10">
            <h4 class="text-lg font-bold text-gray-900 uppercase">Activité Récente</h4>
            <a href="/admin/students" class="text-xs font-bold text-indigo-600 hover:underline uppercase">VOIR TOUT</a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($recentResults as $result)
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-md flex items-center gap-4 group hover:border-indigo-300 transition-all">
                <div class="w-12 h-12 rounded-lg bg-indigo-100 flex items-center justify-center font-bold text-indigo-600">
                    {{ substr($result->user->first_name, 0, 1) }}
                </div>
                <div class="flex-1 overflow-hidden">
                    <p class="font-bold text-gray-900 truncate">{{ $result->user->first_name }} {{ $result->user->last_name }}</p>
                    <p class="text-[10px] text-gray-500 font-medium uppercase truncate">{{ $result->quiz->title }}</p>
                </div>
                <div class="text-right">
                    <div class="font-bold text-emerald-600 text-base">{{ $result->score }}/{{ $result->total_questions }}</div>
                    <p class="text-[8px] text-gray-400 font-medium uppercase">{{ $result->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <div class="col-span-full py-10 text-center text-gray-400 text-sm italic">Aucune activité récente détectée.</div>
            @endforelse
        </div>
    </div>
    
</div>
@endsection

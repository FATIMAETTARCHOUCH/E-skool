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
                    <p class="text-xs font-medium text-gray-600 uppercase tracking-widest">Étudiants</p>
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

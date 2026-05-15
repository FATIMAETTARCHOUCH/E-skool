@extends('layouts.admin')

@section('header', 'Profil de l\'Étudiant')

@section('content')
<div class="space-y-8">

    <!-- Header / Identification -->
    <div class="glass p-10 rounded-[3rem] shadow-glass border border-white/60 flex items-center justify-between">
        <div class="flex items-center gap-6">
            <div class="w-24 h-24 rounded-[2rem] bg-brand-50 text-brand-600 flex items-center justify-center font-black text-4xl shadow-inner border border-brand-100">
                {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
            </div>
            <div>
                <h2 class="text-3xl font-black text-slate-800">{{ $student->first_name }} {{ $student->last_name }}</h2>
                <div class="flex items-center gap-3 mt-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">{{ $student->massar_code }}</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">{{ $student->age }} ans</span>
                    @if($student->group)
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-black bg-indigo-50 text-indigo-600 uppercase tracking-widest border border-indigo-100">{{ $student->group->name }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div>
            @if($student->is_first_login)
                <span class="px-4 py-2 rounded-xl bg-amber-50 text-amber-600 font-black text-xs uppercase tracking-widest border border-amber-200">Jamais connecté</span>
            @else
                <div class="text-right">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Dernière connexion</p>
                    <p class="font-bold text-slate-700">{{ $student->last_login_at ? $student->last_login_at->diffForHumans() : 'Inconnue' }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions Bento Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="glass p-8 rounded-4xl border border-red-100 bg-red-50/20 shadow-glass flex items-center justify-between group">
            <div>
                <h4 class="text-sm font-black text-red-600 uppercase tracking-widest">Réinitialisation Complète</h4>
                <p class="text-[10px] text-slate-400 font-bold mt-1">Efface tous les résultats & progrès</p>
            </div>
            <form id="reset-progress-form" action="{{ route('admin.analytics.student_reset_quizzes', $student->id) }}" method="POST" class="hidden">
                @csrf
            </form>
            <button type="button" onclick="confirmReset()" class="w-14 h-14 rounded-2xl bg-white text-red-500 shadow-sm border border-red-100 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all duration-300 active:scale-90">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </button>
        </div>
    </div>

    <script>
        function confirmReset() {
            if (confirm('⚠️ ATTENTION : Voulez-vous vraiment réinitialiser TOUT le parcours de cet étudiant ?\n\n- Tous les scores seront supprimés.\n- Toutes les leçons redeviendront non-terminées.\n- Cette action est irréversible.')) {
                document.getElementById('reset-progress-form').submit();
            }
        }
    </script>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- Courses Status -->
        <div class="glass p-10 rounded-[3rem] shadow-glass border border-white/60 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-600/5 rounded-full -mr-16 -mt-16 blur-3xl transition-all group-hover:bg-blue-600/10"></div>
            
            <div class="flex items-center gap-4 mb-8 relative">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shadow-inner border border-blue-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">Vue d'ensemble des Cours</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Progression des Apprentissages</p>
                </div>
            </div>

            <div class="flex items-center justify-between p-6 rounded-2xl bg-white/40 border border-slate-100/50 mb-4">
                <span class="font-black text-slate-600 uppercase tracking-widest text-xs">Cours Assignés</span>
                <span class="text-xl font-black text-slate-900">{{ count($assignedCourses) }}</span>
            </div>
            
            <div class="space-y-4">
                @foreach($assignedCourses as $course)
                <div class="p-5 rounded-2xl flex items-center justify-between bg-white/30 border border-slate-100">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-slate-100 text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <span class="font-bold text-slate-700">{{ $course->title }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Quiz Status -->
        <div class="glass p-10 rounded-[3rem] border border-white/60 shadow-glass relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-brand-600/5 rounded-full -mr-16 -mt-16 blur-3xl transition-all group-hover:bg-brand-600/10"></div>
            
            <div class="flex items-center gap-4 mb-8 relative">
                <div class="w-14 h-14 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center shadow-inner border border-brand-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">Performances Quizz</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Évaluation Continue</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-8">
                <div class="p-6 rounded-2xl bg-emerald-50 border border-emerald-100 flex flex-col items-center justify-center text-center">
                    <span class="text-3xl font-black text-emerald-600">{{ $student->results->count() }}</span>
                    <span class="text-[9px] font-black uppercase tracking-widest text-emerald-800 mt-1">Passés</span>
                </div>
                <div class="p-6 rounded-2xl bg-amber-50 border border-amber-100 flex flex-col items-center justify-center text-center">
                    <span class="text-3xl font-black text-amber-600">{{ count($quizzesNotTaken) }}</span>
                    <span class="text-[9px] font-black uppercase tracking-widest text-amber-800 mt-1">Manqués</span>
                </div>
            </div>

            <div class="space-y-4">
                @foreach($student->results as $result)
                <div class="p-5 rounded-2xl flex items-center justify-between border {{ $result->is_passed ? 'bg-emerald-50/50 border-emerald-100' : 'bg-red-50/50 border-red-100' }} group/row">
                    <div>
                        <p class="font-bold text-slate-700 text-sm">{{ $result->quiz->title ?? 'Quiz Supprimé' }}</p>
                        <p class="text-[10px] text-slate-400 uppercase font-bold mt-1">{{ $result->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-lg font-black {{ $result->is_passed ? 'text-emerald-600' : 'text-red-500' }}">
                            {{ $result->total_questions > 0 ? round(($result->score / $result->total_questions) * 100) : 0 }}%
                        </span>
                        
                        <!-- Delete Single Result -->
                        <form action="{{ route('admin.analytics.delete_result', $result->id) }}" method="POST" onsubmit="return confirm('Supprimer ce résultat ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-lg bg-white text-red-400 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center shadow-sm opacity-0 group-hover/row:opacity-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

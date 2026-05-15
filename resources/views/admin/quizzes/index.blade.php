@extends('layouts.admin')

@section('header', 'Evaluation Control')

@section('content')
<div class="grid grid-cols-1 gap-10">
    
    <!-- Quick Action Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center p-10 rounded-[3rem] glass border border-white/60 shadow-glass gap-6">
        <div>
            <h3 class="text-2xl font-black text-slate-800 tracking-tight">Active Quizzes: {{ $quizzes->count() }}</h3>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Assessment Management</p>
        </div>
        <a href="{{ route('admin.quizzes.create') }}" class="px-10 py-4 rounded-2xl bg-brand-600 text-white font-black text-sm shadow-glow hover:bg-brand-500 transition-all uppercase tracking-[0.1em]">
            SCHEDULE NEW QUIZ
        </a>
    </div>

    <!-- Quizzes Table -->
    <div class="p-10 rounded-[3rem] glass shadow-glass border border-white/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-slate-400 uppercase text-[10px] font-black tracking-[0.2em] border-b border-slate-100/50">
                        <th class="px-6 py-6">Evaluation Details</th>
                        <th class="px-6 py-6">Linked Module</th>
                        <th class="px-6 py-6">Visibility</th>
                        <th class="px-6 py-6 text-right">Operations</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50/50">
                    @forelse($quizzes as $quiz)
                    <tr class="hover:bg-brand-50/30 transition-colors duration-300 group">
                        <td class="px-6 py-6">
                            <div class="font-black text-slate-800 text-lg group-hover:text-brand-600 transition-colors">{{ $quiz->title }}</div>
                            <div class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter italic">ID: #{{ $quiz->id }}</div>
                        </td>
                        <td class="px-6 py-6">
                            <div class="text-sm font-bold text-slate-700">{{ $quiz->lesson->title }}</div>
                            <div class="flex flex-wrap gap-1 mt-1">
                                @if($quiz->lesson->course)
                                    @foreach($quiz->lesson->course->groups as $group)
                                    <span class="text-[8px] text-brand-600 uppercase font-black tracking-widest bg-brand-50 px-1.5 py-0.5 rounded border border-brand-100 italic">
                                        {{ $group->name }}
                                    </span>
                                    @endforeach
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <span class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-[0.1em] {{ $quiz->is_active ? 'bg-emerald-100/50 text-emerald-600 border border-emerald-200' : 'bg-red-100/50 text-red-600 border border-red-200' }}">
                                {{ $quiz->is_active ? '● LIVE' : '○ STAGED' }}
                            </span>
                        </td>
                        <td class="px-6 py-6 text-right flex justify-end gap-3">
                            <!-- Manage Questions -->
                            <a href="{{ route('admin.quizzes.questions.index', $quiz->id) }}" class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all duration-300 flex items-center justify-center" title="Build Content">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </a>
                            <!-- Edit Settings -->
                            <button onclick="openEditQuizModal({{ json_encode($quiz) }})" class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 hover:bg-brand-600 hover:text-white transition-all duration-300 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <!-- Delete -->
                            <form action="{{ route('admin.quizzes.destroy', $quiz->id) }}" method="POST" onsubmit="return confirm('Archive evaluation?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-10 h-10 rounded-xl bg-red-50 text-red-500 hover:bg-red-600 hover:text-white transition-all duration-300 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-20 text-center text-slate-300 font-black italic uppercase">No evaluations found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Quiz Modal -->
<div id="edit-quiz-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-xl bg-slate-900/40">
    <div class="glass p-12 rounded-[3rem] shadow-2xl max-w-lg w-full border border-white/60 animate-in fade-in zoom-in duration-300">
        <div class="flex justify-between items-center mb-10">
            <h4 class="text-3xl font-black text-slate-900 tracking-tight italic">Update Quiz</h4>
            <button onclick="document.getElementById('edit-quiz-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="edit-quiz-form" method="POST" class="space-y-8">
            @csrf @method('PUT')
            <div>
                <x-input-label value="Evaluation Title" class="ml-2 mb-1" />
                <x-text-input name="title" id="edit_quiz_title" required />
            </div>
            @php $lessons = \App\Models\Lesson::with('course.groups')->get(); @endphp
            <div>
                <x-input-label value="Linked Module" class="ml-2 mb-1" />
                <select name="lesson_id" id="edit_lesson_id" class="block w-full mt-1 bg-white/50 border border-slate-200 rounded-2xl py-4 px-6 text-slate-700 font-medium">
                    @foreach($lessons as $lesson)
                        <option value="{{ $lesson->id }}">{{ $lesson->title }} ({{ $lesson->course->title ?? 'No Course' }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label value="Passing Score (%)" class="ml-2 mb-1" />
                <x-text-input type="number" name="passing_score" id="edit_passing_score" min="0" max="100" required />
            </div>
            <div>
                <label class="flex items-center cursor-pointer space-x-4 p-4 rounded-2xl bg-white/30 border border-white/40">
                    <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center shadow-sm">
                        <input type="checkbox" name="is_active" id="edit_quiz_active" value="1" class="w-5 h-5 rounded border-slate-200 text-brand-600">
                    </div>
                    <span class="text-sm font-black text-slate-600 uppercase tracking-widest">Active Visibility</span>
                </label>
            </div>
            <div class="flex gap-4 pt-6">
                <button type="button" onclick="document.getElementById('edit-quiz-modal').classList.add('hidden')" class="flex-1 py-5 rounded-2xl bg-slate-100 font-black text-slate-500">Annuler</button>
                <x-primary-button class="flex-1 justify-center py-5 shadow-glow uppercase font-black">SYNC EVALUATION</x-primary-button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditQuizModal(quiz) {
        document.getElementById('edit-quiz-form').action = `/admin/quizzes/${quiz.id}`;
        document.getElementById('edit_quiz_title').value = quiz.title;
        document.getElementById('edit_lesson_id').value = quiz.lesson_id;
        document.getElementById('edit_passing_score').value = quiz.passing_score;
        document.getElementById('edit_quiz_active').checked = quiz.is_active;
        document.getElementById('edit-quiz-modal').classList.remove('hidden');
    }
</script>
@endsection

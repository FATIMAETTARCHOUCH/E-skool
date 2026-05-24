@extends('layouts.admin')

@section('header', 'Evaluation Control')

@section('content')
<div class="grid grid-cols-1 gap-6">
    
    <!-- Quick Action Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center p-6 rounded-lg bg-white border border-gray-200 gap-6">
        <div>
            <h3 class="text-lg font-bold text-gray-900 border-l-4 border-indigo-600 pl-3">quizzes (Active: {{ $quizzes->count() }})</h3>
            <p class="text-xs text-gray-500 font-semibold uppercase tracking-widest mt-1 ml-4">quizzes.title, quizzes.passing_score, quizzes.scheduled_at, quizzes.is_active</p>
        </div>
        <a href="{{ route('admin.quizzes.create') }}" class="px-4 py-2.5 rounded-lg bg-indigo-600 text-white font-medium text-sm hover:bg-indigo-700 transition-colors">
            Create Quiz (quizzes)
        </a>
    </div>

    <!-- Quizzes Table -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-gray-500 uppercase text-xs tracking-wide font-medium border-b border-gray-100 bg-gray-50">
                        <th class="px-6 py-3">quizzes.title, quizzes.id</th>
                        <th class="px-6 py-3">quizzes.lesson_id (lessons)</th>
                        <th class="px-6 py-3">quizzes.is_active</th>
                        <th class="px-6 py-3 text-right">Operations</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($quizzes as $quiz)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $quiz->title }}</div>
                            <div class="text-xs text-gray-400 font-semibold mt-1">quizzes.id: #{{ $quiz->id }} | quizzes.passing_score: {{ $quiz->passing_score }}%</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-700">{{ $quiz->lesson->title }}</div>
                            <div class="flex flex-wrap gap-1 mt-1">
                                @if($quiz->lesson->course)
                                    @foreach($quiz->lesson->course->groups as $group)
                                    <span class="text-[10px] text-indigo-700 font-semibold bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100">
                                        {{ $group->name }}
                                    </span>
                                    @endforeach
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-md text-xs font-medium border {{ $quiz->is_active ? 'bg-green-50 text-green-700 border-green-200' : 'bg-gray-100 text-gray-600 border-gray-200' }}">
                                {{ $quiz->is_active ? '● quizzes.is_active = 1' : '○ quizzes.is_active = 0' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right flex justify-end gap-2">
                            <!-- Manage Questions -->
                            <a href="{{ route('admin.quizzes.questions.index', $quiz->id) }}" class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 flex items-center justify-center border border-indigo-200 transition" title="Manage questions (questions table)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </a>
                            <!-- Edit Settings -->
                            <button onclick="openEditQuizModal({{ json_encode($quiz) }})" class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 flex items-center justify-center border border-indigo-200 transition" title="Edit quizzes fields">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <!-- Delete -->
                            <form action="{{ route('admin.quizzes.destroy', $quiz->id) }}" method="POST" onsubmit="return confirm('Archive this quiz?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center border border-red-200 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">No quizzes found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Quiz Modal -->
<div id="edit-quiz-modal" class="fixed inset-0 z-50 items-center justify-center p-4 bg-black/50 hidden">
    <div class="bg-white p-6 rounded-lg border border-gray-200 max-w-lg w-full">
        <div class="flex justify-between items-center mb-6">
            <h4 class="text-lg font-bold text-gray-900">Update Quiz</h4>
            <button onclick="document.getElementById('edit-quiz-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="edit-quiz-form" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <x-input-label value="Evaluation Title" />
                <x-text-input name="title" id="edit_quiz_title" required class="w-full mt-1" />
            </div>
            @php $lessons = \App\Models\Lesson::with('course.groups')->get(); @endphp
            <div>
                <x-input-label value="Linked Module" />
                <select name="lesson_id" id="edit_lesson_id" class="block w-full mt-1 bg-gray-50 border border-gray-200 focus:border-indigo-400 focus:outline-none focus:bg-white rounded-lg py-2.5 px-4 text-gray-700 text-sm transition">
                    @foreach($lessons as $lesson)
                        <option value="{{ $lesson->id }}">{{ $lesson->title }} ({{ $lesson->course->title ?? 'No Course' }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label value="Passing Score (%)" />
                <x-text-input type="number" name="passing_score" id="edit_passing_score" min="0" max="100" required class="w-full mt-1" />
            </div>
            <div>
                <label class="flex items-center cursor-pointer space-x-3 p-3 rounded-lg bg-indigo-50 border border-indigo-200 mt-2">
                    <input type="checkbox" name="is_active" id="edit_quiz_active" value="1" class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm font-medium text-gray-700">Active Visibility</span>
                </label>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="document.getElementById('edit-quiz-modal').classList.add('hidden')" class="flex-1 py-2.5 rounded-lg bg-gray-100 text-gray-600 font-bold hover:bg-gray-200 transition-colors text-sm border border-transparent">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 rounded-lg bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-colors text-sm">
                    Sync Evaluation
                </button>
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

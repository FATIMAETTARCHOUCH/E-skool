@extends('layouts.admin')

@section('header')
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.quizzes.index') }}" class="p-2 rounded-xl shadow-neumorphic-btn text-gray-500 hover:shadow-neumorphic-btn-active">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Schedule New Quiz</h2>
    </div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto p-10 rounded-[2.5rem] shadow-neumorphic bg-neu-base">
    <form action="{{ route('admin.quizzes.store') }}" method="POST" class="space-y-8">
        @csrf
        
        <div>
            <x-input-label for="title" value="quizzes.title - Quiz Title" />
            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" required placeholder="e.g. Weekly Assessment #1" />
        </div>

        <div>
            <x-input-label for="lesson_id" value="quizzes.lesson_id - Associated Lesson" />
            <select name="lesson_id" id="lesson_id" required class="block w-full mt-1 bg-white/50 border border-slate-200 rounded-2xl py-4 px-6 text-slate-700 font-medium">
                @foreach($lessons as $lesson)
                    <option value="{{ $lesson->id }}">{{ $lesson->title }} ({{ $lesson->course ? $lesson->course->groups->pluck('name')->implode(', ') : 'No Groups' }})</option>
                @endforeach
            </select>
            <p class="text-[10px] text-slate-400 mt-2 italic">Links to lessons table. All groups assigned to this lesson will see this quiz.</p>
        </div>

        <div>
            <x-input-label value="quizzes.passing_score - Passing Score (%)" />
            <x-text-input class="block mt-1 w-full" type="number" name="passing_score" value="80" min="0" max="100" required />
            <p class="text-xs text-slate-400 mt-1 italic">0-100 (stored as number in quizzes.passing_score)</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div>
                <x-input-label for="scheduled_at" value="quizzes.scheduled_at - Schedule Date (Optional)" />
                <x-text-input id="scheduled_at" class="block mt-1 w-full" type="datetime-local" name="scheduled_at" />
            </div>

            <div class="pt-6">
                <label class="flex items-center cursor-pointer space-x-4">
                    <div class="w-12 h-12 rounded-xl shadow-neumorphic-inset flex items-center justify-center bg-neu-base">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-none text-primary focus:ring-0 bg-transparent w-6 h-6" checked>
                    </div>
                    <div>
                        <span class="text-sm font-black text-gray-700 uppercase tracking-widest">quizzes.is_active - Activate</span>
                        <p class="text-[10px] text-gray-400">Unchecked = hidden from students.</p>
                    </div>
                </label>
            </div>
        </div>

        <div class="flex gap-4 pt-6">
            <a href="{{ route('admin.quizzes.index') }}" class="flex-1 py-4 text-center rounded-2xl shadow-neumorphic-btn font-bold text-gray-500 transition hover:shadow-neumorphic-btn-active">Cancel</a>
            <x-primary-button class="flex-1 justify-center py-4 text-lg">
                Create Quiz (quizzes)
            </x-primary-button>
        </div>
    </form>
</div>
@endsection

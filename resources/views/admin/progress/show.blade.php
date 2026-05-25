@extends('layouts.admin')

@section('header', 'Student Progress Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="space-y-6">
        <div class="bg-white border border-gray-200 rounded-lg p-6 flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-widest text-gray-500 font-medium">Student</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $student->first_name }} {{ $student->last_name }}</h3>
            </div>
            <div class="text-sm text-gray-500">{{ $student->group?->name ?? 'Unassigned' }}</div>
        </div>

        <div class="space-y-4">
            @foreach($chapters as $chapter)
                @php
                    $progress = $progresses->get($chapter->id);
                    $attempts = $attemptsByChapter->get($chapter->id, collect());
                @endphp
                <details class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <summary class="cursor-pointer px-6 py-4 flex items-center justify-between">
                        <div>
                            <div class="font-bold text-gray-900">{{ $chapter->title }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">Status: {{ $progress->status ?? 'locked' }}</div>
                        </div>
                        <div class="text-sm text-gray-500">{{ $attempts->count() }} attempt(s)</div>
                    </summary>
                    <div class="px-6 pb-6">
                        <div class="space-y-3">
                            @forelse($attempts as $attempt)
                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 flex items-center justify-between">
                                    <div>
                                        <div class="font-semibold text-gray-900">Attempt #{{ $attempt->attempt_number }}</div>
                                        <div class="text-xs text-gray-500">{{ optional($attempt->created_at)->format('Y-m-d H:i') }}</div>
                                    </div>
                                    <div class="font-bold text-gray-800">Score: {{ $attempt->score }}</div>
                                </div>
                            @empty
                                <div class="text-sm text-gray-400 italic">No attempts yet.</div>
                            @endforelse
                        </div>
                    </div>
                </details>
            @endforeach
        </div>
    </div>
</div>
@endsection

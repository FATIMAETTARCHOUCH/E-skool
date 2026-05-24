@extends('layouts.admin')

@section('header', 'Student Progress')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="space-y-6">
        <div class="bg-white border border-gray-200 rounded-lg p-6 flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-widest text-gray-500 font-medium">Group</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $group->name }}</h3>
            </div>
            <div class="text-sm text-gray-500">{{ $students->count() }} students</div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-gray-500 text-xs uppercase tracking-wide font-medium border-b border-gray-100 bg-gray-50">
                            <th class="px-6 py-3">Student name</th>
                            <th class="px-6 py-3">Lesson name</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Attempts</th>
                            <th class="px-6 py-3">Last score</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($students as $student)
                            @foreach($lessons as $lesson)
                                @php
                                    $progress = optional($progresses->get($student->id))->firstWhere('lesson_id', $lesson->id);
                                    $status = $progress->status ?? 'locked';
                                    $attempts = $student->results->where('quiz.lesson_id', $lesson->id)->count();
                                    $lastScore = optional($student->results->where('quiz.lesson_id', $lesson->id)->sortByDesc('attempt_number')->first())->score;
                                    $badgeClass = 'bg-gray-100 text-gray-700 border-gray-200';

                                    if ($status === 'in_remediation') {
                                        $badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                                    } elseif ($status === 'passed') {
                                        $badgeClass = 'bg-green-50 text-green-700 border-green-200';
                                    } elseif ($status === 'passed_with_help') {
                                        $badgeClass = 'bg-teal-50 text-teal-700 border-teal-200';
                                    } elseif ($status === 'stuck') {
                                        $badgeClass = 'bg-red-50 text-red-700 border-red-200';
                                    }
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-gray-900">{{ $student->first_name }} {{ $student->last_name }}</td>
                                    <td class="px-6 py-4 text-gray-700">{{ $lesson->title }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-md text-xs font-medium uppercase border {{ $badgeClass }}">
                                            {{ $status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">{{ $attempts }}</td>
                                    <td class="px-6 py-4">{{ $lastScore ?? '—' }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('header')
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.groups.index') }}" class="p-2 rounded-xl shadow-neumorphic-btn text-gray-500 hover:shadow-neumorphic-btn-active">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $group->name }} - Students</h2>
            <p class="text-sm text-gray-500">{{ $group->branch->school->name }} | {{ $group->academicYear->name }}</p>
        </div>
    </div>
@endsection

@section('content')
<div class="p-8 rounded-[2rem] shadow-neumorphic bg-neu-base">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-gray-500 uppercase text-xs font-bold border-b border-gray-200">
                    <th class="px-6 py-4">Code Massar</th>
                    <th class="px-6 py-4">Full Name</th>
                    <th class="px-6 py-4">First Login</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($group->users as $student)
                <tr class="hover:bg-gray-50/50 transition duration-150">
                    <td class="px-6 py-5 font-mono text-sm text-primary font-bold">{{ $student->massar_code }}</td>
                    <td class="px-6 py-5 font-medium text-gray-800">{{ $student->first_name }} {{ $student->last_name }}</td>
                    <td class="px-6 py-5">
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $student->is_first_login ? 'bg-orange-50 text-orange-600' : 'bg-emerald-50 text-emerald-600' }}">
                            {{ $student->is_first_login ? 'Pending' : 'Completed' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-10 text-center text-gray-500 italic rounded-xl shadow-neumorphic-inset mt-4">
                        No students found in this group. Use the import feature to add students.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

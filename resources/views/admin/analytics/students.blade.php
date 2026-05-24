@extends('layouts.admin')

@section('header', 'Student Ecosystem')

@section('content')
<div class="space-y-6">
    
    <!-- Search & Filter Bento -->
    <div class="bg-white p-6 rounded-lg border border-gray-200">
        <form action="{{ route('admin.analytics.students') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
            <div class="md:col-span-6 space-y-2">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Search Parameters</label>
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or Massar Code..." class="block w-full bg-gray-50 border border-gray-200 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 rounded-lg py-2.5 pl-12 pr-4 text-gray-700 text-sm transition-all outline-none">
                </div>
            </div>
            
            <div class="md:col-span-4 space-y-2">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Filter by Group</label>
                <select name="group_id" class="block w-full bg-gray-50 border border-gray-200 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 rounded-lg py-2.5 px-4 text-gray-700 text-sm outline-none cursor-pointer">
                    <option value="">All Groups</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <button type="submit" class="w-full justify-center py-2.5 px-4 rounded-lg bg-indigo-600 text-white font-medium text-sm hover:bg-indigo-700 transition-colors">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Students Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($students as $student)
        <a href="{{ route('admin.analytics.student_profile', $student->id) }}" class="bg-white p-6 rounded-lg border border-gray-200 hover:border-indigo-300 transition-all duration-300 group block relative">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg border border-indigo-100 group-hover:scale-105 transition-transform">
                    {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                </div>
                <div>
                    <h4 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition-colors leading-tight">{{ $student->first_name }} {{ $student->last_name }}</h4>
                    <p class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider mt-0.5">{{ $student->massar_code }}</p>
                </div>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between items-center p-3 rounded-lg bg-gray-50 border border-gray-100">
                    <span class="text-[10px] font-semibold text-gray-500 uppercase">Placement</span>
                    <span class="text-xs font-bold text-gray-700">{{ $student->group ? $student->group->name : 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center p-3 rounded-lg bg-gray-50 border border-gray-100">
                    <span class="text-[10px] font-semibold text-gray-500 uppercase">Last Sync</span>
                    <span class="text-xs font-bold text-gray-700">
                        {{ $student->last_login_at ? $student->last_login_at->diffForHumans() : 'Never' }}
                    </span>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-center gap-2 text-indigo-600 font-semibold text-[10px] uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-opacity">
                View Full Profile
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </div>
        </a>
        @empty
        <div class="col-span-full py-20 text-center bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
            <h4 class="text-xl font-bold text-gray-400 italic">Identity Check Failed</h4>
            <p class="text-sm text-gray-500 mt-2">No students match your current filter parameters.</p>
        </div>
        @endforelse
    </div>

</div>
@endsection

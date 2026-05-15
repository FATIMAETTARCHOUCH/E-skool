@extends('layouts.admin')

@section('header', 'Student Ecosystem')

@section('content')
<div class="space-y-10">
    
    <!-- Search & Filter Bento -->
    <div class="glass p-10 rounded-[3rem] border border-white/60 shadow-glass">
        <form action="{{ route('admin.analytics.students') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
            <div class="md:col-span-6 space-y-3">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Search Parameters</label>
                <div class="relative">
                    <svg class="absolute left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or Massar Code..." class="block w-full bg-white/40 backdrop-blur-sm border border-slate-200 focus:border-brand-500 focus:ring-8 focus:ring-brand-500/5 rounded-3xl py-5 pl-16 pr-6 text-slate-700 font-bold transition-all outline-none">
                </div>
            </div>
            
            <div class="md:col-span-4 space-y-3">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Filter by Group</label>
                <select name="group_id" class="block w-full bg-white/40 backdrop-blur-sm border border-slate-200 focus:border-brand-500 focus:ring-8 focus:ring-brand-500/5 rounded-3xl py-5 px-6 text-slate-700 font-bold outline-none appearance-none cursor-pointer">
                    <option value="">All Groups</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <x-primary-button class="w-full justify-center py-5 shadow-glow">
                    FILTER
                </x-primary-button>
            </div>
        </form>
    </div>

    <!-- Students Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($students as $student)
        <a href="{{ route('admin.analytics.student_profile', $student->id) }}" class="glass p-8 rounded-[2.5rem] border border-white/60 shadow-glass hover:border-brand-300 transition-all duration-500 group relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-brand-600/5 rounded-full -mr-12 -mt-12 blur-2xl group-hover:bg-brand-600/10"></div>
            
            <div class="flex items-center gap-6 mb-8">
                <div class="w-16 h-16 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center font-black text-xl shadow-inner border border-brand-100 group-hover:scale-110 transition-transform">
                    {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                </div>
                <div>
                    <h4 class="text-xl font-black text-slate-900 group-hover:text-brand-600 transition-colors leading-tight">{{ $student->first_name }} <br/>{{ $student->last_name }}</h4>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter mt-1">{{ $student->massar_code }}</p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex justify-between items-center p-4 rounded-2xl bg-slate-50 border border-white/50">
                    <span class="text-[10px] font-black text-slate-400 uppercase">Placement</span>
                    <span class="text-xs font-bold text-slate-700">{{ $student->group ? $student->group->name : 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center p-4 rounded-2xl bg-slate-50 border border-white/50">
                    <span class="text-[10px] font-black text-slate-400 uppercase">Last Sync</span>
                    <span class="text-xs font-bold text-slate-700">
                        {{ $student->last_login_at ? $student->last_login_at->diffForHumans() : 'Never' }}
                    </span>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-center gap-2 text-brand-600 font-black text-[10px] uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-opacity">
                VIEW FULL PROFILE
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </div>
        </a>
        @empty
        <div class="col-span-full py-40 text-center glass rounded-[4rem] border-2 border-dashed border-slate-200">
            <h4 class="text-3xl font-black text-slate-200 italic tracking-tighter uppercase leading-none">Identity Check Failed</h4>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-[0.2em] mt-2">No students match your current filter parameters.</p>
        </div>
        @endforelse
    </div>

</div>
@endsection

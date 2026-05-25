@extends('layouts.app')

@section('header')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-6">
            <a href="{{ route('student.dashboard') }}" class="w-12 h-12 rounded-2xl glass flex items-center justify-center text-slate-500 hover:text-brand-600 transition-all border border-white/40 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">Performance <span class="text-brand-600 italic">Analytics</span></h2>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Detailed Assessment Insight</p>
            </div>
        </div>
        <div class="glass px-8 py-4 rounded-3xl border border-white/60 shadow-glass text-center">
            <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Global Proficiency</span>
            <span class="block text-3xl font-black text-brand-600">{{ number_format($averageScore, 1) }}%</span>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
    
    <!-- Results Table Bento -->
    <div class="glass p-10 rounded-[3rem] border border-white/60 shadow-glass overflow-hidden">
        <h3 class="text-xl font-black text-slate-800 mb-10 flex items-center gap-3">
            <svg class="w-6 h-6 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            Examination History
        </h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-slate-400 uppercase text-[10px] font-black tracking-[0.2em] border-b border-slate-100/50">
                        <th class="px-6 py-6">Module & Assessment</th>
                        <th class="px-6 py-6 text-center">Result Metric</th>
                        <th class="px-6 py-6 text-center">Outcome</th>
                        <th class="px-6 py-6 text-right">Completion Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50/50">
                    @forelse($results as $result)
                    @php 
                        $percentage = $result->total_questions > 0 ? ($result->score / $result->total_questions) * 100 : 0; 
                        $passed = $percentage >= 50;
                    @endphp
                    <tr class="hover:bg-brand-50/30 transition-colors duration-300 group">
                        <td class="px-6 py-6">
                            <div class="font-black text-slate-800 text-lg group-hover:text-brand-600 transition-colors leading-none">
                                {{ $result->quiz->title ?? 'Untitled Assessment' }}
                            </div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase mt-2 tracking-tighter italic">
                                Linked Module: {{ $result->quiz->chapter->title ?? 'N/A' }}
                            </p>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 font-black text-sm text-slate-700">
                                {{ $result->score }} <span class="text-slate-400">/ {{ $result->total_questions }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <span class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest border {{ $passed ? 'bg-emerald-100/50 text-emerald-600 border-emerald-200' : 'bg-red-100/50 text-red-600 border-red-200' }}">
                                {{ $passed ? 'Success' : 'Retake Advised' }}
                            </span>
                        </td>
                        <td class="px-6 py-6 text-right">
                            <span class="text-xs font-bold text-slate-400 tracking-tight">{{ $result->created_at->format('M d, Y') }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-20 text-center text-slate-300 font-black italic uppercase">No analytical data available.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

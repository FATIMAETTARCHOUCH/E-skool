@extends('layouts.admin')

@section('header', 'Cohort Management')

@section('content')
<div class="grid grid-cols-1 gap-10">
    
    <!-- Quick Stats Header -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="p-8 rounded-4xl glass shadow-glass flex items-center justify-between group hover:bg-brand-600 transition-all duration-500">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest group-hover:text-brand-100 transition-colors">Active Groups</p>
                <h3 class="text-3xl font-black text-slate-900 mt-1 group-hover:text-white transition-colors">{{ $groups->count() }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 group-hover:bg-white/20 group-hover:text-white transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Groups Bento Table -->
    <div class="p-10 rounded-[3rem] glass shadow-glass border border-white/60">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12 gap-6">
            <div>
                <h3 class="text-2xl font-black text-slate-800 tracking-tight">Existing Cohorts</h3>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Classroom Architecture</p>
            </div>
            <a href="{{ route('admin.groups.create') }}" class="px-10 py-4 rounded-2xl bg-brand-600 text-white font-black text-sm shadow-glow hover:bg-brand-500 transition-all uppercase tracking-[0.1em]">
                NEW GROUP
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-slate-400 uppercase text-[10px] font-black tracking-[0.2em] border-b border-slate-100/50">
                        <th class="px-6 py-6">Group Label</th>
                        <th class="px-6 py-6">Structural Link</th>
                        <th class="px-6 py-6">Session</th>
                        <th class="px-6 py-6 text-right">Operations</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50/50">
                    @forelse($groups as $group)
                    <tr class="hover:bg-brand-50/30 transition-colors duration-300 group">
                        <td class="px-6 py-6 font-black text-slate-800 text-lg group-hover:text-brand-600 transition-colors">{{ $group->name }}</td>
                        <td class="px-6 py-6">
                            <div class="text-sm font-bold text-slate-700">{{ $group->branch->name }}</div>
                            <div class="text-[9px] text-slate-400 uppercase font-black">{{ $group->branch->school->name }}</div>
                        </td>
                        <td class="px-6 py-6">
                            <span class="px-3 py-1 rounded-full bg-slate-100 text-[10px] font-black text-slate-500">
                                {{ $group->academicYear->name }}
                            </span>
                        </td>
                        <td class="px-6 py-6 text-right flex justify-end gap-3">
                            <!-- View Students -->
                            <a href="{{ route('admin.groups.show', $group->id) }}" class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 hover:bg-brand-600 hover:text-white transition-all duration-300 flex items-center justify-center" title="Profiles">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </a>
                            <!-- Edit Button -->
                            <button onclick="openEditGroupModal({{ json_encode($group) }})" class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all duration-300 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <!-- Import -->
                            <button onclick="document.getElementById('import-modal-{{ $group->id }}').classList.remove('hidden')" class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all duration-300 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            </button>
                            <!-- Delete -->
                            <form action="{{ route('admin.groups.destroy', $group->id) }}" method="POST" onsubmit="return confirm('Archive group?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-10 h-10 rounded-xl bg-red-50 text-red-500 hover:bg-red-600 hover:text-white transition-all duration-300 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    
                    <!-- Import Modal -->
                    <div id="import-modal-{{ $group->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-xl bg-slate-900/40">
                        <div class="glass p-12 rounded-[3rem] shadow-2xl max-w-md w-full border border-white/60">
                            <h4 class="text-2xl font-black text-slate-900 mb-2">Import Students</h4>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-8">Target: {{ $group->name }}</p>
                            <form action="{{ route('admin.groups.import', $group->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                                @csrf
                                <div class="p-8 rounded-[2rem] bg-white/40 border-2 border-dashed border-slate-200 text-center">
                                    <input type="file" name="csv_file" required class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-brand-600 file:text-white">
                                    <p class="text-[10px] text-slate-400 mt-4 font-bold italic underline">CSV: first_name, last_name, age, massar_code</p>
                                </div>
                                <div class="flex gap-4">
                                    <button type="button" onclick="document.getElementById('import-modal-{{ $group->id }}').classList.add('hidden')" class="flex-1 py-4 rounded-xl bg-slate-100 font-black text-slate-500">ABORT</button>
                                    <button type="submit" class="flex-1 py-4 rounded-xl bg-brand-600 text-white font-black shadow-glow">UPLOAD</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @empty
                    <tr><td colspan="4" class="px-6 py-20 text-center text-slate-300 font-black italic uppercase">Database Empty</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Group Modal -->
<div id="edit-group-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-xl bg-slate-900/40">
    <div class="glass p-12 rounded-[3rem] shadow-2xl max-w-lg w-full border border-white/60 animate-in fade-in zoom-in duration-300">
        <div class="flex justify-between items-center mb-10">
            <h4 class="text-3xl font-black text-slate-900 tracking-tight italic">Update Cohort</h4>
            <button onclick="document.getElementById('edit-group-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="edit-group-form" method="POST" class="space-y-8">
            @csrf @method('PUT')
            <div>
                <x-input-label value="Cohort Label" class="ml-2 mb-1" />
                <x-text-input name="name" id="edit_group_name" required />
            </div>
            @php
                $branches = \App\Models\Branch::with('school')->get();
                $years = \App\Models\AcademicYear::all();
            @endphp
            <div>
                <x-input-label value="Structural Link (Branch)" class="ml-2 mb-1" />
                <select name="branch_id" id="edit_branch_id" class="block w-full mt-1 bg-white/50 border border-slate-200 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 rounded-2xl py-4 px-6 text-slate-700 font-medium">
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }} ({{ $branch->school->name }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label value="Academic Session" class="ml-2 mb-1" />
                <select name="academic_year_id" id="edit_year_id" class="block w-full mt-1 bg-white/50 border border-slate-200 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 rounded-2xl py-4 px-6 text-slate-700 font-medium">
                    @foreach($years as $year)
                        <option value="{{ $year->id }}">{{ $year->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-4 pt-6">
                <button type="button" onclick="document.getElementById('edit-group-modal').classList.add('hidden')" class="flex-1 py-5 rounded-2xl bg-slate-100 font-black text-slate-500">Annuler</button>
                <x-primary-button class="flex-1 justify-center py-5 shadow-glow uppercase font-black">SYNC CHANGES</x-primary-button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditGroupModal(group) {
        document.getElementById('edit-group-form').action = `/admin/groups/${group.id}`;
        document.getElementById('edit_group_name').value = group.name;
        document.getElementById('edit_branch_id').value = group.branch_id;
        document.getElementById('edit_year_id').value = group.academic_year_id;
        document.getElementById('edit-group-modal').classList.remove('hidden');
    }
</script>
@endsection

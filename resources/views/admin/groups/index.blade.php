@extends('layouts.admin')

@section('header', 'Cohort Management')

@section('content')
<div class="grid grid-cols-1 gap-6">
    
    <!-- Quick Stats Header -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="p-6 rounded-lg bg-white border border-gray-200 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">Active Groups</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $groups->count() }}</h3>
            </div>
            <div class="w-12 h-12 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Groups Bento Table -->
    <div class="bg-white rounded-lg border border-gray-200 pt-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center px-6 mb-6 gap-6">
            <div>
                <h3 class="text-lg font-bold text-gray-900 border-l-4 border-indigo-600 pl-3">Existing Cohorts</h3>
            </div>
            <a href="{{ route('admin.groups.create') }}" class="px-4 py-2.5 rounded-lg bg-indigo-600 text-white font-medium text-sm hover:bg-indigo-700 transition-colors">
                New Group
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-t border-gray-100">
                <thead>
                    <tr class="text-gray-500 uppercase text-xs tracking-wide font-medium bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-3">Group Label</th>
                        <th class="px-6 py-3">Structural Link</th>
                        <th class="px-6 py-3">Session</th>
                        <th class="px-6 py-3 text-right">Operations</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($groups as $group)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-bold text-gray-900">{{ $group->name }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-700">{{ $group->branch->name }}</div>
                            <div class="text-[10px] text-gray-400 uppercase font-semibold">{{ $group->branch->school->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-md bg-gray-100 text-xs font-medium text-gray-600 border border-gray-200">
                                {{ $group->academicYear->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right flex justify-end gap-2">
                            <!-- View Students -->
                            <a href="{{ route('admin.groups.show', $group->id) }}" class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 flex items-center justify-center border border-indigo-200 transition" title="Profiles">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </a>
                            <!-- Edit Button -->
                            <button onclick="openEditGroupModal({{ json_encode($group) }})" class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 flex items-center justify-center border border-indigo-200 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <!-- Import -->
                            <button onclick="document.getElementById('import-modal-{{ $group->id }}').classList.remove('hidden')" class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 flex items-center justify-center border border-emerald-200 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            </button>
                            <!-- Delete -->
                            <form action="{{ route('admin.groups.destroy', $group->id) }}" method="POST" onsubmit="return confirm('Archive group?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center border border-red-200 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    
                    <!-- Import Modal -->
                    <div id="import-modal-{{ $group->id }}" class="fixed inset-0 z-50 items-center justify-center p-4 bg-black/50 hidden">
                        <div class="bg-white p-6 rounded-lg border border-gray-200 max-w-md w-full">
                            <h4 class="text-lg font-bold text-gray-900 mb-1">Import Students</h4>
                            <p class="text-xs text-gray-500 font-medium tracking-wide mb-6">Target: {{ $group->name }}</p>
                            <form action="{{ route('admin.groups.import', $group->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <div class="p-4 rounded-lg bg-gray-50 border border-gray-200 text-center">
                                    <input type="file" name="xlsx_file" accept=".xlsx" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition">
                                    <p class="text-xs text-gray-500 mt-3 font-semibold">XLSX: first_name, last_name, massar_code</p>
                                </div>
                                <div class="flex gap-3 pt-2">
                                    <button type="button" onclick="document.getElementById('import-modal-{{ $group->id }}').classList.add('hidden')" class="flex-1 py-2.5 rounded-lg bg-gray-100 text-gray-600 font-bold hover:bg-gray-200 border border-transparent text-sm transition">Cancel</button>
                                    <button type="submit" class="flex-1 py-2.5 rounded-lg bg-indigo-600 text-white font-bold hover:bg-indigo-700 text-sm transition">Upload</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @empty
                    <tr><td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">No groups found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Group Modal -->
<div id="edit-group-modal" class="fixed inset-0 z-50 items-center justify-center p-4 bg-black/50 hidden">
    <div class="bg-white p-6 rounded-lg border border-gray-200 max-w-lg w-full">
        <div class="flex justify-between items-center mb-6">
            <h4 class="text-lg font-bold text-gray-900 border-l-4 border-indigo-600 pl-3">Update Cohort</h4>
            <button onclick="document.getElementById('edit-group-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="edit-group-form" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <x-input-label value="Cohort Label" />
                <x-text-input name="name" id="edit_group_name" required />
            </div>
            @php
                $branches = \App\Models\Branch::with('school')->get();
                $years = \App\Models\AcademicYear::all();
            @endphp
            <div>
                <x-input-label value="Structural Link (Branch)" />
                <select name="branch_id" id="edit_branch_id" class="block w-full mt-1 bg-gray-50 border border-gray-200 focus:border-indigo-400 focus:outline-none focus:bg-white rounded-lg py-2.5 px-4 text-gray-700 text-sm transition">
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }} ({{ $branch->school->name }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label value="Academic Session" />
                <select name="academic_year_id" id="edit_year_id" class="block w-full mt-1 bg-gray-50 border border-gray-200 focus:border-indigo-400 focus:outline-none focus:bg-white rounded-lg py-2.5 px-4 text-gray-700 text-sm transition">
                    @foreach($years as $year)
                        <option value="{{ $year->id }}">{{ $year->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="document.getElementById('edit-group-modal').classList.add('hidden')" class="flex-1 py-2.5 rounded-lg bg-gray-100 text-gray-600 font-bold hover:bg-gray-200 text-sm transition-colors border border-transparent">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 rounded-lg bg-indigo-600 text-white font-bold hover:bg-indigo-700 text-sm transition-colors">
                    Sync Changes
                </button>
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

@extends('layouts.admin')

@section('header', 'Student Management')

@section('content')
<div class="grid grid-cols-1 gap-8">
    
    <!-- Student Statistics Header -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="p-8 rounded-4xl glass shadow-glass flex items-center justify-between group hover:bg-brand-600 transition-all duration-500">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest group-hover:text-brand-100 transition-colors">Total Students</p>
                <h3 class="text-3xl font-black text-slate-900 mt-1 group-hover:text-white transition-colors">{{ $students->count() }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 group-hover:bg-white/20 group-hover:text-white transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
        </div>
        <!-- Search Filter -->
        <div class="md:col-span-2 p-4 rounded-4xl bg-white/40 backdrop-blur-md border border-white/40 shadow-inner flex items-center px-8">
            <svg class="w-5 h-5 text-slate-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" placeholder="Quick search students..." class="bg-transparent border-none focus:ring-0 w-full text-slate-700 placeholder-slate-400 font-medium">
        </div>
        <div class="flex items-center justify-end">
            <button onclick="document.getElementById('add-student-modal').classList.remove('hidden')" class="w-full h-full rounded-4xl bg-brand-600 text-white font-black text-sm shadow-glow hover:bg-brand-500 transition-all flex items-center justify-center gap-2 py-6">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                ADD NEW STUDENT
            </button>
        </div>
    </div>

    <!-- Manual Add Student Modal -->
    <div id="add-student-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-xl bg-slate-900/40">
        <div class="glass p-12 rounded-[3rem] shadow-2xl max-w-lg w-full border border-white/60 animate-in fade-in zoom-in duration-300">
            <div class="flex justify-between items-center mb-10">
                <h4 class="text-3xl font-black text-slate-900 tracking-tight">New Student</h4>
                <button onclick="document.getElementById('add-student-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="{{ route('admin.students.store') }}" method="POST" class="space-y-8">
                @csrf
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <x-input-label value="First Name" class="ml-2 mb-2" />
                        <x-text-input name="first_name" required placeholder="John" />
                    </div>
                    <div>
                        <x-input-label value="Last Name" class="ml-2 mb-2" />
                        <x-text-input name="last_name" required placeholder="Doe" />
                    </div>
                </div>
                <div>
                    <x-input-label value="Massar Code" class="ml-2 mb-2" />
                    <x-text-input name="massar_code" required placeholder="G123456789" />
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <x-input-label value="Age" class="ml-2 mb-2" />
                        <x-text-input name="age" type="number" required placeholder="15" />
                    </div>
                    <div>
                        <x-input-label value="Target Group" class="ml-2 mb-2" />
                        <select name="group_id" required class="block w-full mt-1 bg-white/50 border border-slate-200 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 rounded-2xl py-4 px-6 text-slate-700 focus:outline-none transition duration-200">
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex gap-4 pt-6">
                    <button type="button" onclick="document.getElementById('add-student-modal').classList.add('hidden')" class="flex-1 py-5 rounded-2xl bg-slate-100 font-black text-slate-500 hover:bg-slate-200 transition-colors uppercase tracking-widest text-xs">Annuler</button>
                    <x-primary-button class="flex-1 justify-center py-5">
                        SAVE STUDENT
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Student Modal -->
    <div id="edit-student-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-xl bg-slate-900/40">
        <div class="glass p-12 rounded-[3rem] shadow-2xl max-w-lg w-full border border-white/60 animate-in fade-in zoom-in duration-300">
            <div class="flex justify-between items-center mb-10">
                <h4 class="text-3xl font-black text-slate-900 tracking-tight italic">Update Records</h4>
                <button onclick="document.getElementById('edit-student-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form id="edit-student-form" method="POST" class="space-y-8">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <x-input-label value="First Name" />
                        <x-text-input name="first_name" id="edit_first_name" required />
                    </div>
                    <div>
                        <x-input-label value="Last Name" />
                        <x-text-input name="last_name" id="edit_last_name" required />
                    </div>
                </div>
                <div>
                    <x-input-label value="Massar Code" />
                    <x-text-input name="massar_code" id="edit_massar_code" required />
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <x-input-label value="Age" />
                        <x-text-input name="age" id="edit_age" type="number" required />
                    </div>
                    <div>
                        <x-input-label value="Current Group" />
                        <select name="group_id" id="edit_group_id" required class="block w-full mt-1 bg-white/50 border border-slate-200 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 rounded-2xl py-4 px-6 text-slate-700 focus:outline-none transition duration-200">
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex gap-4 pt-6">
                    <button type="button" onclick="document.getElementById('edit-student-modal').classList.add('hidden')" class="flex-1 py-5 rounded-2xl bg-slate-100 font-black text-slate-500 hover:bg-slate-200 transition-colors uppercase tracking-widest text-xs">Abort</button>
                    <x-primary-button class="flex-1 justify-center py-5 shadow-glow uppercase font-black">
                        SYNC DATA
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <!-- Students Table -->
    <div class="p-10 rounded-4xl glass shadow-glass border border-white/40 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-slate-400 uppercase text-[10px] font-black tracking-[0.2em] border-b border-slate-100/50">
                        <th class="px-6 py-6">Identity</th>
                        <th class="px-6 py-6">Institutional Code</th>
                        <th class="px-6 py-6">Placement</th>
                        <th class="px-6 py-6">Status</th>
                        <th class="px-6 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50/50">
                    @forelse($students as $student)
                    <tr class="hover:bg-brand-50/30 transition-colors duration-300 group">
                        <td class="px-6 py-6">
                            <div class="font-black text-slate-800 text-lg group-hover:text-brand-600 transition-colors">{{ $student->first_name }} {{ $student->last_name }}</div>
                            <div class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter italic">Student Profile #{{ $student->id }}</div>
                        </td>
                        <td class="px-6 py-6">
                            <span class="px-4 py-2 rounded-xl bg-slate-100 font-mono font-black text-xs text-slate-600 group-hover:bg-brand-100 group-hover:text-brand-700 transition-colors">
                                {{ $student->massar_code }}
                            </span>
                        </td>
                        <td class="px-6 py-6">
                            <div class="text-sm font-black text-slate-700">{{ $student->group ? $student->group->name : 'Unassigned' }}</div>
                            <div class="text-[10px] text-slate-400 font-bold uppercase">{{ $student->group ? $student->group->branch->school->name : '-' }}</div>
                        </td>
                        <td class="px-6 py-6">
                            <span class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest {{ $student->is_first_login ? 'text-orange-600 bg-orange-100/50' : 'text-emerald-600 bg-emerald-100/50' }}">
                                {{ $student->is_first_login ? 'Awaiting Login' : 'Verified' }}
                            </span>
                        </td>
                        <td class="px-6 py-6 text-right flex justify-end gap-4">
                            <!-- Edit Button -->
                            <button onclick="openEditStudentModal({{ json_encode($student) }})" class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 hover:bg-brand-600 hover:text-white transition-all duration-300 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>

                            <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Archive student record?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-10 h-10 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white hover:shadow-lg transition-all duration-300 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="text-slate-300 italic font-black text-xl mb-2">DATABASE EMPTY</div>
                            <p class="text-xs text-slate-400 uppercase tracking-widest font-bold">Import or add your first student record.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    function openEditStudentModal(student) {
        document.getElementById('edit-student-form').action = `/admin/students/${student.id}`;
        document.getElementById('edit_first_name').value = student.first_name;
        document.getElementById('edit_last_name').value = student.last_name;
        document.getElementById('edit_massar_code').value = student.massar_code;
        document.getElementById('edit_age').value = student.age;
        document.getElementById('edit_group_id').value = student.group_id;
        document.getElementById('edit-student-modal').classList.remove('hidden');
    }
</script>
@endsection

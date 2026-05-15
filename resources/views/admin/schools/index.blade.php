@extends('layouts.admin')

@section('header', 'Academic Institutions')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
    
    <!-- Add School Form -->
    <div class="lg:col-span-4 p-8 rounded-[2.5rem] glass border border-white/60 shadow-glass h-fit">
        <h3 class="text-xl font-black text-slate-800 mb-8 flex items-center gap-2">
            <div class="w-2 h-6 bg-brand-600 rounded-full shadow-glow"></div>
            New Institute
        </h3>
        <form action="{{ route('admin.schools.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <x-input-label for="name" value="Official Name" class="ml-2 mb-1" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required placeholder="Enter school name" />
            </div>
            <x-primary-button class="w-full justify-center py-5 shadow-glow uppercase tracking-widest text-xs">
                REGISTER SCHOOL
            </x-primary-button>
        </form>
    </div>

    <!-- Schools List -->
    <div class="lg:col-span-8 p-10 rounded-[3rem] glass border border-white/60 shadow-glass">
        <h3 class="text-xl font-black text-slate-800 mb-10 uppercase tracking-widest italic">Registered Entities</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-slate-400 uppercase text-[10px] font-black tracking-[0.2em] border-b border-slate-100/50">
                        <th class="px-6 py-6">Institution Name</th>
                        <th class="px-6 py-6">Established</th>
                        <th class="px-6 py-6 text-right">Operations</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50/50">
                    @forelse($schools as $school)
                    <tr class="hover:bg-brand-50/30 transition-colors duration-300 group">
                        <td class="px-6 py-6 font-black text-slate-800 text-lg group-hover:text-brand-600 transition-colors">{{ $school->name }}</td>
                        <td class="px-6 py-6 text-xs text-slate-400 font-bold uppercase tracking-tighter">{{ $school->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-6 text-right flex justify-end gap-4">
                            <!-- Edit Button -->
                            <button onclick="openEditModal({{ json_encode($school) }})" class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 hover:bg-brand-600 hover:text-white transition-all duration-300 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>

                            <form action="{{ route('admin.schools.destroy', $school->id) }}" method="POST" onsubmit="return confirm('Archive institution record?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-10 h-10 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all duration-300 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-20 text-center text-slate-400 italic font-black uppercase tracking-widest opacity-50">
                            No institutions found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Edit School Modal -->
<div id="edit-school-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-xl bg-slate-900/40">
    <div class="glass p-12 rounded-[3rem] shadow-2xl max-w-lg w-full border border-white/60 animate-in fade-in zoom-in duration-300">
        <div class="flex justify-between items-center mb-10">
            <h4 class="text-3xl font-black text-slate-900 tracking-tight italic text-center">Modify School</h4>
            <button onclick="document.getElementById('edit-school-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="edit-school-form" method="POST" class="space-y-8">
            @csrf
            @method('PUT')
            <div>
                <x-input-label value="New Institution Name" />
                <x-text-input name="name" id="edit_school_name" required />
            </div>
            <div class="flex gap-4 pt-6">
                <button type="button" onclick="document.getElementById('edit-school-modal').classList.add('hidden')" class="flex-1 py-5 rounded-2xl bg-slate-100 font-black text-slate-500 hover:bg-slate-200 transition-colors uppercase tracking-widest text-xs">Annuler</button>
                <x-primary-button class="flex-1 justify-center py-5 shadow-glow uppercase font-black">
                    UPDATE RECORD
                </x-primary-button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(school) {
        document.getElementById('edit-school-form').action = `/admin/schools/${school.id}`;
        document.getElementById('edit_school_name').value = school.name;
        document.getElementById('edit-school-modal').classList.remove('hidden');
    }
</script>
@endsection

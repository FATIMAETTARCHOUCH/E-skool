@extends('layouts.admin')

@section('header', 'Gestion des Élèves')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="space-y-6">
    <!-- Student Statistics Header -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="p-6 rounded-lg bg-white border border-gray-200 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-600 uppercase tracking-widest">Total des Élèves</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $students->count() }}</h3>
            </div>
            <div class="w-12 h-12 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600 border border-indigo-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
        </div>
        <!-- Search Filter -->
        <div class="md:col-span-2 p-3 rounded-lg bg-white border border-gray-200 flex items-center px-4">
            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" placeholder="Rechercher des élèves..." class="bg-transparent border-none focus:ring-0 w-full text-gray-700 placeholder-gray-400 text-sm">
        </div>
        <div class="flex items-center justify-end">
            <button onclick="document.getElementById('add-student-modal').classList.remove('hidden')" class="px-4 py-2.5 rounded-lg bg-indigo-600 text-white font-bold text-sm hover:bg-indigo-700 transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Ajouter élève
            </button>
        </div>
    </div>

    <!-- Manual Add Student Modal -->
    <div id="add-student-modal" class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-black/50">
        <div class="bg-white p-6 rounded-lg max-w-lg w-full border border-gray-200 animate-in zoom-in duration-300">
            <div class="flex justify-between items-center mb-6">
                <h4 class="text-lg font-bold text-gray-900">Nouvel Élève</h4>
                <button onclick="document.getElementById('add-student-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="{{ route('admin.students.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-input-label value="Prénom" />
                        <x-text-input name="first_name" required placeholder="Ahmed" />
                    </div>
                    <div>
                        <x-input-label value="Nom" />
                        <x-text-input name="last_name" required placeholder="Alami" />
                    </div>
                </div>
                <div>
                    <x-input-label value="Code Massar" />
                    <x-text-input name="massar_code" required placeholder="G123456789" />
                </div>
                <div>
                    <x-input-label value="Groupe Cible" />
                    <select name="group_id" required class="block w-full mt-1 bg-gray-50 border border-gray-200 focus:border-indigo-600 focus:ring-0 rounded-lg py-2.5 px-3 text-gray-700 text-sm">
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('add-student-modal').classList.add('hidden')" class="flex-1 py-2.5 rounded-lg bg-gray-100 text-gray-600 font-bold hover:bg-gray-200 text-sm">Annuler</button>
                    <x-primary-button class="flex-1 justify-center py-2.5 text-sm">
                        Enregistrer l'élève
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Student Modal -->
    <div id="edit-student-modal" class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-black/50">
        <div class="bg-white p-6 rounded-lg max-w-lg w-full border border-gray-200 animate-in zoom-in duration-300">
            <div class="flex justify-between items-center mb-6">
                <h4 class="text-lg font-bold text-gray-900">Modifier l'Élève</h4>
                <button onclick="document.getElementById('edit-student-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form id="edit-student-form" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-input-label value="Prénom" />
                        <x-text-input name="first_name" id="edit_first_name" required />
                    </div>
                    <div>
                        <x-input-label value="Nom" />
                        <x-text-input name="last_name" id="edit_last_name" required />
                    </div>
                </div>
                <div>
                    <x-input-label value="Code Massar" />
                    <x-text-input name="massar_code" id="edit_massar_code" required />
                </div>
                <div>
                    <x-input-label value="Groupe Actuel" />
                    <select name="group_id" id="edit_group_id" required class="block w-full mt-1 bg-gray-50 border border-gray-200 focus:border-indigo-600 focus:ring-0 rounded-lg py-2.5 px-3 text-gray-700 text-sm">
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('edit-student-modal').classList.add('hidden')" class="flex-1 py-2.5 rounded-lg bg-gray-100 text-gray-600 font-bold hover:bg-gray-200 text-sm">Annuler</button>
                    <x-primary-button class="flex-1 justify-center py-2.5 text-sm">
                        Enregistrer les modifications
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-gray-500 text-xs uppercase tracking-wide font-medium border-b border-gray-100 bg-gray-50">
                        <th class="px-6 py-3">Identité</th>
                        <th class="px-6 py-3">Code Massar</th>
                        <th class="px-6 py-3">Groupe</th>
                        <th class="px-6 py-3">Statut</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($students as $student)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $student->first_name }} {{ $student->last_name }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">ID Élève #{{ $student->id }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-md bg-gray-100 font-mono font-medium text-xs text-gray-600 border border-gray-200">
                                {{ $student->massar_code }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-gray-700">{{ $student->group ? $student->group->name : 'Non assigné' }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">{{ $student->group ? $student->group->branch->school->name : '—' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($student->is_first_login)
                                <span class="px-3 py-1 rounded-md text-xs font-medium uppercase border bg-orange-50 text-orange-700 border-orange-200">
                                    Jamais connecté
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-md text-xs font-medium uppercase border bg-green-50 text-green-700 border-green-200">
                                    Connecté
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button onclick="openEditStudentModal({{ json_encode($student) }})" class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 hover:text-indigo-700 flex items-center justify-center border border-indigo-200 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
 
                                <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment archiver cet élève ?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 flex items-center justify-center border border-red-200 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">Aucun élève trouvé</td>
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
        document.getElementById('edit_group_id').value = student.group_id;
        document.getElementById('edit-student-modal').classList.remove('hidden');
    }
</script>
@endsection

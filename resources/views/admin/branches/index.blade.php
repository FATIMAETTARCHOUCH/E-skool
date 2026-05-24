@extends('layouts.admin')

@section('header', 'Branch Infrastructure')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
    <!-- Add Branch Form -->
    <div class="bg-white rounded-lg border border-gray-200 p-6 h-fit">
        <h3 class="text-base font-semibold text-gray-900 mb-6 border-l-4 border-indigo-600 pl-3">New Department</h3>
        <form action="{{ route('admin.branches.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <x-input-label for="school_id" value="Parent Institution" />
                <select name="school_id" id="school_id" required class="block w-full mt-1 bg-gray-50 border border-gray-200 focus:border-indigo-400 focus:outline-none focus:bg-white rounded-lg py-2.5 px-4 text-gray-700 text-sm transition">
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}">{{ $school->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label for="name" value="Branch Title" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required placeholder="e.g. Science, Literature..." />
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                Establish Branch
            </button>
        </form>
    </div>

    <!-- Branches Table -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-gray-500 text-xs uppercase tracking-wide font-medium border-b border-gray-100 bg-gray-50">
                        <th class="px-6 py-3">Branch Name</th>
                        <th class="px-6 py-3">Affiliation</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($branches as $branch)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-bold text-gray-900">{{ $branch->name }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-md bg-gray-100 text-xs font-medium text-gray-600 border border-gray-200">
                                {{ $branch->school->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right flex justify-end gap-2">
                            <!-- Edit Button -->
                            <button onclick="openEditModal({{ json_encode($branch) }})" class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 flex items-center justify-center border border-indigo-200 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>

                            <form action="{{ route('admin.branches.destroy', $branch->id) }}" method="POST" onsubmit="return confirm('Delete branch record?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center border border-red-200 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-gray-400 italic">No branches found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Edit Branch Modal -->
<div id="edit-branch-modal" class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-black/50">
    <div class="bg-white p-6 rounded-lg border border-gray-200 max-w-lg w-full">
        <div class="flex justify-between items-center mb-6">
            <h4 class="text-lg font-bold text-gray-900">Update Branch</h4>
            <button onclick="document.getElementById('edit-branch-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="edit-branch-form" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <x-input-label value="Select Institution" />
                <select name="school_id" id="edit_school_id" required class="block w-full mt-1 bg-gray-50 border border-gray-200 focus:border-indigo-400 focus:outline-none focus:bg-white rounded-lg py-2.5 px-4 text-gray-700 text-sm transition">
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}">{{ $school->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label value="Branch Name" />
                <x-text-input name="name" id="edit_branch_name" required />
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="document.getElementById('edit-branch-modal').classList.add('hidden')" class="flex-1 py-2.5 rounded-lg bg-gray-100 text-gray-600 font-bold hover:bg-gray-200 transition-colors text-sm">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 rounded-lg bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-colors text-sm">
                    Update Branch
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    function openEditModal(branch) {
        document.getElementById('edit-branch-form').action = `/admin/branches/${branch.id}`;
        document.getElementById('edit_branch_name').value = branch.name;
        document.getElementById('edit_school_id').value = branch.school_id;
        document.getElementById('edit-branch-modal').classList.remove('hidden');
    }
</script>
@endsection

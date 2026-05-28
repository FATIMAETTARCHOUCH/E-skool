@extends('layouts.admin')

@section('header', 'User Management')

@section('content')
<div class="space-y-6">
    <!-- Add User Form Card -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-6 border-l-4 border-indigo-600 pl-3">New Account</h3>
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <x-input-label value="First Name" />
                <x-text-input name="first_name" required placeholder="Ex: Ahmed" />
            </div>
            <div>
                <x-input-label value="Last Name" />
                <x-text-input name="last_name" required placeholder="Ex: Alami" />
            </div>
            <div>
                <x-input-label value="System Role" />
                <select name="role" required class="block w-full mt-1 bg-gray-50 border border-gray-200 focus:border-indigo-400 focus:outline-none focus:bg-white rounded-lg py-2.5 px-4 text-gray-700 text-sm transition">
                    <option value="student">Élève</option>
                    <option value="admin">Administrator</option>
                </select>
            </div>
            <div>
                <x-input-label value="Username" />
                <x-text-input name="username" required placeholder="unique_handle" />
            </div>
            <div>
                <x-input-label value="Initial Password" />
                <x-text-input name="password" type="password" required placeholder="••••••••" />
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                Create User
            </button>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-gray-500 text-xs uppercase tracking-wide font-medium border-b border-gray-100 bg-gray-50">
                        <th class="px-6 py-3">User Identity</th>
                        <th class="px-6 py-3">Role / Level</th>
                        <th class="px-6 py-3">Group</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</div>
                            <div class="font-mono text-xs text-gray-400">@ {{ $user->username }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-md text-xs font-medium border {{ $user->role === 'admin' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-indigo-50 text-indigo-700 border-indigo-200' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $user->group ? $user->group->name : '—' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button onclick="openEditModal({{ json_encode($user) }})" class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 flex items-center justify-center border border-indigo-200 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>

                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Archive user access?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center border border-red-200 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                                @else
                                <div class="w-9 h-9 rounded-lg bg-gray-100 text-gray-400 flex items-center justify-center border border-gray-200 cursor-not-allowed" title="Self Protection">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Edit User Modal -->
<div id="edit-user-modal" class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-black/50">
    <div class="bg-white p-6 rounded-lg border border-gray-200 max-w-lg w-full">
        <div class="flex justify-between items-center mb-6">
            <h4 class="text-lg font-bold text-gray-900">Update User</h4>
            <button onclick="document.getElementById('edit-user-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <form id="edit-user-form" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-2 gap-3">
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
                <x-input-label value="Username" />
                <x-text-input name="username" id="edit_username" required />
            </div>

            <div>
                <x-input-label value="New Password (Leave blank to keep current)" />
                <x-text-input name="password" type="password" placeholder="••••••••" />
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="document.getElementById('edit-user-modal').classList.add('hidden')" class="flex-1 py-2.5 rounded-lg bg-gray-100 text-gray-600 font-bold hover:bg-gray-200 transition-colors text-sm">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 rounded-lg bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-colors text-sm">
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(user) {
        document.getElementById('edit-user-form').action = `/admin/users/${user.id}`;
        document.getElementById('edit_first_name').value = user.first_name;
        document.getElementById('edit_last_name').value = user.last_name;
        document.getElementById('edit_username').value = user.username;
        document.getElementById('edit-user-modal').classList.remove('hidden');
    }
</script>
@endsection

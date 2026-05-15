@extends('layouts.admin')

@section('header', 'User Ecosystem')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    
    <!-- Add User Form -->
    <div class="p-8 rounded-[2.5rem] glass border border-white/60 shadow-glass">
        <h3 class="text-xl font-black text-slate-800 mb-8 flex items-center gap-2">
            <div class="w-2 h-6 bg-brand-600 rounded-full"></div>
            New Account
        </h3>
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <x-input-label value="First Name" class="ml-2 mb-1" />
                <x-text-input name="first_name" required placeholder="Ex: Ahmed" />
            </div>
            <div>
                <x-input-label value="Last Name" class="ml-2 mb-1" />
                <x-text-input name="last_name" required placeholder="Ex: Alami" />
            </div>
            <div>
                <x-input-label value="System Role" class="ml-2 mb-1" />
                <select name="role" class="block w-full mt-1 bg-white/50 border border-slate-200 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 rounded-2xl py-4 px-6 text-slate-700 font-medium transition duration-200 focus:outline-none">
                    <option value="student">Étudiant</option>
                    <option value="admin">Administrator</option>
                </select>
            </div>
            <div>
                <x-input-label value="Username" class="ml-2 mb-1" />
                <x-text-input name="username" required placeholder="unique_handle" />
            </div>
            <div>
                <x-input-label value="Initial Password" class="ml-2 mb-1" />
                <x-text-input name="password" type="password" required placeholder="••••••••" />
            </div>
            <x-primary-button class="w-full justify-center pt-4">
                CREATE USER
            </x-primary-button>
        </form>
    </div>

    <!-- Users List -->
    <div class="lg:col-span-3 p-10 rounded-[3rem] glass border border-white/60 shadow-glass overflow-hidden">
        <h3 class="text-xl font-black text-slate-800 mb-10 uppercase tracking-widest italic">All Access Profiles</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-slate-400 uppercase text-[10px] font-black tracking-[0.2em] border-b border-slate-100/50">
                        <th class="px-6 py-6">User Identity</th>
                        <th class="px-6 py-6">Role / Level</th>
                        <th class="px-6 py-6">Groupe</th>
                        <th class="px-6 py-6 text-right">Control</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50/50">
                    @foreach($users as $user)
                    <tr class="hover:bg-brand-50/30 transition-colors duration-300 group">
                        <td class="px-6 py-6">
                            <div class="font-black text-slate-800 text-lg group-hover:text-brand-600 transition-colors">{{ $user->first_name }} {{ $user->last_name }}</div>
                            <div class="text-[10px] text-slate-400 font-mono tracking-wider">@ {{ $user->username }}</div>
                        </td>
                        <td class="px-6 py-6">
                            <span class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest {{ $user->role === 'admin' ? 'bg-purple-100/50 text-purple-600' : 'bg-blue-100/50 text-blue-600' }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="px-6 py-6 text-sm font-bold text-slate-500 italic">
                            {{ $user->group ? $user->group->name : '-' }}
                        </td>
                        <td class="px-6 py-6 text-right flex justify-end gap-3">
                            <!-- Edit Button -->
                            <button onclick="openEditModal({{ json_encode($user) }})" class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 hover:bg-brand-600 hover:text-white transition-all duration-300 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>

                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Archive user access?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-10 h-10 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all duration-300 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                            @else
                            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center cursor-not-allowed" title="Self Protection">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Edit User Modal -->
<div id="edit-user-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-xl bg-slate-900/40">
    <div class="glass p-12 rounded-[3rem] shadow-2xl max-w-lg w-full border border-white/60 animate-in fade-in zoom-in duration-300">
        <div class="flex justify-between items-center mb-10">
            <h4 class="text-3xl font-black text-slate-900 tracking-tight italic">Update Profile</h4>
            <button onclick="document.getElementById('edit-user-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <form id="edit-user-form" method="POST" class="space-y-8">
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
                <x-input-label value="Username" />
                <x-text-input name="username" id="edit_username" required />
            </div>

            <div>
                <x-input-label value="New Password (Leave blank to keep current)" />
                <x-text-input name="password" type="password" placeholder="••••••••" />
            </div>

            <div class="flex gap-4 pt-6">
                <button type="button" onclick="document.getElementById('edit-user-modal').classList.add('hidden')" class="flex-1 py-5 rounded-2xl bg-slate-100 font-black text-slate-500 hover:bg-slate-200 transition-colors uppercase tracking-widest text-xs">Abort</button>
                <x-primary-button class="flex-1 justify-center py-5 shadow-glow">
                    SYNC CHANGES
                </x-primary-button>
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

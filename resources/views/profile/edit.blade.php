@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.app')

@section('header')
    <div class="flex items-center gap-6">
        <div class="w-16 h-16 rounded-2xl bg-brand-600 text-white flex items-center justify-center shadow-glow">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        </div>
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight italic">Security <span class="text-brand-600">&</span> Identity</h2>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Manage your credentials</p>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-10 pb-24">
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        
        <!-- Username Bento -->
        <div class="glass p-10 rounded-[3rem] border border-white/60 shadow-glass flex flex-col relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-brand-600/5 rounded-full -mr-16 -mt-16 blur-3xl"></div>
            
            <div class="mb-10">
                <h3 class="text-xl font-black text-slate-800 flex items-center gap-3">
                    <span class="w-2 h-6 bg-brand-600 rounded-full shadow-glow"></span>
                    Access Identity
                </h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase mt-2 tracking-widest italic">Sync your system handle</p>
            </div>

            <form method="post" action="{{ route('profile.update') }}" class="space-y-8 flex-1 flex flex-col">
                @csrf
                @method('patch')

                <div>
                    <x-input-label for="username" value="Username / Handle" class="ml-2 mb-2" />
                    <x-text-input id="username" name="username" type="text" :value="old('username', $user->username)" required autocomplete="username" />
                    <x-input-error class="mt-2" :messages="$errors->get('username')" />
                </div>

                <div class="mt-auto pt-10">
                    <x-primary-button class="w-full justify-center py-5 shadow-glow">
                        SAVE USERNAME
                    </x-primary-button>
                    
                    @if (session('status') === 'profile-updated')
                        <p class="text-xs font-black text-emerald-500 uppercase mt-4 text-center animate-pulse">✓ Identity Synchronized</p>
                    @endif
                </div>
            </form>
        </div>

        <!-- Password Bento -->
        <div class="glass p-10 rounded-[3rem] border border-white/60 shadow-glass flex flex-col relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-600/5 rounded-full -mr-16 -mt-16 blur-3xl"></div>

            <div class="mb-10">
                <h3 class="text-xl font-black text-slate-800 flex items-center gap-3">
                    <span class="w-2 h-6 bg-indigo-600 rounded-full shadow-glow"></span>
                    Cryptographic Shield
                </h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase mt-2 tracking-widest italic">Rotate security tokens</p>
            </div>

            <form method="post" action="{{ route('password.update') }}" class="space-y-6">
                @csrf
                @method('put')

                <div>
                    <x-input-label for="update_password_current_password" value="Current Shield" class="ml-2 mb-1" />
                    <x-text-input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" placeholder="••••••••" />
                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="update_password_password" value="New Shield" class="ml-2 mb-1" />
                    <x-text-input id="update_password_password" name="password" type="password" autocomplete="new-password" placeholder="••••••••" />
                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="update_password_password_confirmation" value="Confirm New Shield" class="ml-2 mb-1" />
                    <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" placeholder="••••••••" />
                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full py-5 rounded-2xl bg-slate-900 text-white font-black uppercase tracking-[0.2em] text-xs hover:bg-brand-600 transition-all shadow-xl">
                        UPDATE PASSWORD
                    </button>

                    @if (session('status') === 'password-updated')
                        <p class="text-xs font-black text-emerald-500 uppercase mt-4 text-center animate-pulse">✓ Password Rotated</p>
                    @endif
                </div>
            </form>
        </div>

    </div>

    <!-- Danger Zone Bento (Optional - Simple Logout) -->
    <div class="glass p-8 rounded-[2.5rem] border border-red-100 bg-red-50/10 flex items-center justify-between">
        <div>
            <h4 class="font-black text-slate-800">Session Termination</h4>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">Exit current administrative state</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="px-8 py-3 rounded-xl bg-red-50 text-red-600 font-black text-[10px] uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all">
                SECURE LOGOUT
            </button>
        </form>
    </div>

</div>
@endsection

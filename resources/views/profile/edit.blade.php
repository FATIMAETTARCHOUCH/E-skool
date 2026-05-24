@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.app')

@section('header')
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-indigo-600 text-white flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-900 border-l-4 border-indigo-600 pl-3">Security & Identity</h2>
            <p class="text-xs text-gray-500 font-medium mt-0.5 ml-4">Manage your credentials</p>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Username Bento -->
        <div class="bg-white p-6 rounded-lg border border-gray-200 flex flex-col">
            <div class="mb-6">
                <h3 class="text-base font-bold text-gray-900">Access Identity</h3>
                <p class="text-xs text-gray-500 font-medium mt-1">Sync your system handle</p>
            </div>

            <form method="post" action="{{ route('profile.update') }}" class="space-y-4 flex-1 flex flex-col">
                @csrf
                @method('patch')

                <div>
                    <x-input-label for="username" value="Username / Handle" />
                    <x-text-input id="username" name="username" type="text" :value="old('username', $user->username)" required autocomplete="username" class="w-full mt-1" />
                    <x-input-error class="mt-2" :messages="$errors->get('username')" />
                </div>

                <div class="mt-auto pt-4">
                    <button type="submit" class="w-full py-2.5 rounded-lg bg-indigo-600 text-white font-medium text-sm hover:bg-indigo-700 transition-colors">
                        Save Username
                    </button>
                    
                    @if (session('status') === 'profile-updated')
                        <p class="text-xs font-semibold text-green-600 mt-4 text-center">✓ Identity Synchronized</p>
                    @endif
                </div>
            </form>
        </div>

        <!-- Password Bento -->
        <div class="bg-white p-6 rounded-lg border border-gray-200 flex flex-col">
            <div class="mb-6">
                <h3 class="text-base font-bold text-gray-900">Cryptographic Shield</h3>
                <p class="text-xs text-gray-500 font-medium mt-1">Rotate security tokens</p>
            </div>

            <form method="post" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                @method('put')

                <div>
                    <x-input-label for="update_password_current_password" value="Current Shield" />
                    <x-text-input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" placeholder="••••••••" class="w-full mt-1" />
                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="update_password_password" value="New Shield" />
                    <x-text-input id="update_password_password" name="password" type="password" autocomplete="new-password" placeholder="••••••••" class="w-full mt-1" />
                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="update_password_password_confirmation" value="Confirm New Shield" />
                    <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" placeholder="••••••••" class="w-full mt-1" />
                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-2.5 rounded-lg bg-gray-900 text-white font-medium text-sm hover:bg-gray-800 transition-colors">
                        Update Password
                    </button>

                    @if (session('status') === 'password-updated')
                        <p class="text-xs font-semibold text-green-600 mt-4 text-center">✓ Password Rotated</p>
                    @endif
                </div>
            </form>
        </div>

    </div>

    <!-- Danger Zone Bento -->
    <div class="bg-white p-6 rounded-lg border border-red-200 bg-red-50/10 flex items-center justify-between">
        <div>
            <h4 class="font-bold text-gray-900">Session Termination</h4>
            <p class="text-xs text-gray-500 font-medium mt-1">Exit current administrative state</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="px-4 py-2.5 rounded-lg bg-red-50 text-red-600 font-medium text-sm hover:bg-red-100 border border-red-200 transition-colors">
                Secure Logout
            </button>
        </form>
    </div>

</div>
@endsection

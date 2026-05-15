<x-guest-layout>
    <div class="text-center mb-10">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight italic">Espace <span class="text-indigo-600">Massar</span></h1>
        <p class="text-xs font-bold text-slate-400 mt-2 uppercase tracking-widest">Accès Élève & Professeur</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Username (Massar Code) -->
        <div>
            <x-input-label for="username" value="Nom d'utilisateur / Code Massar" />
            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Mot de passe" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mt-6">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <div class="w-10 h-10 rounded-lg shadow-neumorphic-inset flex items-center justify-center bg-neu-base">
                    <input id="remember_me" type="checkbox" class="rounded border-none text-primary shadow-sm focus:ring-0 bg-transparent" name="remember">
                </div>
                <span class="ms-3 text-sm text-gray-600 font-medium italic">Se souvenir de moi</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-gray-400 hover:text-primary transition-colors duration-200 underline underline-offset-4 decoration-gray-300" href="{{ route('password.request') }}">
                    Mot de passe oublié ?
                </a>
            @endif
        </div>

        <div class="mt-10">
            <x-primary-button class="w-full justify-center py-4 text-lg">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

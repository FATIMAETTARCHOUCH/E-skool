<nav x-data="{ open: false }" class="glass-card m-4 rounded-3xl border-none shadow-glass">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex">
                <!-- Logo & Brand -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                        <div class="p-2 bg-white rounded-xl shadow-sm group-hover:scale-105 transition-transform">
                            <x-application-logo class="block h-8 w-auto object-contain" />
                        </div>
                        <span class="text-xl font-black tracking-tighter text-slate-800">Auto<span class="text-indigo-600">Reméd</span></span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-xs font-black uppercase tracking-widest">
                        {{ __('Tableau de Bord') }}
                    </x-nav-link>
                    <x-nav-link :href="route('messages.index')" :active="request()->is('messages*')" class="text-xs font-black uppercase tracking-widest">
                        {{ __('Messages') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-5 py-3 border border-slate-100 text-xs leading-4 font-black rounded-2xl text-slate-600 bg-white/50 hover:bg-white hover:shadow-sm transition-all uppercase tracking-widest">
                            <div>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>

                            <div class="ms-2">
                                <svg class="fill-current h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="text-[10px] font-black uppercase tracking-widest">
                            {{ __('Profil') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();" class="text-[10px] font-black uppercase tracking-widest text-red-500">
                                {{ __('Déconnexion') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-3 rounded-2xl text-slate-400 hover:text-indigo-600 hover:bg-white transition-all">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white/80 backdrop-blur-md rounded-b-3xl border-t border-slate-100 p-4">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="rounded-2xl font-black uppercase tracking-widest text-[10px]">
                {{ __('Tableau de Bord') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('messages.index')" :active="request()->is('messages*')" class="rounded-2xl font-black uppercase tracking-widest text-[10px]">
                {{ __('Messages') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-slate-100">
            <div class="px-4 mb-4">
                <div class="font-black text-xs text-slate-800 uppercase tracking-widest">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                <div class="font-bold text-[10px] text-slate-400 uppercase tracking-widest">{{ Auth::user()->username }}</div>
            </div>

            <div class="space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="rounded-2xl font-black uppercase tracking-widest text-[10px]">
                    {{ __('Profil') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();" class="text-red-500 rounded-2xl font-black uppercase tracking-widest text-[10px]">
                        {{ __('Déconnexion') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — {{ config('app.name') }}</title>
    
    <!-- Dark Mode Initializer -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .mesh-bg {
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.03) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(59, 130, 246, 0.03) 0px, transparent 50%);
        }
        .glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body class="font-sans antialiased text-slate-900 mesh-bg min-h-screen">
    <div class="flex h-screen overflow-hidden p-4 gap-4">
        
        <!-- Sidebar -->
        <aside class="w-72 bg-white rounded-xl flex-shrink-0 flex flex-col overflow-hidden border border-gray-200">
            <div class="p-8 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center border border-indigo-200">
                        <x-application-logo class="w-7 h-7 object-contain" />
                    </div>
                    <h2 class="text-xl font-black tracking-tighter text-slate-800">Auto<span class="text-indigo-600">Reméd</span></h2>
                </div>
            </div>
            <nav class="flex-1 p-6 space-y-1 overflow-y-auto">
                @php
                    $links = [
                        ['url' => '/admin/dashboard', 'label' => 'Aperçu', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                        ['url' => '/admin/schools', 'label' => 'Établissements', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                        ['url' => '/admin/branches', 'label' => 'Filières', 'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16'],
                        ['url' => '/admin/academic_years', 'label' => 'Années Scolaires', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                        ['url' => '/admin/users', 'label' => 'Utilisateurs', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                        ['url' => '/admin/groups', 'label' => 'Groupes', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
                        ['url' => '/admin/analytics/students', 'label' => 'Analytique', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                        ['url' => '/admin/students', 'label' => 'Élèves', 'icon' => 'M12 14l9-5-9-5-9 5 9 5z'],
                        ['url' => '/admin/courses', 'label' => 'Cours', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                        ['url' => '/admin/quizzes', 'label' => 'Évaluations', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                        ['url' => '/messages', 'label' => 'Messages', 'icon' => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z'],
                        ['url' => '/profile', 'label' => 'Profil', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                    ];
                @endphp

                @foreach($links as $link)
                <a href="{{ $link['url'] }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 group
                   {{ request()->is(trim($link['url'], '/').'*') ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }}">
                   <svg class="w-5 h-5 opacity-70 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"></path></svg>
                   <span class="font-black text-[10px] uppercase tracking-widest">{{ $link['label'] }}</span>
                </a>
                @endforeach
            </nav>
            <div class="p-6 border-t border-gray-200 dark:border-slate-800 flex flex-col gap-4">
                <!-- Dark Mode Toggle -->
                <div x-data="{ 
                    darkMode: localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
                    toggleTheme() {
                        this.darkMode = !this.darkMode;
                        if (this.darkMode) {
                            document.documentElement.classList.add('dark');
                            localStorage.theme = 'dark';
                        } else {
                            document.documentElement.classList.remove('dark');
                            localStorage.theme = 'light';
                        }
                    }
                }" class="flex items-center justify-between">
                    <span class="font-black text-[10px] uppercase tracking-widest text-gray-600 dark:text-slate-400">Mode Sombre</span>
                    <button @click="toggleTheme()" class="p-2 text-slate-500 dark:text-slate-400 hover:bg-gray-50 dark:hover:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-900 transition-all">
                        <span x-show="!darkMode">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                        </span>
                        <span x-show="darkMode" style="display: none;">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z"></path></svg>
                        </span>
                    </button>
                </div>

                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 py-3 rounded-lg text-red-600 font-black text-[10px] uppercase tracking-widest hover:bg-red-50 dark:hover:bg-red-950/20 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Déconnexion
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto space-y-6">
            <div class="bg-white p-10 rounded-xl min-h-full border border-gray-200">
                <header class="mb-10">
                    <h1 class="text-2xl font-bold text-gray-900">@yield('header', 'Tableau de Bord')</h1>
                </header>
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'AutoReméd') }} — Soutien Scolaire Maroc</title>

        <!-- Dark Mode Initializer -->
        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark')
            } else {
                document.documentElement.classList.remove('dark')
            }
        </script>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
            .mesh-bg {
                background-color: #f8fafc;
                background-image: 
                    radial-gradient(at 0% 0%, rgba(79, 70, 229, 0.08) 0px, transparent 50%),
                    radial-gradient(at 100% 0%, rgba(168, 85, 247, 0.08) 0px, transparent 50%),
                    radial-gradient(at 100% 100%, rgba(236, 72, 153, 0.08) 0px, transparent 50%),
                    radial-gradient(at 0% 100%, rgba(59, 130, 246, 0.08) 0px, transparent 50%);
            }
            .glass-card {
                background: white;
                background: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border: 1px solid rgba(255, 255, 255, 0.6);
                box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.03);
            }
            .brand-gradient-text {
                background: linear-gradient(135deg, #1e40af 0%, #4338ca 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
        </style>
    </head>
    <body class="antialiased text-slate-900 mesh-bg min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10">
            <!-- Navigation -->
            <nav class="flex justify-between items-center mb-12 sm:mb-20 glass-card px-8 py-4 rounded-3xl">
                <div class="flex items-center gap-3">
                    <x-application-logo class="w-10 h-10 object-contain rounded-xl" />
                    <span class="text-xl sm:text-2xl font-black tracking-tight text-slate-800">Auto<span class="text-indigo-600">Reméd</span></span>
                </div>
                <div class="flex items-center gap-6">
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
                    }">
                        <button @click="toggleTheme()" class="p-3 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-900/50 transition-all shadow-sm">
                            <span x-show="!darkMode">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                            </span>
                            <span x-show="darkMode" style="display: none;">
                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z"></path></svg>
                            </span>
                        </button>
                    </div>

                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-xs font-bold text-slate-600 hover:text-indigo-600 transition-colors uppercase tracking-widest">Mon Espace</a>
                    @else
                        <a href="{{ route('login') }}" class="bg-slate-900 text-white px-8 py-3 rounded-2xl font-black text-[10px] shadow-xl hover:bg-indigo-600 transition-all uppercase tracking-widest">Se Connecter</a>
                    @endauth
                </div>
            </nav>

            <main class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Main Hero -->
                <div class="lg:col-span-8 glass-card p-10 sm:p-20 rounded-[3rem] sm:rounded-[4rem] relative overflow-hidden flex flex-col justify-center min-h-[500px]">
                    <div class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>
                    
                    <div class="relative z-10 space-y-8">
                        <div class="inline-flex items-center gap-2 bg-indigo-50 px-4 py-2 rounded-full border border-indigo-100 shadow-sm">
                            <span class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></span>
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-600">Système Éducatif Marocain</span>
                        </div>
                        
                        <h1 class="text-5xl sm:text-7xl lg:text-8xl font-black text-slate-900 leading-[0.95] tracking-tighter">
                            La Réussite au <br>
                            <span class="brand-gradient-text italic">Collège & Lycée.</span>
                        </h1>
                        
                        <p class="text-lg text-slate-500 max-w-xl leading-relaxed font-medium">
                            AutoReméd accompagne les élèves marocains dans leur parcours de remédiation. Un soutien intelligent adapté au programme national pour exceller dans vos études.
                        </p>

                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 pt-4">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto bg-indigo-600 text-white px-10 py-5 rounded-3xl font-black text-xs shadow-lg shadow-indigo-200 hover:scale-105 transition-all uppercase tracking-widest text-center">
                                    Accéder à mes cours
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="w-full sm:w-auto bg-indigo-600 text-white px-10 py-5 rounded-3xl font-black text-xs shadow-lg shadow-indigo-200 hover:scale-105 transition-all uppercase tracking-widest text-center">
                                    Accéder à mon espace
                                </a>
                            @endauth
                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-white rounded-2xl shadow-sm border border-slate-100">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/1f/Flag_of_Morocco.svg/1200px-Flag_of_Morocco.svg.png" class="w-8 h-auto rounded-sm" alt="Maroc">
                                </div>
                                <span class="text-[10px] font-bold text-slate-400 tracking-tight uppercase leading-tight">Conforme au Programme<br>du Ministère de l'Éducation</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Features Side -->
                <div class="lg:col-span-4 grid grid-cols-1 gap-8">
                    <div class="glass-card p-10 rounded-[3rem] bg-slate-900 text-white relative overflow-hidden group h-full">
                        <div class="absolute bottom-0 right-0 w-32 h-32 bg-indigo-500/20 rounded-full -mb-16 -mr-16 blur-2xl"></div>
                        <div class="relative z-10 flex flex-col justify-between h-full">
                            <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center mb-8 border border-white/10">
                                <svg class="w-7 h-7 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black leading-tight mb-4">Soutien Massar</h3>
                                <p class="text-slate-400 text-sm font-medium leading-relaxed">
                                    Utilisez vos identifiants fournis pour accéder à un contenu personnalisé selon votre niveau scolaire.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="glass-card p-10 rounded-[3rem] flex flex-col justify-between group">
                        <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-8 border border-indigo-100">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-slate-800 leading-tight mb-2">Objectif Excellence</h3>
                            <p class="text-slate-400 text-sm font-medium leading-relaxed">
                                Préparation intensive pour les contrôles continus et les examens régionaux/nationaux.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer Bento -->
                <div class="lg:col-span-12 glass-card p-10 rounded-[3rem] flex flex-col md:flex-row items-center justify-between gap-8">
                    <div class="flex items-center gap-6">
                        <div class="w-16 h-16 bg-indigo-600 text-white rounded-[2rem] flex items-center justify-center shrink-0 shadow-lg shadow-indigo-200">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-black text-slate-800">Votre Établissement Connecté</h4>
                            <p class="text-slate-400 text-sm font-medium">Une plateforme dédiée à la réussite des élèves du Collège et du Lycée.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 text-[10px] font-black text-slate-300 uppercase tracking-[0.3em]">
                        <span>Collège</span>
                        <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                        <span>Lycée</span>
                        <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                        <span>Maroc</span>
                    </div>
                </div>
            </main>
            
            <footer class="mt-16 text-center">
                <p class="text-[9px] font-black text-slate-300 uppercase tracking-[0.5em]">© {{ date('Y') }} {{ config('app.name') }} — Ministère de l'Éducation Nationale</p>
            </footer>
        </div>
    </body>
</html>

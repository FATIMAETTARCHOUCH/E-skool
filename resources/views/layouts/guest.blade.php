<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AutoReméd') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
            .mesh-bg {
                background-color: #f8fafc;
                background-image: 
                    radial-gradient(at 0% 0%, rgba(79, 70, 229, 0.05) 0px, transparent 50%),
                    radial-gradient(at 100% 100%, rgba(168, 85, 247, 0.05) 0px, transparent 50%);
            }
            .glass-card {
                background: rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(12px);
                border: 1px solid rgba(255, 255, 255, 0.5);
            }
        </style>
    </head>
    <body class="antialiased text-slate-900 mesh-bg">
        <div class="min-h-screen flex flex-col sm:justify-center items-center p-6">
            <div class="mb-12">
                <a href="/" class="block p-4 rounded-3xl bg-white shadow-xl hover:scale-110 transition-transform">
                    <x-application-logo class="w-16 h-16 object-contain" />
                </a>
            </div>

            <div class="w-full sm:max-w-md bg-white p-12 rounded-xl border border-gray-200 overflow-hidden relative">
                {{ $slot }}
            </div>
            
            <p class="mt-12 text-[10px] font-black text-slate-300 uppercase tracking-[0.4em]">© {{ date('Y') }} {{ config('app.name') }}</p>
        </div>
    </body>
</html>

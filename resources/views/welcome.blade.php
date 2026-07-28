<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'TimeTracker') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Outfit', sans-serif; }
        
        .bg-pattern {
            background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .glass-panel {
            background: rgba(17, 24, 39, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body class="antialiased bg-gray-900 text-gray-100 selection:bg-indigo-500 selection:text-white relative overflow-x-hidden">

    <!-- Dekoracyjne elementy tła -->
    <div class="fixed inset-0 bg-pattern z-0 opacity-20 pointer-events-none"></div>
    <div class="fixed top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-indigo-600 blur-[120px] opacity-40 animate-float pointer-events-none" style="animation-delay: 0s;"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full bg-purple-600 blur-[150px] opacity-30 animate-float pointer-events-none" style="animation-delay: 2s;"></div>

    <!-- MAIN HERO SECTION -->
    <main x-data class="relative z-10 w-full max-w-7xl mx-auto px-6 lg:px-8 min-h-screen flex flex-col lg:flex-row items-center justify-between gap-12 lg:gap-20 py-12 lg:py-0">
        
        <!-- Hero Copy -->
        <div class="flex-1 text-center lg:text-left mt-10 lg:mt-0">
            <div class="inline-flex items-center space-x-2 px-4 py-2 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 text-sm font-semibold tracking-wide mb-6">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Rewolucja w śledzeniu czasu</span>
            </div>
            
            <h1 class="text-5xl lg:text-7xl font-extrabold tracking-tight text-white mb-6 leading-tight drop-shadow-sm">
                Zarządzaj czasem <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-400">swojego zespołu</span>
            </h1>
            
            <p class="text-lg lg:text-xl text-gray-400 max-w-2xl mx-auto lg:mx-0 font-light leading-relaxed mb-10">
                Odkryj zaawansowaną platformę TimeTracker. Monitoruj projekty, generuj automatyczne raporty i zyskaj pełną przejrzystość w codziennej pracy. Dołącz do najlepszych już dziś.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                <button type="button" @click.prevent="$dispatch('switch-mode', 'register'); window.scrollTo({ top: 0, behavior: 'smooth' })" class="px-8 py-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold shadow-[0_0_20px_rgba(79,70,229,0.3)] transition-all duration-300 transform hover:-translate-y-1 cursor-pointer">
                    Rozpocznij za darmo
                </button>
                <button type="button" @click.prevent="document.getElementById('features').scrollIntoView({ behavior: 'smooth' })" class="px-8 py-4 rounded-xl bg-gray-800 hover:bg-gray-700 border border-gray-700 text-gray-300 font-semibold transition-all duration-300 transform hover:-translate-y-1 cursor-pointer">
                    Dowiedz się więcej
                </button>
            </div>
        </div>

        <!-- Auth Box -->
        <div id="auth-box" class="w-full max-w-md">
            <x-auth-box initialMode="login" />
        </div>

    </main>

    <!-- FEATURES SECTION -->
    <section id="features" class="relative z-10 w-full bg-gray-900/50 border-t border-gray-800 py-24">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl lg:text-5xl font-bold text-white mb-6">Wszystko, czego potrzebujesz</h2>
                <p class="text-lg text-gray-400">Nasza platforma to nie tylko zliczanie minut. To ekosystem pozwalający Ci w pełni kontrolować czas, koszty i produktywność zespołu z jednego, pięknego miejsca.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="glass-panel p-8 rounded-3xl hover:-translate-y-2 transition-transform duration-300 border-t border-gray-700">
                    <div class="w-14 h-14 bg-indigo-500/20 text-indigo-400 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Zarządzanie Czasem</h3>
                    <p class="text-gray-400 leading-relaxed">Inteligentne stopery lub dodawanie czasu ręcznie. Bądź zawsze na bieżąco z czasem przepracowanym nad każdym projektem i zadaniem.</p>
                </div>

                <!-- Feature 2 -->
                <div class="glass-panel p-8 rounded-3xl hover:-translate-y-2 transition-transform duration-300 border-t border-gray-700">
                    <div class="w-14 h-14 bg-purple-500/20 text-purple-400 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Generowanie Raportów</h3>
                    <p class="text-gray-400 leading-relaxed">Błyskawicznie pobieraj przepiękne PDF-y za pomocą jednego kliknięcia. Filtruj dane z dokładnością do konkretnego pracownika lub miesiąca.</p>
                </div>

                <!-- Feature 3 -->
                <div class="glass-panel p-8 rounded-3xl hover:-translate-y-2 transition-transform duration-300 border-t border-gray-700">
                    <div class="w-14 h-14 bg-cyan-500/20 text-cyan-400 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Nielimitowany Zespół</h3>
                    <p class="text-gray-400 leading-relaxed">Błyskawicznie zapraszaj i przypisuj osoby do zadań. System automatycznych przypisań na podstawie adresu e-mail załatwi całą resztę.</p>
                </div>
            </div>
        </div>
    </section>

</body>
</html>

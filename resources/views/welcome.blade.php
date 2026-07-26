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
<body class="antialiased bg-gray-900 text-gray-100 selection:bg-indigo-500 selection:text-white relative overflow-x-hidden" 
      x-data="{ 
          mode: '{{ auth()->check() && !auth()->user()->hasVerifiedEmail() ? 'verify' : old('mode', (session('status') ? 'forgot_password' : 'login')) }}',
          containerHeight: 0,
          updateHeight() {
              this.$nextTick(() => {
                  const el = this.$refs[this.mode];
                  if (el) {
                      this.containerHeight = el.offsetHeight;
                  }
              });
          }
      }"
      x-init="$watch('mode', () => updateHeight()); updateHeight();"
      @resize.window="updateHeight()">

    <!-- Dekoracyjne elementy tła -->
    <div class="fixed inset-0 bg-pattern z-0 opacity-20 pointer-events-none"></div>
    <div class="fixed top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-indigo-600 blur-[120px] opacity-40 animate-float pointer-events-none" style="animation-delay: 0s;"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full bg-purple-600 blur-[150px] opacity-30 animate-float pointer-events-none" style="animation-delay: 2s;"></div>

    <!-- MAIN HERO SECTION -->
    <main class="relative z-10 w-full max-w-7xl mx-auto px-6 lg:px-8 min-h-screen flex flex-col lg:flex-row items-center justify-between gap-12 lg:gap-20 py-12 lg:py-0">
        
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
                <button type="button" @click.prevent="window.scrollTo({ top: 0, behavior: 'smooth' })" class="px-8 py-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold shadow-[0_0_20px_rgba(79,70,229,0.3)] transition-all duration-300 transform hover:-translate-y-1 cursor-pointer">
                    Rozpocznij za darmo
                </button>
                <button type="button" @click.prevent="document.getElementById('features').scrollIntoView({ behavior: 'smooth' })" class="px-8 py-4 rounded-xl bg-gray-800 hover:bg-gray-700 border border-gray-700 text-gray-300 font-semibold transition-all duration-300 transform hover:-translate-y-1 cursor-pointer">
                    Dowiedz się więcej
                </button>
            </div>
        </div>

        <!-- Auth Box -->
        <div id="auth-box" class="w-full max-w-md">
            <div class="glass-panel rounded-3xl p-8 lg:p-10 shadow-2xl relative overflow-hidden">
                <!-- Highlight line -->
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-cyan-500"></div>

                <!-- Toggles -->
                <div class="flex items-center justify-between mb-8 border-b border-gray-700 pb-4 relative">
                    <!-- Tryby Logowanie i Rejestracja -->
                    <button @click="mode = 'login'" 
                            x-show="mode !== 'forgot_password' && mode !== 'verify'"
                            :class="mode === 'login' ? 'text-white' : 'text-gray-400 hover:text-gray-200'"
                            class="text-lg font-semibold transition-colors duration-200 w-1/2 text-center">
                        Logowanie
                        <div x-show="mode === 'login'" class="h-0.5 w-8 bg-indigo-500 mx-auto mt-2 rounded-full"></div>
                    </button>
                    <button @click="mode = 'register'" 
                            x-show="mode !== 'forgot_password' && mode !== 'verify'"
                            :class="mode === 'register' ? 'text-white' : 'text-gray-400 hover:text-gray-200'"
                            class="text-lg font-semibold transition-colors duration-200 w-1/2 text-center">
                        Rejestracja
                        <div x-show="mode === 'register'" class="h-0.5 w-8 bg-indigo-500 mx-auto mt-2 rounded-full"></div>
                    </button>

                    <!-- Tryb Zapomniałem hasła -->
                    <button x-show="mode === 'forgot_password'" x-cloak
                            class="text-lg font-semibold text-white transition-colors duration-200 w-full text-center cursor-default">
                        Odzyskiwanie hasła
                        <div class="h-0.5 w-16 bg-indigo-500 mx-auto mt-2 rounded-full"></div>
                    </button>

                    <!-- Tryb Weryfikacji konta (PIN) -->
                    <button x-show="mode === 'verify'" x-cloak
                            class="text-lg font-semibold text-white transition-colors duration-200 w-full text-center cursor-default">
                        Aktywacja konta
                        <div class="h-0.5 w-16 bg-indigo-500 mx-auto mt-2 rounded-full"></div>
                    </button>
                </div>



                <!-- Formularze (Smooth Height + Swipe) -->
                <div class="relative w-full transition-[height] duration-500 ease-in-out" :style="'height: ' + containerHeight + 'px'">
                    <!-- Logowanie Form -->
                    <form x-ref="login" x-show="mode === 'login'" 
                          x-transition:enter="transition transform ease-out duration-500"
                          x-transition:enter-start="opacity-0 translate-x-12"
                          x-transition:enter-end="opacity-100 translate-x-0"
                          x-transition:leave="transition transform ease-in duration-300"
                          x-transition:leave-start="opacity-100 translate-x-0"
                          x-transition:leave-end="opacity-0 -translate-x-12"
                          action="{{ route('login') }}" method="POST" class="absolute top-0 left-0 w-full space-y-4">
                        
                        <a href="{{ url('/auth/google') }}" class="w-full flex items-center justify-center gap-3 bg-white hover:bg-gray-50 text-gray-900 font-medium py-3.5 px-4 rounded-xl shadow-sm transition-all duration-200 mb-6 group transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            <span>Zaloguj z Google</span>
                        </a>

                        <div class="relative flex items-center justify-center mb-6">
                            <span class="absolute bg-gray-800/80 px-3 text-sm text-gray-500 rounded-full">LUB</span>
                            <div class="w-full h-px bg-gray-700"></div>
                        </div>

                        @csrf
                        <input type="hidden" name="mode" value="login">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Email</label>
                            <input type="email" name="email" value="{{ old('mode') === 'login' ? old('email') : '' }}" required class="w-full bg-gray-900/50 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder-gray-600" placeholder="twoj@email.com">
                            @if(old('mode') === 'login')
                                @error('email') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            @endif
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-sm font-medium text-gray-300">Hasło</label>
                                @if (Route::has('password.request'))
                                    <a href="#" @click.prevent="mode = 'forgot_password'" class="text-xs text-indigo-400 hover:text-indigo-300 transition-colors">Zapomniałeś hasła?</a>
                                @endif
                            </div>
                            <input type="password" name="password" required class="w-full bg-gray-900/50 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder-gray-600" placeholder="••••••••">
                            @if(old('mode') === 'login')
                                @error('password') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            @endif
                        </div>
                        <div class="flex items-center pt-2">
                            <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded border-gray-700 bg-gray-900 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-gray-900">
                            <label for="remember" class="ml-2 text-sm text-gray-400 cursor-pointer">Pamiętaj mnie</label>
                        </div>
                        <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-semibold py-3.5 px-4 rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg shadow-indigo-500/25 mt-4">
                            Zaloguj się
                        </button>
                    </form>

                    <!-- Rejestracja Form -->
                    <form x-ref="register" x-show="mode === 'register'" 
                          x-transition:enter="transition transform ease-out duration-500"
                          x-transition:enter-start="opacity-0 translate-x-12"
                          x-transition:enter-end="opacity-100 translate-x-0"
                          x-transition:leave="transition transform ease-in duration-300"
                          x-transition:leave-start="opacity-100 translate-x-0"
                          x-transition:leave-end="opacity-0 -translate-x-12"
                          action="{{ route('register') }}" method="POST" class="absolute top-0 left-0 w-full space-y-4">
                        
                        <a href="{{ url('/auth/google') }}" class="w-full flex items-center justify-center gap-3 bg-white hover:bg-gray-50 text-gray-900 font-medium py-3.5 px-4 rounded-xl shadow-sm transition-all duration-200 mb-6 group transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            <span>Zarejestruj z Google</span>
                        </a>

                        <div class="relative flex items-center justify-center mb-6">
                            <span class="absolute bg-gray-800/80 px-3 text-sm text-gray-500 rounded-full">LUB</span>
                            <div class="w-full h-px bg-gray-700"></div>
                        </div>

                        @csrf
                        <input type="hidden" name="mode" value="register">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Imię i nazwisko</label>
                            <input type="text" name="name" value="{{ old('mode') === 'register' ? old('name') : '' }}" required class="w-full bg-gray-900/50 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder-gray-600" placeholder="Jan Kowalski">
                            @if(old('mode') === 'register')
                                @error('name') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Email</label>
                            <input type="email" name="email" value="{{ old('mode') === 'register' ? old('email') : '' }}" required class="w-full bg-gray-900/50 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder-gray-600" placeholder="twoj@email.com">
                            @if(old('mode') === 'register')
                                @error('email') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Hasło</label>
                            <input type="password" name="password" required class="w-full bg-gray-900/50 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder-gray-600" placeholder="••••••••">
                            @if(old('mode') === 'register')
                                @error('password') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Potwierdź hasło</label>
                            <input type="password" name="password_confirmation" required class="w-full bg-gray-900/50 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder-gray-600" placeholder="••••••••">
                        </div>
                        <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-semibold py-3.5 px-4 rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg shadow-purple-500/25 mt-4">
                            Utwórz konto
                        </button>
                    </form>

                    <!-- Odzyskiwanie hasła Form -->
                    <div x-ref="forgot_password" x-show="mode === 'forgot_password'" 
                         x-transition:enter="transition transform ease-out duration-500"
                         x-transition:enter-start="opacity-0 translate-x-12"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         x-transition:leave="transition transform ease-in duration-300"
                         x-transition:leave-start="opacity-100 translate-x-0"
                         x-transition:leave-end="opacity-0 -translate-x-12"
                         class="absolute top-0 left-0 w-full">
                        <div class="mb-4 text-sm text-gray-400 leading-relaxed">
                            Zapomniałeś hasła? Żaden problem. Podaj swój adres e-mail, a my wyślemy Ci link do wygenerowania nowego.
                        </div>

                        <!-- Session Status -->
                        @if (session('status'))
                            <div class="mb-4 font-medium text-sm text-green-400">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="mode" value="forgot_password">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Email</label>
                                <input type="email" name="email" value="{{ old('mode') === 'forgot_password' ? old('email') : '' }}" required class="w-full bg-gray-900/50 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder-gray-600" placeholder="twoj@email.com">
                                @if(old('mode') === 'forgot_password')
                                    @error('email') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                                @endif
                            </div>
                            <div class="flex flex-col gap-3 pt-2">
                                <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-semibold py-3.5 px-4 rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg shadow-indigo-500/25">
                                    Wyślij link
                                </button>
                                <button type="button" @click="mode = 'login'" class="w-full bg-transparent hover:bg-gray-800 text-gray-300 font-semibold py-3.5 px-4 rounded-xl transition-all duration-300">
                                    Wróć do logowania
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Weryfikacja konta Form -->
                    <div x-ref="verify" x-show="mode === 'verify'" x-cloak
                         x-transition:enter="transition transform ease-out duration-500"
                         x-transition:enter-start="opacity-0 translate-x-12"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         x-transition:leave="transition transform ease-in duration-300"
                         x-transition:leave-start="opacity-100 translate-x-0"
                         x-transition:leave-end="opacity-0 -translate-x-12"
                         class="absolute top-0 left-0 w-full">
                        <div class="mb-4 text-sm text-gray-400 leading-relaxed text-center">
                            Na Twój adres e-mail został wysłany kod aktywacyjny. Wprowadź go poniżej, aby uzyskać pełny dostęp do systemu.
                            <br><span class="text-xs text-gray-500 mt-2 block">Pamiętaj, aby sprawdzić folder SPAM.</span>
                        </div>

                        <!-- Session Status -->
                        @if (session('status') == 'verification-link-sent')
                            <div class="mb-4 font-medium text-sm text-green-400 text-center bg-green-500/10 border border-green-500/20 py-2 rounded-lg">
                                Nowy link weryfikacyjny i PIN został wysłany.
                            </div>
                        @endif

                        <form action="{{ route('verification.pin') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <input type="text" name="pin" maxlength="6" required class="w-full bg-gray-900/50 border border-gray-700 rounded-xl px-4 py-4 text-white text-center text-3xl font-bold tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder-gray-700" placeholder="000000">
                                @error('pin') <span class="text-red-400 text-xs mt-1 block text-center">{{ $message }}</span> @enderror
                            </div>
                            <div class="pt-4">
                                <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-400 hover:to-emerald-500 text-white font-semibold py-3.5 px-4 rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg shadow-emerald-500/25">
                                    Aktywuj konto
                                </button>
                            </div>
                        </form>

                        <form action="{{ route('verification.send') }}" method="POST" class="mt-4" x-data="{ sending: false }" @submit="sending = true">
                            @csrf
                            <button type="submit" :disabled="sending" class="w-full bg-transparent hover:bg-gray-800 text-gray-400 hover:text-gray-300 font-semibold py-3 px-4 rounded-xl transition-all duration-300 text-sm flex items-center justify-center gap-2">
                                <svg x-show="sending" class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span x-text="sending ? 'Wysyłanie...' : 'Wyślij kod ponownie (co 60s)'"></span>
                            </button>
                        </form>
                        
                        <form action="{{ route('logout') }}" method="POST" class="mt-2 text-center">
                            @csrf
                            <button type="submit" class="text-xs text-indigo-400 hover:text-indigo-300 underline">
                                Wyloguj i wróć później
                            </button>
                        </form>
                    </div>

                </div>

            </div>
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

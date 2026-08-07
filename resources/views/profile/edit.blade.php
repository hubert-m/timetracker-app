<x-app-layout>
    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Nagłówek profilu z avatarem --}}
            <div class="mb-10">
                <div class="flex items-center gap-6">
                    <div class="relative group">
                        @if ($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="w-24 h-24 rounded-2xl object-cover shadow-xl shadow-indigo-500/20 border-2 border-gray-700">
                        @else
                            <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-indigo-500 to-cyan-400 flex items-center justify-center text-4xl font-extrabold text-white shadow-xl shadow-indigo-500/30">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        {{-- Overlay uploadu --}}
                        <label for="avatar_upload" class="absolute inset-0 rounded-2xl bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </label>
                        <form id="avatarForm" method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" class="hidden">
                            @csrf
                            <input type="file" id="avatar_upload" name="avatar" accept="image/jpeg,image/png,image/webp" onchange="document.getElementById('avatarForm').submit();">
                        </form>
                    </div>
                    <div>
                        <h1 class="text-3xl font-extrabold text-white tracking-tight">Mój Profil</h1>
                        <p class="text-gray-400 mt-1">Zarządzaj swoimi danymi osobowymi i ustawieniami bezpieczeństwa</p>
                        <div class="flex items-center gap-3 mt-3">
                            <span class="text-xs text-gray-500">Kliknij na zdjęcie, aby zmienić avatar</span>
                            @if ($user->avatar)
                                <form method="POST" action="{{ route('profile.avatar.destroy') }}" class="inline">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-300 underline cursor-pointer transition-colors">Usuń zdjęcie</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Flash messages --}}
            @if (session('status') === 'profile-updated')
                <div class="mb-6 p-4 rounded-2xl bg-emerald-900/30 border border-emerald-700/40 text-emerald-300 text-sm font-medium flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Dane profilu zostały zaktualizowane pomyślnie.
                </div>
            @endif
            @if (session('status') === 'avatar-updated')
                <div class="mb-6 p-4 rounded-2xl bg-emerald-900/30 border border-emerald-700/40 text-emerald-300 text-sm font-medium flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Zdjęcie profilowe zostało zaktualizowane.
                </div>
            @endif
            @if (session('status') === 'avatar-removed')
                <div class="mb-6 p-4 rounded-2xl bg-emerald-900/30 border border-emerald-700/40 text-emerald-300 text-sm font-medium flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Zdjęcie profilowe zostało usunięte.
                </div>
            @endif
            @if (session('status') === 'password-set')
                <div class="mb-6 p-4 rounded-2xl bg-emerald-900/30 border border-emerald-700/40 text-emerald-300 text-sm font-medium flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Hasło zostało ustawione pomyślnie. Od teraz możesz logować się również hasłem.
                </div>
            @endif
            @if (session('status') === 'password-updated')
                <div class="mb-6 p-4 rounded-2xl bg-emerald-900/30 border border-emerald-700/40 text-emerald-300 text-sm font-medium flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Hasło zostało zmienione pomyślnie.
                </div>
            @endif
            @error('avatar')
                <div class="mb-6 p-4 rounded-2xl bg-red-900/30 border border-red-700/40 text-red-300 text-sm font-medium flex items-center gap-3">
                    <svg class="w-5 h-5 text-red-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                    {{ $message }}
                </div>
            @enderror

            <div class="flex flex-wrap gap-8 items-start">

                {{-- ========================================================= --}}
                {{-- SEKCJA 1: DANE OSOBOWE                                    --}}
                {{-- ========================================================= --}}
                <div class="flex-1 min-w-[350px] bg-gray-800/60 backdrop-blur-xl rounded-2xl border border-gray-700/60 shadow-xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-700/50 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-indigo-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-white">Dane osobowe</h2>
                            <p class="text-xs text-gray-400">Zmień swoje imię lub adres e-mail</p>
                        </div>
                    </div>

                    <form method="post" action="{{ route('profile.update') }}" class="px-6 py-6 space-y-5">
                        @csrf
                        @method('patch')

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-300 mb-1.5">Imię i nazwisko</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus
                                   class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-shadow">
                            @error('name')
                                <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Adres e-mail</label>
                            <div class="flex items-center gap-3 w-full bg-gray-900/50 border border-gray-700/50 rounded-xl px-4 py-3">
                                <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <span class="text-gray-300">{{ $user->email }}</span>
                            </div>
                        </div>

                        {{-- Info o koncie Google --}}
                        @if ($user->google_id)
                            <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-900/60 border border-gray-700/40">
                                <svg class="w-5 h-5 text-gray-400 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                                <span class="text-sm text-gray-300">Konto połączone z <span class="font-semibold text-white">Google</span></span>
                            </div>
                        @endif

                        <div class="flex justify-end pt-2">
                            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-500/20 transition-all hover:shadow-indigo-500/30 cursor-pointer">
                                Zapisz zmiany
                            </button>
                        </div>
                    </form>

                    <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="hidden">
                        @csrf
                    </form>
                </div>

                {{-- ========================================================= --}}
                {{-- SEKCJA 2: BEZPIECZEŃSTWO — HASŁO                          --}}
                {{-- ========================================================= --}}
                <div class="flex-1 min-w-[350px] bg-gray-800/60 backdrop-blur-xl rounded-2xl border border-gray-700/60 shadow-xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-700/50 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-white">Bezpieczeństwo</h2>
                            <p class="text-xs text-gray-400">
                                @if (is_null($user->password))
                                    Ustaw hasło, aby logować się również tradycyjnie
                                @else
                                    Zmień hasło do swojego konta
                                @endif
                            </p>
                        </div>
                    </div>

                    <form method="post" action="{{ route('password.update') }}" class="px-6 py-6 space-y-5">
                        @csrf
                        @method('put')

                        @if (is_null($user->password))
                            {{-- Użytkownik Google — ustawianie pierwszego hasła --}}
                            <div class="p-4 rounded-xl bg-indigo-900/20 border border-indigo-700/30 mb-2">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-indigo-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-sm text-indigo-200">Twoje konto zostało utworzone przez logowanie Google. Możesz ustawić hasło, aby mieć również możliwość logowania się tradycyjnie za pomocą adresu e-mail i hasła.</p>
                                </div>
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-300 mb-1.5">Nowe hasło</label>
                                <input id="password" name="password" type="password"
                                       class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-shadow"
                                       placeholder="Minimum 8 znaków">
                                @error('password', 'updatePassword')
                                    <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-1.5">Potwierdź hasło</label>
                                <input id="password_confirmation" name="password_confirmation" type="password"
                                       class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-shadow"
                                       placeholder="Powtórz nowe hasło">
                                @error('password_confirmation', 'updatePassword')
                                    <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        @else
                            {{-- Użytkownik z hasłem — wymaga podania starego --}}
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-300 mb-1.5">Aktualne hasło</label>
                                <input id="current_password" name="current_password" type="password"
                                       class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-shadow"
                                       placeholder="Wpisz obecne hasło">
                                @error('current_password', 'updatePassword')
                                    <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-300 mb-1.5">Nowe hasło</label>
                                <input id="password" name="password" type="password"
                                       class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-shadow"
                                       placeholder="Minimum 8 znaków">
                                @error('password', 'updatePassword')
                                    <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-1.5">Potwierdź nowe hasło</label>
                                <input id="password_confirmation" name="password_confirmation" type="password"
                                       class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-shadow"
                                       placeholder="Powtórz nowe hasło">
                                @error('password_confirmation', 'updatePassword')
                                    <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <div class="flex justify-end pt-2">
                            <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-amber-500/20 transition-all hover:shadow-amber-500/30 cursor-pointer">
                                @if (is_null($user->password))
                                    Ustaw hasło
                                @else
                                    Zmień hasło
                                @endif
                            </button>
                        </div>
                    </form>
                </div>

                {{-- ========================================================= --}}
                {{-- SEKCJA 3: STREFA ZAGROŻENIA — USUWANIE KONTA              --}}
                {{-- ========================================================= --}}
                <div class="flex-1 min-w-[350px] bg-gray-800/60 backdrop-blur-xl rounded-2xl border border-red-900/40 shadow-xl overflow-hidden" x-data="{ confirmDelete: false }">
                    <div class="px-6 py-5 border-b border-red-900/30 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-red-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-red-300">Strefa zagrożenia</h2>
                            <p class="text-xs text-gray-400">Trwałe usunięcie konta i wszystkich danych</p>
                        </div>
                    </div>

                    <div class="px-6 py-6">
                        <p class="text-sm text-gray-400 mb-5">Po usunięciu konta wszystkie zasoby i dane zostaną trwale usunięte. Tej operacji nie da się cofnąć.</p>

                        <button @click="confirmDelete = true" x-show="!confirmDelete"
                                class="px-5 py-2.5 bg-red-600/20 hover:bg-red-600/40 text-red-400 hover:text-red-300 text-sm font-semibold rounded-xl border border-red-700/40 transition-all cursor-pointer">
                            Usuń moje konto
                        </button>

                        <div x-show="confirmDelete" x-transition style="display:none;">
                            <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4">
                                @csrf
                                @method('delete')

                                <div class="p-4 rounded-xl bg-red-900/20 border border-red-700/30">
                                    <p class="text-sm text-red-200 font-medium">Czy na pewno chcesz usunąć swoje konto? Wpisz hasło, aby potwierdzić.</p>
                                </div>

                                @if (!is_null($user->password))
                                <div>
                                    <input name="password" type="password" placeholder="Twoje hasło"
                                           class="w-full bg-gray-900/80 border border-red-700/40 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-shadow">
                                    @error('password', 'userDeletion')
                                        <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                @endif

                                <div class="flex items-center gap-3">
                                    <button type="submit"
                                            class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl shadow-lg transition-all cursor-pointer">
                                        Tak, usuń konto na stałe
                                    </button>
                                    <button type="button" @click="confirmDelete = false"
                                            class="px-5 py-2.5 bg-gray-700 hover:bg-gray-600 text-gray-300 text-sm font-medium rounded-xl transition-colors cursor-pointer">
                                        Anuluj
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

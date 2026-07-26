@props(['initialMode' => 'login'])

<div class="w-full max-w-md" 
      x-data="{ 
          mode: '{{ auth()->check() && !auth()->user()->hasVerifiedEmail() ? 'verify' : old('mode', (session('status') ? 'forgot_password' : $initialMode)) }}',
          containerHeight: 0,
          errors: {},
          isLoading: false,
          isLoaded: false,
          resendTimer: 0,
          resendInterval: null,
          notify(text, type = 'info') {
              window.dispatchEvent(new CustomEvent('notify', { detail: { text, type } }));
          },
          startResendTimer() {
              this.resendTimer = 60;
              clearInterval(this.resendInterval);
              this.resendInterval = setInterval(() => {
                  if (this.resendTimer > 0) this.resendTimer--;
                  else clearInterval(this.resendInterval);
              }, 1000);
          },
          updateHeight() {
              this.$nextTick(() => {
                  const el = this.$refs[this.mode];
                  if (el) {
                      this.containerHeight = el.offsetHeight;
                  }
              });
          },
          async submitForm(e, actionForm) {
              this.isLoading = true;
              this.errors = {};
              
              const form = e.target.closest('form');
              const formData = new FormData(form);
              const dataObj = Object.fromEntries(formData.entries());
              
              try {
                  const response = await fetch(actionForm, {
                      method: 'POST',
                      body: JSON.stringify(dataObj),
                      credentials: 'same-origin',
                      headers: {
                          'Content-Type': 'application/json',
                          'Accept': 'application/json',
                          'X-Requested-With': 'XMLHttpRequest'
                      }
                  });
                  
                  if (!response.ok) {
                      if (response.status === 422) {
                          const data = await response.json();
                          this.errors = data.errors || {};
                          this.updateHeight();
                      } else if (response.status === 429) {
                          this.notify('Zbyt wiele żądań. Odczekaj chwilę przed ponowną próbą.', 'error');
                      } else if (response.status === 419) {
                          this.notify('Czas sesji wygasł, odśwież stronę (F5) i spróbuj ponownie.', 'error');
                      } else {
                          console.error('Błąd pobierania danych', response.status);
                          this.notify('Wystąpił błąd serwera (' + response.status + '). Spróbuj ponownie.', 'error');
                      }
                  } else {
                      const data = await response.json().catch(() => ({}));
                      if (data && data.requires_verification) {
                          this.mode = 'verify';
                          this.startResendTimer();
                          return;
                      }

                      if (actionForm.includes('password')) {
                          this.notify('Odnośnik został wysłany. Sprawdź swoją pocztę e-mail.', 'success');
                          this.mode = 'login';
                      } else if (actionForm.includes('verification-notification')) {
                          this.notify('Nowy kod PIN został wysłany!', 'success');
                          this.startResendTimer();
                      } else {
                          window.location.href = '/dashboard';
                      }
                  }
              } catch (error) {
                  console.error('Fatalny błąd sieciowy:', error);
              } finally {
                  this.isLoading = false;
              }
          }
      }"
      x-init="
          $watch('mode', () => { errors = {}; updateHeight(); }); 
          updateHeight(); 
          setTimeout(() => isLoaded = true, 50);
          
          const urlParams = new URLSearchParams(window.location.search);
          if (urlParams.get('status') === 'reset-success') {
              setTimeout(() => notify('Hasło zostało pomyślnie zmienione! Możesz się teraz zalogować.', 'success'), 500);
              window.history.replaceState({}, document.title, window.location.pathname);
          }
          
          @if(session('error'))
              setTimeout(() => notify('{{ session('error') }}', 'error'), 500);
          @endif
      "
      @resize.window="updateHeight()">
    <div class="glass-panel rounded-3xl p-8 lg:p-10 shadow-2xl relative overflow-hidden">
        <!-- Highlight line -->
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-cyan-500"></div>

        <!-- Toggles -->
        <div x-cloak x-show="mode === 'login' || mode === 'register' || mode === 'forgot_password'" class="flex items-center justify-between mb-8 border-b border-gray-700 pb-4 relative">
            <!-- Tryby Logowanie i Rejestracja -->
            <button @click="mode = 'login'" x-cloak
                    x-show="mode === 'login' || mode === 'register'"
                    :class="mode === 'login' ? 'text-white' : 'text-gray-400 hover:text-gray-200'"
                    class="text-lg font-semibold transition-colors duration-200 w-1/2 text-center">
                Logowanie
                <div x-show="mode === 'login'" class="h-0.5 w-8 bg-indigo-500 mx-auto mt-2 rounded-full"></div>
            </button>
            <button @click="mode = 'register'" x-cloak
                    x-show="mode === 'login' || mode === 'register'"
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
        <div class="relative w-full" :class="isLoaded ? 'transition-[height] duration-500 ease-in-out' : ''" :style="'height: ' + containerHeight + 'px'">
            <!-- Logowanie Form -->
            <form x-ref="login" x-show="mode === 'login'" 
                  x-transition:enter="transition transform ease-out duration-500"
                  x-transition:enter-start="opacity-0 translate-x-12"
                  x-transition:enter-end="opacity-100 translate-x-0"
                  x-transition:leave="transition transform ease-in duration-300"
                  x-transition:leave-start="opacity-100 translate-x-0"
                  x-transition:leave-end="opacity-0 -translate-x-12"
                  @submit.prevent="submitForm($event, '{{ route('login') }}')" class="absolute top-0 left-0 w-full space-y-4">
                
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
                    <span x-show="errors.email" x-text="errors.email ? errors.email[0] : ''" class="text-red-400 text-xs mt-1 block"></span>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-sm font-medium text-gray-300">Hasło</label>
                        @if (Route::has('password.request'))
                            <a href="#" @click.prevent="mode = 'forgot_password'" class="text-xs text-indigo-400 hover:text-indigo-300 transition-colors">Zapomniałeś hasła?</a>
                        @endif
                    </div>
                    <input type="password" name="password" required class="w-full bg-gray-900/50 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder-gray-600" placeholder="••••••••">
                    <span x-show="errors.password" x-text="errors.password ? errors.password[0] : ''" class="text-red-400 text-xs mt-1 block"></span>
                </div>
                <div class="flex items-center pt-2">
                    <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded border-gray-700 bg-gray-900 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-gray-900">
                    <label for="remember" class="ml-2 text-sm text-gray-400 cursor-pointer">Pamiętaj mnie</label>
                </div>
                <button type="submit" :disabled="isLoading" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-semibold py-3.5 px-4 rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg shadow-indigo-500/25 mt-4 disabled:opacity-75 flex justify-center items-center gap-2">
                    <svg x-show="isLoading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
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
                  @submit.prevent="submitForm($event, '{{ route('register') }}')" class="absolute top-0 left-0 w-full space-y-4">
                
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
                    <span x-show="errors.name" x-text="errors.name ? errors.name[0] : ''" class="text-red-400 text-xs mt-1 block"></span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('mode') === 'register' ? old('email') : '' }}" required class="w-full bg-gray-900/50 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder-gray-600" placeholder="twoj@email.com">
                    <span x-show="errors.email" x-text="errors.email ? errors.email[0] : ''" class="text-red-400 text-xs mt-1 block"></span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Hasło</label>
                    <input type="password" name="password" required class="w-full bg-gray-900/50 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder-gray-600" placeholder="••••••••">
                    <span x-show="errors.password" x-text="errors.password ? errors.password[0] : ''" class="text-red-400 text-xs mt-1 block"></span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Potwierdź hasło</label>
                    <input type="password" name="password_confirmation" required class="w-full bg-gray-900/50 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder-gray-600" placeholder="••••••••">
                </div>
                <button type="submit" :disabled="isLoading" class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-semibold py-3.5 px-4 rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg shadow-purple-500/25 mt-4 disabled:opacity-75 flex justify-center items-center gap-2">
                    <svg x-show="isLoading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
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

                <form @submit.prevent="submitForm($event, '{{ route('password.email') }}')" class="space-y-4">
                    @csrf
                    <input type="hidden" name="mode" value="forgot_password">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('mode') === 'forgot_password' ? old('email') : '' }}" required class="w-full bg-gray-900/50 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder-gray-600" placeholder="twoj@email.com">
                        <span x-show="errors.email" x-text="errors.email ? errors.email[0] : ''" class="text-red-400 text-xs mt-1 block"></span>
                    </div>
                    <div class="flex flex-col gap-3 pt-2">
                        <button type="submit" :disabled="isLoading" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-semibold py-3.5 px-4 rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg shadow-indigo-500/25 disabled:opacity-75 flex justify-center items-center gap-2">
                            <svg x-show="isLoading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
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
                    <br><span class="text-xs text-indigo-400 mt-2 block font-medium">Ważne: Pamiętaj, aby sprawdzić folder SPAM, jeśli nie widzisz wiadomości!</span>
                </div>

                <!-- Session Status -->
                @if (session('status') == 'verification-link-sent')
                    <div class="mb-4 font-medium text-sm text-green-400 text-center bg-green-500/10 border border-green-500/20 py-2 rounded-lg">
                        Nowy link weryfikacyjny i PIN został wysłany.
                    </div>
                @endif

                <form @submit.prevent="submitForm($event, '{{ route('verification.pin') }}')" class="space-y-4">
                    @csrf
                    <div>
                        <input type="text" name="pin" maxlength="6" required class="w-full bg-gray-900/50 border border-gray-700 rounded-xl px-4 py-4 text-white text-center text-3xl font-bold tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder-gray-700" placeholder="000000">
                        <span x-show="errors.pin" x-text="errors.pin ? errors.pin[0] : ''" class="text-red-400 text-xs mt-1 block text-center"></span>
                    </div>
                    <div class="pt-4">
                        <button type="submit" :disabled="isLoading" class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-400 hover:to-emerald-500 text-white font-semibold py-3.5 px-4 rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg shadow-emerald-500/25 disabled:opacity-75 flex justify-center items-center gap-2">
                            <svg x-show="isLoading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Aktywuj konto
                        </button>
                    </div>
                </form>

                <form @submit.prevent="submitForm($event, '{{ route('verification.send') }}')" class="mt-4">
                    @csrf
                    <input type="hidden" name="mode" value="verify">
                    <button type="submit" :disabled="isLoading || resendTimer > 0" class="w-full bg-transparent hover:bg-gray-800 text-indigo-400 hover:text-indigo-300 font-semibold py-3 px-4 rounded-xl transition-all duration-300 text-sm flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg x-show="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-text="resendTimer > 0 ? 'Wyślij kod ponownie za (' + resendTimer + 's)' : 'Wyślij kod ponownie'"></span>
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

<!-- Global Toasts Container -->
<div x-data="{ toasts: [] }" @notify.window="toasts.push({ id: Date.now(), text: $event.detail.text, type: $event.detail.type }); setTimeout(() => { toasts = toasts.filter(t => t.id !== toasts[0].id) }, 4000)">
    <template x-teleport="body">
        <div class="fixed bottom-6 right-6 z-[99999] flex flex-col gap-3 pointer-events-none">
            <template x-for="toast in toasts" :key="toast.id">
                <div x-transition:enter="transition ease-out duration-300 transform" 
                     x-transition:enter-start="opacity-0 translate-y-8 scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100" 
                     x-transition:leave="transition ease-in duration-200 transform" 
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-8 scale-95" 
                     :class="{'bg-red-500/80 border-red-500/50 shadow-red-500/20': toast.type === 'error', 'bg-green-500/80 border-green-500/50 shadow-green-500/20': toast.type === 'success', 'bg-indigo-500/80 border-indigo-500/50 shadow-indigo-500/20': toast.type === 'info'}" 
                     class="px-6 py-4 rounded-2xl shadow-xl border backdrop-blur-xl text-white font-medium text-sm pointer-events-auto flex items-center gap-3 w-max max-w-sm">
                     
                    <!-- Icon based on type -->
                    <div x-show="toast.type === 'success'" class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div x-show="toast.type === 'error'" class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                    
                    <span x-text="toast.text"></span>
                </div>
            </template>
        </div>
    </template>
</div>

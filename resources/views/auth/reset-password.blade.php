<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Zresetuj hasło - {{ config('app.name', 'TimeTracker') }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .bg-pattern { background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px); background-size: 40px 40px; }
        .glass-panel { background: rgba(17, 24, 39, 0.7); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .animate-float { animation: float 6s ease-in-out infinite; }
        @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-20px); } 100% { transform: translateY(0px); } }
    </style>
</head>
<body class="antialiased bg-gray-900 text-gray-100 selection:bg-indigo-500 selection:text-white relative overflow-x-hidden min-h-screen flex items-center justify-center">

    <div class="fixed inset-0 bg-pattern z-0 opacity-20 pointer-events-none"></div>
    <div class="fixed top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-indigo-600 blur-[120px] opacity-40 animate-float pointer-events-none" style="animation-delay: 0s;"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full bg-purple-600 blur-[150px] opacity-30 animate-float pointer-events-none" style="animation-delay: 2s;"></div>

    <div class="relative z-10 w-full max-w-md px-6 py-12"
         x-data="{
             isLoading: false,
             errors: {},
             async submitForm(e) {
                 this.isLoading = true;
                 this.errors = {};
                 const form = e.target;
                 const formData = new FormData(form);
                 const dataObj = Object.fromEntries(formData.entries());
                 
                 try {
                     const response = await fetch(form.action, {
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
                         } else {
                             window.dispatchEvent(new CustomEvent('notify', { detail: { text: 'Wystąpił błąd serwera (' + response.status + '). Spróbuj ponownie.', type: 'error' } }));
                         }
                     } else {
                         const data = await response.json();
                         window.location.href = '{{ route('login') }}?status=reset-success';
                     }
                 } catch (e) {
                     window.dispatchEvent(new CustomEvent('notify', { detail: { text: 'Błąd sieci. Sprawdź połączenie.', type: 'error' } }));
                 } finally {
                     this.isLoading = false;
                 }
             }
         }">
         
        <div class="text-center mb-8">
            <a href="/" class="text-3xl font-extrabold tracking-tight text-white hover:opacity-80 transition-opacity">
                Time<span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-400">Tracker</span>
            </a>
            <p class="text-gray-400 text-sm mt-2 font-light">Utwórz nowe, silne hasło</p>
        </div>

        <div class="glass-panel rounded-3xl p-8 lg:p-10 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 to-purple-500"></div>
            
            <form action="{{ route('password.store') }}" @submit.prevent="submitForm($event)" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Address -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Adres e-mail</label>
                    <input type="email" name="email" value="{{ old('email', $request->email) }}" required class="w-full bg-gray-900/50 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder-gray-600" placeholder="twoj@email.com">
                    <span x-show="errors.email" x-text="errors.email ? errors.email[0] : ''" class="text-red-400 text-xs mt-1 block"></span>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Nowe hasło</label>
                    <input type="password" name="password" required class="w-full bg-gray-900/50 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder-gray-600" placeholder="••••••••">
                    <span x-show="errors.password" x-text="errors.password ? errors.password[0] : ''" class="text-red-400 text-xs mt-1 block"></span>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Potwierdź nowe hasło</label>
                    <input type="password" name="password_confirmation" required class="w-full bg-gray-900/50 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder-gray-600" placeholder="••••••••">
                    <span x-show="errors.password_confirmation" x-text="errors.password_confirmation ? errors.password_confirmation[0] : ''" class="text-red-400 text-xs mt-1 block"></span>
                </div>

                <div class="pt-2">
                    <button type="submit" :disabled="isLoading" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-semibold py-3.5 px-4 rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg shadow-indigo-500/25 mt-4 disabled:opacity-75 flex justify-center items-center gap-2">
                        <svg x-show="isLoading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Zresetuj hasło
                    </button>
                </div>
            </form>
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

</body>
</html>

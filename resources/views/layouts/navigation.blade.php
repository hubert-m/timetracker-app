<nav x-data="{ open: false }" class="bg-gray-900/60 backdrop-blur-xl border-b border-gray-800 sticky top-0 z-50 shadow-2xl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            
            <div class="flex items-center">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-cyan-400 flex items-center justify-center shadow-lg shadow-indigo-500/30 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-xl font-extrabold tracking-tight text-white group-hover:text-indigo-400 transition-colors">TimeTracker</span>
                </a>

                <!-- Desktop Links -->
                <div class="hidden sm:flex sm:items-center sm:ml-10 space-x-2">
                    <a href="{{ route('dashboard') }}" class="text-gray-300 hover:text-white px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-gray-800/80 font-bold' : 'hover:bg-gray-800/50 font-medium' }}">
                        Dashboard
                    </a>
                    
                    <a href="{{ route('projects.index') }}" class="text-gray-300 hover:text-white px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('projects.*') ? 'bg-gray-800/80 font-bold' : 'hover:bg-gray-800/50 font-medium' }}">
                        Projekty
                    </a>
                    
                    <a href="{{ route('tasks.index') }}" class="text-gray-300 hover:text-white px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('tasks.*') ? 'bg-gray-800/80 font-bold' : 'hover:bg-gray-800/50 font-medium' }}">
                        Zadania
                    </a>
                    
                    <button type="button" class="text-gray-300 hover:text-white px-4 py-2 rounded-lg transition-colors hover:bg-gray-800/50 cursor-pointer flex items-center gap-2 font-medium">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Zaraportuj czas
                    </button>
                    
                    <button type="button" class="text-gray-300 hover:text-white px-4 py-2 rounded-lg transition-colors hover:bg-gray-800/50 cursor-pointer flex items-center gap-2 font-medium">
                        <svg class="w-4 h-4 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Raporty
                    </button>
                </div>
            </div>

            <!-- Prawa sekcja (Desktop) -->
            <div class="hidden sm:flex sm:items-center sm:gap-2">
                <!-- Profile Dropdown (Desktop) -->
                <div class="relative" x-data="{ dropdownOpen: false }" @click.away="dropdownOpen = false">
                    <button @click="dropdownOpen = !dropdownOpen" class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-300 hover:text-white focus:outline-none transition-colors rounded-xl hover:bg-gray-800/50 cursor-pointer">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-8 h-8 rounded-full object-cover border border-gray-600">
                        @else
                            <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-white border border-gray-600">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        @endif
                        <span>{{ Auth::user()->name }}</span>
                        <svg class="fill-current h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div x-show="dropdownOpen" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 top-12 mt-2 w-48 bg-gray-800 rounded-xl border border-gray-700 shadow-2xl py-1 overflow-hidden z-50"
                         style="display: none;">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                            {{ __('Profile') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="block px-4 py-2 text-sm text-red-400 hover:bg-gray-700 hover:text-red-300 transition-colors">
                                {{ __('Log Out') }}
                            </a>
                        </form>
                    </div>
                </div>

                <!-- Notifications Dropdown (Desktop) -->
                <div class="relative" x-data="notificationsMenu()" @click.away="open = false">
                    <button @click="toggleMenu()" class="relative p-2 text-gray-400 hover:text-white transition-colors rounded-xl hover:bg-gray-800/50 focus:outline-none cursor-pointer">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute top-1 right-1.5 flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                            </span>
                        @endif
                    </button>
                    
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 top-12 mt-2 w-80 bg-gray-800 rounded-2xl border border-gray-700 shadow-2xl overflow-hidden z-50"
                         style="display: none;">
                        
                        <div class="px-4 py-3 border-b border-gray-700 bg-gray-900">
                            <h3 class="text-sm font-bold text-white">Powiadomienia</h3>
                        </div>
                        
                        <div class="max-h-96 overflow-y-auto bg-gray-800">
                            @forelse(auth()->user()->unreadNotifications as $notification)
                                <a href="{{ $notification->data['url'] ?? '#' }}" @click="markAsRead('{{ $notification->id }}')" class="block p-4 border-b border-gray-700/50 hover:bg-gray-700 transition-colors">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-full bg-indigo-500/20 text-indigo-400 flex items-center justify-center flex-shrink-0 mt-1">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                @if(($notification->data['icon'] ?? '') == 'briefcase')
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                                @endif
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-300">{{ $notification->data['message'] }}</p>
                                            <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="p-6 text-center">
                                    <svg class="w-10 h-10 text-gray-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                    <p class="text-sm text-gray-400">Brak nowych powiadomień</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hamburger (Mobile) -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-800/50 focus:outline-none focus:bg-gray-800 focus:text-white transition duration-150 ease-in-out cursor-pointer">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-gray-900 border-b border-gray-800 shadow-xl">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('dashboard') ? 'border-indigo-400 text-white bg-gray-800' : 'border-transparent text-gray-400 hover:text-white hover:bg-gray-800 hover:border-gray-600' }} text-base font-medium transition-colors">
                Dashboard
            </a>
            <a href="{{ route('projects.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('projects.*') ? 'border-indigo-400 text-white bg-gray-800' : 'border-transparent text-gray-400 hover:text-white hover:bg-gray-800 hover:border-gray-600' }} text-base font-medium transition-colors">
                Projekty
            </a>
            <a href="{{ route('tasks.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('tasks.*') ? 'border-indigo-400 text-white bg-gray-800' : 'border-transparent text-gray-400 hover:text-white hover:bg-gray-800 hover:border-gray-600' }} text-base font-medium transition-colors">
                Zadania
            </a>
        </div>
        
        <div class="pt-4 pb-3 border-t border-gray-800">
            <div class="px-4">
                <div class="font-medium text-base text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-base font-medium text-gray-400 hover:text-white hover:bg-gray-800 transition-colors">
                    {{ __('Profile') }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="block px-4 py-2 text-base font-medium text-red-400 hover:text-red-300 hover:bg-gray-800 transition-colors">
                        {{ __('Log Out') }}
                    </a>
                </form>
            </div>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('notificationsMenu', () => ({
            open: false,
            toggleMenu() {
                this.open = !this.open;
            },
            async markAsRead(id) {
                try {
                    await fetch('/notifications/' + id + '/read', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                } catch (e) {
                    console.error("Error marking notification as read", e);
                }
            }
        }));
    });
</script>

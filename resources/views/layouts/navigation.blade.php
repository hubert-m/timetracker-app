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

            <!-- Profile Dropdown (Desktop) -->
            <div class="hidden sm:flex sm:items-center sm:ml-6 relative" x-data="{ dropdownOpen: false }" @click.away="dropdownOpen = false">
                <button @click="dropdownOpen = !dropdownOpen" class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-300 hover:text-white focus:outline-none transition-colors rounded-xl hover:bg-gray-800/50 cursor-pointer">
                    <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-white border border-gray-600">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
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

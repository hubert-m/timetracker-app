<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full">
            <h2 class="font-extrabold text-2xl text-white tracking-tight drop-shadow-sm">
                {{ __('Zarządzanie Projektami') }}
            </h2>
            <button class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow-lg transition-transform transform hover:-translate-y-1 font-semibold flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nowy Projekt
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-3xl p-8 shadow-xl text-center">
                <div class="w-20 h-20 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-600">
                    <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Moduł Projektów (W Budowie)</h3>
                <p class="text-gray-400 max-w-md mx-auto">Zakładka z nawigacji została poprawnie podpięta pod ten zasób. Rozbuduj interfejs korzystając z API w celu operacji na projektach i dodawaniu pracowników.</p>
            </div>
        </div>
    </div>
</x-app-layout>

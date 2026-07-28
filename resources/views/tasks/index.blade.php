<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full">
            <h2 class="font-extrabold text-2xl text-white tracking-tight drop-shadow-sm">
                {{ __('Lista Zadań') }}
            </h2>
            <button class="px-5 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl shadow-lg transition-transform transform hover:-translate-y-1 font-semibold flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nowe Zadanie
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-3xl p-8 shadow-xl text-center">
                <div class="w-20 h-20 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-600">
                    <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Moduł Zadań (W Budowie)</h3>
                <p class="text-gray-400 max-w-md mx-auto">Nawigacja w panelu jest podłączona. Dodaj tabele lub kafelki z renderowaniem stanu Twoich tasków, oraz logiką rozpoczynania odliczania czasu do tego widoku.</p>
            </div>
        </div>
    </div>
</x-app-layout>

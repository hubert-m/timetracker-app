<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full" x-data>
            <h2 class="font-extrabold text-2xl text-white tracking-tight drop-shadow-sm">
                {{ __('Zarządzanie Projektami') }}
            </h2>
            <button @click="$dispatch('open-modal')" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow-[0_0_15px_rgba(79,70,229,0.5)] transition-transform transform hover:-translate-y-1 font-semibold flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nowy Projekt
            </button>
        </div>
    </x-slot>

    <div class="py-12" x-data="projectManager()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($projects as $project)
                <a href="{{ route('projects.show', $project->id) }}" class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-3xl p-6 shadow-xl hover:shadow-[0_0_20px_rgba(99,102,241,0.15)] hover:border-indigo-500/30 transition-all duration-300 transform hover:-translate-y-1 group relative overflow-hidden flex flex-col h-full">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-12 h-12 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                    </div>
                    <div class="mb-4">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold text-lg mb-3" style="background-color: {{ $project->color ?? '#6366f1' }}">
                            {{ strtoupper(substr($project->name ?? $project->title, 0, 1)) }}
                        </div>
                        <h3 class="text-xl font-bold text-white">{{ $project->name ?? $project->title }}</h3>
                        <p class="text-gray-400 text-sm mt-2 line-clamp-3">{{ $project->description ?? 'Brak opisu projektu.' }}</p>
                    </div>
                    <div class="mt-auto pt-4 border-t border-gray-700/50 flex justify-between items-center">
                        <span class="text-xs font-semibold text-gray-500">Członkowie: {{ $project->users()->count() }}</span>
                        <span class="text-xs font-bold text-indigo-400 group-hover:text-indigo-300">Zarządzaj &rarr;</span>
                    </div>
                </a>
                @empty
                <div class="col-span-full bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-3xl p-8 shadow-xl text-center">
                    <div class="w-20 h-20 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-600">
                        <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Brak projektów</h3>
                    <p class="text-gray-400 max-w-md mx-auto">Nie należysz jeszcze do żadnego projektu. Wykorzystaj przycisk u góry, aby utworzyć swój pierwszy projekt!</p>
                </div>
                @endforelse
            </div>

            <!-- Modal Tworzenia -->
            <div x-show="showModal" @open-modal.window="showModal = true" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" @click="showModal = false" aria-hidden="true"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    
                    <div x-show="showModal" 
                         x-transition:enter="ease-out duration-300" 
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave="ease-in duration-200" 
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         class="inline-block align-bottom bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-700">
                        <form @submit.prevent="submitForm">
                            <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                        <h3 class="text-xl leading-6 font-bold text-white mb-6" id="modal-title">Tworzenie nowego projektu</h3>
                                        
                                        <div class="mb-4">
                                            <label for="title" class="block text-sm font-medium text-gray-300 mb-1">Nazwa projektu</label>
                                            <input type="text" id="title" x-model="form.title" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-shadow">
                                        </div>
                                        
                                        <div class="mb-4">
                                            <label for="description" class="block text-sm font-medium text-gray-300 mb-1">Opis projektu</label>
                                            <textarea id="description" x-model="form.description" rows="3" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-shadow"></textarea>
                                        </div>
                                        
                                        <div x-show="errorMessage" x-text="errorMessage" class="text-red-400 text-sm mt-2 font-medium" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-900/50 px-4 py-4 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-700/50">
                                <button type="submit" :disabled="isSubmitting" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-2.5 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 transition-colors cursor-pointer">
                                    <span x-show="!isSubmitting">Utwórz projekt</span>
                                    <span x-show="isSubmitting">Tworzenie...</span>
                                </button>
                                <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-600 shadow-sm px-6 py-2.5 bg-gray-800 text-base font-medium text-gray-300 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors cursor-pointer">
                                    Anuluj
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('projectManager', () => ({
                showModal: false,
                isSubmitting: false,
                errorMessage: '',
                form: {
                    title: '',
                    description: ''
                },
                async submitForm() {
                    this.isSubmitting = true;
                    this.errorMessage = '';
                    try {
                        const res = await fetch('{{ route('projects.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.form)
                        });
                        
                        if (res.ok) {
                            window.location.reload();
                        } else {
                            const data = await res.json();
                            this.errorMessage = data.message || 'Wystąpił błąd podczas zapisywania.';
                        }
                    } catch(err) {
                        this.errorMessage = 'Błąd połączenia z serwerem.';
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }));
        });
    </script>
</x-app-layout>

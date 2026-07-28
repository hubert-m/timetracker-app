<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between md:items-center w-full gap-4">
            <div>
                <a href="{{ route('projects.index') }}" class="text-indigo-400 hover:text-indigo-300 text-sm font-semibold mb-1 block">&larr; Wróć do projektów</a>
                <h2 class="font-extrabold text-2xl text-white tracking-tight drop-shadow-sm flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-sm" style="background-color: {{ $project->color ?? '#6366f1' }}">
                        {{ strtoupper(substr($project->name ?? $project->title, 0, 1)) }}
                    </div>
                    {{ $project->name ?? $project->title }}
                </h2>
            </div>
            
            @if($isProjectMember)
            <button onclick="document.getElementById('inviteModal').style.display='block'" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow-[0_0_15px_rgba(79,70,229,0.5)] transition-transform transform hover:-translate-y-1 font-semibold flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Zaproś członka
            </button>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Główna zawartość -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Opis Projektu -->
                    <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-3xl p-8 shadow-xl">
                        <h3 class="text-xl font-bold text-white mb-4">O projekcie</h3>
                        <p class="text-gray-300 leading-relaxed">{{ $project->description ?? 'Nie dodano opisu do tego projektu.' }}</p>
                    </div>

                    <!-- Zadania w projekcie -->
                    <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-3xl p-8 shadow-xl">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-white">Zadania</h3>
                            @if($isProjectMember)
                            <button onclick="document.getElementById('taskModal').style.display='block'" class="px-3 py-1.5 bg-green-600 hover:bg-green-500 text-white rounded-lg shadow-[0_0_15px_rgba(34,197,94,0.5)] transition-transform transform hover:-translate-y-1 font-semibold flex items-center gap-1 text-sm">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Nowe Zadanie
                            </button>
                            @endif
                        </div>
                        <div class="space-y-4">
                            @forelse($tasks as $task)
                                <div class="p-4 bg-gray-900/60 rounded-2xl border border-gray-700/50 hover:border-indigo-500/50 transition-colors group relative">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="text-lg font-bold text-white">{{ $task->title }}</h4>
                                            <p class="text-sm text-gray-400 mt-1">{{ $task->description ?? 'Brak opisu.' }}</p>
                                        </div>
                                        <button onclick="window.dispatchEvent(new CustomEvent('open-task-invite', { detail: { id: {{ $task->id }} } }))" class="px-3 py-1 text-xs font-semibold bg-gray-700 text-gray-300 rounded-lg hover:bg-indigo-600 hover:text-white transition opacity-0 group-hover:opacity-100 cursor-pointer">
                                            Zaproś
                                        </button>
                                    </div>
                                    <div class="mt-4 flex -space-x-2">
                                        @foreach($task->users as $tUser)
                                            <div class="w-8 h-8 rounded-full bg-gray-600 flex items-center justify-center text-xs font-bold text-white border-2 border-gray-900" title="{{ $tUser->name }}">
                                                {{ substr($tUser->name, 0, 1) }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <p class="text-gray-500">Brak przypisanych zadań do wyświetlenia.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                @if($isProjectMember)
                <!-- Panel Boczny: Zespół -->
                <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-3xl p-8 shadow-xl h-fit">
                    <h3 class="text-xl font-bold text-white mb-6 flex items-center justify-between">
                        Twój Zespół
                        <span class="bg-indigo-500/20 text-indigo-400 py-1 px-3 rounded-full text-sm">{{ count($users) }}</span>
                    </h3>
                    
                    <ul class="space-y-4">
                        @foreach($users as $user)
                        <li class="flex items-center gap-4 p-3 bg-gray-900/50 rounded-xl border border-gray-700/30">
                            <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center text-sm font-bold text-white border border-gray-600">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-white truncate">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Zaproszenia (Alpine.js) -->
    <div id="inviteModal" style="display:none;" x-data="invitationForm()" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" onclick="document.getElementById('inviteModal').style.display='none'" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-700">
                <form @submit.prevent="submitInvite">
                    <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-xl leading-6 font-bold text-white mb-6" id="modal-title">Zaproś do projektu</h3>
                                
                                <div class="mb-4">
                                    <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Adres e-mail członka</label>
                                    <input type="email" id="email" x-model="email" required placeholder="np. jan@kowalski.pl" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-shadow">
                                </div>
                                
                                <div x-show="message" x-text="message" :class="isSuccess ? 'text-green-400' : 'text-red-400'" class="text-sm mt-2 font-medium" style="display: none;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-900/50 px-4 py-4 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-700/50">
                        <button type="submit" :disabled="isSubmitting" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-2.5 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 transition-colors cursor-pointer">
                            <span x-show="!isSubmitting">Zaproś</span>
                            <span x-show="isSubmitting">Wysyłanie...</span>
                        </button>
                        <button type="button" onclick="document.getElementById('inviteModal').style.display='none'" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-600 shadow-sm px-6 py-2.5 bg-gray-800 text-base font-medium text-gray-300 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors cursor-pointer">
                            Zamknij
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Zadań (Alpine.js) -->
    <div id="taskModal" style="display:none;" x-data="taskForm()" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" onclick="document.getElementById('taskModal').style.display='none'" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-700">
                <form @submit.prevent="submitTask">
                    <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-xl leading-6 font-bold text-white mb-6">Utwórz zadanie w projekcie</h3>
                                
                                <div class="mb-4">
                                    <label for="title" class="block text-sm font-medium text-gray-300 mb-1">Nazwa zadania</label>
                                    <input type="text" id="title" x-model="title" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-shadow">
                                </div>
                                
                                <div class="mb-4">
                                    <label for="description" class="block text-sm font-medium text-gray-300 mb-1">Opis (opcjonalnie)</label>
                                    <textarea id="description" x-model="description" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-shadow" rows="3"></textarea>
                                </div>
                                
                                <div x-show="message" x-text="message" :class="isSuccess ? 'text-green-400' : 'text-red-400'" class="text-sm mt-2 font-medium" style="display: none;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-900/50 px-4 py-4 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-700/50">
                        <button type="submit" :disabled="isSubmitting" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-2.5 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 transition-colors cursor-pointer">
                            <span x-show="!isSubmitting">Utwórz</span>
                            <span x-show="isSubmitting">Tworzenie...</span>
                        </button>
                        <button type="button" onclick="document.getElementById('taskModal').style.display='none'" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-600 shadow-sm px-6 py-2.5 bg-gray-800 text-base font-medium text-gray-300 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors cursor-pointer">
                            Zamknij
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Zaproszenia do Zadania (Alpine.js) -->
    <div id="taskInviteModal" style="display:none;" x-data="taskInviteForm()" @open-task-invite.window="openModal($event.detail.id)" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" @click="closeModal()" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-700">
                <form @submit.prevent="submitTaskInvite">
                    <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-xl leading-6 font-bold text-white mb-6">Zaproś do Zadania</h3>
                                
                                <div class="mb-4">
                                    <label for="task_email" class="block text-sm font-medium text-gray-300 mb-1">Adres e-mail zapraszanego</label>
                                    <input type="email" id="task_email" x-model="email" required placeholder="np. jan@kowalski.pl" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-shadow">
                                </div>
                                
                                <div x-show="message" x-text="message" :class="isSuccess ? 'text-green-400' : 'text-red-400'" class="text-sm mt-2 font-medium" style="display: none;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-900/50 px-4 py-4 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-700/50">
                        <button type="submit" :disabled="isSubmitting" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-2.5 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 transition-colors cursor-pointer">
                            <span x-show="!isSubmitting">Wyślij zaproszenie</span>
                            <span x-show="isSubmitting">Wysyłanie...</span>
                        </button>
                        <button type="button" @click="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-600 shadow-sm px-6 py-2.5 bg-gray-800 text-base font-medium text-gray-300 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors cursor-pointer">
                            Zamknij
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('invitationForm', () => ({
                email: '',
                isSubmitting: false,
                message: '',
                isSuccess: false,
                
                async submitInvite() {
                    this.isSubmitting = true;
                    this.message = '';
                    try {
                        const res = await fetch('{{ route('invitations.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                email: this.email,
                                resource_type: 'Project',
                                resource_id: {{ $project->id }}
                            })
                        });
                        
                        const data = await res.json();
                        
                        if (res.ok) {
                            this.isSuccess = true;
                            this.message = data.message || 'Pomyślnie wysłano zaproszenie!';
                            this.email = '';
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            this.isSuccess = false;
                            this.message = data.message || 'Nie udało się wysłać zaproszenia.';
                        }
                    } catch (e) {
                        this.isSuccess = false;
                        this.message = 'Błąd połączenia z serwerem.';
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }));

            Alpine.data('taskForm', () => ({
                title: '',
                description: '',
                isSubmitting: false,
                message: '',
                isSuccess: false,
                
                async submitTask() {
                    this.isSubmitting = true;
                    this.message = '';
                    try {
                        const res = await fetch('{{ route('tasks.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                project_id: {{ $project->id }},
                                title: this.title,
                                description: this.description
                            })
                        });
                        
                        const data = await res.json();
                        
                        if (res.ok) {
                            this.isSuccess = true;
                            this.message = 'Pomyślnie utworzono zadanie!';
                            this.title = '';
                            this.description = '';
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            this.isSuccess = false;
                            this.message = data.message || 'Wystąpił błąd.';
                        }
                    } catch (e) {
                        this.isSuccess = false;
                        this.message = 'Błąd połączenia z serwerem.';
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }));

            Alpine.data('taskInviteForm', () => ({
                taskId: null,
                email: '',
                isSubmitting: false,
                message: '',
                isSuccess: false,
                
                openModal(id) {
                    this.taskId = id;
                    document.getElementById('taskInviteModal').style.display = 'block';
                    this.message = '';
                    this.email = '';
                },

                closeModal() {
                    document.getElementById('taskInviteModal').style.display = 'none';
                },

                async submitTaskInvite() {
                    this.isSubmitting = true;
                    this.message = '';
                    try {
                        const res = await fetch('{{ route('invitations.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                email: this.email,
                                resource_type: 'Task',
                                resource_id: this.taskId
                            })
                        });
                        
                        const data = await res.json();
                        
                        if (res.ok) {
                            this.isSuccess = true;
                            this.message = data.message || 'Pomyślnie wysłano zaproszenie!';
                            this.email = '';
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            this.isSuccess = false;
                            this.message = data.message || 'Nie udało się wysłać zaproszenia.';
                        }
                    } catch (e) {
                        this.isSuccess = false;
                        this.message = 'Błąd połączenia z serwerem.';
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }));
        });
    </script>
</x-app-layout>

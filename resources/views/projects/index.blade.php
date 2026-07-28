<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full" x-data>
            <h2 class="font-extrabold text-2xl text-white tracking-tight drop-shadow-sm">
                {{ __('Zarządzanie Projektami') }}
            </h2>
            <button @click="$dispatch('open-modal')" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow-[0_0_15px_rgba(79,70,229,0.5)] transition-transform transform hover:-translate-y-1 font-semibold flex items-center gap-2 text-sm cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nowy Projekt
            </button>
        </div>
    </x-slot>

    <div class="py-12" x-data="projectManager()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Sticky Folder Bar --}}
            <div class="sticky top-20 z-30 mb-8 -mx-4 px-4 py-4 bg-gray-900/80 backdrop-blur-xl border-b border-gray-800/50">
                <div class="flex items-center gap-3 overflow-x-auto pb-1 scrollbar-none">
                    {{-- Chip "Wszystkie" --}}
                    <button @click="activeFolder = null"
                            @dragover.prevent="dragOverFolder($event, null)"
                            @dragleave="dragLeaveFolder($event)"
                            @drop.prevent="dropOnFolder($event, null)"
                            :class="activeFolder === null ? 'bg-white/10 border-white/30 text-white ring-2 ring-white/20' : 'bg-gray-800/60 border-gray-700/50 text-gray-300 hover:bg-gray-800'"
                            class="flex-shrink-0 px-4 py-2 rounded-xl border text-sm font-semibold transition-all cursor-pointer flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        Wszystkie
                        <span class="text-xs opacity-60" x-text="'(' + projects.length + ')'"></span>
                    </button>

                    {{-- Dynamiczne foldery --}}
                    <template x-for="folder in folders" :key="folder.id">
                        <div class="flex-shrink-0 relative group">
                            <button @click="activeFolder = folder.id"
                                    @dragover.prevent="dragOverFolder($event, folder.id)"
                                    @dragleave="dragLeaveFolder($event)"
                                    @drop.prevent="dropOnFolder($event, folder.id)"
                                    :data-folder-id="folder.id"
                                    :class="activeFolder === folder.id ? 'ring-2 ring-offset-1 ring-offset-gray-900 text-white' : 'text-gray-300 hover:text-white'"
                                    :style="activeFolder === folder.id ? 'background-color:' + folder.color + '30; border-color:' + folder.color + '80; --tw-ring-color:' + folder.color + '60' : 'border-color: rgba(107,114,128,0.3)'"
                                    class="px-4 py-2 rounded-xl border text-sm font-semibold transition-all cursor-pointer flex items-center gap-2 bg-gray-800/60">
                                <span class="w-3 h-3 rounded-full flex-shrink-0" :style="'background-color:' + folder.color"></span>
                                <span x-text="folder.name"></span>
                                <span class="text-xs opacity-60" x-text="'(' + projects.filter(p => p.folder_id == folder.id).length + ')'"></span>
                            </button>
                            {{-- Delete button on hover --}}
                            <button @click.stop="deleteFolder(folder.id)"
                                    class="absolute -top-2 -right-2 w-5 h-5 bg-red-600 rounded-full text-white text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer shadow-lg hover:bg-red-500"
                                    title="Usuń katalog">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </template>

                    {{-- Przycisk dodawania folderu --}}
                    <button @click="showFolderModal = true"
                            class="flex-shrink-0 px-3 py-2 rounded-xl border border-dashed border-gray-600 text-gray-400 hover:text-white hover:border-indigo-500 text-sm font-medium transition-all cursor-pointer flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Nowy katalog
                    </button>
                </div>
            </div>

            {{-- Grid projektów --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($projects as $project)
                <a href="{{ route('projects.show', $project->id) }}"
                   draggable="true"
                   x-show="activeFolder === null || {{ $project->folder_id ?? 'null' }} === activeFolder"
                   x-transition:enter="transition ease-out duration-200"
                   x-transition:enter-start="opacity-0 scale-95"
                   x-transition:enter-end="opacity-100 scale-100"
                   @dragstart="startDrag($event, {{ $project->id }})"
                   @dragend="endDrag($event)"
                   class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-3xl p-6 shadow-xl hover:shadow-[0_0_20px_rgba(99,102,241,0.15)] hover:border-indigo-500/30 transition-all duration-300 transform hover:-translate-y-1 group relative overflow-hidden flex flex-col h-full cursor-grab active:cursor-grabbing"
                   data-project-id="{{ $project->id }}"
                   data-folder-id="{{ $project->folder_id }}">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-12 h-12 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                    </div>
                    {{-- Folder badge --}}
                    @if($project->folder_id && $project->folder)
                        <div class="absolute top-4 left-4">
                            <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-md" style="background-color: {{ $project->folder->color }}20; color: {{ $project->folder->color }}">
                                {{ $project->folder->name }}
                            </span>
                        </div>
                    @endif
                    <div class="mb-4 {{ $project->folder_id ? 'mt-4' : '' }}">
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

            {{-- Drag overlay indicator --}}
            <div x-show="isDragging" x-transition.opacity style="display:none;"
                 class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-indigo-600/90 backdrop-blur-lg text-white px-6 py-3 rounded-2xl shadow-2xl text-sm font-semibold z-50 flex items-center gap-3 pointer-events-none">
                <svg class="w-5 h-5 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path></svg>
                Przeciągnij na katalog, aby przypisać
            </div>

            {{-- Modal Tworzenia Projektu --}}
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

            {{-- Modal Tworzenia Folderu --}}
            <div x-show="showFolderModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" @click="showFolderModal = false"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                    <div x-transition class="inline-block align-bottom bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-700">
                        <form @submit.prevent="submitFolder">
                            <div class="bg-gray-800 px-6 pt-6 pb-4">
                                <h3 class="text-lg font-bold text-white mb-5">Nowy katalog</h3>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-300 mb-1">Nazwa katalogu</label>
                                    <input type="text" x-model="folderForm.name" required maxlength="50" placeholder="np. Klienci, Wewnętrzne..." class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-shadow">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Kolor</label>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="c in colorPalette" :key="c">
                                            <button type="button" @click="folderForm.color = c"
                                                    :style="'background-color:' + c"
                                                    :class="folderForm.color === c ? 'ring-2 ring-white ring-offset-2 ring-offset-gray-800 scale-110' : ''"
                                                    class="w-8 h-8 rounded-lg cursor-pointer transition-all hover:scale-110">
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-900/50 px-6 py-4 flex justify-end gap-3 border-t border-gray-700/50">
                                <button type="button" @click="showFolderModal = false" class="px-4 py-2 text-sm text-gray-300 hover:text-white transition cursor-pointer">Anuluj</button>
                                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition cursor-pointer">Utwórz</button>
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
                showFolderModal: false,
                isSubmitting: false,
                errorMessage: '',
                isDragging: false,
                draggedProjectId: null,
                activeFolder: null,
                form: { title: '', description: '' },
                folderForm: { name: '', color: '#6366f1' },
                colorPalette: ['#6366f1', '#8b5cf6', '#ec4899', '#ef4444', '#f97316', '#eab308', '#22c55e', '#14b8a6', '#06b6d4', '#3b82f6'],
                
                folders: @json($folders),
                projects: @json($projects->map(fn($p) => ['id' => $p->id, 'folder_id' => $p->folder_id])),

                // Drag & Drop
                startDrag(e, projectId) {
                    this.isDragging = true;
                    this.draggedProjectId = projectId;
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/plain', projectId);
                    e.target.closest('a').style.opacity = '0.5';
                },
                endDrag(e) {
                    this.isDragging = false;
                    this.draggedProjectId = null;
                    e.target.closest('a').style.opacity = '1';
                    document.querySelectorAll('.folder-drag-over').forEach(el => el.classList.remove('folder-drag-over'));
                },
                dragOverFolder(e, folderId) {
                    e.currentTarget.classList.add('folder-drag-over');
                    e.currentTarget.style.transform = 'scale(1.08)';
                },
                dragLeaveFolder(e) {
                    e.currentTarget.classList.remove('folder-drag-over');
                    e.currentTarget.style.transform = 'scale(1)';
                },
                async dropOnFolder(e, folderId) {
                    e.currentTarget.classList.remove('folder-drag-over');
                    e.currentTarget.style.transform = 'scale(1)';
                    const projectId = parseInt(e.dataTransfer.getData('text/plain'));
                    if (!projectId) return;

                    const url = folderId === null ? '/folders/unassign' : '/folders/assign';
                    const body = folderId === null 
                        ? { project_id: projectId } 
                        : { project_id: projectId, folder_id: folderId };

                    try {
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(body)
                        });
                        if (res.ok) {
                            // Aktualizacja client-side
                            const proj = this.projects.find(p => p.id === projectId);
                            if (proj) proj.folder_id = folderId;
                            // Aktualizacja data-folder-id na karcie
                            const card = document.querySelector(`[data-project-id="${projectId}"]`);
                            if (card) card.setAttribute('data-folder-id', folderId || '');
                            window.location.reload();
                        }
                    } catch(err) {
                        console.error('Drop error:', err);
                    }
                },

                // Folders CRUD
                async submitFolder() {
                    try {
                        const res = await fetch('/folders', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.folderForm)
                        });
                        if (res.ok) {
                            const folder = await res.json();
                            this.folders.push(folder);
                            this.showFolderModal = false;
                            this.folderForm = { name: '', color: '#6366f1' };
                        }
                    } catch(err) {
                        console.error(err);
                    }
                },
                async deleteFolder(folderId) {
                    if (!confirm('Usunąć ten katalog? Projekty wrócą do widoku bez katalogu.')) return;
                    try {
                        const res = await fetch('/folders/' + folderId, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        if (res.ok) {
                            this.folders = this.folders.filter(f => f.id !== folderId);
                            this.projects.forEach(p => { if (p.folder_id === folderId) p.folder_id = null; });
                            if (this.activeFolder === folderId) this.activeFolder = null;
                            window.location.reload();
                        }
                    } catch(err) {
                        console.error(err);
                    }
                },

                // Projects
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

    <style>
        .scrollbar-none::-webkit-scrollbar { display: none; }
        .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
        .folder-drag-over { box-shadow: 0 0 20px rgba(99,102,241,0.5) !important; border-color: rgba(99,102,241,0.8) !important; }
    </style>
</x-app-layout>

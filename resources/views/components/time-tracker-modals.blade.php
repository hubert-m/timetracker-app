<div x-data="timeTracker()" x-init="initTracker()" @open-track-time-modal.window="openTrackModal()" @open-manual-time-modal.window="openManualModal()">
    
    {{-- Global Widget --}}
    <div x-show="activeTimer" x-transition style="display: none;" class="fixed bottom-6 left-6 bg-gray-900 border-2 border-emerald-500/30 shadow-[0_0_30px_rgba(16,185,129,0.15)] rounded-2xl p-4 flex items-center gap-5 z-40 backdrop-blur-xl">
        <div class="relative w-10 h-10 flex items-center justify-center flex-shrink-0">
            <svg class="animate-[spin_3s_linear_infinite] absolute w-full h-full text-emerald-500" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" stroke-dasharray="15 85" stroke-linecap="round"></circle></svg>
            <div class="w-6 h-6 bg-emerald-500/20 rounded-full flex items-center justify-center animate-pulse">
                <div class="w-2.5 h-2.5 bg-emerald-400 rounded-sm"></div>
            </div>
        </div>
        <div class="flex-grow min-w-[150px]">
            <p class="text-xs text-gray-400 font-semibold truncate" x-text="activeProjectName"></p>
            <p class="text-sm text-gray-200 font-medium truncate mb-1" x-text="activeTaskName"></p>
            <p class="text-2xl font-mono font-bold text-emerald-400 leading-none" x-text="formattedTime"></p>
        </div>
        <button @click="stopTimer()" :disabled="isLoading" class="flex-shrink-0 px-4 py-2 bg-red-600/20 hover:bg-red-600/40 text-red-400 hover:text-red-300 rounded-xl text-sm font-bold transition-colors border border-red-500/20 shadow-lg cursor-pointer">
            <span x-show="!isLoading">Stop</span>
            <span x-show="isLoading" class="animate-pulse">...</span>
        </button>
    </div>

    {{-- Overlay for Modals --}}
    <div x-show="trackModalOpen || manualModalOpen" style="display: none;" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 overflow-y-auto">
        
        {{-- Track Time Modal --}}
        <div x-show="trackModalOpen" @click.away="trackModalOpen = false" x-transition class="min-h-screen flex items-center justify-center p-4">
            <div class="bg-gray-800 rounded-2xl border border-gray-700 shadow-2xl max-w-md w-full overflow-hidden flex flex-col max-h-[90vh]">
                <div class="px-6 py-5 border-b border-gray-700 flex justify-between items-center bg-gray-900/50">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Trackuj czas
                    </h3>
                    <button @click="trackModalOpen = false" class="text-gray-400 hover:text-white cursor-pointer"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <div class="p-6 overflow-y-auto flex-grow space-y-4">
                    <div x-show="resourcesLoading" class="text-center py-4 text-gray-400">Ładowanie zadań...</div>
                    <div x-show="!resourcesLoading">
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Wybierz zadanie do trackowania</label>
                        <select x-model="selectedTaskId" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                            <option value="">-- Wybierz zadanie --</option>
                            <template x-for="folder in folders" :key="'f-'+folder.id">
                                <optgroup :label="folder.name">
                                    <template x-for="project in folder.projects" :key="'p-'+project.id">
                                        <template x-for="task in project.tasks" :key="'t-'+task.id">
                                            <option :value="task.id" x-text="project.title + ' - ' + task.title"></option>
                                        </template>
                                    </template>
                                </optgroup>
                            </template>
                            <optgroup label="Projekty bez folderu" x-show="unassignedProjects.length > 0">
                                <template x-for="project in unassignedProjects" :key="'up-'+project.id">
                                    <template x-for="task in project.tasks" :key="'ut-'+task.id">
                                        <option :value="task.id" x-text="project.title + ' - ' + task.title"></option>
                                    </template>
                                </template>
                            </optgroup>
                        </select>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-700 bg-gray-900/50 flex justify-end gap-3">
                    <button @click="trackModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white cursor-pointer">Anuluj</button>
                    <button @click="startTimer()" :disabled="!selectedTaskId || isLoading" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-500/20 transition-all cursor-pointer flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path></svg>
                        Start
                    </button>
                </div>
            </div>
        </div>

        {{-- Manual Time Modal --}}
        <div x-show="manualModalOpen" @click.away="manualModalOpen = false" x-transition class="min-h-screen flex items-center justify-center p-4">
            <div class="bg-gray-800 rounded-2xl border border-gray-700 shadow-2xl max-w-md w-full overflow-hidden flex flex-col max-h-[90vh]">
                <div class="px-6 py-5 border-b border-gray-700 flex justify-between items-center bg-gray-900/50">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Wpisz czas
                    </h3>
                    <button @click="manualModalOpen = false" class="text-gray-400 hover:text-white cursor-pointer"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <div class="p-6 overflow-y-auto flex-grow space-y-5">
                    <div x-show="resourcesLoading" class="text-center py-4 text-gray-400">Ładowanie zadań...</div>
                    <div x-show="!resourcesLoading" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Zadanie</label>
                            <select x-model="manualForm.task_id" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                <option value="">-- Wybierz zadanie --</option>
                                <template x-for="folder in folders" :key="'mf-'+folder.id">
                                    <optgroup :label="folder.name">
                                        <template x-for="project in folder.projects" :key="'mp-'+project.id">
                                            <template x-for="task in project.tasks" :key="'mt-'+task.id">
                                                <option :value="task.id" x-text="project.title + ' - ' + task.title"></option>
                                            </template>
                                        </template>
                                    </optgroup>
                                </template>
                                <optgroup label="Projekty bez folderu" x-show="unassignedProjects.length > 0">
                                    <template x-for="project in unassignedProjects" :key="'mup-'+project.id">
                                        <template x-for="task in project.tasks" :key="'mut-'+task.id">
                                            <option :value="task.id" x-text="project.title + ' - ' + task.title"></option>
                                        </template>
                                    </template>
                                </optgroup>
                            </select>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Data</label>
                                <input type="date" x-model="manualForm.date" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="w-1/3">
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Czas</label>
                                <input type="text" x-model="manualForm.time" placeholder="01:30 lub 1.5" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Komentarz (opcjonalnie)</label>
                            <input type="text" x-model="manualForm.description" placeholder="Nad czym pracowałeś?" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-700 bg-gray-900/50 flex justify-end gap-3">
                    <button @click="manualModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white cursor-pointer">Anuluj</button>
                    <button @click="saveManualTime()" :disabled="!manualForm.task_id || !manualForm.time || isLoading" class="px-6 py-2 bg-blue-600 hover:bg-blue-500 disabled:opacity-50 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-500/20 transition-all cursor-pointer">
                        Zapisz
                    </button>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('timeTracker', () => ({
        activeTimer: null,
        activeTaskName: '',
        activeProjectName: '',
        elapsedSeconds: 0,
        formattedTime: '00:00:00',
        timerInterval: null,
        
        trackModalOpen: false,
        manualModalOpen: false,
        
        folders: [],
        unassignedProjects: [],
        resourcesLoaded: false,
        resourcesLoading: false,
        
        selectedTaskId: '',
        isLoading: false,
        
        manualForm: {
            task_id: '',
            date: new Date().toISOString().split('T')[0],
            time: '',
            description: ''
        },

        initTracker() {
            this.fetchActiveTimer();
            // Start the interval for formatted time
            setInterval(() => {
                if (this.activeTimer) {
                    this.elapsedSeconds++;
                    this.updateFormattedTime();
                }
            }, 1000);
        },

        async fetchActiveTimer() {
            try {
                let res = await fetch('/time-logs/active');
                let data = await res.json();
                if (data.active_log) {
                    this.activeTimer = data.active_log;
                    this.activeTaskName = data.active_log.task.title;
                    this.activeProjectName = data.active_log.task.project.title;
                    
                    // Calculate elapsed seconds from start_time and date
                    let startDateTime = new Date(data.active_log.date + 'T' + data.active_log.start_time);
                    let now = new Date();
                    this.elapsedSeconds = Math.floor((now - startDateTime) / 1000);
                    if (this.elapsedSeconds < 0) this.elapsedSeconds = 0;
                    this.updateFormattedTime();
                } else {
                    this.activeTimer = null;
                }
            } catch (e) {
                console.error(e);
            }
        },

        updateFormattedTime() {
            let h = Math.floor(this.elapsedSeconds / 3600).toString().padStart(2, '0');
            let m = Math.floor((this.elapsedSeconds % 3600) / 60).toString().padStart(2, '0');
            let s = (this.elapsedSeconds % 60).toString().padStart(2, '0');
            this.formattedTime = `${h}:${m}:${s}`;
        },

        async loadResources() {
            if (this.resourcesLoaded) return;
            this.resourcesLoading = true;
            try {
                let res = await fetch('/time-logs/resources');
                let data = await res.json();
                this.folders = data.folders;
                this.unassignedProjects = data.unassigned_projects;
                this.resourcesLoaded = true;
            } catch (e) {
                console.error(e);
            }
            this.resourcesLoading = false;
        },

        openTrackModal() {
            if (this.activeTimer) {
                if(confirm('Masz już obecnie trackowany czas do zadania "'+this.activeTaskName+'". Czy chcesz go zatrzymać?')) {
                    this.stopTimer();
                }
                return;
            }
            this.trackModalOpen = true;
            this.loadResources();
        },

        openManualModal() {
            this.manualModalOpen = true;
            this.loadResources();
        },

        async startTimer() {
            if (!this.selectedTaskId) return;
            this.isLoading = true;
            try {
                let res = await fetch('/time-logs/start', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ task_id: this.selectedTaskId })
                });
                
                if (res.ok) {
                    this.trackModalOpen = false;
                    this.selectedTaskId = '';
                    this.fetchActiveTimer();
                } else {
                    let err = await res.json();
                    alert(err.error || 'Wystąpił błąd');
                }
            } catch (e) {
                console.error(e);
            }
            this.isLoading = false;
        },

        async stopTimer() {
            if (!this.activeTimer) return;
            this.isLoading = true;
            try {
                let res = await fetch('/time-logs/' + this.activeTimer.id + '/stop', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (res.ok) {
                    this.activeTimer = null;
                }
            } catch (e) {
                console.error(e);
            }
            this.isLoading = false;
        },

        parseTime(timeStr) {
            // Parses "HH:MM" or "2.5" to minutes
            if (timeStr.includes(':')) {
                let parts = timeStr.split(':');
                return parseInt(parts[0]) * 60 + parseInt(parts[1]);
            } else {
                return Math.round(parseFloat(timeStr) * 60);
            }
        },

        async saveManualTime() {
            let minutes = this.parseTime(this.manualForm.time);
            if (!minutes || minutes < 0) {
                alert('Nieprawidłowy format czasu.');
                return;
            }
            this.isLoading = true;
            try {
                let res = await fetch('/time-logs', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        task_id: this.manualForm.task_id,
                        date: this.manualForm.date,
                        duration_minutes: minutes,
                        description: this.manualForm.description
                    })
                });
                
                if (res.ok) {
                    this.manualModalOpen = false;
                    this.manualForm.time = '';
                    this.manualForm.description = '';
                    this.manualForm.task_id = '';
                } else {
                    let err = await res.json();
                    alert(err.message || 'Wystąpił błąd');
                }
            } catch (e) {
                console.error(e);
            }
            this.isLoading = false;
        }
    }));
});
</script>

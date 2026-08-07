<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold leading-tight text-white flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-500/20 flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            Generowanie Raportów
        </h2>
    </x-slot>

    <div class="py-12" x-data="reportForm()">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-gray-800/40 backdrop-blur-xl border border-gray-700/50 shadow-2xl rounded-3xl p-8 relative overflow-hidden">
                <div class="absolute -top-24 -right-24 w-48 h-48 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <form action="{{ route('reports.pdf') }}" method="GET" class="space-y-6 relative z-10" target="_blank">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Wybór Projektu -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Wybierz projekt</label>
                            <select name="project_id" x-model="selectedProjectId" @change="updateUsersList()" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-500 shadow-inner">
                                <option value="">Wszystkie projekty</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Wybór Pracownika -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Wybierz pracownika</label>
                            <select name="user_id" x-model="selectedUserId" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-500 shadow-inner">
                                <option value="">Wszyscy pracownicy</option>
                                <template x-for="user in currentUsers" :key="user.id">
                                    <option :value="user.id" x-text="user.name"></option>
                                </template>
                            </select>
                            <p class="text-[10px] text-gray-500 mt-1" x-show="!selectedProjectId">Wszyscy powiązani (dla wybranych projektów).</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-700/50 pt-6 mt-6">
                        <label class="block text-sm font-semibold text-gray-300 mb-4">Zakres czasowy</label>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Od daty</label>
                                <input type="date" name="date_from" x-model="dateFrom" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Do daty</label>
                                <input type="date" name="date_to" x-model="dateTo" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <button type="button" @click="setQuickDate('this_week')" class="px-3 py-1.5 text-xs font-semibold bg-gray-700 hover:bg-gray-600 text-gray-300 rounded-lg transition-colors cursor-pointer border border-gray-600">Obecny tydzień</button>
                            <button type="button" @click="setQuickDate('last_week')" class="px-3 py-1.5 text-xs font-semibold bg-gray-700 hover:bg-gray-600 text-gray-300 rounded-lg transition-colors cursor-pointer border border-gray-600">Zeszły tydzień</button>
                            <button type="button" @click="setQuickDate('this_month')" class="px-3 py-1.5 text-xs font-semibold bg-gray-700 hover:bg-gray-600 text-gray-300 rounded-lg transition-colors cursor-pointer border border-gray-600">Obecny miesiąc</button>
                            <button type="button" @click="setQuickDate('last_month')" class="px-3 py-1.5 text-xs font-semibold bg-gray-700 hover:bg-gray-600 text-gray-300 rounded-lg transition-colors cursor-pointer border border-gray-600">Poprzedni miesiąc</button>
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-xl shadow-[0_0_20px_rgba(147,51,234,0.3)] transition-transform transform hover:-translate-y-1 flex items-center justify-center gap-2 cursor-pointer border border-purple-500/50">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Generuj Raport PDF
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('reportForm', () => ({
                projectsData: @json($projects),
                selectedProjectId: '',
                selectedUserId: '',
                currentUsers: [],
                dateFrom: '',
                dateTo: '',

                init() {
                    // Domyślnie pełny bieżący miesiąc
                    this.setQuickDate('this_month');
                },

                updateUsersList() {
                    if (!this.selectedProjectId) {
                        this.currentUsers = [];
                        this.selectedUserId = '';
                        return;
                    }

                    let project = this.projectsData.find(p => p.id == this.selectedProjectId);
                    if (project && project.users) {
                        this.currentUsers = project.users;
                    } else {
                        this.currentUsers = [];
                    }
                    this.selectedUserId = ''; // Zresetuj wybranego pracownika
                },

                setQuickDate(range) {
                    let today = new Date();
                    let from, to;

                    if (range === 'this_week') {
                        let day = today.getDay() || 7; // Niedziela to 7
                        from = new Date(today);
                        from.setDate(today.getDate() - day + 1);
                        to = new Date(from);
                        to.setDate(from.getDate() + 6);
                    } else if (range === 'last_week') {
                        let day = today.getDay() || 7;
                        from = new Date(today);
                        from.setDate(today.getDate() - day - 6);
                        to = new Date(from);
                        to.setDate(from.getDate() + 6);
                    } else if (range === 'this_month') {
                        from = new Date(today.getFullYear(), today.getMonth(), 1);
                        to = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    } else if (range === 'last_month') {
                        from = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                        to = new Date(today.getFullYear(), today.getMonth(), 0);
                    }

                    // Format YYYY-MM-DD
                    const format = (d) => {
                        let y = d.getFullYear();
                        let m = (d.getMonth() + 1).toString().padStart(2, '0');
                        let day = d.getDate().toString().padStart(2, '0');
                        return `${y}-${m}-${day}`;
                    };

                    this.dateFrom = format(from);
                    this.dateTo = format(to);
                }
            }));
        });
    </script>
</x-app-layout>

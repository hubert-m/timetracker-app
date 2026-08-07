<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold leading-tight text-white flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                Zestawienia godzin
            </h2>
        </div>
    </x-slot>

    <div class="py-8" x-data="timesheet()">
        <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800/60 backdrop-blur-xl border border-gray-700/60 shadow-xl rounded-2xl overflow-hidden relative">
                <div class="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-transparent pointer-events-none"></div>
                
                <div class="p-6 overflow-x-auto relative z-10">
                    <p class="text-sm text-gray-400 mb-6">Wprowadź czas dla odpowiednich dni (możesz wpisać w formacie HH:MM np. <code>02:30</code> lub ułamek np. <code>2.5</code>). Dane zapisują się automatycznie po odkliknięciu.</p>

                    <table class="w-full text-left text-sm text-gray-300 rounded-xl overflow-hidden border-collapse">
                        <thead class="bg-gray-900/80 text-gray-400 uppercase font-bold text-xs border-b border-gray-700">
                            <tr>
                                <th class="px-6 py-4 rounded-tl-xl w-1/4">Zadanie</th>
                                @foreach($dates as $date)
                                    <th class="px-4 py-4 text-center border-l border-gray-700/30 {{ $loop->last ? 'rounded-tr-xl' : '' }}">
                                        {{ \Carbon\Carbon::parse($date)->translatedFormat('d.m') }}<br>
                                        <span class="text-[10px] text-gray-500">{{ \Carbon\Carbon::parse($date)->translatedFormat('D') }}</span>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/50 bg-gray-900/30">
                            @foreach($folders as $folder)
                                @php
                                    $hasTasks = false;
                                    foreach($folder->projects as $p) {
                                        if($p->tasks->count() > 0) $hasTasks = true;
                                    }
                                @endphp
                                @if($hasTasks)
                                    <tr class="bg-gray-800/80 border-t-2 border-indigo-500/20">
                                        <td colspan="{{ count($dates) + 1 }}" class="px-6 py-2.5 font-bold text-indigo-300 text-xs uppercase tracking-wider flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                            {{ $folder->name }}
                                        </td>
                                    </tr>
                                    @foreach($folder->projects as $project)
                                        @foreach($project->tasks as $task)
                                            <tr class="hover:bg-gray-800/80 transition-colors group">
                                                <td class="px-6 py-3 border-r border-gray-700/30">
                                                    <div class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-0.5 group-hover:text-indigo-400 transition-colors">{{ $project->title }}</div>
                                                    <div class="font-medium text-gray-200">{{ $task->title }}</div>
                                                </td>
                                                @foreach($dates as $date)
                                                    @php
                                                        $mins = $timeLogsMatrix[$task->id][$date] ?? 0;
                                                        $formatted = $mins > 0 ? floor($mins / 60) . ':' . str_pad($mins % 60, 2, '0', STR_PAD_LEFT) : '';
                                                    @endphp
                                                    <td class="px-3 py-2 text-center border-r border-gray-700/30 relative">
                                                        <input type="text" 
                                                            class="w-full max-w-[80px] bg-transparent border border-transparent hover:border-gray-600 focus:border-amber-500 focus:bg-gray-900 focus:ring-2 focus:ring-amber-500/50 rounded-lg px-2 py-1.5 text-center text-amber-400 font-mono text-sm transition-all placeholder-gray-700"
                                                            value="{{ $formatted }}"
                                                            placeholder="-"
                                                            @change="updateTime({{ $task->id }}, '{{ $date }}', $event.target.value, $event.target)"
                                                        >
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endif
                            @endforeach
                            
                            @php
                                $hasUnassignedTasks = false;
                                foreach($unassignedProjects as $p) {
                                    if($p->tasks->count() > 0) $hasUnassignedTasks = true;
                                }
                            @endphp
                            
                            @if($hasUnassignedTasks)
                                <tr class="bg-gray-800/80 border-t-2 border-gray-600/20">
                                    <td colspan="{{ count($dates) + 1 }}" class="px-6 py-2.5 font-bold text-gray-400 text-xs uppercase tracking-wider flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        Projekty bez katalogu
                                    </td>
                                </tr>
                                @foreach($unassignedProjects as $project)
                                    @foreach($project->tasks as $task)
                                        <tr class="hover:bg-gray-800/80 transition-colors group">
                                            <td class="px-6 py-3 border-r border-gray-700/30">
                                                <div class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-0.5 group-hover:text-indigo-400 transition-colors">{{ $project->title }}</div>
                                                <div class="font-medium text-gray-200">{{ $task->title }}</div>
                                            </td>
                                            @foreach($dates as $date)
                                                @php
                                                    $mins = $timeLogsMatrix[$task->id][$date] ?? 0;
                                                    $formatted = $mins > 0 ? floor($mins / 60) . ':' . str_pad($mins % 60, 2, '0', STR_PAD_LEFT) : '';
                                                @endphp
                                                <td class="px-3 py-2 text-center border-r border-gray-700/30 relative">
                                                    <input type="text" 
                                                        class="w-full max-w-[80px] bg-transparent border border-transparent hover:border-gray-600 focus:border-amber-500 focus:bg-gray-900 focus:ring-2 focus:ring-amber-500/50 rounded-lg px-2 py-1.5 text-center text-amber-400 font-mono text-sm transition-all placeholder-gray-700"
                                                        value="{{ $formatted }}"
                                                        placeholder="-"
                                                        @change="updateTime({{ $task->id }}, '{{ $date }}', $event.target.value, $event.target)"
                                                    >
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('timesheet', () => ({
            async updateTime(taskId, date, value, inputEl) {
                let originalValue = inputEl.defaultValue;
                
                // parse value "HH:MM" or "2.5" to minutes
                let minutes = 0;
                if (value.trim() !== '') {
                    if (value.includes(':')) {
                        let parts = value.split(':');
                        minutes = parseInt(parts[0]) * 60 + parseInt(parts[1]);
                    } else {
                        minutes = Math.round(parseFloat(value) * 60);
                    }
                }
                
                if (isNaN(minutes) || minutes < 0) minutes = 0;
                
                // Re-format back to input to show the user parsed value
                if (minutes > 0) {
                    let h = Math.floor(minutes / 60);
                    let m = (minutes % 60).toString().padStart(2, '0');
                    inputEl.value = h + ':' + m;
                } else {
                    inputEl.value = '';
                }
                
                // Pokaż UI ładowania
                inputEl.classList.add('opacity-50');

                try {
                    let res = await fetch('/time-logs/update-inline', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            task_id: taskId,
                            date: date,
                            duration_minutes: minutes
                        })
                    });
                    
                    if (!res.ok) {
                        let err = await res.json();
                        alert(err.message || 'Wystąpił błąd przy zapisie.');
                        inputEl.value = originalValue; // revert
                    } else {
                        inputEl.defaultValue = inputEl.value; // set new default
                        
                        // Zaznaczenie pomyślnego zapisu błyskiem na zielono
                        inputEl.classList.remove('focus:border-amber-500', 'text-amber-400');
                        inputEl.classList.add('border-emerald-500', 'text-emerald-400');
                        setTimeout(() => {
                            inputEl.classList.add('focus:border-amber-500', 'text-amber-400');
                            inputEl.classList.remove('border-emerald-500', 'text-emerald-400');
                        }, 1000);
                    }
                } catch (e) {
                    console.error(e);
                    alert('Błąd sieci.');
                    inputEl.value = originalValue; // revert
                }
                
                inputEl.classList.remove('opacity-50');
            }
        }));
    });
    </script>
</x-app-layout>

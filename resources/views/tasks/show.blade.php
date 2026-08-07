<x-app-layout>
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(31, 41, 55, 0.5); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(75, 85, 99, 0.8); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(107, 114, 128, 1); }
    </style>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between md:items-center w-full gap-4">
            <div>
                <a href="{{ route('projects.show', $task->project->id) }}" class="text-indigo-400 hover:text-indigo-300 text-sm font-semibold mb-1 block transition-colors">&larr; Wróć do projektu: {{ $task->project->name ?? $task->project->title }}</a>
                <h2 class="font-extrabold text-2xl text-white tracking-tight drop-shadow-sm flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-sm" style="background-color: {{ $task->project->color ?? '#6366f1' }}">
                        {{ strtoupper(substr($task->title, 0, 1)) }}
                    </div>
                    {{ $task->title }}
                </h2>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                <button onclick="toggleComplete({{ $task->id }})" class="px-5 py-2 {{ $task->is_completed ? 'bg-gray-700 hover:bg-gray-600 border-gray-500' : 'bg-emerald-600 hover:bg-emerald-500 shadow-[0_0_15px_rgba(16,185,129,0.4)] border-emerald-500/50' }} text-white rounded-xl transition-transform transform hover:-translate-y-1 font-semibold flex items-center gap-2 text-sm cursor-pointer border">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ $task->is_completed ? 'Otwórz ponownie' : 'Ukończ zadanie' }}
                </button>
                @if($isProjectMember && ($isOwner || ($permissions && $permissions['can_add_task_members'])))
                <button onclick="document.getElementById('taskInviteModal').style.display='block'" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow-[0_0_15px_rgba(79,70,229,0.5)] transition-transform transform hover:-translate-y-1 font-semibold flex items-center gap-2 text-sm cursor-pointer border border-indigo-500/50">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Zaproś do Zadania
                </button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Główna zawartość -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Opis Zadania -->
                    <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-3xl p-8 shadow-xl relative group">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold text-white">O zadaniu</h3>
                            @if($isProjectMember && ($isOwner || ($permissions && $permissions['can_edit_tasks'])))
                            <button onclick="document.getElementById('editTaskModal').style.display='block'" class="px-3 py-1 bg-gray-700 hover:bg-gray-600 text-gray-300 text-xs rounded-lg transition opacity-0 group-hover:opacity-100 cursor-pointer">
                                Edytuj
                            </button>
                            @endif
                        </div>
                        <p class="text-gray-300 leading-relaxed">{{ $task->description ?? 'Nie dodano opisu do tego zadania.' }}</p>
                    </div>

                    <!-- Historia Trackowania (Time Logs) -->
                    <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-3xl p-8 shadow-xl">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-white">Zaraportowany Czas</h3>
                            <div class="text-indigo-400 font-bold bg-indigo-900/30 px-4 py-2 rounded-xl text-sm border border-indigo-700/50 shadow-inner">
                                @php
                                    $totalSeconds = 0;
                                    foreach($timeLogs as $log) {
                                        if($log->end_time) {
                                            $totalSeconds += \Carbon\Carbon::parse($log->start_time)->diffInSeconds(\Carbon\Carbon::parse($log->end_time));
                                        }
                                    }
                                    $h = floor($totalSeconds / 3600);
                                    $m = floor(($totalSeconds % 3600) / 60);
                                @endphp
                                Łącznie: {{ $h }}h {{ $m }}m
                            </div>
                        </div>
                        
                        <div class="space-y-4 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                            @forelse($timeLogs as $log)
                                @if($log->start_time == $log->end_time && $log->duration_minutes == 0)
                                <!-- Event Systemowy -->
                                @php $isCompletedEvent = str_contains($log->description, 'ukończone'); @endphp
                                <div class="p-3 {{ $isCompletedEvent ? 'bg-emerald-900/20 border-emerald-700/30' : 'bg-indigo-900/20 border-indigo-700/30' }} rounded-2xl border flex items-center gap-4 transition-colors">
                                    @if($log->user && $log->user->avatar)
                                        <img src="{{ asset('storage/' . $log->user->avatar) }}" alt="{{ $log->user->name }}" class="w-8 h-8 rounded-full object-cover ring-2 {{ $isCompletedEvent ? 'ring-emerald-500/60' : 'ring-indigo-500/60' }}">
                                    @else
                                        <div class="w-8 h-8 rounded-full {{ $isCompletedEvent ? 'bg-emerald-500/20 text-emerald-400' : 'bg-indigo-500/20 text-indigo-400' }} flex items-center justify-center text-xs font-bold">
                                            {{ substr($log->user->name ?? '?', 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <p class="text-sm {{ $isCompletedEvent ? 'text-emerald-300' : 'text-indigo-300' }}">
                                            <span class="font-bold text-white">{{ $log->user->name ?? 'System' }}</span> 
                                            <span class="text-gray-300 font-medium ml-1">&mdash; <span class="{{ $isCompletedEvent ? 'text-emerald-400 font-semibold' : '' }}">{{ $log->description }}</span></span>
                                        </p>
                                        <p class="text-xs {{ $isCompletedEvent ? 'text-emerald-500/70' : 'text-indigo-500/70' }} mt-0.5">{{ \Carbon\Carbon::parse($log->start_time)->timezone(Auth::user()->timezone ?? 'Europe/Warsaw')->format('d.m.Y H:i') }}</p>
                                    </div>
                                </div>
                                @else
                                <div class="p-4 bg-gray-900/60 rounded-2xl border border-gray-700/50 flex flex-col sm:flex-row justify-between sm:items-center gap-4 hover:border-indigo-500/30 transition-colors">
                                    <div class="flex items-center gap-3">
                                        @if($log->user && $log->user->avatar)
                                            <img src="{{ asset('storage/' . $log->user->avatar) }}" alt="{{ $log->user->name }}" class="w-10 h-10 rounded-full object-cover border border-gray-600 shadow-sm">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center text-sm font-bold text-white border border-gray-600 shadow-sm">
                                                {{ substr($log->user->name ?? '?', 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-sm font-bold text-white">{{ $log->user->name ?? 'Nieznany' }}</p>
                                            <p class="text-xs text-gray-400 mt-0.5">
                                                {{ \Carbon\Carbon::parse($log->date ?? $log->start_time)->timezone(Auth::user()->timezone ?? 'Europe/Warsaw')->format('d.m.Y') }} &middot; 
                                                <span class="text-gray-300">
                                                    @if($log->start_time)
                                                        {{ \Carbon\Carbon::parse($log->start_time)->timezone(Auth::user()->timezone ?? 'Europe/Warsaw')->format('H:i') }} - {{ $log->end_time ? \Carbon\Carbon::parse($log->end_time)->timezone(Auth::user()->timezone ?? 'Europe/Warsaw')->format('H:i') : 'Teraz' }}
                                                    @else
                                                        Wpis ręczny
                                                    @endif
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right flex items-center gap-4">
                                        @if($log->description)
                                            <span class="text-xs text-gray-500 italic max-w-[150px] truncate hidden sm:block" title="{{ $log->description }}">
                                                "{{ $log->description }}"
                                            </span>
                                        @endif
                                        <div class="bg-gray-800/80 px-3 py-1.5 rounded-lg border border-gray-700/50">
                                            <span class="font-mono text-sm font-semibold {{ ($log->start_time && !$log->end_time) ? 'text-yellow-400 animate-pulse' : 'text-emerald-400' }}">
                                                @if($log->start_time && !$log->end_time)
                                                    Trwa...
                                                @elseif($log->start_time && $log->end_time)
                                                    @php
                                                        $s = \Carbon\Carbon::parse($log->start_time)->diffInSeconds(\Carbon\Carbon::parse($log->end_time));
                                                        echo sprintf('%02d:%02d:%02d', floor($s/3600), floor(($s%3600)/60), $s%60);
                                                    @endphp
                                                @else
                                                    @php
                                                        $m = $log->duration_minutes;
                                                        echo sprintf('%02d:%02d:00', floor($m/60), $m%60);
                                                    @endphp
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @empty
                                <div class="text-center py-10 bg-gray-900/30 rounded-2xl border border-gray-800 border-dashed">
                                    <p class="text-gray-500 text-sm">Brak historii zaraportowanego czasu dla tego zadania.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Panel Boczny: Zespół -->
                <div class="space-y-6 h-fit">
                    <!-- Sekcja: Członkowie projektu -->
                    <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-3xl p-8 shadow-xl">
                        <h3 class="text-xl font-bold text-white mb-6 flex items-center justify-between">
                            Członkowie projektu
                            <span class="bg-indigo-500/20 text-indigo-400 py-1 px-3 rounded-full text-sm">{{ count($projectUsers) }}</span>
                        </h3>
                        
                        <ul class="space-y-4">
                            @foreach($projectUsers as $user)
                            <li class="group/puser flex items-center gap-4 p-3 bg-gray-900/50 rounded-xl border border-gray-700/30 relative hover:border-gray-600 transition-colors">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover border border-gray-600 shadow-sm">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center text-sm font-bold text-white border border-gray-600 shadow-sm">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0 flex items-center gap-2">
                                    <div>
                                        <p class="text-sm font-semibold text-white truncate">{{ $user->name }}</p>
                                        <p class="text-[10px] text-gray-500 tracking-wide">{{ $user->email }}</p>
                                    </div>
                                    @if($user->id === $task->creator_id)
                                    <div class="relative flex items-center justify-center group/creator cursor-default ml-auto mr-2">
                                        <svg class="w-5 h-5 text-emerald-500 drop-shadow-[0_0_5px_rgba(16,185,129,0.6)]" fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                                        </svg>
                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2.5 py-1 bg-gray-800 text-emerald-500 text-[10px] uppercase font-extrabold rounded-md shadow-xl opacity-0 group-hover/creator:opacity-100 transition-opacity pointer-events-none whitespace-nowrap border border-emerald-500/20 z-50 tracking-wider">
                                            Twórca zadania
                                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-800"></div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </li>
                            @endforeach
                            
                            @foreach($pendingProject as $pending)
                            <li class="group/puser flex items-center gap-4 p-3 bg-gray-900/50 rounded-xl border border-gray-700/30 opacity-50 border-dashed relative">
                                <div class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-sm font-bold text-gray-400 border border-gray-600 shadow-sm">
                                    {{ strtoupper(substr($pending->email, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-300 italic truncate">{{ $pending->email }}</p>
                                    <p class="text-[10px] text-gray-500 uppercase tracking-wide font-bold">Oczekujący</p>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Sekcja: Przypisani do zadania -->
                    <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/50 rounded-3xl p-8 shadow-xl">
                        <h3 class="text-xl font-bold text-white mb-6 flex items-center justify-between">
                            Przypisani do zadania
                            <span class="bg-indigo-500/20 text-indigo-400 py-1 px-3 rounded-full text-sm">{{ count($taskUsers) }}</span>
                        </h3>
                        
                        <ul class="space-y-4">
                            @foreach($taskUsers as $user)
                            <li class="group/puser flex items-center gap-4 p-3 bg-gray-900/50 rounded-xl border border-gray-700/30 relative hover:border-gray-600 transition-colors">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover border border-gray-600 shadow-sm">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center text-sm font-bold text-white border border-gray-600 shadow-sm">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0 flex items-center gap-2">
                                    <div>
                                        <p class="text-sm font-semibold text-white truncate">{{ $user->name }}</p>
                                        <p class="text-[10px] text-gray-500 tracking-wide">{{ $user->email }}</p>
                                    </div>
                                    @if($user->id === $task->creator_id)
                                    <div class="relative flex items-center justify-center group/creator cursor-default ml-auto mr-2">
                                        <svg class="w-5 h-5 text-emerald-500 drop-shadow-[0_0_5px_rgba(16,185,129,0.6)]" fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                                        </svg>
                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2.5 py-1 bg-gray-800 text-emerald-500 text-[10px] uppercase font-extrabold rounded-md shadow-xl opacity-0 group-hover/creator:opacity-100 transition-opacity pointer-events-none whitespace-nowrap border border-emerald-500/20 z-50 tracking-wider">
                                            Twórca zadania
                                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-800"></div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @if($isProjectMember && ($isOwner || ($permissions && $permissions['can_remove_task_members'])) && $user->id !== Auth::id())
                                    <button onclick="removeTaskUser({{ $task->id }}, {{ $user->id }})" class="absolute right-3 top-1/2 -translate-y-1/2 px-3 py-1.5 bg-red-600/90 hover:bg-red-500 text-white font-medium text-xs rounded-lg opacity-0 group-hover/puser:opacity-100 transition-opacity cursor-pointer shadow-lg shadow-red-900/20">
                                        Usuń
                                    </button>
                                @endif
                            </li>
                            @endforeach
                            
                            @foreach($pendingTask as $pending)
                            <li class="group/puser flex items-center gap-4 p-3 bg-gray-900/50 rounded-xl border border-gray-700/30 opacity-50 border-dashed relative">
                                <div class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-sm font-bold text-gray-400 border border-gray-600 shadow-sm">
                                    {{ strtoupper(substr($pending->email, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-300 italic truncate">{{ $pending->email }}</p>
                                    <p class="text-[10px] text-gray-500 uppercase tracking-wide font-bold">Oczekujący</p>
                                </div>
                                @if($isProjectMember && ($isOwner || ($permissions && $permissions['can_remove_task_members'])))
                                <button onclick="cancelInvitation({{ $pending->id }})" class="absolute right-3 top-1/2 -translate-y-1/2 px-3 py-1.5 bg-red-600/90 hover:bg-red-500 text-white font-medium text-xs rounded-lg opacity-0 group-hover/puser:opacity-100 transition-opacity cursor-pointer shadow-lg shadow-red-900/20">
                                    Wycofaj
                                </button>
                                @endif
                            </li>
                            @endforeach

                            @if($taskUsers->isEmpty() && $pendingTask->isEmpty())
                                <p class="text-gray-500 text-sm italic text-center py-4">Brak przypisanych osób spoza projektu.</p>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modale i Skrypty -->
    <div id="taskInviteModal" style="display:none;" x-data="taskInviteForm()" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" onclick="document.getElementById('taskInviteModal').style.display='none'"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-gray-800 rounded-2xl text-left overflow-visible shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-700">
                <form @submit.prevent="submitTaskInvite">
                    <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-xl font-bold text-white mb-6">Zaproś do Zadania</h3>
                        <div class="mb-4 relative">
                            <label class="block text-sm font-medium text-gray-300 mb-1">Adres e-mail zapraszanego</label>
                            <input type="email" autocomplete="off" x-model="email" @input.debounce.300ms="fetchSuggestions" @focus="showSuggestions = true" @click.away="showSuggestions = false" required placeholder="np. jan@kowalski.pl" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <div x-show="showSuggestions && suggestions.length > 0" style="display: none;" class="absolute z-10 w-full mt-1 bg-gray-800 border border-gray-700 rounded-xl shadow-2xl max-h-48 overflow-y-auto">
                                <template x-for="suggestion in suggestions" :key="suggestion.email">
                                    <div @click="!suggestion.is_member && selectSuggestion(suggestion.email)" 
                                         :class="suggestion.is_member ? 'opacity-50 cursor-not-allowed bg-gray-900' : 'cursor-pointer hover:bg-indigo-600'" 
                                         class="px-4 py-3 text-gray-200 text-sm transition-colors flex items-center justify-between border-b border-gray-700/50 last:border-0 group">
                                        <div class="flex items-center gap-3">
                                            <div class="w-7 h-7 rounded-full bg-gray-900 flex items-center justify-center text-xs font-bold text-gray-300 border border-gray-700" x-text="suggestion.email.charAt(0).toUpperCase()"></div>
                                            <span x-text="suggestion.email" class="font-medium"></span>
                                        </div>
                                        <span x-show="suggestion.is_member" class="text-[10px] uppercase font-bold text-gray-500 bg-gray-800 px-2 py-0.5 rounded">Już należy</span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div x-show="message" x-text="message" :class="isSuccess ? 'text-green-400' : 'text-red-400'" class="text-sm mt-2 font-medium" style="display: none;"></div>
                    </div>
                    <div class="bg-gray-900/50 px-4 py-4 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-700/50">
                        <button type="submit" :disabled="isSubmitting" class="w-full inline-flex justify-center rounded-xl px-6 py-2.5 bg-indigo-600 text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto text-sm disabled:opacity-50 transition cursor-pointer">
                            <span x-show="!isSubmitting">Wyślij zaproszenie</span><span x-show="isSubmitting">Wysyłanie...</span>
                        </button>
                        <button type="button" onclick="document.getElementById('taskInviteModal').style.display='none'" class="mt-3 w-full inline-flex justify-center rounded-xl px-6 py-2.5 bg-gray-800 text-gray-300 hover:bg-gray-700 border border-gray-600 sm:mt-0 sm:ml-3 sm:w-auto text-sm transition cursor-pointer">Zamknij</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edycji Zadania -->
    <div id="editTaskModal" style="display:none;" x-data="editTaskForm()" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" onclick="document.getElementById('editTaskModal').style.display='none'"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-700">
                <form @submit.prevent="submitEditTask">
                    <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-xl font-bold text-white mb-6">Edytuj zadanie</h3>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-300 mb-1">Nazwa zadania</label>
                            <input type="text" x-model="title" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-300 mb-1">Opis (opcjonalnie)</label>
                            <textarea x-model="description" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" rows="3"></textarea>
                        </div>
                        <div x-show="message" x-text="message" class="text-sm mt-2 font-medium text-green-400" style="display: none;"></div>
                    </div>
                    <div class="bg-gray-900/50 px-4 py-4 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-700/50">
                        <button type="submit" :disabled="isSubmitting" class="w-full inline-flex justify-center rounded-xl px-6 py-2.5 bg-indigo-600 text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto text-sm disabled:opacity-50 transition cursor-pointer">Zapisz</button>
                        <button type="button" onclick="document.getElementById('editTaskModal').style.display='none'" class="mt-3 w-full inline-flex justify-center rounded-xl px-6 py-2.5 bg-gray-800 text-gray-300 hover:bg-gray-700 border border-gray-600 sm:mt-0 sm:ml-3 sm:w-auto text-sm transition cursor-pointer">Anuluj</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        async function toggleComplete(taskId) {
            const res = await fetch(`/tasks/${taskId}/toggle-complete`, {
                method: 'PATCH',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });
            if(res.ok) window.location.reload();
            else alert('Wystąpił błąd.');
        }

        async function removeTaskUser(taskId, userId) {
            if(!confirm('Czy usunąć tę osobę z wybranego zadania?')) return;
            const res = await fetch(`/tasks/${taskId}/users/${userId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });
            if(res.ok) window.location.reload();
            else alert('Wystąpił błąd przy usuwaniu.');
        }

        async function cancelInvitation(invitationId) {
            if(!confirm('Czy na pewno chcesz wycofać to zaproszenie?')) return;
            const res = await fetch(`/invitations/${invitationId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });
            if(res.ok) window.location.reload();
            else alert('Wystąpił błąd podczas anulowania zaproszenia.');
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('taskInviteForm', () => ({
                email: '',
                isSubmitting: false,
                message: '',
                isSuccess: false,
                suggestions: [],
                showSuggestions: false,

                async fetchSuggestions() {
                    if (this.email.length < 3) {
                        this.suggestions = [];
                        return;
                    }
                    try {
                        const res = await fetch(`/invitations/suggestions?q=${encodeURIComponent(this.email)}&task_id={{ $task->id }}`);
                        if (res.ok) {
                            this.suggestions = await res.json();
                            this.showSuggestions = true;
                        }
                    } catch (e) {
                        console.error('Błąd podpowiedzi', e);
                    }
                },

                selectSuggestion(suggestion) {
                    this.email = suggestion;
                    this.showSuggestions = false;
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
                                resource_id: {{ $task->id }}
                            })
                        });
                        
                        const data = await res.json();
                        if (res.ok) {
                            this.isSuccess = true;
                            this.message = data.message || 'Pomyślnie wysłano zaproszenie!';
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

            Alpine.data('editTaskForm', () => ({
                title: '{{ addslashes($task->title) }}',
                description: '{{ addslashes($task->description ?? '') }}',
                isSubmitting: false,
                message: '',
                
                async submitEditTask() {
                    this.isSubmitting = true;
                    this.message = '';
                    try {
                        const res = await fetch(`/tasks/{{ $task->id }}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                title: this.title,
                                description: this.description
                            })
                        });
                        if (res.ok) {
                            this.message = 'Zapisano zadanie pomyślnie!';
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            this.message = 'Nie udało się zaktualizować.';
                        }
                    } catch (e) {
                        this.message = 'Błąd serwera.';
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }));
        });
    </script>
</x-app-layout>

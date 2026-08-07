<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TimeLog;
use App\Models\ProjectFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TimeLogController extends Controller
{
    private function checkAccess(Task $task)
    {
        $userId = Auth::id();
        
        $isAssignedToTask = $task->users()->where('user_id', $userId)->exists();
        if ($isAssignedToTask) {
            return true;
        }

        $isAssignedToProject = $task->project->users()->where('user_id', $userId)->exists();
        if ($isAssignedToProject) {
            return true;
        }

        abort(403, 'Nie masz dostępu do logowania czasu w tym zadaniu.');
    }

    public function resources(Request $request)
    {
        $userId = Auth::id();

        // Zwracamy katalogi z projektami z zadaniami dla aktualnego uzytkownika
        $folders = ProjectFolder::with(['projects' => function ($q) use ($userId) {
            $q->whereHas('users', function ($q2) use ($userId) {
                $q2->where('users.id', $userId);
            })->with(['tasks' => function ($q3) use ($userId) {
                $q3->whereHas('users', function ($q4) use ($userId) {
                    $q4->where('users.id', $userId);
                });
            }]);
        }])
        ->where('user_id', $userId)
        ->get();

        // Pobieramy tez projekty bez folderow
        $unassignedProjects = \App\Models\Project::whereHas('users', function ($q) use ($userId) {
                $q->where('users.id', $userId);
            })
            ->whereDoesntHave('folders', function($q) use ($userId) {
                $q->where('project_folder_assignments.user_id', $userId);
            })
            ->with(['tasks' => function ($q) use ($userId) {
                $q->whereHas('users', function ($q2) use ($userId) {
                    $q2->where('users.id', $userId);
                });
            }])->get();

        return response()->json([
            'folders' => $folders,
            'unassigned_projects' => $unassignedProjects
        ]);
    }

    public function activeTimer(Request $request)
    {
        $activeLog = TimeLog::with('task.project')
            ->where('user_id', Auth::id())
            ->whereNull('end_time')
            ->whereNotNull('start_time')
            ->first();
            
        return response()->json(['active_log' => $activeLog]);
    }

    public function start(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
        ]);

        $task = Task::findOrFail($validated['task_id']);
        $this->checkAccess($task);

        $userId = Auth::id();

        // Zabezpieczenie przed dwoma stoperami naraz
        $activeLog = TimeLog::where('user_id', $userId)
            ->whereNull('end_time')
            ->whereNotNull('start_time')
            ->first();

        if ($activeLog) {
            return response()->json(['error' => 'Masz już uruchomiony stoper.'], 422);
        }

        $now = Carbon::now();

        $timeLog = TimeLog::create([
            'user_id' => $userId,
            'task_id' => $task->id,
            'date' => $now->toDateString(),
            'start_time' => $now->toTimeString(),
            'duration_minutes' => 0,
        ]);

        return response()->json(['message' => 'Stoper uruchomiony.', 'time_log_id' => $timeLog->id], 201);
    }

    public function stop(Request $request, TimeLog $timeLog)
    {
        if ($timeLog->user_id !== Auth::id()) {
            abort(403, 'Brak dostępu.');
        }

        if ($timeLog->end_time !== null) {
            return response()->json(['error' => 'Stoper został już zatrzymany.'], 422);
        }

        $now = Carbon::now();

        // Obliczanie czasu
        // Uwaga: Zapisujemy do DB tylko date i time. Żeby precyzyjnie wyliczyć czas dla stopera
        // stworzymy pełną datę startu z date i start_time, i porównamy z now.
        $startDateTime = Carbon::parse($timeLog->date . ' ' . $timeLog->start_time);
        $durationMinutes = $startDateTime->diffInMinutes($now);

        $timeLog->update([
            'end_time' => $now->toTimeString(),
            'duration_minutes' => $durationMinutes,
        ]);

        return response()->json(['message' => 'Stoper zatrzymany.', 'time_log' => $timeLog]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'date' => 'required|date',
            'duration_minutes' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $task = Task::findOrFail($validated['task_id']);
        $this->checkAccess($task);

        $timeLog = TimeLog::create([
            'user_id' => Auth::id(),
            'task_id' => $task->id,
            'date' => $validated['date'],
            'start_time' => null,
            'end_time' => null,
            'duration_minutes' => $validated['duration_minutes'],
            'description' => $validated['description'] ?? null,
        ]);

        return response()->json(['message' => 'Czas dodany ręcznie.', 'time_log' => $timeLog], 201);
    }

    public function updateInline(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'date' => 'required|date',
            'duration_minutes' => 'required|numeric|min:0', // umozliwiamy tez np. usuniecie czasu ustawiając 0
        ]);

        $task = Task::findOrFail($validated['task_id']);
        $this->checkAccess($task);

        if ($validated['duration_minutes'] == 0) {
            TimeLog::where('user_id', Auth::id())
                ->where('task_id', $task->id)
                ->where('date', $validated['date'])
                ->delete();
                
            return response()->json(['message' => 'Czas zresetowany do zera.']);
        }

        // Najprościej wyczyścić dotychczasowe logi dla tego dnia (za ten task) i utworzyć jeden sumaryczny
        TimeLog::where('user_id', Auth::id())
            ->where('task_id', $task->id)
            ->where('date', $validated['date'])
            ->delete();

        $timeLog = TimeLog::create([
            'user_id' => Auth::id(),
            'task_id' => $task->id,
            'date' => $validated['date'],
            'start_time' => null,
            'end_time' => null,
            'duration_minutes' => $validated['duration_minutes'],
            'description' => null,
        ]);

        return response()->json(['message' => 'Czas zaktualizowany.', 'time_log' => $timeLog]);
    }
}

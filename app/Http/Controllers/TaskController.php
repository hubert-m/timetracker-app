<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Auth::user()->tasks()->with('project')->get();
        if (request()->wantsJson()) {
            return response()->json($tasks);
        }
        return view('tasks.index', compact('tasks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project = Project::findOrFail($validated['project_id']);
        $permissions = $project->userPermissions(Auth::id());
        if (!$permissions || !$permissions['can_create_tasks']) {
            abort(403, 'Brak uprawnień do tworzenia zadań w tym projekcie.');
        }

        $validated['creator_id'] = Auth::id();
        $task = Task::create($validated);
        $task->users()->attach(Auth::id());

        return response()->json($task, 201);
    }

    public function show(Task $task)
    {
        $userId = Auth::id();
        $isProjectMember = $task->project->users()->where('user_id', $userId)->exists();
        $isTaskMember = $task->users()->where('user_id', $userId)->exists();

        if (!$isProjectMember && !$isTaskMember) {
            abort(403, 'Unauthorized action.');
        }

        $projectUsers = $task->project->users;
        $taskUsers = $task->users->reject(function ($user) use ($projectUsers) {
            return $projectUsers->contains('id', $user->id);
        });

        $pendingProject = $task->project->pendingInvitations;
        $pendingTask = $task->pendingInvitations->reject(function ($pending) use ($pendingProject) {
            return $pendingProject->contains('email', $pending->email);
        });

        $timeLogs = $task->timeLogs()->with('user')->orderByDesc('start_time')->get();

        if (request()->wantsJson()) {
            return response()->json($task);
        }

        $permissions = $task->project->userPermissions($userId);
        $isOwner = $task->project->isOwner($userId);

        return view('tasks.show', compact('task', 'projectUsers', 'taskUsers', 'pendingProject', 'pendingTask', 'isProjectMember', 'timeLogs', 'permissions', 'isOwner'));
    }

    public function update(Request $request, Task $task)
    {
        $permissions = $task->project->userPermissions(Auth::id());
        $isTaskMember = $task->users()->where('user_id', Auth::id())->exists();
        if (!$isTaskMember && (!$permissions || !$permissions['can_edit_tasks'])) {
            abort(403, 'Brak uprawnień do edycji zadania.');
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $task->update($validated);
        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        $permissions = $task->project->userPermissions(Auth::id());
        $isTaskMember = $task->users()->where('user_id', Auth::id())->exists();
        if (!$isTaskMember && (!$permissions || !$permissions['can_edit_tasks'])) {
            abort(403, 'Brak uprawnień do usunięcia zadania.');
        }

        $task->delete();
        return response()->noContent();
    }

    public function removeUser(Task $task, $userId)
    {
        $permissions = $task->project->userPermissions(Auth::id());
        if (!$permissions || !$permissions['can_remove_task_members']) {
            abort(403, 'Brak uprawnień do usuwania członków z zadań.');
        }

        $user = \App\Models\User::findOrFail($userId);
        
        if ($task->creator_id == $userId) {
            abort(403, 'Nie można usunąć twórcy zadania.');
        }

        $task->users()->detach($userId);

        $user->notify(new \App\Notifications\UserRemovedNotification('Task', $task->title));

        return response()->json(['message' => 'Użytkownik został usunięty z zadania.']);
    }

    public function toggleComplete(Task $task)
    {
        $userId = Auth::id();
        $isProjectMember = $task->project->users()->where('user_id', $userId)->exists();
        $isTaskMember = $task->users()->where('user_id', $userId)->exists();

        if (!$isProjectMember && !$isTaskMember) {
            abort(403, 'Unauthorized action.');
        }

        $task->update(['is_completed' => !$task->is_completed]);

        $statusText = $task->is_completed ? 'Zadanie zostało ukończone.' : 'Zadanie zostało ponownie otwarte.';
        
        \App\Models\TimeLog::create([
            'user_id' => $userId,
            'task_id' => $task->id,
            'date' => now()->toDateString(),
            'start_time' => now(),
            'end_time' => now(),
            'duration_minutes' => 0,
            'description' => $statusText
        ]);

        return response()->json(['is_completed' => $task->is_completed, 'message' => $statusText]);
    }
}

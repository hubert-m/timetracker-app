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
        if (!$project->users()->where('user_id', Auth::id())->exists()) {
            abort(403, 'Unauthorized action.');
        }

        $task = Task::create($validated);
        $task->users()->attach(Auth::id());

        return response()->json($task, 201);
    }

    public function show(Task $task)
    {
        if (!$task->users()->where('user_id', Auth::id())->exists()) {
            abort(403, 'Unauthorized action.');
        }
        return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        if (!$task->users()->where('user_id', Auth::id())->exists()) {
            abort(403, 'Unauthorized action.');
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
        if (!$task->users()->where('user_id', Auth::id())->exists()) {
            abort(403, 'Unauthorized action.');
        }

        $task->delete();
        return response()->noContent();
    }

    public function removeUser(Task $task, $userId)
    {
        // Tylko admin głównego projektu może kogoś wyrzucić z zadania
        if (!$task->project->users()->where('user_id', Auth::id())->exists()) {
            abort(403, 'Unauthorized action.');
        }

        $user = \App\Models\User::findOrFail($userId);
        $task->users()->detach($userId);

        $user->notify(new \App\Notifications\UserRemovedNotification('Task', $task->title));

        return response()->json(['message' => 'Użytkownik został usunięty z zadania.']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $projects = Project::whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->orWhereHas('tasks.users', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();

        if (request()->wantsJson()) {
            return response()->json($projects);
        }
        return view('projects.index', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project = Project::create($validated);
        $project->users()->attach(Auth::id());

        return response()->json($project, 201);
    }

    public function show(Project $project)
    {
        $userId = Auth::id();
        $isProjectMember = $project->users()->where('user_id', $userId)->exists();
        
        // Sprawdź czy chociaż na jednym zadaniu w tym projekcie dany user jest przypisany
        $isTaskMember = $project->tasks()->whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->exists();

        if (!$isProjectMember && !$isTaskMember) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->wantsJson()) {
            return response()->json($project);
        }

        $users = $project->users()->get();

        // Pobranie Tasków odpowiednio do uprawnień
        if ($isProjectMember) {
            $tasks = $project->tasks()->with(['users', 'pendingInvitations'])->latest()->get();
        } else {
            $tasks = $project->tasks()->with(['users', 'pendingInvitations'])->whereHas('users', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->latest()->get();
        }

        $pendingInvitations = $project->pendingInvitations()->get();

        return view('projects.show', compact('project', 'users', 'tasks', 'isProjectMember', 'pendingInvitations'));
    }

    public function update(Request $request, Project $project)
    {
        if (!$project->users()->where('user_id', Auth::id())->exists()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project->update($validated);
        return response()->json($project);
    }

    public function destroy(Project $project)
    {
        if (!$project->users()->where('user_id', Auth::id())->exists()) {
            abort(403, 'Unauthorized action.');
        }

        $project->delete();
        return response()->noContent();
    }

    public function removeUser(Project $project, $userId)
    {
        if (!$project->users()->where('user_id', Auth::id())->exists()) {
            abort(403, 'Unauthorized action.');
        }

        $user = \App\Models\User::findOrFail($userId);
        $project->users()->detach($userId);

        $user->notify(new \App\Notifications\UserRemovedNotification('Project', $project->title ?? $project->name));

        return response()->json(['message' => 'Użytkownik został usunięty.']);
    }
}

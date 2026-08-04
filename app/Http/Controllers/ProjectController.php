<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $projects = Project::with('users')->whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->orWhereHas('tasks.users', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();

        $folders = ProjectFolder::where('user_id', $userId)
            ->orderBy('position')
            ->get();

        // Mapka project_id => folder_id dla bieżącego użytkownika
        $folderAssignments = DB::table('project_folder_assignments')
            ->where('user_id', $userId)
            ->pluck('folder_id', 'project_id')
            ->toArray();

        // Dołóż info o folderze do każdego projektu
        $foldersById = $folders->keyBy('id');

        if (request()->wantsJson()) {
            return response()->json($projects);
        }
        return view('projects.index', compact('projects', 'folders', 'folderAssignments', 'foldersById'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project = Project::create($validated);
        $project->users()->attach(Auth::id(), [
            'role' => 'owner',
            'can_add_members' => true,
            'can_remove_members' => true,
            'can_edit_project' => true,
            'can_create_tasks' => true,
            'can_edit_tasks' => true,
            'can_add_task_members' => true,
            'can_remove_task_members' => true,
        ]);

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
            $tasks = $project->tasks()->with(['users', 'pendingInvitations'])->orderBy('is_completed', 'asc')->latest()->get();
        } else {
            $tasks = $project->tasks()->with(['users', 'pendingInvitations'])->whereHas('users', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->orderBy('is_completed', 'asc')->latest()->get();
        }

        $pendingInvitations = $project->pendingInvitations()->get();

        $permissions = $isProjectMember ? $project->userPermissions($userId) : null;
        $isOwner = $isProjectMember ? $project->isOwner($userId) : false;

        return view('projects.show', compact('project', 'users', 'tasks', 'isProjectMember', 'pendingInvitations', 'permissions', 'isOwner'));
    }

    public function update(Request $request, Project $project)
    {
        $permissions = $project->userPermissions(Auth::id());
        if (!$permissions || !$permissions['can_edit_project']) {
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
        if (!$project->isOwner(Auth::id())) {
            abort(403, 'Tylko właściciel może usunąć projekt.');
        }

        $project->delete();
        return response()->noContent();
    }

    public function removeUser(Project $project, $userId)
    {
        $permissions = $project->userPermissions(Auth::id());
        if (!$permissions || !$permissions['can_remove_members']) {
            abort(403, 'Brak uprawnień do usuwania członków z projektu.');
        }

        if ($project->isOwner($userId)) {
            abort(403, 'Nie można usunąć właściciela z projektu.');
        }

        $user = \App\Models\User::findOrFail($userId);
        $project->users()->detach($userId);

        $user->notify(new \App\Notifications\UserRemovedNotification('Project', $project->title ?? $project->name));

        return response()->json(['message' => 'Użytkownik został usunięty.']);
    }

    public function updatePermissions(Request $request, Project $project, $userId)
    {
        if (!$project->isOwner(Auth::id())) {
            abort(403, 'Tylko właściciel może zmieniać uprawnienia.');
        }

        if ($project->isOwner($userId)) {
            abort(403, 'Nie można zmieniać uprawnień właścicielowi.');
        }

        $validated = $request->validate([
            'can_add_members' => 'boolean',
            'can_remove_members' => 'boolean',
            'can_edit_project' => 'boolean',
            'can_create_tasks' => 'boolean',
            'can_edit_tasks' => 'boolean',
            'can_add_task_members' => 'boolean',
            'can_remove_task_members' => 'boolean',
        ]);

        $project->users()->updateExistingPivot($userId, $validated);

        return response()->json(['message' => 'Uprawnienia zaktualizowane.']);
    }
}

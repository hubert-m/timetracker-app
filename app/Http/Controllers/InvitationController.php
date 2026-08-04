<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\PendingInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewInvitationNotification;

class InvitationController extends Controller
{
    public function suggestions(Request $request)
    {
        $q = $request->input('q');
        if (!$q || strlen($q) < 3) {
            return response()->json([]);
        }

        $userId = Auth::id();
        
        $projectIds = Auth::user()->projects()->pluck('projects.id');
        $taskIds = Auth::user()->tasks()->pluck('tasks.id');

        $users = User::where('id', '!=', $userId)
            ->where('email', 'like', "%{$q}%")
            ->where(function ($query) use ($projectIds, $taskIds) {
                $query->whereHas('projects', function ($qp) use ($projectIds) {
                    $qp->whereIn('projects.id', $projectIds);
                })->orWhereHas('tasks', function ($qt) use ($taskIds) {
                    $qt->whereIn('tasks.id', $taskIds);
                });
            })
            ->pluck('email');

        $pending = PendingInvitation::where('email', 'like', "%{$q}%")
            ->where(function ($query) use ($projectIds, $taskIds) {
                $query->where(function ($qp) use ($projectIds) {
                    $qp->where('invitable_type', Project::class)
                       ->whereIn('invitable_id', $projectIds);
                })->orWhere(function ($qt) use ($taskIds) {
                    $qt->where('invitable_type', Task::class)
                       ->whereIn('invitable_id', $taskIds);
                });
            })
            ->pluck('email');

        $suggestions = collect($users)
            ->merge($pending)
            ->unique()
            ->values();

        $projectId = $request->input('project_id');
        $taskId = $request->input('task_id');

        $result = $suggestions->map(function ($email) use ($projectId, $taskId) {
            $isMember = false;
            
            if ($projectId) {
                $project = Project::find($projectId);
                if ($project) {
                    $isMember = $project->users()->where('email', $email)->exists() ||
                                $project->pendingInvitations()->where('email', $email)->exists();
                }
            } elseif ($taskId) {
                $task = Task::find($taskId);
                if ($task) {
                    $isMember = $task->users()->where('email', $email)->exists() ||
                                $task->project->users()->where('email', $email)->exists() ||
                                $task->pendingInvitations()->where('email', $email)->exists();
                }
            }

            return [
                'email' => $email,
                'is_member' => $isMember
            ];
        });

        return response()->json($result);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'resource_type' => 'required|in:Project,Task',
            'resource_id' => 'required|integer',
        ]);

        $resourceClass = $validated['resource_type'] === 'Project' ? Project::class : Task::class;
        $resource = $resourceClass::findOrFail($validated['resource_id']);

        if ($validated['resource_type'] === 'Project') {
            $permissions = $resource->userPermissions(Auth::id());
            $hasAccess = $permissions && $permissions['can_add_members'];
        } elseif ($validated['resource_type'] === 'Task') {
            $permissions = $resource->project->userPermissions(Auth::id());
            $hasAccess = $permissions && $permissions['can_add_task_members'];
        }

        if (!$hasAccess) {
            abort(403, 'Brak uprawnień do zapraszania do tego zasobu.');
        }

        $invitedUser = User::where('email', $validated['email'])->first();

        if ($invitedUser) {
            $resource->users()->syncWithoutDetaching([$invitedUser->id]);
            
            $url = $validated['resource_type'] === 'Project' 
                ? route('projects.show', $resource->id) 
                : route('tasks.show', $resource->id);

            $invitedUser->notify(new NewInvitationNotification(
                $resource->title ?? $resource->name ?? 'Zasób',
                $validated['resource_type'],
                $url,
                Auth::user()->name
            ));

            return response()->json(['message' => 'Użytkownik został przypisany do zasobu oraz powiadomiony.']);
        }

        PendingInvitation::updateOrCreate(
            [
                'email' => $validated['email'],
                'invitable_type' => $resourceClass,
                'invitable_id' => $resource->id,
            ],
            [
                'inviter_id' => Auth::id(),
            ]
        );

        return response()->json(['message' => 'Zaproszenie zostało dodane do oczekujących.']);
    }

    public function destroy(PendingInvitation $invitation)
    {
        $resource = $invitation->invitable;
        $hasAccess = false;

        if ($invitation->invitable_type === 'App\Models\Project') {
            $permissions = $resource->userPermissions(Auth::id());
            $hasAccess = $permissions && $permissions['can_remove_members'];
        } elseif ($invitation->invitable_type === 'App\Models\Task') {
            $permissions = $resource->project->userPermissions(Auth::id());
            $hasAccess = $permissions && $permissions['can_remove_task_members'];
        }

        if (!$hasAccess) {
            abort(403, 'Brak uprawnień do anulowania tego zaproszenia.');
        }

        $invitation->delete();

        return response()->json(['message' => 'Zaproszenie zostało anulowane pomyślnie.']);
    }
}

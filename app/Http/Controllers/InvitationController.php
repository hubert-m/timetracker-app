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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'resource_type' => 'required|in:Project,Task',
            'resource_id' => 'required|integer',
        ]);

        $resourceClass = $validated['resource_type'] === 'Project' ? Project::class : Task::class;
        $resource = $resourceClass::findOrFail($validated['resource_id']);

        $hasAccess = $resource->users()->where('user_id', Auth::id())->exists();
        if (!$hasAccess && $validated['resource_type'] === 'Task') {
            $hasAccess = $resource->project->users()->where('user_id', Auth::id())->exists();
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
}

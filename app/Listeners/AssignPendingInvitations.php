<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\NewInvitationNotification;
use App\Notifications\UserAcceptedInvitationNotification;

class AssignPendingInvitations
{
    public function __construct() {}

    public function handle(Registered $event): void
    {
        $user = $event->user;
        $invitations = \App\Models\PendingInvitation::where('email', $user->email)->with('inviter')->get();

        foreach ($invitations as $invitation) {
            $invitable = $invitation->invitable;
            
            if ($invitable && method_exists($invitable, 'users')) {
                $invitable->users()->syncWithoutDetaching([$user->id]);
                
                $resourceType = class_basename($invitable);
                $resourceName = $invitable->title ?? $invitable->name ?? 'Zasób';
                $url = $resourceType === 'Project' ? route('projects.show', $invitable->id) : route('tasks.show', $invitable->id);
                $inviterName = $invitation->inviter ? $invitation->inviter->name : 'Członka platformy';

                $projectName = $resourceType === 'Task' ? ($invitable->project->title ?? $invitable->project->name ?? null) : null;

                // Powiadomienie dla nowo-zarejestrowanego o przypisaniu z kolejki
                $user->notify(new NewInvitationNotification($resourceName, $resourceType, $url, $inviterName, $projectName));

                // Powiadomienie dla autora zaproszenia
                if ($invitation->inviter) {
                    $invitation->inviter->notify(new UserAcceptedInvitationNotification($user->name, $resourceType, $resourceName, $url));
                }
            }
            
            $invitation->delete();
        }
    }
}

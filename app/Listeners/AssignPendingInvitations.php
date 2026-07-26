<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AssignPendingInvitations
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    public function handle(Registered $event): void
    {
        $user = $event->user;

        $invitations = \App\Models\PendingInvitation::where('email', $user->email)->get();

        foreach ($invitations as $invitation) {
            $invitable = $invitation->invitable;
            
            if ($invitable && method_exists($invitable, 'users')) {
                $invitable->users()->syncWithoutDetaching([$user->id]);
            }
            
            $invitation->delete();
        }
    }
}

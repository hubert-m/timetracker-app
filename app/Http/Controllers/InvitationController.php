<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\PendingInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        if (!$resource->users()->where('user_id', Auth::id())->exists()) {
            abort(403, 'Brak uprawnień do zapraszania do tego zasobu.');
        }

        $invitedUser = User::where('email', $validated['email'])->first();

        if ($invitedUser) {
            $resource->users()->syncWithoutDetaching([$invitedUser->id]);
            return response()->json(['message' => 'Użytkownik został przypisany do zasobu.']);
        }

        PendingInvitation::firstOrCreate([
            'email' => $validated['email'],
            'invitable_type' => $resourceClass,
            'invitable_id' => $resource->id,
        ]);

        return response()->json(['message' => 'Zaproszenie zostało dodane do oczekujących.']);
    }
}

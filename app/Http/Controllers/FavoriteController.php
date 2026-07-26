<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'resource_type' => 'required|in:Project,Task',
            'resource_id' => 'required|integer',
        ]);

        $resourceClass = $validated['resource_type'] === 'Project' ? Project::class : Task::class;
        $resource = $resourceClass::findOrFail($validated['resource_id']);

        if (!$resource->users()->where('user_id', Auth::id())->exists()) {
            abort(403, 'Brak dostępu do tego zasobu.');
        }

        $user = Auth::user();
        
        $favoritesRelation = $validated['resource_type'] === 'Project' ? 'favoriteProjects' : 'favoriteTasks';

        $isFavorited = $user->{$favoritesRelation}()->where('favoritable_id', $resource->id)->exists();

        if ($isFavorited) {
            $user->{$favoritesRelation}()->detach($resource->id);
            $status = 'removed';
        } else {
            $user->{$favoritesRelation}()->attach($resource->id);
            $status = 'added';
        }

        return response()->json(['message' => "Zasób został zaktualizowany w ulubionych.", 'status' => $status]);
    }
}

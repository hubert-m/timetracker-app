<?php

namespace App\Http\Controllers;

use App\Models\ProjectFolder;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectFolderController extends Controller
{
    public function index()
    {
        $folders = ProjectFolder::where('user_id', Auth::id())
            ->orderBy('position')
            ->withCount('projects')
            ->get();

        return response()->json($folders);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'color' => 'required|string|max:7',
        ]);

        $maxPosition = ProjectFolder::where('user_id', Auth::id())->max('position') ?? 0;

        $folder = ProjectFolder::create([
            'name' => $validated['name'],
            'color' => $validated['color'],
            'user_id' => Auth::id(),
            'position' => $maxPosition + 1,
        ]);

        return response()->json($folder, 201);
    }

    public function update(Request $request, ProjectFolder $folder)
    {
        if ($folder->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:50',
            'color' => 'sometimes|required|string|max:7',
        ]);

        $folder->update($validated);

        return response()->json($folder);
    }

    public function destroy(ProjectFolder $folder)
    {
        if ($folder->user_id !== Auth::id()) {
            abort(403);
        }

        // Projekty z tego folderu wracają do "bez folderu"
        Project::where('folder_id', $folder->id)->update(['folder_id' => null]);

        $folder->delete();

        return response()->json(['message' => 'Katalog usunięty.']);
    }

    public function assignProject(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|integer|exists:projects,id',
            'folder_id' => 'required|integer|exists:project_folders,id',
        ]);

        $folder = ProjectFolder::where('id', $validated['folder_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $project = Project::findOrFail($validated['project_id']);

        // Sprawdź czy user ma dostęp do projektu
        $hasAccess = $project->users()->where('user_id', Auth::id())->exists()
            || $project->tasks()->whereHas('users', fn($q) => $q->where('user_id', Auth::id()))->exists();

        if (!$hasAccess) {
            abort(403);
        }

        $project->update(['folder_id' => $folder->id]);

        return response()->json(['message' => 'Projekt przypisany do katalogu.']);
    }

    public function unassignProject(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|integer|exists:projects,id',
        ]);

        $project = Project::findOrFail($validated['project_id']);
        $project->update(['folder_id' => null]);

        return response()->json(['message' => 'Projekt usunięty z katalogu.']);
    }
}

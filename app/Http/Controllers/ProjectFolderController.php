<?php

namespace App\Http\Controllers;

use App\Models\ProjectFolder;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectFolderController extends Controller
{
    public function index()
    {
        $folders = ProjectFolder::where('user_id', Auth::id())
            ->orderBy('position')
            ->get();

        // Doliczymy projekty per folder dla bieżącego usera
        $folders->each(function ($folder) {
            $folder->projects_count = DB::table('project_folder_assignments')
                ->where('folder_id', $folder->id)
                ->where('user_id', Auth::id())
                ->count();
        });

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

        // Usunięcie przypisań tego folderu (cascade na FK też to zrobi, ale explicite)
        DB::table('project_folder_assignments')
            ->where('folder_id', $folder->id)
            ->delete();

        $folder->delete();

        return response()->json(['message' => 'Katalog usunięty.']);
    }

    public function assignProject(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|integer|exists:projects,id',
            'folder_id' => 'required|integer|exists:project_folders,id',
        ]);

        $userId = Auth::id();

        // Sprawdź czy folder należy do bieżącego usera
        $folder = ProjectFolder::where('id', $validated['folder_id'])
            ->where('user_id', $userId)
            ->firstOrFail();

        $project = Project::findOrFail($validated['project_id']);

        // Sprawdź czy user ma dostęp do projektu
        $hasAccess = $project->users()->where('user_id', $userId)->exists()
            || $project->tasks()->whereHas('users', fn($q) => $q->where('user_id', $userId))->exists();

        if (!$hasAccess) {
            abort(403);
        }

        // Upsert — zaktualizuj istniejące przypisanie lub utwórz nowe
        DB::table('project_folder_assignments')->updateOrInsert(
            ['user_id' => $userId, 'project_id' => $project->id],
            ['folder_id' => $folder->id, 'updated_at' => now(), 'created_at' => now()]
        );

        return response()->json(['message' => 'Projekt przypisany do katalogu.']);
    }

    public function unassignProject(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|integer|exists:projects,id',
        ]);

        DB::table('project_folder_assignments')
            ->where('user_id', Auth::id())
            ->where('project_id', $validated['project_id'])
            ->delete();

        return response()->json(['message' => 'Projekt usunięty z katalogu.']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Auth::user()->projects()->get();
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
        if (!$project->users()->where('user_id', Auth::id())->exists()) {
            abort(403, 'Unauthorized action.');
        }
        return response()->json($project);
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
}

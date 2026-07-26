<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Pobieramy ulubione projekty wraz z liczbą przypisanych użytkowników (statystyki)
        $favoriteProjects = $user->favoriteProjects()
            ->withCount('users')
            ->get();

        // Pobieramy ulubione zadania wraz z powiązanym projektem (N+1) oraz sumą zaraportowanego czasu (N+1 agregacja)
        $favoriteTasks = $user->favoriteTasks()
            ->with('project')
            ->withSum('timeLogs', 'duration_minutes')
            ->get();

        if ($request->wantsJson()) {
            return response()->json([
                'favoriteProjects' => $favoriteProjects,
                'favoriteTasks' => $favoriteTasks,
            ]);
        }

        return view('dashboard', compact('favoriteProjects', 'favoriteTasks'));
    }
}

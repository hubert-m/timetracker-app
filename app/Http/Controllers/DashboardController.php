<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TimeLog;
use Illuminate\Support\Carbon;

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

    public function stats(Request $request)
    {
        $user = Auth::user();

        // 1. Wykres kołowy: Czas w projektach (bieżący miesiąc)
        $projectsData = TimeLog::where('time_logs.user_id', $user->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->join('tasks', 'time_logs.task_id', '=', 'tasks.id')
            ->join('projects', 'tasks.project_id', '=', 'projects.id')
            ->groupBy('projects.name', 'projects.color')
            ->selectRaw('projects.name as label, projects.color, SUM(duration_minutes) as total_minutes')
            ->get();

        // 2. Wykres słupkowy: Ostatnie 14 dni
        $startDate = now()->subDays(13)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');
        
        $dailyDataRaw = TimeLog::where('user_id', $user->id)
            ->where('date', '>=', $startDate)
            ->where('date', '<=', $endDate)
            ->groupBy('date')
            ->orderBy('date')
            ->selectRaw('date, SUM(duration_minutes) as total_minutes')
            ->get()
            ->keyBy('date');

        // Wypełnienie pustych dat zerami
        $dailyData = [];
        $currentDate = Carbon::parse($startDate);
        for ($i = 0; $i < 14; $i++) {
            $dateString = $currentDate->format('Y-m-d');
            $dailyData[] = [
                'date' => $dateString,
                'label' => $currentDate->format('d M'),
                'total_minutes' => $dailyDataRaw->has($dateString) ? $dailyDataRaw->get($dateString)->total_minutes : 0
            ];
            $currentDate->addDay();
        }

        // 3. KPI
        $monthlyMinutes = TimeLog::where('user_id', $user->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('duration_minutes');
            
        $monthlyHours = floor($monthlyMinutes / 60);
        $monthlyRemainingMins = $monthlyMinutes % 60;
        $formattedTime = sprintf('%02d:%02d', $monthlyHours, $monthlyRemainingMins);

        $activeProjectsCount = $user->projects()->count();

        return response()->json([
            'pie_chart' => $projectsData,
            'bar_chart' => $dailyData,
            'kpi' => [
                'total_formatted_time' => $formattedTime,
                'active_projects' => $activeProjectsCount
            ]
        ]);
    }
}

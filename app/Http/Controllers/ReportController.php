<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\ProjectFolder;
use App\Models\TimeLog;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function download(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'project_id' => 'nullable|exists:projects,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if (empty($validated['date_from'])) {
            $validated['date_from'] = Carbon::now()->startOfMonth()->toDateString();
        }
        if (empty($validated['date_to'])) {
            $validated['date_to'] = Carbon::now()->endOfMonth()->toDateString();
        }

        $data = $this->reportService->getReportData($validated);

        $pdf = Pdf::loadView('reports.pdf', $data);

        return $pdf->download('raport.pdf');
    }

    public function timesheet(Request $request)
    {
        $userId = Auth::id();
        $userTimezone = Auth::user()->timezone ?? 'Europe/Warsaw';
        
        // Wygenerowanie ostatnich 7 dni do kolumn (najstarsza z lewej, dzisiaj z prawej) z uwzględnieniem strefy
        $dates = [];
        for ($i = 6; $i >= 0; $i--) {
            $dates[] = Carbon::today($userTimezone)->subDays($i)->toDateString();
        }

        // Pobieramy hierarchię: Katalog -> Projekt -> Zadanie (tylko te dostępne dla usera)
        $folders = ProjectFolder::with(['projects' => function ($q) use ($userId) {
            $q->whereHas('users', function ($q2) use ($userId) {
                $q2->where('users.id', $userId);
            })->with(['tasks' => function ($q3) use ($userId) {
                $q3->whereHas('users', function ($q4) use ($userId) {
                    $q4->where('users.id', $userId);
                });
            }]);
        }])
        ->where('user_id', $userId)
        ->get();

        $unassignedProjects = \App\Models\Project::whereHas('users', function ($q) use ($userId) {
                $q->where('users.id', $userId);
            })
            ->whereDoesntHave('folders', function($q) use ($userId) {
                $q->where('project_folder_assignments.user_id', $userId);
            })
            ->with(['tasks' => function ($q) use ($userId) {
                $q->whereHas('users', function ($q2) use ($userId) {
                    $q2->where('users.id', $userId);
                });
            }])->get();

        // Pobieranie wszystkich logów z tych 7 dni dla użytkownika, zgrupowane po task_id i dacie
        $logs = TimeLog::where('user_id', $userId)
            ->whereBetween('date', [$dates[0], $dates[6]])
            ->get();
            
        // Tworzymy słownik czasu: timeLogsMatrix[task_id][date] = sumaryczny czas w minutach
        $timeLogsMatrix = [];
        foreach ($logs as $log) {
            if (!isset($timeLogsMatrix[$log->task_id])) {
                $timeLogsMatrix[$log->task_id] = [];
            }
            if (!isset($timeLogsMatrix[$log->task_id][$log->date])) {
                $timeLogsMatrix[$log->task_id][$log->date] = 0;
            }
            $timeLogsMatrix[$log->task_id][$log->date] += $log->duration_minutes;
        }

        return view('reports.timesheet', compact('dates', 'folders', 'unassignedProjects', 'timeLogsMatrix'));
    }
}

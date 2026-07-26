<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

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
}

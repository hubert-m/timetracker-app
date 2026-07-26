<?php

namespace App\Services;

use App\Models\TimeLog;

class ReportService
{
    public function getReportData($filters = [])
    {
        $query = TimeLog::with(['user', 'task.project']);

        if (!empty($filters['date_from'])) {
            $query->whereDate('date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('date', '<=', $filters['date_to']);
        }

        if (!empty($filters['project_id'])) {
            $query->whereHas('task', function ($q) use ($filters) {
                $q->where('project_id', $filters['project_id']);
            });
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        $logs = $query->orderBy('date', 'asc')->get();

        $totalMinutes = $logs->sum('duration_minutes');
        $totalFormatted = $this->formatMinutes($totalMinutes);

        // Format each log
        $logs->transform(function ($log) {
            $log->formatted_duration = $this->formatMinutes($log->duration_minutes);
            return $log;
        });

        return [
            'logs' => $logs,
            'total_formatted' => $totalFormatted,
            'filters' => $filters,
        ];
    }

    private function formatMinutes($minutes)
    {
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        return sprintf('%02d:%02d', $hours, $remainingMinutes);
    }
}

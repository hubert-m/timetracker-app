<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Raport Czasu Pracy</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total { font-weight: bold; font-size: 14px; margin-top: 20px; text-align: right; }
    </style>
</head>
<body>
    <h2>Raport Czasu Pracy</h2>
    <p>Okres: {{ $filters['date_from'] }} - {{ $filters['date_to'] }}</p>
    
    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Pracownik</th>
                <th>Projekt</th>
                <th>Zadanie</th>
                <th>Przepracowany Czas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <td>{{ $log->date }}</td>
                <td>{{ $log->user->name }}</td>
                <td>{{ $log->task->project->title ?? '-' }}</td>
                <td>{{ $log->task->title }}</td>
                <td>{{ $log->formatted_duration }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="total">
        Suma godzin: {{ $total_formatted }}
    </div>
</body>
</html>

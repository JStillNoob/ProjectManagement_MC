<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Damage/Loss Report</title>
    <style>
        @page { margin: 80px 50px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #333; }
        header { position: fixed; top: -60px; left: 0; right: 0; height: 60px; text-align: center; }
        footer { position: fixed; bottom: -60px; left: 0; right: 0; height: 50px; text-align: center; font-size: 9px; color: #666; border-top: 1px solid #87A96B; padding-top: 10px; }
        .header-title { font-size: 24px; font-weight: bold; color: #87A96B; margin-bottom: 5px; }
        .header-subtitle { font-size: 11px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        thead th { background-color: #87A96B; color: white; padding: 10px 8px; text-align: left; font-size: 10px; font-weight: bold; }
        tbody td { padding: 8px; border-bottom: 1px solid #dee2e6; font-size: 9px; }
        .summary { margin-bottom: 20px; padding: 15px; background: #f8f9fa; }
    </style>
</head>
<body>
    <header>
        <div class="header-title">MACUA CONSTRUCTION</div>
        <div class="header-subtitle">Damage/Loss Report - {{ $validated['date_from'] }} to {{ $validated['date_to'] }}</div>
    </header>

    <footer>
        <div>Generated on {{ now()->format('M d, Y h:i A') }}</div>
    </footer>

    <div class="summary">
        <strong>Summary:</strong> Total Incidents: {{ $summary['total_incidents'] }}, Total Cost: ₱{{ number_format($summary['total_cost'], 2) }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Incident Date</th>
                <th>Type</th>
                <th>Equipment</th>
                <th>Project</th>
                <th>Status</th>
                <th>Estimated Cost</th>
            </tr>
        </thead>
        <tbody>
            @foreach($incidents as $incident)
                <tr>
                    <td>{{ $incident->IncidentDate->format('M d, Y') }}</td>
                    <td>{{ $incident->IncidentType }}</td>
                    <td>{{ $incident->inventoryItem->resourceCatalog->ItemName ?? 'N/A' }}</td>
                    <td>{{ $incident->project->ProjectName ?? 'N/A' }}</td>
                    <td>{{ $incident->Status ?? 'N/A' }}</td>
                    <td>₱{{ number_format($incident->EstimatedCost ?? 0, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>




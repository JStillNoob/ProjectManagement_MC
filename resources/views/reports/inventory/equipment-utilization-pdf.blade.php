<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Equipment Utilization Report</title>
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
    </style>
</head>
<body>
    <header>
        <div class="header-title">MACUA CONSTRUCTION</div>
        <div class="header-subtitle">Equipment Utilization Report - {{ $validated['date_from'] }} to {{ $validated['date_to'] }}</div>
    </header>

    <footer>
        <div>Generated on {{ now()->format('M d, Y h:i A') }}</div>
    </footer>

    <table>
        <thead>
            <tr>
                <th>Equipment</th>
                <th>Times Assigned</th>
                <th>Total Days Used</th>
                <th>Currently In Use</th>
                <th>Projects</th>
            </tr>
        </thead>
        <tbody>
            @foreach($utilizationStats as $stat)
                <tr>
                    <td><strong>{{ $stat['item']->resourceCatalog->ItemName ?? 'N/A' }}</strong></td>
                    <td>{{ $stat['times_assigned'] }}</td>
                    <td>{{ $stat['total_days_used'] }} days</td>
                    <td>{{ $stat['currently_in_use'] }}</td>
                    <td>{{ $stat['projects']->implode(', ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>




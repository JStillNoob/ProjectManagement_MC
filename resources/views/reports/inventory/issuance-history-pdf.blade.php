<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Issuance History Report</title>
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
        <div class="header-subtitle">Issuance History Report - {{ $validated['date_from'] }} to {{ $validated['date_to'] }}</div>
    </header>

    <footer>
        <div>Generated on {{ now()->format('M d, Y h:i A') }}</div>
    </footer>

    <div class="summary">
        <strong>Summary:</strong> Total Issuances: {{ $summary['total_issuances'] }}, Total Items: {{ number_format($summary['total_items'], 2) }}, Materials: {{ number_format($summary['materials_issued'], 2) }}, Equipment: {{ number_format($summary['equipment_issued'], 2) }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Issuance #</th>
                <th>Date</th>
                <th>Project</th>
                <th>Issued By</th>
                <th>Received By</th>
                <th>Items</th>
            </tr>
        </thead>
        <tbody>
            @foreach($issuances as $issuance)
                <tr>
                    <td>{{ $issuance->IssuanceNumber }}</td>
                    <td>{{ $issuance->IssuanceDate->format('M d, Y') }}</td>
                    <td>{{ $issuance->project->ProjectName ?? 'N/A' }}</td>
                    <td>{{ $issuance->issuer->full_name ?? 'N/A' }}</td>
                    <td>{{ $issuance->receiver->full_name ?? 'N/A' }}</td>
                    <td>{{ $issuance->items->count() }} items</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>




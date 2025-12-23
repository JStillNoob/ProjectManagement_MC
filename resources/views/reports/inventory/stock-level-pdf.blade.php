<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Level Report</title>
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
        .page-number:after { content: "Page " counter(page); }
    </style>
</head>
<body>
    <header>
        <div class="header-title">MACUA CONSTRUCTION</div>
        <div class="header-subtitle">Stock Level Report - {{ date('M d, Y') }}</div>
    </header>

    <footer>
        <div class="page-number"></div>
        <div>Generated on {{ now()->format('M d, Y h:i A') }}</div>
    </footer>

    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Type</th>
                <th>Total Qty</th>
                <th>Available</th>
                <th>Committed</th>
                <th>Reorder Level</th>
                <th>Unit</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                @php
                    $catalog = $item->resourceCatalog ?? null;
                    $isLowStock = $item->AvailableQuantity <= $item->MinimumStockLevel && $item->AvailableQuantity > 0;
                    $isOutOfStock = $item->AvailableQuantity <= 0;
                    $requiresInteger = $catalog && $catalog->requiresIntegerQuantity();
                @endphp
                <tr>
                    <td><strong>{{ $catalog->ItemName ?? 'N/A' }}</strong></td>
                    <td>{{ $catalog->Type ?? 'N/A' }}</td>
                    <td>{{ $requiresInteger ? number_format((int) $item->TotalQuantity, 0) : number_format($item->TotalQuantity, 2) }}</td>
                    <td><strong>{{ $requiresInteger ? number_format((int) $item->AvailableQuantity, 0) : number_format($item->AvailableQuantity, 2) }}</strong></td>
                    <td>{{ $requiresInteger ? number_format((int) $item->CommittedQuantity, 0) : number_format($item->CommittedQuantity, 2) }}</td>
                    <td>{{ $requiresInteger ? number_format((int) $item->MinimumStockLevel, 0) : number_format($item->MinimumStockLevel, 2) }}</td>
                    <td>{{ $catalog->Unit ?? 'unit' }}</td>
                    <td>
                        @if($isOutOfStock)
                            Out of Stock
                        @elseif($isLowStock)
                            Low Stock
                        @else
                            OK
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>




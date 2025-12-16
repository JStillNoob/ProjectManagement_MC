<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Inventory Report</title>
    <style>
        @page {
            margin: 80px 50px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
        }

        header {
            position: fixed;
            top: -60px;
            left: 0;
            right: 0;
            height: 60px;
            text-align: center;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #87A96B;
            padding-top: 10px;
        }

        .header-title {
            font-size: 24px;
            font-weight: bold;
            color: #87A96B;
            margin-bottom: 5px;
        }

        .header-subtitle {
            font-size: 11px;
            color: #666;
        }

        .summary-cards {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .summary-card {
            display: table-cell;
            width: 33%;
            padding: 15px;
            text-align: center;
            background: #f8f9fa;
            border-left: 4px solid #87A96B;
            margin-right: 10px;
        }

        .summary-value {
            font-size: 24px;
            font-weight: bold;
            color: #87A96B;
        }

        .summary-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-top: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        thead th {
            background-color: #87A96B;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        tbody td {
            padding: 8px;
            border-bottom: 1px solid #dee2e6;
            font-size: 9px;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .low-stock {
            background-color: #fff3cd !important;
        }

        .low-stock-indicator {
            color: #dc3545;
            font-weight: bold;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #87A96B;
            margin: 30px 0 15px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #87A96B;
        }
    </style>
</head>

<body>
    <header>
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="font-size: 24pt; font-weight: bold; color: #009900; text-transform: uppercase; margin: 0; line-height: 1; text-shadow: 1px 1px 1px #ccc;">
                MACUA CONSTRUCTION
            </div>
            <div style="font-size: 9pt; font-weight: bold; color: #000; text-transform: uppercase; margin: 5px 0 0 0; letter-spacing: 1px;">
                General Contractor – Mechanical Works - Fabrication
            </div>
            <div style="font-size: 8pt; color: #000; font-weight: bold; margin-top: 2px;">
                PCAB LICENSE NO. 41994
            </div>
            <div style="background-color: #009900; height: 4px; width: 100%; margin-top: 10px; margin-bottom: 20px;"></div>
            <div style="font-size: 16pt; font-weight: bold; color: #0056b3; text-transform: uppercase;">INVENTORY REPORT</div>
        </div>
    </header>

    <footer>
        <div>Page <span class="pagenum"></span></div>
        <div>Generated on {{ now()->format('F d, Y h:i A') }}</div>
    </footer>

    <main>
        <div class="summary-cards">
            <div class="summary-card">
                <div class="summary-value">{{ $totalItems }}</div>
                <div class="summary-label">Total Items</div>
            </div>
            <div class="summary-card">
                <div class="summary-value">{{ number_format($totalValue) }}</div>
                <div class="summary-label">Total Stock Units</div>
            </div>
            <div class="summary-card" style="margin-right: 0;">
                <div class="summary-value" style="color: #dc3545;">{{ $lowStockCount }}</div>
                <div class="summary-label">Low Stock Alerts</div>
            </div>
        </div>

        @if($lowStockItems->count() > 0)
            <div class="section-title">⚠️ Low Stock Items</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 8%">Item ID</th>
                        <th style="width: 35%">Item Name</th>
                        <th style="width: 20%">Type</th>
                        <th style="width: 12%" class="text-center">In Stock</th>
                        <th style="width: 12%" class="text-center">Min Level</th>
                        <th style="width: 13%">Location</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lowStockItems as $item)
                        <tr class="low-stock">
                            <td class="text-center">{{ $item->ItemID }}</td>
                            <td>{{ $item->ItemName }}</td>
                            <td>{{ $item->type->ItemTypeName ?? 'N/A' }}</td>
                            <td class="text-center low-stock-indicator">{{ number_format($item->QuantityInStock, 2) }}
                                {{ $item->Unit }}</td>
                            <td class="text-center">{{ number_format($item->MinimumStockLevel, 2) }} {{ $item->Unit }}</td>
                            <td>{{ $item->Location ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="section-title">📦 Complete Inventory List</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 8%">Item ID</th>
                    <th style="width: 35%">Item Name</th>
                    <th style="width: 20%">Type</th>
                    <th style="width: 12%" class="text-center">In Stock</th>
                    <th style="width: 12%" class="text-center">Min Level</th>
                    <th style="width: 13%">Location</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr class="{{ $item->QuantityInStock <= $item->MinimumStockLevel ? 'low-stock' : '' }}">
                        <td class="text-center">{{ $item->ItemID }}</td>
                        <td>{{ $item->ItemName }}</td>
                        <td>{{ $item->type->ItemTypeName ?? 'N/A' }}</td>
                        <td
                            class="text-center {{ $item->QuantityInStock <= $item->MinimumStockLevel ? 'low-stock-indicator' : '' }}">
                            {{ number_format($item->QuantityInStock, 2) }} {{ $item->Unit }}
                        </td>
                        <td class="text-center">{{ number_format($item->MinimumStockLevel, 2) }} {{ $item->Unit }}</td>
                        <td>{{ $item->Location ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
            $size = 9;
            $font = $fontMetrics->getFont("sans-serif");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 40;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>

</html>
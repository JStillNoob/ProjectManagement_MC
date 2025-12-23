<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Issuance Record - {{ $issuance->IssuanceNumber }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        /* Styles to mimic the Logo without an image */
        .company-name {
            font-size: 24pt;
            font-weight: bold;
            color: #009900;
            /* Macua Green */
            text-transform: uppercase;
            margin: 0;
            line-height: 1;
            text-shadow: 1px 1px 1px #ccc;
            /* Subtle shadow like the logo */
        }

        .company-tagline {
            font-size: 9pt;
            font-weight: bold;
            color: #000;
            text-transform: uppercase;
            margin: 5px 0 0 0;
            letter-spacing: 1px;
        }

        .license-no {
            font-size: 8pt;
            color: #000;
            font-weight: bold;
            margin-top: 2px;
        }

        .green-bar {
            background-color: #009900;
            height: 4px;
            width: 100%;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .document-title {
            font-size: 16pt;
            font-weight: bold;
            color: #0056b3;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .info-section {
            margin: 20px 0;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            width: 150px;
            display: inline-block;
        }

        .info-value {
            flex: 1;
        }

        .section-title {
            font-size: 12pt;
            font-weight: bold;
            margin: 20px 0 10px 0;
            color: #0056b3;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        th {
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }

        td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 9pt;
            font-weight: bold;
            border-radius: 3px;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #333;
        }

        .badge-info {
            background-color: #17a2b8;
            color: white;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }

        .notes {
            background-color: #f9f9f9;
            padding: 10px;
            border-left: 3px solid #0056b3;
            margin: 10px 0;
        }
    </style>
</head>

<body>
    {{-- TEXT-BASED HEADER (Replaces Image) --}}
    <div class="header">
        <div class="company-name">MACUA CONSTRUCTION</div>
        <div class="company-tagline">General Contractor – Mechanical Works - Fabrication</div>
        <div class="license-no">PCAB LICENSE NO. 41994</div>

        {{-- The Green Line from the logo --}}
        <div class="green-bar"></div>

        <div class="document-title">ISSUANCE RECORD</div>

    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Issuance Number:</span>
            <span class="info-value"><strong>{{ $issuance->IssuanceNumber }}</strong></span>
        </div>
        <div class="info-row">
            <span class="info-label">Issuance Date:</span>
            <span class="info-value">{{ $issuance->IssuanceDate->format('F d, Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Status:</span>
            <span class="info-value">
                <span class="badge badge-{{ $issuance->Status == 'Issued' ? 'success' : 'warning' }}">
                    {{ $issuance->Status }}
                </span>
            </span>
        </div>
        @if($issuance->inventoryRequest)
            <div class="info-row">
                <span class="info-label">Related Request:</span>
                <span class="info-value">{{ $issuance->inventoryRequest->RequestNumber }}</span>
            </div>
        @endif
    </div>

    <div class="section-title">Project Information</div>
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Project:</span>
            <span class="info-value">{{ $issuance->project->ProjectName ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Milestone:</span>
            <span class="info-value">{{ $issuance->milestone->milestone_name ?? 'N/A' }}</span>
        </div>

    </div>

    <div class="section-title">Personnel</div>
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Issued By:</span>
            <span class="info-value">{{ $issuance->issuedBy->full_name ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Received By:</span>
            <span class="info-value">{{ $issuance->receivedBy->full_name ?? 'N/A' }}</span>
        </div>
    </div>

    <div class="section-title">Items Issued</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 45%;">Item Description</th>
                <th style="width: 15%;">Type</th>
                <th style="width: 15%;" class="text-right">Quantity</th>
                <th style="width: 20%;">Unit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($issuance->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->inventoryItem->resourceCatalog->ItemName ?? 'N/A' }}</td>
                    <td class="text-center">
                        {{ $item->inventoryItem->resourceCatalog->Type ?? 'N/A' }}
                    </td>
                    <td class="text-right">
                        @if($item->inventoryItem && $item->inventoryItem->requiresIntegerQuantity())
                            {{ number_format((int) $item->QuantityIssued, 0) }}
                        @else
                            {{ number_format($item->QuantityIssued, 2) }}
                        @endif
                    </td>
                    <td>{{ $item->inventoryItem->resourceCatalog->Unit ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 10px;">
        <strong>Total Items:</strong> {{ $issuance->items->count() }}
    </div>

    @if($issuance->Notes)
        <div class="section-title">Notes</div>
        <div class="notes">
            {{ $issuance->Notes }}
        </div>
    @endif

    <div class="footer">
        <p style="font-size: 9pt; color: #666; text-align: center;">
            This is a system-generated document. Generated on {{ now()->format('F d, Y h:i A') }}
        </p>
    </div>
</body>

</html>
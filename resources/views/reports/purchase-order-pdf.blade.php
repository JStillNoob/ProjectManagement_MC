<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Purchase Order {{ str_pad($purchaseOrder->POID, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page {
            margin: 100px 50px 80px 50px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
        }

        header {
            position: fixed;
            top: -80px;
            left: 0;
            right: 0;
            height: 100px;
        }

        .company-header {
            width: 100%;
            margin-bottom: 20px;
        }

        .company-header img {
            width: 100%;
            height: auto;
            display: block;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 60px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #87A96B;
            padding-top: 10px;
        }

        .page-number:after {
            content: "Page " counter(page);
        }

        .header-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin: 20px 0 10px 0;
            text-align: center;
        }

        .header-subtitle {
            font-size: 12px;
            color: #666;
        }

        .info-section {
            margin: 20px 0;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .info-column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .info-box {
            background: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #87A96B;
            margin-right: 10px;
        }

        .info-box h3 {
            margin: 0 0 10px 0;
            font-size: 12px;
            color: #87A96B;
            text-transform: uppercase;
        }

        .info-box p {
            margin: 5px 0;
            font-size: 10px;
        }

        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 100px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        thead th {
            background-color: #87A96B;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        tbody td {
            padding: 10px 8px;
            border-bottom: 1px solid #dee2e6;
        }

        tbody tr:hover {
            background-color: #f8f9fa;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .summary-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .summary-row td {
            padding: 15px 8px;
            border-top: 2px solid #87A96B;
        }

        .signatures {
            margin-top: 50px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 33%;
            text-align: center;
            padding: 10px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin: 50px 20px 5px 20px;
        }

        .signature-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .status-draft {
            background-color: #ffc107;
            color: #000;
        }

        .status-approved {
            background-color: #28a745;
            color: white;
        }

        .status-sent {
            background-color: #17a2b8;
            color: white;
        }
    </style>
</head>

<body>
    <header>
        <div class="company-header" style="text-align: center; margin-bottom: 20px;">
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
        </div>
    </header>

    <footer>
        <div class="page-number"></div>
        <div>Generated on {{ now()->format('F d, Y h:i A') }}</div>
    </footer>

    <main>
        <div class="header-title" style="color: #0056b3; font-size: 16pt; font-weight: bold; text-transform: uppercase;">PURCHASE ORDER</div>
        <div class="info-section">
            <div class="info-row">
                <div class="info-column">
                    <div class="info-box">
                        <h3>Purchase Order Details</h3>
                        <p><span class="info-label">PO Number:</span>
                            {{ str_pad($purchaseOrder->POID, 4, '0', STR_PAD_LEFT) }}</p>
                        <p><span class="info-label">Order Date:</span>
                            {{ \Carbon\Carbon::parse($purchaseOrder->OrderDate)->format('F d, Y') }}</p>
                        <p>
                            <span class="info-label">Status:</span>
                            <span class="status-badge status-{{ strtolower($purchaseOrder->Status) }}">
                                {{ $purchaseOrder->Status }}
                            </span>
                        </p>
                        @if($purchaseOrder->creator)
                            <p><span class="info-label">Prepared By:</span> {{ $purchaseOrder->creator->FirstName }}
                                {{ $purchaseOrder->creator->LastName }}</p>
                        @endif
                    </div>
                </div>

                <div class="info-column">
                    <div class="info-box" style="margin-right: 0;">
                        <h3>Supplier Information</h3>
                        @if($purchaseOrder->supplier)
                            <p><span class="info-label">Supplier:</span> {{ $purchaseOrder->supplier->SupplierName }}</p>
                            @if($purchaseOrder->supplier->ContactNumber)
                                <p><span class="info-label">Contact:</span> {{ $purchaseOrder->supplier->ContactNumber }}</p>
                            @endif
                            @if($purchaseOrder->supplier->Email)
                                <p><span class="info-label">Email:</span> {{ $purchaseOrder->supplier->Email }}</p>
                            @endif
                            @if($purchaseOrder->supplier->Address)
                                <p><span class="info-label">Address:</span> {{ $purchaseOrder->supplier->Address }}</p>
                            @endif
                        @else
                            <p>No supplier assigned</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 35%">Item Description</th>
                    <th style="width: 20%">Item Type</th>
                    <th style="width: 25%">Specifications</th>
                    <th style="width: 15%" class="text-right">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->item->ItemName ?? 'N/A' }}</td>
                        <td>{{ $item->item->type->ItemTypeName ?? 'N/A' }}</td>
                        <td>{{ $item->Specifications ?? '-' }}</td>
                        <td class="text-right">{{ number_format($item->QuantityOrdered, 2) }} {{ $item->Unit }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="summary-row">
                    <td colspan="4" class="text-right">TOTAL QUANTITY:</td>
                    <td class="text-right">{{ number_format($totalQuantity, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Prepared By</div>
                @if($purchaseOrder->creator)
                    <div style="margin-top: 5px; font-size: 10px;">{{ $purchaseOrder->creator->FirstName }}
                        {{ $purchaseOrder->creator->LastName }}</div>
                @endif
            </div>

            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Approved By</div>
                @if($purchaseOrder->approver)
                    <div style="margin-top: 5px; font-size: 10px;">{{ $purchaseOrder->approver->FirstName }}
                        {{ $purchaseOrder->approver->LastName }}</div>
                @endif
            </div>

            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Received By</div>
            </div>
        </div>
    </main>
</body>

</html>
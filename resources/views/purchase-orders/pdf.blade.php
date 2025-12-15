<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order #{{ $purchaseOrder->POID }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #000;
        }

        .header p {
            margin: 5px 0;
        }

        .info-section {
            margin-bottom: 20px;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .info-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .info-col h3 {
            font-size: 14px;
            margin: 0 0 10px 0;
            color: #666;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }

        .info-col p {
            margin: 3px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th {
            background-color: #f0f0f0;
            padding: 8px;
            text-align: left;
            border: 1px solid #ccc;
            font-weight: bold;
        }

        table td {
            padding: 8px;
            border: 1px solid #ccc;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ccc;
        }

        .signature-section {
            margin-top: 50px;
        }

        .signature-box {
            display: inline-block;
            width: 45%;
            text-align: center;
            margin: 0 2%;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 50px;
            padding-top: 5px;
        }

        .terms {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }

        .terms h4 {
            margin: 0 0 10px 0;
            font-size: 13px;
        }

        .terms p {
            margin: 5px 0;
            white-space: pre-line;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>PURCHASE ORDER</h1>
        <p><strong>PO Number:</strong> #{{ $purchaseOrder->POID }}</p>
        <p><strong>Date:</strong> {{ $purchaseOrder->OrderDate->format('F d, Y') }}</p>
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-col">
                <h3>Supplier</h3>
                <p><strong>{{ $purchaseOrder->supplier->SupplierName ?? 'N/A' }}</strong></p>
                @if($purchaseOrder->supplier->ContactPerson)
                    <p>Attn: {{ $purchaseOrder->supplier->ContactPerson }}</p>
                @endif
                @if($purchaseOrder->supplier->Address)
                    <p>{{ $purchaseOrder->supplier->Address }}</p>
                @endif
                @if($purchaseOrder->supplier->PhoneNumber)
                    <p>Phone: {{ $purchaseOrder->supplier->PhoneNumber }}</p>
                @endif
                @if($purchaseOrder->supplier->Email)
                    <p>Email: {{ $purchaseOrder->supplier->Email }}</p>
                @endif
                @if($purchaseOrder->supplier->TIN)
                    <p>TIN: {{ $purchaseOrder->supplier->TIN }}</p>
                @endif
            </div>
            <div class="info-col">
                <h3>Delivery Information</h3>
                <p><strong>Delivery Address:</strong></p>
                <p>[Your Company Address]</p>
                <p>[City, Province, ZIP]</p>
                <p><strong>Contact Person:</strong> {{ $purchaseOrder->creator->FirstName ?? '' }}
                    {{ $purchaseOrder->creator->LastName ?? '' }}
                </p>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="50%">Item Description</th>
                <th width="25%">Specifications</th>
                <th width="20%" class="text-center">Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrder->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->inventoryItem->ItemName ?? 'N/A' }}</strong><br>
                        <small>Code: {{ $item->inventoryItem->ItemCode ?? '' }}</small>
                    </td>
                    <td>{{ $item->Specifications ?? '-' }}</td>
                    <td class="text-center">{{ $item->QuantityOrdered }} {{ $item->Unit }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-right"><strong>TOTAL QUANTITY:</strong></td>
                <td class="text-center"><strong>{{ $purchaseOrder->items->sum('QuantityOrdered') }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                <strong>{{ $purchaseOrder->creator->FirstName ?? '' }}
                    {{ $purchaseOrder->creator->LastName ?? '' }}</strong><br>
                Prepared By
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                @if($purchaseOrder->ApprovedBy)
                    <strong>{{ $purchaseOrder->approver->FirstName ?? '' }}
                        {{ $purchaseOrder->approver->LastName ?? '' }}</strong><br>
                @endif
                Approved By
            </div>
        </div>
    </div>

    <div class="footer">
        <p style="text-align: center; font-size: 10px; color: #666;">
            This is a computer-generated document. No signature is required.<br>
            Generated on {{ now()->format('F d, Y h:i A') }}
        </p>
    </div>
</body>

</html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Milestone Report - {{ $milestone->milestone_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
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
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .info-section h3 {
            color: #87A96B;
            margin-top: 0;
            font-size: 16px;
            border-bottom: 1px solid #87A96B;
            padding-bottom: 5px;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .info-label {
            display: table-cell;
            width: 40%;
            font-weight: bold;
            color: #555;
        }
        .info-value {
            display: table-cell;
            width: 60%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #87A96B;
            color: white;
            padding: 10px;
            text-align: left;
            border: 1px solid #87A96B;
        }
        td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #333;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        .summary {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .summary-label {
            display: table-cell;
            width: 50%;
            font-weight: bold;
        }
        .summary-value {
            display: table-cell;
            width: 50%;
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 10px;
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

        <div class="document-title">MILESTONE REPORT</div>
    </div>

    <!-- Milestone Information -->
    <div class="info-section">
        <h3>Milestone Information</h3>
        <div class="info-row">
            <div class="info-label">Milestone Name:</div>
            <div class="info-value">{{ $milestone->milestone_name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Project:</div>
            <div class="info-value">{{ $milestone->project->ProjectName ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Client:</div>
            <div class="info-value">{{ $milestone->project->client->ClientName ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Description:</div>
            <div class="info-value">{{ $milestone->description ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value"><span class="badge badge-success">Completed</span></div>
        </div>
        <div class="info-row">
            <div class="info-label">Target Date:</div>
            <div class="info-value">
                @if($milestone->target_date)
                    {{ $milestone->target_date->format('M d, Y') }}
                @else
                    N/A
                @endif
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Completion Date:</div>
            <div class="info-value">
                @if($milestone->actual_date)
                    {{ $milestone->actual_date->format('M d, Y') }}
                @else
                    N/A
                @endif
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Estimated Days:</div>
            <div class="info-value">{{ $milestone->EstimatedDays ?? 'N/A' }} days</div>
        </div>
    </div>

    <!-- Comparison Table -->
    <h3 style="color: #87A96B; margin-top: 30px; border-bottom: 1px solid #87A96B; padding-bottom: 5px;">
        Required Items vs Actual Items Used
    </h3>

    @if(count($comparisonData) > 0)
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Type</th>
                    <th class="text-right">Required Qty</th>
                    <th class="text-right">Actual Qty</th>
                    <th class="text-right">Variance</th>
                    <th class="text-right">Percentage</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($comparisonData as $item)
                    <tr>
                        <td><strong>{{ $item['item_name'] }}</strong></td>
                        <td>
                            <span class="badge badge-info">{{ $item['item_type'] }}</span>
                        </td>
                        <td class="text-right">
                            {{ number_format($item['required_qty'], 2) }} {{ $item['unit'] }}
                        </td>
                        <td class="text-right">
                            {{ number_format($item['actual_qty'], 2) }} {{ $item['unit'] }}
                        </td>
                        <td class="text-right">
                            @if($item['variance'] > 0)
                                <span style="color: #dc3545;">+{{ number_format($item['variance'], 2) }}</span>
                            @elseif($item['variance'] < 0)
                                <span style="color: #ffc107;">{{ number_format($item['variance'], 2) }}</span>
                            @else
                                {{ number_format($item['variance'], 2) }}
                            @endif
                            {{ $item['unit'] }}
                        </td>
                        <td class="text-right">
                            {{ number_format($item['percentage'], 2) }}%
                        </td>
                        <td class="text-center">
                            <span class="badge badge-{{ $item['status_color'] }}">
                                {{ $item['status_text'] }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary">
            <h3 style="color: #87A96B; margin-top: 0; border-bottom: 1px solid #87A96B; padding-bottom: 5px;">
                Summary Statistics
            </h3>
            <div class="summary-row">
                <div class="summary-label">Total Items:</div>
                <div class="summary-value">{{ count($comparisonData) }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">On Target (within ±5%):</div>
                <div class="summary-value" style="color: #28a745;">
                    {{ collect($comparisonData)->where('status_color', 'success')->count() }}
                </div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Needs Attention (5-20% variance):</div>
                <div class="summary-value" style="color: #ffc107;">
                    {{ collect($comparisonData)->where('status_color', 'warning')->count() }}
                </div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Critical (>20% variance):</div>
                <div class="summary-value" style="color: #dc3545;">
                    {{ collect($comparisonData)->where('status_color', 'danger')->count() }}
                </div>
            </div>
        </div>
    @else
        <p style="text-align: center; color: #666; padding: 20px;">
            No required items found for this milestone.
        </p>
    @endif

    <div class="footer">
        <p>This report was generated by MACUA CONSTRUCTION Project Management System</p>
        <p>Page 1 of 1</p>
    </div>
</body>
</html>


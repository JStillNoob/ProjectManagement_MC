<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Project Employee QR Codes - {{ $project->ProjectName }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12pt;
            color: #333;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 3px solid #87A96B;
            margin-bottom: 20px;
        }

        .header h1 {
            color: #87A96B;
            font-size: 24pt;
            margin-bottom: 5px;
        }

        .header p {
            color: #666;
            font-size: 10pt;
        }

        .project-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .project-info h2 {
            color: #87A96B;
            font-size: 14pt;
            margin-bottom: 10px;
        }

        .project-info table {
            width: 100%;
        }

        .project-info td {
            padding: 3px 10px 3px 0;
            font-size: 10pt;
        }

        .project-info td:first-child {
            color: #666;
            width: 100px;
        }

        .qr-grid {
            width: 100%;
        }

        .qr-row {
            width: 100%;
            margin-bottom: 20px;
        }

        .qr-card {
            display: inline-block;
            width: 48%;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-right: 2%;
            margin-bottom: 15px;
            vertical-align: top;
            page-break-inside: avoid;
        }

        .qr-card:nth-child(2n) {
            margin-right: 0;
        }

        .qr-card-header {
            text-align: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .employee-name {
            font-size: 12pt;
            font-weight: bold;
            color: #333;
            margin-bottom: 3px;
        }

        .employee-position {
            font-size: 9pt;
            color: #666;
        }

        .qr-image {
            text-align: center;
            padding: 10px 0;
        }

        .qr-image img {
            width: 150px;
            height: 150px;
        }

        .qr-data {
            text-align: center;
            background-color: #f8f9fa;
            padding: 8px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 8pt;
            color: #666;
            word-break: break-all;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }

        .page-break {
            page-break-after: always;
        }

        .no-employees {
            text-align: center;
            padding: 50px;
            color: #666;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 8pt;
            border-radius: 3px;
            color: white;
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-secondary {
            background-color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>PROJECT QR CODES</h1>
        <p>Project Management System - Attendance Module</p>
    </div>

    <div class="project-info">
        <h2>{{ $project->ProjectName }}</h2>
        <table>
            <tr>
                <td>Status:</td>
                <td><strong>{{ $project->status->StatusName ?? 'N/A' }}</strong></td>
            </tr>
            <tr>
                <td>Duration:</td>
                <td>{{ $project->formatted_start_date }} - {{ $project->formatted_end_date }}</td>
            </tr>
            @if($project->client)
                <tr>
                    <td>Client:</td>
                    <td>{{ $project->client->ClientName }}</td>
                </tr>
            @endif
            <tr>
                <td>Employees:</td>
                <td><strong>{{ $project->projectEmployees->count() }}</strong> assigned</td>
            </tr>
        </table>
    </div>

    @if($project->projectEmployees->count() > 0)
        <div class="qr-grid">
            @foreach($project->projectEmployees as $index => $assignment)
                <div class="qr-card">
                    <div class="qr-card-header">
                        <div class="employee-name">{{ $assignment->employee->full_name ?? 'N/A' }}</div>
                        <div class="employee-position">{{ $assignment->employee->position->PositionName ?? 'No Position' }}
                        </div>
                        <span class="badge {{ $assignment->status == 'Active' ? 'badge-success' : 'badge-secondary' }}">
                            {{ $assignment->status ?? 'Active' }}
                        </span>
                    </div>
                    <div class="qr-image">
                        @if($assignment->qr_code)
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($assignment->qr_code) }}"
                                alt="QR Code">
                        @else
                            <p style="color: #999; padding: 50px 0;">No QR Code</p>
                        @endif
                    </div>
                    <div class="qr-data">
                        {{ $assignment->qr_code ?? 'N/A' }}
                    </div>
                </div>

                {{-- Add page break after every 4 cards --}}
                @if(($index + 1) % 4 == 0 && $index + 1 < $project->projectEmployees->count())
                    <div class="page-break"></div>
                @endif
            @endforeach
        </div>
    @else
        <div class="no-employees">
            <p>No employees assigned to this project.</p>
        </div>
    @endif

    <div class="footer">
        <p>Generated on {{ now()->format('F d, Y h:i A') }}</p>
        <p style="margin-top: 5px;">Use these QR codes for project attendance tracking.</p>
    </div>
</body>

</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report - All Projects</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
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
        
        .overall-summary {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .summary-card {
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            border: 2px solid #e9ecef;
        }
        
        .summary-card h3 {
            margin: 0;
            font-size: 2rem;
            color: #495057;
        }
        
        .summary-card p {
            margin: 5px 0 0 0;
            color: #6c757d;
            font-weight: 500;
        }
        
        .summary-present { border-color: #28a745; }
        .summary-late { border-color: #ffc107; }
        .summary-overtime { border-color: #17a2b8; }
        .summary-absent { border-color: #dc3545; }
        
        .summary-present h3 { color: #28a745; }
        .summary-late h3 { color: #ffc107; }
        .summary-overtime h3 { color: #17a2b8; }
        .summary-absent h3 { color: #dc3545; }
        
        .report-info {
            background: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .report-info h4 {
            margin: 0 0 10px 0;
            color: #495057;
        }
        
        .report-info p {
            margin: 5px 0;
            color: #6c757d;
        }
        
        .project-section {
            background: white;
            border-radius: 8px;
            margin-bottom: 30px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .project-header {
            background: #007bff;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .project-header h3 {
            margin: 0;
            font-size: 1.3rem;
        }
        
        .project-stats {
            display: flex;
            gap: 20px;
            text-align: center;
        }
        
        .project-stat {
            min-width: 60px;
        }
        
        .project-stat .number {
            font-size: 1.2rem;
            font-weight: bold;
            display: block;
        }
        
        .project-stat .label {
            font-size: 0.8rem;
            opacity: 0.9;
        }
        
        .attendance-table {
            background: white;
        }
        
        .attendance-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .attendance-table th {
            background: #343a40;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
        }
        
        .attendance-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .attendance-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-present { background-color: #d4edda; color: #155724; }
        .status-late { background-color: #fff3cd; color: #856404; }
        .status-overtime { background-color: #cce5ff; color: #004085; }
        .status-absent { background-color: #f8d7da; color: #721c24; }
        
        .employee-summary-section {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .employee-summary-section h4 {
            color: #495057;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }
        
        .employee-summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
        }
        
        .employee-summary-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .employee-name {
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .employee-name strong {
            color: #212529;
            font-size: 1rem;
        }
        
        .employee-name small {
            color: #6c757d;
            font-size: 0.85rem;
        }
        
        .employee-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }
        
        .stat-item {
            text-align: center;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }
        
        .stat-item.present {
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        
        .stat-item.late {
            background-color: #fff3cd;
            border-color: #ffeaa7;
        }
        
        .stat-item.overtime {
            background-color: #cce5ff;
            border-color: #a8d8ff;
        }
        
        .stat-item.total {
            background-color: #e9ecef;
            border-color: #dee2e6;
        }
        
        .stat-number {
            display: block;
            font-size: 1.2rem;
            font-weight: 700;
            color: #212529;
        }
        
        .stat-label {
            display: block;
            font-size: 0.75rem;
            color: #6c757d;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
        }
        
        @media print {
            body {
                background-color: white;
                padding: 0;
            }
            
            .header {
                background: #343a40 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .project-header {
                background: #007bff !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .attendance-table th {
                background: #343a40 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .status-badge {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .employee-summary-section {
                background: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .employee-summary-card {
                background: white !important;
                border: 1px solid #dee2e6 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .stat-item.present {
                background-color: #d4edda !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .stat-item.late {
                background-color: #fff3cd !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .stat-item.overtime {
                background-color: #cce5ff !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .stat-item.total {
                background-color: #e9ecef !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    {{-- TEXT-BASED HEADER (Replaces Image) --}}
    <div class="header">
        <div class="company-name">MACUA CONSTRUCTION</div>
        <div class="company-tagline">General Contractor – Mechanical Works - Fabrication</div>
        <div class="license-no">PCAB LICENSE NO. 41994</div>

        {{-- The Green Line from the logo --}}
        <div class="green-bar"></div>

        <div class="document-title">ATTENDANCE REPORT</div>
        <p style="color: #666; margin: 5px 0; font-size: 10pt;">All Projects - {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
    </div>

    <!-- Report Information -->
    <div class="report-info">
        <h4>📋 Report Information</h4>
        <p><strong>Generated on:</strong> {{ now()->format('F d, Y \a\t h:i A') }}</p>
        <p><strong>Period:</strong> {{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }}</p>
        <p><strong>Total Days:</strong> {{ $overallSummary['total_days'] }} days</p>
        <p><strong>Total Employees:</strong> {{ $overallSummary['total_employees'] }} employees across all projects</p>
    </div>

    <!-- Overall Summary Statistics -->
    <div class="overall-summary">
        <h3>📈 Overall Summary Statistics</h3>
        <div class="summary-grid">
            <div class="summary-card summary-present">
                <h3>{{ $overallSummary['present_count'] }}</h3>
                <p>Present Days</p>
            </div>
            <div class="summary-card summary-late">
                <h3>{{ $overallSummary['late_count'] }}</h3>
                <p>Late Arrivals</p>
            </div>
            <div class="summary-card summary-overtime">
                <h3>{{ $overallSummary['overtime_count'] }}</h3>
                <p>Overtime Days</p>
            </div>
            <div class="summary-card summary-absent">
                <h3>{{ $overallSummary['absent_count'] }}</h3>
                <p>Absent Days</p>
            </div>
        </div>
        <div class="text-center">
            <h4>Overall Attendance Rate: <span style="color: #28a745;">{{ $overallSummary['attendance_rate'] }}%</span></h4>
        </div>
    </div>

    <!-- Project-Specific Sections -->
    @forelse($projectAttendanceData as $projectData)
        <div class="project-section">
            <!-- Project Header -->
            <div class="project-header">
                <div>
                    <h3>🏗️ {{ $projectData['project']->ProjectName }}</h3>
                    <small>Project Attendance Summary</small>
                </div>
                <div class="project-stats">
                    <div class="project-stat">
                        <span class="number">{{ $projectData['summary']['total_employees'] }}</span>
                        <span class="label">Employees</span>
                    </div>
                    <div class="project-stat">
                        <span class="number">{{ $projectData['summary']['attendance_rate'] }}%</span>
                        <span class="label">Rate</span>
                    </div>
                    <div class="project-stat">
                        <span class="number">{{ $projectData['summary']['present_count'] }}</span>
                        <span class="label">Present</span>
                    </div>
                    <div class="project-stat">
                        <span class="number">{{ $projectData['summary']['absent_count'] }}</span>
                        <span class="label">Absent</span>
                    </div>
                </div>
            </div>
            
            <!-- Employee Summary for this Project -->
            <div class="employee-summary-section">
                <h4>👥 Employee Attendance Summary</h4>
                <div class="employee-summary-grid">
                    @php
                        $employeeStats = [];
                        foreach($projectData['attendance'] as $record) {
                            $empId = $record->employee_id;
                            if (!isset($employeeStats[$empId])) {
                                $employeeStats[$empId] = [
                                    'name' => $record->employee->full_name,
                                    'position' => $record->employee->position->PositionName ?? 'N/A',
                                    'present' => 0,
                                    'late' => 0,
                                    'overtime' => 0,
                                    'total_days' => 0
                                ];
                            }
                            $employeeStats[$empId]['total_days']++;
                            if ($record->status == 'Present') $employeeStats[$empId]['present']++;
                            if ($record->status == 'Late') $employeeStats[$empId]['late']++;
                            if ($record->isOvertime()) $employeeStats[$empId]['overtime']++;
                        }
                    @endphp
                    
                    @foreach($employeeStats as $empId => $stats)
                        <div class="employee-summary-card">
                            <div class="employee-name">
                                <strong>{{ $stats['name'] }}</strong>
                                <br>
                                <small>{{ $stats['position'] }}</small>
                            </div>
                            <div class="employee-stats-grid">
                                <div class="stat-item present">
                                    <span class="stat-number">{{ $stats['present'] }}</span>
                                    <span class="stat-label">Present</span>
                                </div>
                                <div class="stat-item late">
                                    <span class="stat-number">{{ $stats['late'] }}</span>
                                    <span class="stat-label">Late</span>
                                </div>
                                <div class="stat-item overtime">
                                    <span class="stat-number">{{ $stats['overtime'] }}</span>
                                    <span class="stat-label">Overtime</span>
                                </div>
                                <div class="stat-item total">
                                    <span class="stat-number">{{ $stats['total_days'] }}</span>
                                    <span class="stat-label">Total Days</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Project Attendance Table -->
            <div class="attendance-table">
                <table>
                    <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Date</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Status</th>
                            <th>Working Hours</th>
                            <th>Overtime</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projectData['attendance'] as $record)
                            <tr>
                                <td>
                                    <strong>{{ $record->employee->full_name ?? 'N/A' }}</strong><br>
                                    <small style="color: #6c757d;">{{ $record->employee->position->PositionName ?? 'N/A' }}</small>
                                </td>
                                <td>{{ $record->attendance_date->format('M d, Y') }}</td>
                                <td>{{ $record->formatted_time_in ?? 'N/A' }}</td>
                                <td>{{ $record->formatted_time_out ?? 'N/A' }}</td>
                                <td>
                                    @if($record->status == 'Present')
                                        <span class="status-badge status-present">{{ $record->status }}</span>
                                    @elseif($record->status == 'Late')
                                        <span class="status-badge status-late">{{ $record->status }}</span>
                                    @elseif($record->status == 'Overtime')
                                        <span class="status-badge status-overtime">{{ $record->status }}</span>
                                    @else
                                        <span class="status-badge status-absent">{{ $record->status ?? 'N/A' }}</span>
                                    @endif
                                </td>
                                <td>{{ $record->working_hours ?? 'N/A' }}</td>
                                <td>
                                    @if($record->isOvertime())
                                        <span style="color: #17a2b8;">{{ $record->overtime_hours ?? 'N/A' }}</span>
                                    @else
                                        <span style="color: #6c757d;">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->remarks)
                                        <span style="color: #6c757d;">{{ Str::limit($record->remarks, 30) }}</span>
                                    @else
                                        <span style="color: #6c757d;">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 40px; color: #6c757d;">
                                    No attendance records found for this project
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="project-section">
            <div class="project-header">
                <h3>📋 No Projects Found</h3>
            </div>
            <div style="padding: 40px; text-align: center; color: #6c757d;">
                <p>No active projects with employees found for the selected period.</p>
            </div>
        </div>
    @endforelse

    <!-- Footer -->
    <div class="footer">
        <p>Generated by Project Management System - {{ now()->format('F d, Y \a\t h:i A') }}</p>
        <p>This report contains confidential information and is intended for authorized personnel only.</p>
    </div>

    <script>
        // Auto-print when page loads (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>

@extends('layouts.app')

@section('title', 'Attendance Overview - Production Head')

@push('styles')
    <!-- DataTables CSS from CDN -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">
    <style>
        .summary-card {
            background: linear-gradient(135deg, #7fb069 0%, #6fa05a 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .summary-card h5 {
            color: white !important;
            margin-bottom: 10px;
            font-weight: 600;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .summary-card .stat-number {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 5px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .summary-card small {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
        }
        
        .filter-section {
            background: #f0f8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e0e8e0;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid transparent;
        }
        
        .status-present { 
            background-color: #28a745; 
            color: white; 
            border-color: #1e7e34;
        }
        .status-late { 
            background-color: #ffc107; 
            color: #212529; 
            border-color: #e0a800;
        }
        .status-overtime { 
            background-color: #17a2b8; 
            color: white; 
            border-color: #138496;
        }
        .status-absent { 
            background-color: #dc3545; 
            color: white; 
            border-color: #bd2130;
        }
        
        .export-buttons {
            margin-bottom: 20px;
        }
        
        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        /* Project header improvements */
        .card-header.bg-primary {
            background: linear-gradient(135deg, #7fb069 0%, #6fa05a 100%) !important;
            border: none;
        }
        
        .card-header h5 {
            color: white !important;
            font-weight: 600;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .card-header .small {
            color: rgba(255,255,255,0.9) !important;
        }
        
        .card-header .h6 {
            color: white !important;
            font-weight: 700;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        /* Table improvements */
        .table th {
            background-color: #f0f8f0 !important;
            color: #2d5a3d !important;
            font-weight: 600;
            border-bottom: 2px solid #7fb069;
            padding: 12px 8px;
        }
        
        .table td {
            color: #495057 !important;
            padding: 12px 8px;
            vertical-align: middle;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        /* Employee name styling */
        .table td strong {
            color: #212529 !important;
            font-weight: 600;
        }
        
        .table td small {
            color: #6c757d !important;
        }
        
        /* Time and hours styling */
        .table td:not(:first-child):not(:last-child) {
            font-family: 'Courier New', monospace;
            font-weight: 500;
        }
        
        /* Remarks styling */
        .table td[title] {
            cursor: help;
        }
        
        /* Status breakdown cards */
        .card.border-success .card-title {
            color: #155724 !important;
            font-weight: 600;
        }
        
        .card.border-warning .card-title {
            color: #856404 !important;
            font-weight: 600;
        }
        
        .card.border-info .card-title {
            color: #0c5460 !important;
            font-weight: 600;
        }
        
        .card.border-danger .card-title {
            color: #721c24 !important;
            font-weight: 600;
        }
        
        .card h3 {
            font-weight: 700;
            font-size: 2rem;
        }
        
        /* Page header improvements */
        .page-header h2 {
            color: #212529 !important;
            font-weight: 600;
        }
        
        .page-header .text-muted {
            color: #6c757d !important;
        }
        
        /* Filter section improvements */
        .filter-section .form-label {
            color: #495057 !important;
            font-weight: 600;
        }
        
        .filter-section .form-control {
            border: 1px solid #ced4da;
            border-radius: 6px;
        }
        
        .filter-section .form-control:focus {
            border-color: #7fb069;
            box-shadow: 0 0 0 0.2rem rgba(127,176,105,0.25);
        }
        
        /* DataTables styling */
        .dataTables_wrapper {
            padding: 0;
        }
        
        .dataTables_filter {
            margin-bottom: 15px;
        }
        
        .dataTables_filter input {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 6px 12px;
        }
        
        .dataTables_filter input:focus {
            border-color: #7fb069;
            box-shadow: 0 0 0 0.2rem rgba(127,176,105,0.25);
        }
        
        .dataTables_length {
            margin-bottom: 15px;
        }
        
        .dataTables_length select {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 4px 8px;
        }
        
        .dt-buttons {
            margin-bottom: 15px;
        }
        
        .dt-buttons .btn {
            margin-right: 5px;
            margin-bottom: 5px;
        }
        
        .dataTables_info {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .dataTables_paginate {
            margin-top: 15px;
        }
        
        .dataTables_paginate .paginate_button {
            padding: 6px 12px;
            margin: 0 2px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background: white;
            color: #7fb069;
            font-size: 0.875rem;
            min-width: auto;
            height: auto;
        }
        
        .dataTables_paginate .paginate_button:hover {
            background: #f0f8f0;
            border-color: #7fb069;
            color: #6fa05a;
        }
        
        .dataTables_paginate .paginate_button.current {
            background: #7fb069;
            color: white;
            border-color: #7fb069;
        }
        
        .dataTables_paginate .paginate_button.disabled {
            color: #6c757d;
            background: #f8f9fa;
            border-color: #dee2e6;
        }
        
        /* Fix oversized pagination elements */
        .dataTables_paginate .paginate_button.previous,
        .dataTables_paginate .paginate_button.next {
            padding: 6px 12px !important;
            font-size: 0.875rem !important;
            min-width: auto !important;
            height: auto !important;
        }
        
        .dataTables_paginate .paginate_button.first,
        .dataTables_paginate .paginate_button.last {
            padding: 6px 12px !important;
            font-size: 0.875rem !important;
            min-width: auto !important;
            height: auto !important;
        }
        
        /* Fix any oversized input fields in pagination */
        .dataTables_paginate input,
        .dataTables_paginate select {
            padding: 4px 8px !important;
            font-size: 0.875rem !important;
            height: auto !important;
            width: auto !important;
            max-width: 60px !important;
        }
        
        /* Ensure pagination container has proper sizing */
        .dataTables_paginate {
            font-size: 0.875rem;
            line-height: 1.5;
        }
        
        /* Project filters styling */
        .project-filters {
            background: #f8f9fa !important;
            border-bottom: 1px solid #dee2e6 !important;
        }
        
        .project-filters .form-label {
            color: #495057 !important;
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 5px;
        }
        
        .project-filters .form-control-sm {
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 0.85rem;
        }
        
        .project-filters .form-control-sm:focus {
            border-color: #7fb069;
            box-shadow: 0 0 0 0.2rem rgba(127,176,105,0.25);
        }
        
        .project-filters .btn-sm {
            font-size: 0.8rem;
            padding: 4px 12px;
        }
        
        /* Badge styling for counts */
        .badge-lg {
            font-size: 0.9rem;
            padding: 6px 10px;
            font-weight: 600;
        }
        
        .badge-success {
            background-color: #28a745 !important;
            color: white !important;
        }
        
        .badge-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }
        
        .badge-info {
            background-color: #17a2b8 !important;
            color: white !important;
        }
        
        .badge-secondary {
            background-color: #6c757d !important;
            color: white !important;
        }
        
        /* Attendance rate styling */
        .attendance-rate {
            min-width: 80px;
        }
        
        .rate-number {
            font-weight: 600;
            font-size: 0.9rem;
            color: #495057;
        }
        
        .progress {
            background-color: #e9ecef;
            border-radius: 3px;
        }
        
        .progress-bar {
            border-radius: 3px;
        }
        
        .progress-bar.bg-success {
            background-color: #7fb069 !important;
        }
        
        .progress-bar.bg-warning {
            background-color: #ffc107 !important;
        }
        
        .progress-bar.bg-danger {
            background-color: #dc3545 !important;
        }
    </style>
@endpush

@section('content')


    <!-- Filters Section -->
    <div class="filter-section">
        <form method="GET" action="{{ route('prodhead.attendance') }}" id="filterForm">
            <div class="row">
                <div class="col-md-5">
                    <label for="start_date" class="form-label"><i class="fas fa-calendar-alt mr-1"></i>Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                </div>
                <div class="col-md-5">
                    <label for="end_date" class="form-label"><i class="fas fa-calendar-alt mr-1"></i>End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-block" style="background: #7fb069; color: white; border: none;">
                            <i class="fas fa-filter mr-1"></i>Filter
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- All Projects Section -->
    <div class="export-buttons">
        <div>
            <h5><i class="fas fa-globe mr-2"></i>All Projects</h5>
            <p class="text-muted mb-0">Attendance data separated by project</p>
        </div>
    </div>

    <!-- Project-Specific Attendance Tables -->
    @forelse($projectAttendanceData as $projectData)
        <div class="table-container mb-4">
            <!-- Project Header -->
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">
                        <i class="fas fa-project-diagram mr-2"></i>{{ $projectData['project']->ProjectName }}
                    </h5>
                    <small>Project Attendance Summary</small>
                </div>
                <div class="text-right">
                    <div class="row text-center">
                        <div class="col-3">
                            <div class="small">Employees</div>
                            <div class="h6 mb-0">{{ $projectData['summary']['total_employees'] }}</div>
                        </div>
                        <div class="col-3">
                            <div class="small">Rate</div>
                            <div class="h6 mb-0">{{ $projectData['summary']['attendance_rate'] }}%</div>
                        </div>
                        <div class="col-3">
                            <div class="small">Present</div>
                            <div class="h6 mb-0">{{ $projectData['summary']['present_count'] }}</div>
                        </div>
                        <div class="col-3">
                            <div class="small">Absent</div>
                            <div class="h6 mb-0">{{ $projectData['summary']['absent_count'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Project-Specific Filters -->
            <div class="project-filters bg-light p-3 border-bottom">
                <div class="row">
                    <div class="col-md-3">
                        <label for="project_start_date_{{ $projectData['project']->ProjectID }}" class="form-label">
                            <i class="fas fa-calendar-alt mr-1"></i>Start Date
                        </label>
                        <input type="date" class="form-control form-control-sm" 
                               id="project_start_date_{{ $projectData['project']->ProjectID }}" 
                               value="{{ $startDate }}">
                    </div>
                    <div class="col-md-3">
                        <label for="project_end_date_{{ $projectData['project']->ProjectID }}" class="form-label">
                            <i class="fas fa-calendar-alt mr-1"></i>End Date
                        </label>
                        <input type="date" class="form-control form-control-sm" 
                               id="project_end_date_{{ $projectData['project']->ProjectID }}" 
                               value="{{ $endDate }}">
                    </div>
                    <div class="col-md-3">
                        <label for="project_status_filter_{{ $projectData['project']->ProjectID }}" class="form-label">
                            <i class="fas fa-filter mr-1"></i>Status
                        </label>
                        <select class="form-control form-control-sm" 
                                id="project_status_filter_{{ $projectData['project']->ProjectID }}">
                            <option value="">All Status</option>
                            <option value="Present">Present</option>
                            <option value="Late">Late</option>
                            <option value="Overtime">Overtime</option>
                            <option value="Absent">Absent</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="project_employee_filter_{{ $projectData['project']->ProjectID }}" class="form-label">
                            <i class="fas fa-user mr-1"></i>Employee
                        </label>
                        <select class="form-control form-control-sm" 
                                id="project_employee_filter_{{ $projectData['project']->ProjectID }}">
                            <option value="">All Employees</option>
                            @foreach($projectData['employees'] as $employee)
                                <option value="{{ $employee->full_name }}">{{ $employee->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-sm" 
                                onclick="filterProjectTable({{ $projectData['project']->ProjectID }})"
                                style="background: #7fb069; color: white; border: none;">
                            <i class="fas fa-filter mr-1"></i>Apply Filters
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm ml-2" 
                                onclick="clearProjectFilters({{ $projectData['project']->ProjectID }})">
                            <i class="fas fa-times mr-1"></i>Clear
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Project Employee Summary Table -->
            <div class="table-responsive">
                <table id="projectTable_{{ $projectData['project']->ProjectID }}" class="table table-striped table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Employee Name</th>
                            <th>Position</th>
                            <th>Days Present</th>
                            <th>Days Late</th>
                            <th>Days Overtime</th>
                            <th>Total Days</th>
                            <th>Attendance Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $employeeStats = [];
                            foreach($projectData['attendance'] as $record) {
                                $empId = $record->employee_id;
                                if (!isset($employeeStats[$empId])) {
                                    $position = $record->employee->relationLoaded('position') 
                                        ? $record->employee->getRelation('position') 
                                        : $record->employee->position()->first();
                                    $positionName = $position ? $position->PositionName : 'N/A';
                                    
                                    $employeeStats[$empId] = [
                                        'name' => $record->employee->full_name,
                                        'position' => $positionName,
                                        'present' => 0,
                                        'late' => 0,
                                        'overtime' => 0,
                                        'total' => 0
                                    ];
                                }
                                $employeeStats[$empId]['total']++;
                                if ($record->status == 'Present') $employeeStats[$empId]['present']++;
                                if ($record->status == 'Late') $employeeStats[$empId]['late']++;
                                if ($record->status == 'Overtime') $employeeStats[$empId]['overtime']++;
                            }
                        @endphp
                        
                        @forelse($employeeStats as $empId => $stats)
                            @php
                                $attendanceRate = $stats['total'] > 0 ? round(($stats['present'] / $stats['total']) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $stats['name'] }}</strong>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $stats['position'] ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-success badge-lg">{{ $stats['present'] }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-warning badge-lg">{{ $stats['late'] }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-info badge-lg">{{ $stats['overtime'] }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-secondary badge-lg">{{ $stats['total'] }}</span>
                                </td>
                                <td>
                                    <div class="attendance-rate">
                                        <span class="rate-number">{{ $attendanceRate }}%</span>
                                        <div class="progress mt-1" style="height: 6px;">
                                            <div class="progress-bar 
                                                @if($attendanceRate >= 90) bg-success
                                                @elseif($attendanceRate >= 70) bg-warning
                                                @else bg-danger
                                                @endif" 
                                                role="progressbar" 
                                                style="width: {{ $attendanceRate }}%">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <br>No attendance records found for this project
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="table-container">
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Projects Found</h5>
                <p class="text-muted">No active projects with employees found for the selected period.</p>
            </div>
        </div>
    @endforelse
</div>
@endsection

@push('scripts')
    <!-- DataTables JS from CDN -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTables for each project table
            @foreach($projectAttendanceData ?? [] as $projectData)
                @php
                    $tableId = 'projectTable_' . $projectData['project']->ProjectID;
                @endphp
                
                var table_{{ $projectData['project']->ProjectID }} = $('#{{ $tableId }}').DataTable({
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    order: [[0, 'asc']], // Sort by Employee Name
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'excel',
                            text: '<i class="fas fa-file-excel"></i> Excel',
                            className: 'btn btn-success btn-sm',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6]
                            }
                        },
                        {
                            extend: 'pdf',
                            text: '<i class="fas fa-file-pdf"></i> PDF',
                            className: 'btn btn-danger btn-sm',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6]
                            }
                        },
                        {
                            extend: 'print',
                            text: '<i class="fas fa-print"></i> Print',
                            className: 'btn btn-info btn-sm',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6]
                            },
                            customize: function (win) {
                                // Get the project name and dates
                                var projectName = @json($projectData['project']->ProjectName ?? 'All Projects');
                                var startDate = @json(\Carbon\Carbon::parse($startDate)->format('M d, Y'));
                                var endDate = @json(\Carbon\Carbon::parse($endDate)->format('M d, Y'));
                                var generatedOn = @json(now()->format('F d, Y \a\t h:i A'));
                                
                                // Create header HTML
                                var header = '<div style="text-align: center; margin-bottom: 30px; page-break-after: avoid;">' +
                                    '<div style="font-size: 24pt; font-weight: bold; color: #009900; text-transform: uppercase; margin: 0; line-height: 1;">MACUA CONSTRUCTION</div>' +
                                    '<div style="font-size: 9pt; font-weight: bold; color: #000; text-transform: uppercase; margin: 5px 0 0 0;">General Contractor – Mechanical Works - Fabrication</div>' +
                                    '<div style="font-size: 8pt; color: #000; font-weight: bold; margin-top: 2px;">PCAB LICENSE NO. 41994</div>' +
                                    '<div style="background-color: #009900; height: 4px; width: 100%; margin: 10px 0 20px 0;"></div>' +
                                    '<div style="font-size: 16pt; font-weight: bold; color: #0056b3; margin-bottom: 5px; text-transform: uppercase;">ATTENDANCE REPORT</div>' +
                                    '<p style="color: #666; margin: 5px 0; font-size: 10pt;">' + projectName + ' - ' + startDate + ' to ' + endDate + '</p>' +
                                    '<p style="color: #666; margin: 5px 0; font-size: 9pt;">Generated on: ' + generatedOn + '</p>' +
                                    '</div>';
                                
                                // Add header to the print window
                                $(win.document.body).prepend(header);
                                
                                // Style the print window
                                $(win.document.body).css('font-size', '11pt');
                                $(win.document.body).find('table').css('font-size', '10pt');
                                $(win.document.body).find('table').css('margin-top', '20px');
                                
                                // Remove DataTables default title
                                $(win.document.body).find('h1').remove();
                            }
                        }
                    ],
                    language: {
                        search: "Search employees:",
                        lengthMenu: "Show _MENU_ employees per page",
                        info: "Showing _START_ to _END_ of _TOTAL_ employees",
                        infoEmpty: "No employees found",
                        infoFiltered: "(filtered from _MAX_ total employees)",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    },
                    columnDefs: [
                        {
                            targets: [2, 3, 4, 5], // Days Present, Days Late, Days Overtime, Total Days
                            orderable: true,
                            searchable: false
                        },
                        {
                            targets: 6, // Attendance Rate
                            orderable: true,
                            searchable: false,
                            type: 'num'
                        }
                    ]
                });

                // Custom filter function for project-specific filters
                window.filterProjectTable_{{ $projectData['project']->ProjectID }} = function() {
                    var employee = $('#project_employee_filter_{{ $projectData['project']->ProjectID }}').val();
                    
                    // Filter by employee name (column 0)
                    if (employee) {
                        table_{{ $projectData['project']->ProjectID }}.column(0).search(employee).draw();
                    } else {
                        table_{{ $projectData['project']->ProjectID }}.column(0).search('').draw();
                    }
                    
                    // Note: Date and status filtering would require server-side filtering
                    // For now, we'll just filter by employee name
                };

                // Clear filters function
                window.clearProjectFilters_{{ $projectData['project']->ProjectID }} = function() {
                    $('#project_start_date_{{ $projectData['project']->ProjectID }}').val('{{ $startDate }}');
                    $('#project_end_date_{{ $projectData['project']->ProjectID }}').val('{{ $endDate }}');
                    $('#project_status_filter_{{ $projectData['project']->ProjectID }}').val('');
                    $('#project_employee_filter_{{ $projectData['project']->ProjectID }}').val('');
                    table_{{ $projectData['project']->ProjectID }}.search('').columns().search('').draw();
                };
            @endforeach

            // Global filter function (for backward compatibility)
            window.filterProjectTable = function(projectId) {
                if (typeof window['filterProjectTable_' + projectId] === 'function') {
                    window['filterProjectTable_' + projectId]();
                }
            };

            // Global clear filters function (for backward compatibility)
            window.clearProjectFilters = function(projectId) {
                if (typeof window['clearProjectFilters_' + projectId] === 'function') {
                    window['clearProjectFilters_' + projectId]();
                }
            };
        });
    </script>
@endpush


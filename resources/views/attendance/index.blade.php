@extends('layouts.app')

@section('title', 'Employee Attendance')
@section('page-title', 'Employee Attendance')

@push('styles')
<!-- DataTables CSS from CDN -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">
<style>
    .info-box {
        cursor: pointer;
        transition: transform 0.2s ease-in-out;
    }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        /* QR detected container styling */
        .qr-detected-container .form-group {
            margin-bottom: 1rem;
        }

        .qr-detected-container .form-group label {
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

    .info-box:hover {
        transform: translateY(-5px);
    }

    .table-responsive {
        overflow-x: auto;
    }

        .table td,
        .table th {
        vertical-align: middle;
    }

    .card-header .btn {
        opacity: 1 !important;
        visibility: visible !important;
        display: inline-block !important;
    }

    .scanner-container {
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        min-height: 300px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .scanner-placeholder {
        font-size: 48px;
        color: #6c757d;
        margin-bottom: 15px;
    }

    /* DataTables Borderline Styling - Only horizontal borders between rows */
    #attendanceTable, #absentEmployeesTable {
        border: none !important;
    }

    #attendanceTable thead th, #absentEmployeesTable thead th {
        border: none !important;
        border-bottom: 1px solid #dee2e6 !important;
    }

    #attendanceTable tbody td, #absentEmployeesTable tbody td {
        border: none !important;
        border-bottom: 1px solid #dee2e6 !important;
    }

    #attendanceTable tbody tr:last-child td, #absentEmployeesTable tbody tr:last-child td {
        border-bottom: none !important;
    }

    /* Remove margins from DataTable wrapper */
    #attendanceTable_wrapper, #absentEmployeesTable_wrapper {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    #attendanceTable_wrapper .dataTables_length,
    #attendanceTable_wrapper .dataTables_filter,
    #absentEmployeesTable_wrapper .dataTables_length,
    #absentEmployeesTable_wrapper .dataTables_filter {
        margin-left: 0 !important;
        margin-right: 0 !important;
        margin-top: 0.5rem !important;
        margin-bottom: 0.25rem !important;
        padding-left: 1rem !important;
        padding-right: 1rem !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }

    #attendanceTable_wrapper .dataTables_length,
    #absentEmployeesTable_wrapper .dataTables_length {
        display: flex !important;
        align-items: center !important;
    }

    #attendanceTable_wrapper .dataTables_length label,
    #absentEmployeesTable_wrapper .dataTables_length label {
        display: flex !important;
        align-items: center !important;
        margin-bottom: 0 !important;
    }

    #attendanceTable_wrapper .dataTables_length label::before,
    #absentEmployeesTable_wrapper .dataTables_length label::before {
        content: "Show entries" !important;
        margin-right: 0.5rem !important;
        font-weight: normal !important;
    }

    #attendanceTable_wrapper .dataTables_length label select,
    #absentEmployeesTable_wrapper .dataTables_length label select {
        margin: 0 !important;
    }

    #attendanceTable_wrapper .dataTables_info,
    #attendanceTable_wrapper .dataTables_paginate,
    #absentEmployeesTable_wrapper .dataTables_info,
    #absentEmployeesTable_wrapper .dataTables_paginate {
        margin-left: 0 !important;
        margin-right: 0 !important;
        margin-top: 0.5rem !important;
        padding-left: 1rem !important;
        padding-right: 1rem !important;
        padding-top: 0 !important;
        padding-bottom: 1rem !important;
    }

    #attendanceTable_wrapper .dataTables_paginate .paginate_button,
    #absentEmployeesTable_wrapper .dataTables_paginate .paginate_button {
        background: none !important;
        border: none !important;
        padding: 0.25rem 0.5rem !important;
        text-decoration: underline !important;
        color: #007bff !important;
    }

    #attendanceTable_wrapper .dataTables_paginate .paginate_button.current,
    #absentEmployeesTable_wrapper .dataTables_paginate .paginate_button.current {
        background: none !important;
        border: none !important;
        color: #007bff !important;
        text-decoration: underline !important;
        font-weight: bold !important;
    }

    #attendanceTable_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled),
    #absentEmployeesTable_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
        background: none !important;
        border: none !important;
        text-decoration: underline !important;
    }

    #attendanceTable_wrapper .dataTables_paginate .paginate_button.disabled,
    #absentEmployeesTable_wrapper .dataTables_paginate .paginate_button.disabled {
        background: none !important;
        border: none !important;
        text-decoration: none !important;
        color: #6c757d !important;
        opacity: 0.5 !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <!-- Date and Filter Section -->


        <!-- Dashboard Section - Under Construction -->
       


        <!-- Main Content Row -->
        <!-- Project Information -->
        @if($project)
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Auto-detected Project:</strong> You are viewing attendance for your assigned project:
                        <strong>{{ $project->ProjectName }}</strong>
                    </div>
                </div>
            </div>
        @else
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="card">
        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fas fa-project-diagram mr-1"></i> Select Project for
                                Attendance</h5>
        </div>
        <div class="card-body">
                            <form method="GET" id="projectSelectForm">
                <div class="row">
                                    <div class="col-md-8">
                                        <select name="project_id" id="projectSelect" class="form-control" required>
                                            <option value="">-- Select a Project --</option>
                                            @foreach(\App\Models\Project::active()->get() as $proj)
                                                <option value="{{ $proj->ProjectID }}">{{ $proj->ProjectName }}</option>
                                            @endforeach
                            </select>
                    </div>
                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-arrow-right mr-1"></i> Go to Project Attendance
                                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
                </div>
                </div>
        @endif

    <div class="row">
        <!-- QR Scanner Section -->
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-qrcode mr-1"></i> QR Code Scanner
                            @if($project)
                                <br><small class="text-muted">Scan QR codes for {{ $project->ProjectName }} team members</small>
                            @endif
                        </h3>
                </div>
                <div class="card-body">
                    <div class="scanner-container">
                            <div class="scanner-con">
                                <h5 class="text-center mb-3">
                                    @if($project)
                                        Scan QR codes for {{ $project->ProjectName }} team members
                                    @else
                                        Scan your QR Code here for your attendance
                                    @endif
                                </h5>
                                <video id="interactive" class="viewport" width="100%"
                                    style="border-radius: 8px; background: #000; min-height: 200px;"></video>
                                <button type="button" id="btnCameraToggle" class="btn btn-success btn-block mt-3">
                            <i class="fas fa-camera mr-1"></i> Turn Camera On
                        </button>
                            </div>
                    </div>

                    <div class="qr-detected-container mt-3" style="display: none;">
                        <div class="alert alert-info text-center">
                            <h4><i class="fas fa-check-circle mr-2"></i>Employee QR Detected!</h4>
                                <p class="mb-2">Employee: <strong id="detected-employee">Loading...</strong></p>
                                
                                <!-- Attendance Status Display -->
                                <div id="attendance-status-display" class="mb-3" style="display: none;">
                                    <small class="text-muted">
                                        <i class="fas fa-clock mr-1"></i>
                                        <span id="status-text">Checking attendance status...</span>
                                    </small>
                                </div>
                                
                            <input type="hidden" id="detected-employee-id" name="detected_employee_id" value="">
                                
                            <!-- Auto-Action Button (recommended action based on time) -->
                            <div class="row mb-2">
                                <div class="col-12">
                                    <button type="button" id="btnAutoAction" class="btn btn-success btn-block btn-lg">
                                        <i class="fas fa-clock mr-1"></i> <span id="auto-action-label">Time In</span>
                                    </button>
                                    <small class="text-muted d-block text-center mt-1" id="auto-action-hint">Recommended action based on current time</small>
                                </div>
                            </div>
                            
                            <!-- Manual Action Buttons (4 clock events) -->
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <button type="button" id="btnTimeIn" class="btn btn-dark btn-block btn-sm" data-action="time_in">
                                        <i class="fas fa-sign-in-alt mr-1"></i> Time In
                                    </button>
                                </div>
                                <div class="col-6 mb-2">
                                    <button type="button" id="btnLunchOut" class="btn btn-warning btn-block btn-sm" data-action="lunch_out">
                                        <i class="fas fa-utensils mr-1"></i> Lunch Out
                                    </button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <button type="button" id="btnLunchIn" class="btn btn-info btn-block btn-sm" data-action="lunch_in">
                                        <i class="fas fa-utensils mr-1"></i> Lunch In
                                    </button>
                                </div>
                                <div class="col-6 mb-2">
                                    <button type="button" id="btnTimeOut" class="btn btn-danger btn-block btn-sm" data-action="time_out">
                                        <i class="fas fa-sign-out-alt mr-1"></i> Time Out
                                    </button>
                                </div>
                            </div>
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <button type="button" id="btnScanAgain" class="btn btn-secondary btn-block">
                                            <i class="fas fa-redo mr-1"></i> Scan Again
                                        </button>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Table Section -->
        <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h3 class="card-title mb-0">
                                    <i class="fas fa-clipboard-list mr-1"></i>
                                    @if($project)
                                        {{ $project->ProjectName }} - Daily Attendance Records
                                    @else
                                        Daily Attendance Records
                                    @endif
                                </h3>
                            </div>
                            <div class="col-md-6 text-right">
                                <strong>Date: {{ \Carbon\Carbon::parse($date ?? now())->format('M d, Y') }}</strong>
                            </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="attendanceTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Time In</th>
                                    <th>Lunch Out</th>
                                    <th>Lunch In</th>
                                    <th>Time Out</th>
                                    <th>Status</th>
                                    <th>Working Hours</th>
                                    <th style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendanceRecords ?? [] as $record)
                                <tr>
                                    <td>{{ $record->employee->full_name ?? 'N/A' }}</td>
                                    <td>{{ $record->employee->position->PositionName ?? 'N/A' }}</td>
                                            <td>{{ $record->formatted_time_in ?? 'N/A' }}</td>
                                            <td>{{ $record->formatted_lunch_out ?? 'N/A' }}</td>
                                            <td>{{ $record->formatted_lunch_in ?? 'N/A' }}</td>
                                            <td>{{ $record->formatted_time_out ?? 'N/A' }}</td>
                                    <td>
                                        @if($record->status == 'Present')
                                            <span class="badge badge-success">{{ $record->status }}</span>
                                        @elseif($record->status == 'Late')
                                            <span class="badge badge-warning">{{ $record->status }}</span>
                                        @elseif($record->status == 'Absent')
                                            <span class="badge badge-danger">{{ $record->status }}</span>
                                        @elseif($record->status == 'Overtime')
                                            <span class="badge badge-info">{{ $record->status }}</span>
                                        @elseif($record->status == 'Half Day')
                                            <span class="badge badge-secondary">{{ $record->status }}</span>
                                        @else
                                                    <span class="badge badge-primary">{{ $record->status ?? 'N/A' }}</span>
                                        @endif
                                    </td>
                                            <td>{{ $record->working_hours ?? 'N/A' }}</td>
                                    <td>
                                        <div class="btn-group">
                                                    <button type="button" id="edit-attendance-{{ $record->id }}"
                                                        name="edit_attendance_{{ $record->id }}"
                                                        class="btn btn-warning btn-sm edit-attendance"
                                                        data-id="{{ $record->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                                    <button type="button" id="delete-attendance-{{ $record->id }}"
                                                        name="delete_attendance_{{ $record->id }}"
                                                        class="btn btn-danger btn-sm delete-attendance"
                                                        data-id="{{ $record->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">No attendance records found for this date.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Absent Employees Section -->
    <div class="card card-secondary card-outline">
        <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user-slash mr-1"></i> Absent Employees for
                    {{ \Carbon\Carbon::parse($date ?? now())->format('M d, Y') }}
                </h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="absentEmployeesTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Position/Department</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absentEmployees ?? [] as $employee)
                        <tr>
                            <td>{{ $employee->full_name ?? 'N/A' }}</td>
                            <td>{{ $employee->position->PositionName ?? 'N/A' }}</td>
                            <td>
                                        <button type="button" id="view-history-{{ $employee->id }}"
                                            name="view_history_{{ $employee->id }}" class="btn btn-info btn-sm view-history"
                                            data-employee-id="{{ $employee->id }}">
                                    <i class="fas fa-history"></i> View History
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center">All employees are present today!</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div><!-- /.container-fluid -->

<!-- Mark Attendance Modal -->
    <div class="modal fade" id="markAttendanceModal" tabindex="-1" role="dialog" aria-labelledby="markAttendanceModalLabel"
        aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="markAttendanceModalLabel"><i class="fas fa-clock mr-1"></i> Mark Employee
                        Attendance</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="markAttendanceForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="employee_select">Select Employee:</label>
                        <select class="form-control" id="employee_select" name="employee_id" required>
                            <option value="">-- Select Employee --</option>
                            @foreach($allEmployees ?? [] as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->full_name }} ({{ $employee->position->PositionName ?? 'N/A' }})
                                    </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="attendance_type">Attendance Type:</label>
                        <select class="form-control" id="attendance_type" name="type" required>
                            <option value="">-- Select Type --</option>
                            <option value="time_in">Time In</option>
                            <option value="time_out">Time Out</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                        <button type="button" id="modal-close-btn" name="modal_close_btn" class="btn btn-secondary"
                            data-dismiss="modal">Close</button>
                        <button type="submit" id="save-attendance-btn" name="save_attendance_btn" class="btn btn-primary"><i
                                class="fas fa-save"></i> Save Attendance</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- DataTables & Plugins from CDN -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>

<!-- Moment.js for date handling -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

    <!-- Instascan QR Scanner (matching your native PHP version) -->
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
<script>
        // Instascan library check
        window.addEventListener('load', function () {
            if (typeof Instascan !== 'undefined') {
                console.log('Instascan library loaded successfully');
        } else {
                console.log('Instascan library not loaded');
        }
    });
</script>

<script>
    $(function () {

            // QUICK FIX: Set this to false to completely disable DataTables and use basic table styling
            const DISABLE_DATATABLES = false;

            if (DISABLE_DATATABLES) {
                console.log('DataTables completely disabled - using basic table styling only');
                $("#attendanceTable, #absentEmployeesTable").addClass('table-striped table-bordered');
                return;
            }
        
        // Date picker is now a simple HTML5 date input - no initialization needed

        // Initialize DataTables with error handling
        function initializeAttendanceTable() {
                // Set this to false to disable DataTables completely and use basic table
                const ENABLE_DATATABLES = true;

                if (!ENABLE_DATATABLES) {
                    console.log('DataTables disabled - using basic table styling');
                    $("#attendanceTable").addClass('table-striped table-bordered');
                    return;
                }

            try {
                // Check if table exists and has correct structure
                const table = $("#attendanceTable");
                if (table.length === 0) {
                    console.log('Attendance table not found');
                    return;
                }
                
                const headerCols = table.find('thead tr th').length;
                    const bodyRows = table.find('tbody tr');
                    let columnMismatch = false;
                    let validRows = 0;

                    // Check all body rows for column consistency
                    console.log('Checking table structure:');
                    console.log('Header columns:', headerCols);
                    console.log('Total body rows:', bodyRows.length);

                    bodyRows.each(function (index) {
                        const rowCols = $(this).find('td').length;
                        console.log(`Row ${index}: ${rowCols} columns`);

                        // Skip empty rows (colspan rows)
                        if (rowCols > 0 && rowCols !== headerCols) {
                            console.warn('Column count mismatch in row:', index, 'Header:', headerCols, 'vs Row:', rowCols);
                            console.warn('Row content:', $(this).html());
                            columnMismatch = true;
                        }
                        if (rowCols === headerCols) {
                            validRows++;
                        }
                    });

                    if (columnMismatch) {
                        console.warn('Column count mismatch detected. Header:', headerCols, 'Valid rows:', validRows);
                    }

                    // Only initialize DataTables if we have valid rows and no column mismatches
                    if (validRows > 0 && !columnMismatch) {
                        // Destroy existing DataTable instance if it exists
                        if ($.fn.DataTable.isDataTable('#attendanceTable')) {
                            $('#attendanceTable').DataTable().destroy();
                        }

                        console.log('Initializing DataTables with', validRows, 'valid rows');

                        // Try minimal DataTables configuration first
                        try {
                table.DataTable({
                    "responsive": true, 
                    "lengthChange": false, 
                    "autoWidth": false,
                    "pageLength": 15,
                                "searching": true, // Enable DataTables search
                                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                                    '<"row"<"col-sm-12"tr>>' +
                                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                                "language": {
                                    "search": "Search:",
                                    "lengthMenu": "Show _MENU_ entries",
                                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                                    "infoFiltered": "(filtered from _MAX_ total entries)",
                                    "paginate": {
                                        "first": "First",
                                        "last": "Last",
                                        "next": "Next",
                                        "previous": "Previous"
                                    }
                                },
                                "destroy": true, // Allow re-initialization
                                "retrieve": true // Allow retrieval of existing instance
                            });
                            console.log('DataTables initialized successfully');
                        } catch (dtError) {
                            console.error('DataTables initialization failed:', dtError);
                            table.addClass('table-striped table-bordered');
                        }
                    } else {
                        console.warn('Skipping DataTables initialization due to column issues');
                        console.warn('Valid rows:', validRows, 'Column mismatch:', columnMismatch);
                        // Just apply basic styling
                        table.addClass('table-striped table-bordered');
                    }

            } catch (error) {
                console.error('DataTables initialization error:', error);
                // Fallback: just make it a basic table without DataTables features
                $("#attendanceTable").addClass('table-striped table-bordered');
            }
        }
        
            // Initialize the table after ensuring DOM is fully ready
            $(document).ready(function () {
                // Wait a bit longer to ensure all content is loaded
                setTimeout(initializeAttendanceTable, 500);
            });

        // Initialize Absent Employees DataTable with error handling
        function initializeAbsentEmployeesTable() {
            try {
                const table = $("#absentEmployeesTable");
                if (table.length === 0) {
                    console.log('Absent employees table not found');
                    return;
                    }

                    const headerCols = table.find('thead tr th').length;
                    const bodyRows = table.find('tbody tr');
                    let validRows = 0;

                    // Check all body rows for column consistency
                    bodyRows.each(function () {
                        const rowCols = $(this).find('td').length;
                        if (rowCols === headerCols) {
                            validRows++;
                        }
                    });

                    // Only initialize DataTables if we have valid rows or if it's just an empty table
                    if (validRows > 0 || bodyRows.length === 1) {
                        // Destroy existing DataTable instance if it exists
                        if ($.fn.DataTable.isDataTable('#absentEmployeesTable')) {
                            $('#absentEmployeesTable').DataTable().destroy();
                }
                
                table.DataTable({
                    "responsive": true, 
                    "lengthChange": false, 
                    "autoWidth": false,
                    "pageLength": 15,
                            "searching": true, // Enable DataTables search
                            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                                '<"row"<"col-sm-12"tr>>' +
                                '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                            "language": {
                                "search": "Search:",
                                "lengthMenu": "Show _MENU_ entries",
                                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                                "infoEmpty": "Showing 0 to 0 of 0 entries",
                                "infoFiltered": "(filtered from _MAX_ total entries)",
                                "paginate": {
                                    "first": "First",
                                    "last": "Last",
                                    "next": "Next",
                                    "previous": "Previous"
                                }
                            },
                    "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                    "columnDefs": [
                        { "orderable": false, "targets": 2 } // Disable sorting on Actions column
                    ],
                            "order": [[0, "asc"]], // Sort by Name column
                            "destroy": true, // Allow re-initialization
                            "retrieve": true // Allow retrieval of existing instance
        }).buttons().container().appendTo('#absentEmployeesTable_wrapper .col-md-6:eq(0)');
                    } else {
                        console.warn('Skipping Absent Employees DataTables initialization due to column mismatch');
                        // Just apply basic styling
                        table.addClass('table-striped table-bordered');
                    }

            } catch (error) {
                console.error('Absent Employees DataTable initialization error:', error);
                // Fallback: just make it a basic table without DataTables features
                $("#absentEmployeesTable").addClass('table-striped table-bordered');
            }
        }
        
            // Initialize the absent employees table after ensuring DOM is ready
            $(document).ready(function () {
                setTimeout(initializeAbsentEmployeesTable, 600);

                // Handle project selection form
                $('#projectSelectForm').on('submit', function (e) {
                    e.preventDefault();
                    const projectId = $('#projectSelect').val();
                    if (projectId) {
                        window.location.href = '/attendance/project/' + projectId;
                    }
                });
            });

            // QR Scanner functionality using Instascan (matching your native PHP version)
        let scanner = null;
        let isScanning = false;

        // Use event delegation to handle dynamically created buttons
            $(document).on('click', '#btnCameraToggle', function () {
            console.log('Camera toggle clicked, isScanning:', isScanning);
                console.log('Instascan library available:', typeof Instascan !== 'undefined');
            
            if (!isScanning) {
                console.log('Starting QR Scanner...');
                startQRScanner();
            } else {
                console.log('Stopping QR Scanner...');
                stopQRScanner();
            }
        });



        function startQRScanner() {
            // Check if we're on HTTPS or localhost
            const isSecure = location.protocol === 'https:' || 
                           location.hostname === 'localhost' || 
                           location.hostname === '127.0.0.1' ||
                           location.hostname === '0.0.0.0';
            
            if (!isSecure) {
                console.warn('Camera access requires HTTPS or localhost. Current URL:', location.href);
                alert('❌ Camera access requires HTTPS or localhost.\n\nCurrent URL: ' + location.href + '\n\nPlease access via:\n• http://localhost:8000/attendance\n• Or set up HTTPS\n• Or use Chrome with --unsafely-treat-insecure-origin-as-secure flag');
                return;
            }
            
                // Use Instascan (matching your native PHP version)
                if (typeof Instascan !== 'undefined') {
                    // Use existing video element (matching your native PHP version)
                    const video = document.getElementById('interactive');
                if (video) {
                    try {
                            // Create Instascan scanner (matching your native PHP version)
                            scanner = new Instascan.Scanner({ video: video });

                            scanner.addListener('scan', function (content) {
                                console.log('QR Code detected:', content);
                                handleQRCode(content);
                            });

                            // Get cameras and start scanner (matching your native PHP version)
                            Instascan.Camera.getCameras()
                                .then(function (cameras) {
                                    if (cameras.length > 0) {
                                        scanner.start(cameras[0]);
                                        console.log('Instascan started successfully');
                            isScanning = true;
                            // Update button state
                            $("#btnCameraToggle").html('<i class="fas fa-stop mr-1"></i> Turn Camera Off').removeClass('btn-success').addClass('btn-danger');
                                        // Update scanner container text
                                        $('.scanner-con h5').text('✅ Camera Active - Scanning for QR codes...');
                                    } else {
                                        console.error('No cameras found.');
                                        alert('No cameras found.');
                                    }
                                })
                                .catch(function (err) {
                                    console.error('Camera access error:', err);
                                    alert('Camera access error: ' + err);
                        });
                    } catch (error) {
                            console.warn('Instascan initialization failed:', error);
                            alert('❌ Instascan initialization failed: ' + error.message);
                    }
                }
            } else {
                    console.warn('Instascan library not available');
                    alert('❌ Instascan library not available. Please refresh the page and try again.');
            }
        }

        function stopQRScanner() {
            console.log('Stopping QR scanner...');
            isScanning = false;
            
                // Stop Instascan scanner
            if (scanner) {
                scanner.stop();
                scanner = null;
            }
            
            // Stop video stream
            const video = document.getElementById('qr-video');
            if (video && video.srcObject) {
                const tracks = video.srcObject.getTracks();
                tracks.forEach(track => {
                    track.stop();
                });
                video.srcObject = null;
            }

            // Reset button and container
            $("#btnCameraToggle").html('<i class="fas fa-camera mr-1"></i> Turn Camera On').removeClass('btn-danger').addClass('btn-success');
                $('.scanner-con h5').text('Scan your QR Code here for your attendance');
                $('.scanner-con').show();
                $('.qr-detected-container').hide();
        }

            function handleQRCode(content) {
                console.log('QR Code detected:', content);

                // Stop scanner after detection (matching your native PHP version)
                if (scanner) {
                    scanner.stop();
                }

                // Show detected container and hide scanner (matching your native PHP version)
                $('.qr-detected-container').show();
                $('.scanner-con').hide();

                // Set the detected QR code value
                $("#detected-employee-id").val(content);

                // Look up employee by QR code (matching your native PHP approach)
                findEmployeeByQrCode(content);
        }

        function findEmployeeByQrCode(qrCode) {
            console.log('Finding employee by QR code:', qrCode);

                // Get project ID from the page (if available)
                const projectId = '{{ $project->ProjectID ?? "" }}';
            
            $.ajax({
                url: '/api/employee/qr/' + encodeURIComponent(qrCode),
                method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    data: {
                        project_id: projectId
                    },
                    success: function (response) {
                    console.log('Employee found by QR code:', response);
                    if (response.success) {
                        showEmployeeDetected(response.employee.id, response.employee.full_name);
                    } else {
                            alert('❌ ' + response.message || 'Employee not found for QR code: ' + qrCode);
                    }
                },
                    error: function (xhr, status, error) {
                    console.error('Error finding employee by QR code:', { xhr, status, error });
                        if (xhr.status === 403) {
                            alert('❌ Employee is not assigned to this project');
                        } else {
                    alert('❌ Error looking up employee by QR code: ' + error);
                        }
                }
            });
        }


        function showEmployeeDetected(employeeId, employeeName = null) {
            console.log('showEmployeeDetected called with:', { employeeId, employeeName });
            
            if (!employeeName) {
                console.log('Fetching employee name from server for ID:', employeeId);
                // Fetch employee name from server
                $.ajax({
                    url: '/api/employee/' + employeeId,
                    method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json'
                        },
                        success: function (response) {
                        console.log('Employee API response:', response);
                        if (response.success) {
                            displayEmployeeOptions(employeeId, response.employee.full_name);
                        } else {
                            console.error('Employee not found in API response');
                            displayEmployeeOptions(employeeId, 'Employee #' + employeeId);
                        }
                    },
                        error: function (xhr, status, error) {
                        console.error('Employee API error:', { xhr, status, error });
                        displayEmployeeOptions(employeeId, 'Employee #' + employeeId);
                    }
                });
            } else {
                console.log('Using provided employee name:', employeeName);
                displayEmployeeOptions(employeeId, employeeName);
            }
        }

        function displayEmployeeOptions(employeeId, employeeName) {
            $('#detected-employee').text(employeeName);
            $('#detected-employee-id').val(employeeId);
                
                // Check employee's attendance status for today
                checkAttendanceStatus(employeeId);
            }

            function checkAttendanceStatus(employeeId) {
                const date = '{{ $date ?? now()->format("Y-m-d") }}';
                
                $.ajax({
                    url: '/api/employee/' + employeeId + '/attendance-status',
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    data: {
                        date: date
                    },
                    success: function (response) {
                        if (response.success) {
                            updateAttendanceButtons(response.attendance_status);
                        } else {
                            // Default to showing both buttons if API fails
                            showDefaultButtons();
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Attendance status API error:', { xhr, status, error });
                        // Default to showing both buttons if API fails
                        showDefaultButtons();
                    }
                });
            }

            function updateAttendanceButtons(status) {
                // Disable all manual buttons first, enable based on status
                $('#btnTimeIn, #btnLunchOut, #btnLunchIn, #btnTimeOut').prop('disabled', true).addClass('disabled');
                
                // Enable buttons based on what actions are allowed
                if (status.can_time_in) {
                    $('#btnTimeIn').prop('disabled', false).removeClass('disabled');
                }
                if (status.can_lunch_out) {
                    $('#btnLunchOut').prop('disabled', false).removeClass('disabled');
                }
                if (status.can_lunch_in) {
                    $('#btnLunchIn').prop('disabled', false).removeClass('disabled');
                }
                if (status.can_time_out) {
                    $('#btnTimeOut').prop('disabled', false).removeClass('disabled');
                }
                
                // Update the auto-action button based on next expected action
                const autoActionLabels = {
                    'time_in': '<i class="fas fa-sign-in-alt mr-1"></i> Time In',
                    'lunch_out': '<i class="fas fa-utensils mr-1"></i> Lunch Out',
                    'lunch_in': '<i class="fas fa-utensils mr-1"></i> Lunch In',
                    'time_out': '<i class="fas fa-sign-out-alt mr-1"></i> Time Out',
                    'complete': '<i class="fas fa-check mr-1"></i> Attendance Complete'
                };
                
                const nextAction = status.next_action || 'time_in';
                $('#btnAutoAction').html(autoActionLabels[nextAction] || autoActionLabels['time_in']);
                $('#auto-action-label').text(status.next_action_label || 'Time In');
                
                // Store the next action for the auto button
                $('#btnAutoAction').data('action', nextAction);
                
                // Disable auto button if attendance is complete
                if (nextAction === 'complete' || status.is_completed) {
                    $('#btnAutoAction').prop('disabled', true).removeClass('btn-success').addClass('btn-secondary');
                    $('#auto-action-hint').text('All attendance recorded for today');
                } else {
                    $('#btnAutoAction').prop('disabled', false).removeClass('btn-secondary').addClass('btn-success');
                    $('#auto-action-hint').text('Recommended action based on current time');
                }
                
                // Update status display text
                let statusText = '';
                if (status.has_time_in) {
                    statusText = 'Time In: ' + formatTime(status.time_in);
                }
                if (status.has_lunch_out) {
                    statusText += ' | Lunch Out: ' + formatTime(status.lunch_out);
                }
                if (status.has_lunch_in) {
                    statusText += ' | Lunch In: ' + formatTime(status.lunch_in);
                }
                if (status.has_time_out) {
                    statusText += ' | Time Out: ' + formatTime(status.time_out);
                }
                if (!statusText) {
                    statusText = 'Ready to clock in for today';
                }
                
                $('#status-text').text(statusText);
                $('#attendance-status-display').show();
                
                // Show the container
                $('.qr-detected-container').show();
            }
            
            function formatTime(timeString) {
                if (!timeString) return 'N/A';
                // Handle both ISO datetime strings and time-only strings
                try {
                    const date = new Date(timeString);
                    if (!isNaN(date.getTime())) {
                        return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
                    }
                    return timeString.substring(0, 5);
                } catch (e) {
                    return timeString.substring(0, 5);
                }
            }

            function showDefaultButtons() {
                // Show all buttons and enable time_in as default
                $('#btnTimeIn').prop('disabled', false).removeClass('disabled');
                $('#btnLunchOut, #btnLunchIn, #btnTimeOut').prop('disabled', true).addClass('disabled');
                $('#btnAutoAction').html('<i class="fas fa-sign-in-alt mr-1"></i> Time In');
                $('#btnAutoAction').data('action', 'time_in');
                $('#btnAutoAction').prop('disabled', false).removeClass('btn-secondary').addClass('btn-success');
                $('#auto-action-hint').text('Recommended action based on current time');
                $('#attendance-status-display').hide();
            $('.qr-detected-container').show();
        }

        // Auto Action Button (recommended action based on time)
            $(document).on('click', '#btnAutoAction', function () {
            const employeeId = $('#detected-employee-id').val();
            if (!employeeId) {
                alert('No employee detected');
                return;
            }
            
            const action = $(this).data('action');
            if (action && action !== 'complete') {
                markAttendance(employeeId, action);
            }
        });

        // Manual Time In/Out Buttons (4 actions)
            $(document).on('click', '#btnTimeIn', function () {
            const employeeId = $('#detected-employee-id').val();
            if (!employeeId) {
                alert('No employee detected');
                return;
            }
            markAttendance(employeeId, 'time_in');
        });

            $(document).on('click', '#btnLunchOut', function () {
            const employeeId = $('#detected-employee-id').val();
            if (!employeeId) {
                alert('No employee detected');
                return;
            }
            markAttendance(employeeId, 'lunch_out');
        });

            $(document).on('click', '#btnLunchIn', function () {
            const employeeId = $('#detected-employee-id').val();
            if (!employeeId) {
                alert('No employee detected');
                return;
            }
            markAttendance(employeeId, 'lunch_in');
        });

            $(document).on('click', '#btnTimeOut', function () {
            const employeeId = $('#detected-employee-id').val();
            if (!employeeId) {
                alert('No employee detected');
                return;
            }
            markAttendance(employeeId, 'time_out');
        });

            // Scan Again Button
            $(document).on('click', '#btnScanAgain', function () {
                $('.qr-detected-container').hide();
                $('.scanner-con').show();
                // Reset button states
                resetAttendanceButtons();
                // Restart scanner
                if (typeof Instascan !== 'undefined') {
                    startQRScanner();
                }
            });

            function resetAttendanceButtons() {
                // Reset buttons to default state
                $('#btnTimeIn').prop('disabled', false).removeClass('disabled');
                $('#btnLunchOut, #btnLunchIn, #btnTimeOut').prop('disabled', true).addClass('disabled');
                $('#btnAutoAction').html('<i class="fas fa-sign-in-alt mr-1"></i> Time In');
                $('#btnAutoAction').data('action', 'time_in');
                $('#btnAutoAction').prop('disabled', false).removeClass('btn-secondary').addClass('btn-success');
                $('#auto-action-hint').text('Recommended action based on current time');
                $('#attendance-status-display').hide();
            }


        // Mark Attendance Form
            $('#markAttendanceForm').on('submit', function (e) {
            e.preventDefault();
            
            const employeeId = $('#employee_select').val();
            const action = $('#attendance_type').val();
            
            if (!employeeId || !action) {
                alert('Please select employee and attendance type');
                return;
            }
            
                markAttendanceWithoutRemarks(employeeId, action);
            });

            // Clear modal form when modal is closed
            $('#markAttendanceModal').on('hidden.bs.modal', function () {
                $('#markAttendanceForm')[0].reset();
            });


        function markAttendance(employeeId, action) {
                markAttendanceWithoutRemarks(employeeId, action);
            }

            function markAttendanceWithoutRemarks(employeeId, action) {
                // Show loading state on all buttons
                const buttonMap = {
                    'time_in': '#btnTimeIn',
                    'lunch_out': '#btnLunchOut',
                    'lunch_in': '#btnLunchIn',
                    'time_out': '#btnTimeOut'
                };
                const button = $(buttonMap[action] || '#btnAutoAction');
                const originalText = button.html();
                button.html('<i class="fas fa-spinner fa-spin mr-1"></i> Processing...').prop('disabled', true);
                $('#btnAutoAction').html('<i class="fas fa-spinner fa-spin mr-1"></i> Processing...').prop('disabled', true);

                // Create a form and submit it (matching your native PHP approach)
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("attendance.mark") }}';

                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = $('meta[name="csrf-token"]').attr('content');
                form.appendChild(csrfToken);

                // Add employee ID
                const employeeIdInput = document.createElement('input');
                employeeIdInput.type = 'hidden';
                employeeIdInput.name = 'employee_id';
                employeeIdInput.value = employeeId;
                form.appendChild(employeeIdInput);

                // Add attendance date
                const dateInput = document.createElement('input');
                dateInput.type = 'hidden';
                dateInput.name = 'attendance_date';
                dateInput.value = '{{ $date ?? now()->format("Y-m-d") }}';
                form.appendChild(dateInput);

                // Add action
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = action;
                form.appendChild(actionInput);

                // Append form to body and submit (this will cause page reload like native PHP)
                document.body.appendChild(form);
                form.submit();
        }

        // Edit attendance
            $(document).on('click', '.edit-attendance', function () {
            const attendanceId = $(this).data('id');
            // You can implement a modal for editing attendance
            alert('Edit functionality for attendance ID: ' + attendanceId);
        });

        // Delete attendance
            $(document).on('click', '.delete-attendance', function () {
            const attendanceId = $(this).data('id');
            if (confirm('Are you sure you want to delete this attendance record?')) {
                $.ajax({
                    url: '/attendance/' + attendanceId,
                    method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json'
                        },
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                        success: function (response) {
                        if (response.success) {
                            alert('Attendance record deleted successfully!');
                            location.reload();
                        }
                    },
                        error: function (xhr) {
                        alert('Error deleting attendance record');
                    }
                });
            }
        });

        // View history for absent employees
            $(document).on('click', '.view-history', function () {
            const employeeId = $(this).data('employee-id');
            // You can implement a modal to show attendance history
            alert('View history for employee ID: ' + employeeId);
        });
    });
</script>
@endpush
@extends('layouts.app')

@section('title', 'Employee Attendance')
@section('page-title', 'Employee Attendance')

@push('styles')
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
<style>
    .info-box {
        cursor: pointer;
        transition: transform 0.2s ease-in-out;
    }
    .info-box:hover {
        transform: translateY(-5px);
    }
    .dataTables_filter {
        margin-bottom: 1rem;
    }
    .dataTables_length {
        margin-bottom: 1rem;
    }
    .dataTables_info {
        margin-top: 1rem;
    }
    .dataTables_paginate {
        margin-top: 1rem;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .table td, .table th {
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
</style>
@endpush

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Date and Filter Section -->
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filter Attendance</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('attendance.index') }}" method="GET" id="attendanceFilterForm">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="filter_date">Date:</label>
                            <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                <input type="text" name="date" class="form-control datetimepicker-input" 
                                       data-target="#reservationdate" value="{{ date('Y-m-d') }}" />
                                <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status_filter">Status:</label>
                            <select name="status" id="status_filter" class="form-control">
                                <option value="all">All</option>
                                <option value="Present">Present</option>
                                <option value="Late">Late</option>
                                <option value="Absent">Absent</option>
                                <option value="Half Day">Half Day</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="position_filter">Department/Position:</label>
                            <select name="position" id="position_filter" class="form-control">
                                <option value="all">All</option>
                                <option value="1">Foreman</option>
                                <option value="2">Engineer</option>
                                <option value="3">Laborer</option>
                                <option value="4">Supervisor</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                        <a href="{{ route('attendance.index') }}" class="btn btn-secondary"><i class="fas fa-sync-alt"></i> Clear Filters</a>
                        <a href="#" class="btn btn-success"><i class="fas fa-file-csv"></i> Export CSV</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Dashboard Section -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info info-box">
                <div class="inner">
                    <h3>7</h3>
                    <p>Total Employees</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-stalker"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success info-box">
                <div class="inner">
                    <h3>5<sup style="font-size: 20px">%</sup></h3>
                    <p>Present (71.4%)</p>
                </div>
                <div class="icon">
                    <i class="ion ion-checkmark-round"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning info-box">
                <div class="inner">
                    <h3>1<sup style="font-size: 20px">%</sup></h3>
                    <p>Late (14.3%)</p>
                </div>
                <div class="icon">
                    <i class="ion ion-alert-circled"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger info-box">
                <div class="inner">
                    <h3>1<sup style="font-size: 20px">%</sup></h3>
                    <p>Absent (14.3%)</p>
                </div>
                <div class="icon">
                    <i class="ion ion-close-circled"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row">
        <!-- QR Scanner Section -->
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-qrcode mr-1"></i> QR Code Scanner</h3>
                </div>
                <div class="card-body">
                    <div class="scanner-container">
                        <div class="scanner-placeholder">
                            <i class="fas fa-camera"></i>
                        </div>
                        <h5 class="text-center mb-3">Scan QR Code for Attendance</h5>
                        <p class="text-muted text-center mb-3">Camera will be activated when you click the button below</p>
                        <button type="button" id="btnCameraToggle" class="btn btn-success btn-block">
                            <i class="fas fa-camera mr-1"></i> Turn Camera On
                        </button>
                    </div>

                    <div class="qr-detected-container mt-3" style="display: none;">
                        <div class="alert alert-info text-center">
                            <h4><i class="fas fa-check-circle mr-2"></i>Employee QR Detected!</h4>
                            <p class="mb-3">Employee: <strong id="detected-employee">John Doe</strong></p>
                            <div class="row">
                                <div class="col-6">
                                    <button type="button" id="btnTimeIn" class="btn btn-dark btn-block">
                                        <i class="fas fa-sign-in-alt mr-1"></i> Time In
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" id="btnTimeOut" class="btn btn-dark btn-block">
                                        <i class="fas fa-sign-out-alt mr-1"></i> Time Out
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
                    <h3 class="card-title"><i class="fas fa-clipboard-list mr-1"></i> Daily Attendance Records for {{ date('M d, Y') }}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#markAttendanceModal">
                            <i class="fas fa-plus"></i> Mark Attendance
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="attendanceTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Position/Department</th>
                                    <th>Time In</th>
                                    <th>Time Out</th>
                                    <th>Status</th>
                                    <th>Working Hours</th>
                                    <th>Remarks</th>
                                    <th style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dummy Data -->
                                <tr>
                                    <td>John Doe</td>
                                    <td>Foreman</td>
                                    <td>08:00 AM</td>
                                    <td>05:00 PM</td>
                                    <td><span class="badge badge-success">Present</span></td>
                                    <td>9h 0m</td>
                                    <td>On time</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Jane Smith</td>
                                    <td>Engineer</td>
                                    <td>08:15 AM</td>
                                    <td>05:15 PM</td>
                                    <td><span class="badge badge-warning">Late</span></td>
                                    <td>9h 0m</td>
                                    <td>Traffic delay</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Mike Johnson</td>
                                    <td>Laborer</td>
                                    <td>08:00 AM</td>
                                    <td>05:00 PM</td>
                                    <td><span class="badge badge-success">Present</span></td>
                                    <td>9h 0m</td>
                                    <td>-</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Sarah Wilson</td>
                                    <td>Supervisor</td>
                                    <td>08:00 AM</td>
                                    <td>05:00 PM</td>
                                    <td><span class="badge badge-success">Present</span></td>
                                    <td>9h 0m</td>
                                    <td>-</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>David Brown</td>
                                    <td>Engineer</td>
                                    <td>08:00 AM</td>
                                    <td>05:00 PM</td>
                                    <td><span class="badge badge-success">Present</span></td>
                                    <td>9h 0m</td>
                                    <td>-</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Lisa Garcia</td>
                                    <td>Laborer</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td><span class="badge badge-danger">Absent</span></td>
                                    <td>0h 0m</td>
                                    <td>Sick leave</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tom Anderson</td>
                                    <td>Foreman</td>
                                    <td>08:00 AM</td>
                                    <td>05:00 PM</td>
                                    <td><span class="badge badge-success">Present</span></td>
                                    <td>9h 0m</td>
                                    <td>-</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
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
            <h3 class="card-title"><i class="fas fa-user-slash mr-1"></i> Absent Employees for {{ date('M d, Y') }}</h3>
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
                        <tr>
                            <td>Lisa Garcia</td>
                            <td>Laborer</td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm">
                                    <i class="fas fa-history"></i> View History
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div><!-- /.container-fluid -->

<!-- Mark Attendance Modal -->
<div class="modal fade" id="markAttendanceModal" tabindex="-1" role="dialog" aria-labelledby="markAttendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="markAttendanceModalLabel"><i class="fas fa-clock mr-1"></i> Mark Employee Attendance</h5>
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
                            <option value="1">John Doe (Foreman)</option>
                            <option value="2">Jane Smith (Engineer)</option>
                            <option value="3">Mike Johnson (Laborer)</option>
                            <option value="4">Sarah Wilson (Supervisor)</option>
                            <option value="5">David Brown (Engineer)</option>
                            <option value="6">Lisa Garcia (Laborer)</option>
                            <option value="7">Tom Anderson (Foreman)</option>
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Attendance</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- DataTables  & Plugins -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

<!-- InputMask -->
<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>

<script>
    $(function () {
        // Initialize DatePicker
        $('#reservationdate').datetimepicker({
            format: 'YYYY-MM-DD'
        });

        // Initialize DataTables
        $("#attendanceTable").DataTable({
            "responsive": true, "lengthChange": false, "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#attendanceTable_wrapper .col-md-6:eq(0)');

        $("#absentEmployeesTable").DataTable({
            "responsive": true, "lengthChange": false, "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#absentEmployeesTable_wrapper .col-md-6:eq(0)');

        // Camera Toggle Button
        $("#btnCameraToggle").click(function() {
            if ($(this).text().includes('On')) {
                $(this).html('<i class="fas fa-camera mr-1"></i> Turn Camera Off').removeClass('btn-success').addClass('btn-danger');
                $('.scanner-container').html(`
                    <div class="scanner-placeholder">
                        <i class="fas fa-video"></i>
                    </div>
                    <h5 class="text-center mb-3">Camera Active</h5>
                    <p class="text-muted text-center mb-3">Scanning for QR codes...</p>
                    <button type="button" id="btnCameraToggle" class="btn btn-danger btn-block">
                        <i class="fas fa-stop mr-1"></i> Turn Camera Off
                    </button>
                `);
                // Simulate QR detection after 3 seconds
                setTimeout(function() {
                    $('.qr-detected-container').show();
                }, 3000);
            } else {
                $(this).html('<i class="fas fa-camera mr-1"></i> Turn Camera On').removeClass('btn-danger').addClass('btn-success');
                $('.scanner-container').html(`
                    <div class="scanner-placeholder">
                        <i class="fas fa-camera"></i>
                    </div>
                    <h5 class="text-center mb-3">Scan QR Code for Attendance</h5>
                    <p class="text-muted text-center mb-3">Camera will be activated when you click the button below</p>
                    <button type="button" id="btnCameraToggle" class="btn btn-success btn-block">
                        <i class="fas fa-camera mr-1"></i> Turn Camera On
                    </button>
                `);
                $('.qr-detected-container').hide();
            }
        });

        // Time In/Out Buttons
        $(document).on('click', '#btnTimeIn', function() {
            alert('Time In recorded successfully!');
            $('.qr-detected-container').hide();
        });

        $(document).on('click', '#btnTimeOut', function() {
            alert('Time Out recorded successfully!');
            $('.qr-detected-container').hide();
        });

        // Mark Attendance Form
        $('#markAttendanceForm').on('submit', function(e) {
            e.preventDefault();
            alert('Attendance marked successfully!');
            $('#markAttendanceModal').modal('hide');
        });
    });
</script>
@endpush
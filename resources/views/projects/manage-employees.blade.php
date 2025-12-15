@extends('layouts.app')

@section('title', 'Project Employee Management')
@section('page-title', 'Project Employee Management')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header" style="position: relative;">
                        <h3 class="card-title mb-0" style="display: inline-block;">
                            Project Employees for {{ $project->ProjectName }}
                            <small class="text-muted">({{ $project->status->StatusName }})</small>
                        </h3>
                        <div style="position: absolute; top: 50%; right: 20px; transform: translateY(-50%);">
                            <a href="{{ route('projects.employees.qr-pdf', $project) }}" class="btn btn-info me-2"
                                target="_blank"
                                style="background-color: #17a2b8 !important; border: 2px solid #17a2b8 !important; color: white !important;">
                                <i class="fas fa-qrcode"></i> Print All QR Codes
                            </a>
                            <button type="button" class="btn btn-primary me-2" data-toggle="modal"
                                data-target="#assignEmployeeModal"
                                style="background-color: #52b788 !important; border: 2px solid #52b788 !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                                <i class="fas fa-plus"></i> Assign Employees
                            </button>
                            <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary"
                                style="background-color: #6c757d !important; border: 2px solid #6c757d !important; color: white !important; opacity: 1 !important; visibility: visible !important;">
                                <i class="fas fa-arrow-left"></i> Back to Project
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle mr-2"></i>
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif


                        <!-- Current Project Employees -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3">Current Project Employees</h5>
                                @if($project->projectEmployees->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Employee</th>
                                                    <th>Position</th>
                                                    <th>Employee Type</th>
                                                    <th>Assigned Date</th>
                                                    <th>End Date</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($project->projectEmployees as $assignment)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                @if($assignment->employee->image_name)
                                                                    <img src="{{ asset('storage/' . $assignment->employee->image_name) }}"
                                                                        alt="{{ $assignment->employee->full_name }}"
                                                                        class="rounded-circle mr-2"
                                                                        style="width: 40px; height: 40px; object-fit: cover;">
                                                                @else
                                                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mr-2"
                                                                        style="width: 40px; height: 40px;">
                                                                        <i class="fas fa-user text-white"></i>
                                                                    </div>
                                                                @endif
                                                                <div>
                                                                    <strong>{{ $assignment->employee->full_name }}</strong>
                                                                    <br>
                                                                    <small class="text-muted">ID:
                                                                        {{ $assignment->employee->id }}</small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-primary">
                                                                {{ $assignment->employee->position }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="badge badge-{{ $assignment->employee->status == 'Active' ? 'success' : 'danger' }}">
                                                                {{ $assignment->employee->status }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $assignment->assigned_date ? $assignment->assigned_date->format('M d, Y') : 'Not set' }}
                                                        </td>
                                                        <td>
                                                            @if($assignment->end_date)
                                                                <span class="text-success">
                                                                    <i class="fas fa-check-circle"></i>
                                                                    {{ $assignment->end_date ? $assignment->end_date->format('M d, Y') : 'N/A' }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted">Ongoing</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($assignment->status == 'Active')
                                                                <span class="badge badge-success">Active</span>
                                                            @else
                                                                <span class="badge badge-secondary">Completed</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($assignment->status == 'Active' && !$assignment->end_date)
                                                                <div class="btn-group" role="group">
                                                                    <button type="button" class="btn btn-sm btn-info"
                                                                        data-toggle="modal" data-target="#qrCodeModal"
                                                                        data-employee-name="{{ $assignment->employee->full_name }}"
                                                                        data-qr-code="{{ $assignment->qr_code }}"
                                                                        data-project-name="{{ $project->ProjectName }}"
                                                                        style="background-color: #17a2b8 !important; border: 2px solid #17a2b8 !important; color: white !important; opacity: 1 !important; visibility: visible !important;">
                                                                        <i class="fas fa-qrcode"></i> QR Code
                                                                    </button>
                                                                    <form
                                                                        action="{{ route('projects.assignments.complete', [$project, $assignment]) }}"
                                                                        method="POST" style="display: inline;">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="submit" class="btn btn-sm btn-success"
                                                                            onclick="return confirm('Mark this employee\'s job as completed?')"
                                                                            style="background-color: #28a745 !important; border: 2px solid #28a745 !important; color: white !important; opacity: 1 !important; visibility: visible !important;">
                                                                            <i class="fas fa-check"></i> Complete
                                                                        </button>
                                                                    </form>
                                                                    <form
                                                                        action="{{ route('projects.assignments.remove', [$project, $assignment]) }}"
                                                                        method="POST" style="display: inline;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                                            onclick="return confirm('Are you sure you want to remove this employee from the project?')"
                                                                            style="background-color: #dc3545 !important; border: 2px solid #dc3545 !important; color: white !important; opacity: 1 !important; visibility: visible !important;">
                                                                            <i class="fas fa-trash"></i> Remove
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            @elseif($assignment->end_date)
                                                                <div class="btn-group" role="group">
                                                                    <button type="button" class="btn btn-sm btn-info"
                                                                        data-toggle="modal" data-target="#qrCodeModal"
                                                                        data-employee-name="{{ $assignment->employee->full_name }}"
                                                                        data-qr-code="{{ $assignment->qr_code }}"
                                                                        data-project-name="{{ $project->ProjectName }}"
                                                                        style="background-color: #17a2b8 !important; border: 2px solid #17a2b8 !important; color: white !important; opacity: 1 !important; visibility: visible !important;">
                                                                        <i class="fas fa-qrcode"></i> QR Code
                                                                    </button>
                                                                    <span class="text-muted">
                                                                        <i class="fas fa-check-circle text-success"></i> Job Completed
                                                                    </span>
                                                                </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        No employees assigned to this project.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Employee Modal -->
    <div class="modal fade" id="assignEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="assignEmployeeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="assignEmployeeModalLabel">
                        <i class="fas fa-users mr-2"></i>
                        Assign Employees to {{ $project->ProjectName }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('projects.assign-multiple-employees', $project) }}" method="POST"
                    id="assignEmployeeForm">
                    @csrf
                    <div class="modal-body p-4">
                        <!-- Selection Controls Section -->


                        @if($availableEmployees->count() > 0)
                            <!-- Employee Selection Table -->
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-list text-primary mr-2"></i>
                                        Available Employees ({{ $availableEmployees->count() }})
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover table-bordered mb-0" id="employeeTable">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th width="60" class="text-center align-middle">
                                                        <input type="checkbox" id="selectAllCheckbox" class="form-check-input">
                                                    </th>
                                                    <th class="align-middle">Employee</th>
                                                    <th class="align-middle">Position</th>
                                                    <th class="align-middle">Employee Type</th>
                                                    <th class="align-middle">Contact</th>
                                                    <th class="align-middle text-right">Salary</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($availableEmployees as $employee)
                                                    <tr>
                                                        <td class="text-center align-middle">
                                                            <input type="checkbox" name="employee_ids[]" value="{{ $employee->id }}"
                                                                class="employee-checkbox form-check-input">
                                                        </td>
                                                        <td class="align-middle">
                                                            <div class="d-flex align-items-center">
                                                                @if($employee->image_name)
                                                                    <img src="{{ asset('storage/' . $employee->image_name) }}"
                                                                        alt="{{ $employee->full_name }}" class="rounded-circle mr-3"
                                                                        style="width: 45px; height: 45px; object-fit: cover;">
                                                                @else
                                                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mr-3"
                                                                        style="width: 45px; height: 45px;">
                                                                        <i class="fas fa-user text-white"></i>
                                                                    </div>
                                                                @endif
                                                                <div>
                                                                    <strong class="d-block">{{ $employee->full_name }}</strong>
                                                                    <small class="text-muted">ID: {{ $employee->id }}</small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="align-middle">
                                                            @php
                                                                $position = $employee->relationLoaded('position')
                                                                    ? $employee->getRelation('position')
                                                                    : $employee->position()->first();
                                                            @endphp
                                                            <span class="badge badge-primary badge-pill">
                                                                {{ $position ? $position->PositionName : 'N/A' }}
                                                            </span>
                                                        </td>
                                                        <td class="align-middle">
                                                            <span
                                                                class="badge badge-{{ $employee->status == 'Active' ? 'success' : 'danger' }} badge-pill">
                                                                {{ $employee->status }}
                                                            </span>
                                                        </td>
                                                        <td class="align-middle">
                                                            <small class="text-muted">
                                                                <i class="fas fa-phone mr-1"></i>
                                                                {{ $employee->contact_number ?? 'No contact' }}
                                                            </small>
                                                        </td>
                                                        <td class="align-middle text-right">
                                                            <span class="text-success font-weight-bold">
                                                                ₱{{ number_format($employee->base_salary, 2) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-users fa-4x text-muted"></i>
                                </div>
                                <h4 class="text-muted mb-3">No Available Employees</h4>
                                <p class="text-muted mb-0">All employees are already assigned to this project.</p>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <div class="d-flex justify-content-between w-100">
                            <div class="text-muted">
                                <small id="selectionInfo">Select employees to assign</small>
                            </div>
                            <div>
                                <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal"
                                    style="background-color: #6c757d !important; border: 2px solid #6c757d !important; color: white !important; opacity: 1 !important; visibility: visible !important;">
                                    <i class="fas fa-times mr-1"></i> Cancel
                                </button>
                                <button type="submit" class="btn btn-primary" id="assignBtn" disabled
                                    style="background-color: #52b788 !important; border: 2px solid #52b788 !important; color: white !important; opacity: 1 !important; visibility: visible !important;">
                                    <i class="fas fa-plus mr-1"></i> Assign Selected Employees
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- QR Code Modal -->
    <div class="modal fade" id="qrCodeModal" tabindex="-1" role="dialog" aria-labelledby="qrCodeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="qrCodeModalLabel">
                        <i class="fas fa-qrcode mr-2"></i>
                        Project QR Code
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center p-4">
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Employee</h6>
                        <h5 class="text-primary" id="modalEmployeeName">-</h5>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Project</h6>
                        <h6 class="text-secondary" id="modalProjectName">-</h6>
                    </div>
                    <div class="mb-4">
                        <div class="qr-code-container bg-white p-3 rounded shadow-sm d-inline-block">
                            <img id="qrCodeImage" src="" alt="QR Code" class="img-fluid" style="max-width: 250px;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">QR Code Data</h6>
                        <div class="bg-light p-2 rounded">
                            <code id="qrCodeData" class="text-dark small">-</code>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <small>This QR code is specific to this project. Use it for attendance tracking within this project
                            only.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Close
                    </button>
                    <button type="button" class="btn btn-info" id="downloadQrBtn">
                        <i class="fas fa-download mr-1"></i> Download QR Code
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        /* Custom DataTable Styling */
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            background: none !important;
            border: none !important;
            padding: 0.25rem 0.5rem !important;
            text-decoration: underline !important;
            color: #007bff !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: none !important;
            border: none !important;
            color: #007bff !important;
            text-decoration: underline !important;
            font-weight: bold !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
            background: none !important;
            border: none !important;
            text-decoration: underline !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            background: none !important;
            border: none !important;
            text-decoration: none !important;
            color: #6c757d !important;
            opacity: 0.5 !important;
        }

        /* Modal DataTable specific styling */
        #assignEmployeeModal .dataTables_wrapper {
            padding: 0;
        }

        #assignEmployeeModal .table-responsive {
            border: none;
        }

        /* Checkbox styling */
        .form-check-input {
            width: 1.25rem;
            height: 1.25rem;
            margin-top: 0;
        }

        /* Employee table row hover effect */
        #employeeTable tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.1);
            cursor: pointer;
        }

        /* Badge styling in table */
        #employeeTable .badge {
            font-size: 0.75rem;
            padding: 0.375rem 0.5rem;
        }

        /* Modal alignment improvements */
        .modal-dialog-centered {
            display: flex;
            align-items: center;
            min-height: calc(100% - 1rem);
        }

        .modal-xl {
            max-width: 95%;
        }

        @media (min-width: 1200px) {
            .modal-xl {
                max-width: 1140px;
            }
        }

        /* Modal header styling */
        .modal-header.bg-primary {
            border-bottom: none;
        }

        .modal-header.bg-primary .modal-title {
            font-weight: 600;
        }

        /* Modal body improvements */
        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }

        /* Card styling in modal */
        .modal-body .card {
            border: 1px solid #e3e6f0;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .modal-body .card-header {
            border-bottom: 1px solid #e3e6f0;
            background-color: #f8f9fc;
        }

        /* Table improvements */
        #employeeTable {
            width: 100% !important;
            table-layout: fixed;
        }

        #employeeTable th {
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            vertical-align: middle !important;
            padding: 1rem 0.75rem;
            border-bottom: 2px solid #dee2e6;
        }

        #employeeTable td {
            vertical-align: middle !important;
            padding: 1rem 0.75rem;
            border-top: 1px solid #dee2e6;
        }

        /* DataTable specific alignment fixes */
        #employeeTable thead th {
            text-align: center;
        }

        #employeeTable thead th:first-child {
            text-align: center;
            width: 60px;
        }

        #employeeTable thead th:nth-child(2) {
            text-align: left;
            width: 35%;
        }

        #employeeTable thead th:nth-child(3),
        #employeeTable thead th:nth-child(4) {
            text-align: center;
            width: 15%;
        }

        #employeeTable thead th:nth-child(5) {
            text-align: left;
            width: 20%;
        }

        #employeeTable thead th:nth-child(6) {
            text-align: right;
            width: 15%;
        }

        /* DataTable wrapper alignment */
        .dataTables_wrapper {
            width: 100% !important;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-left: 0 !important;
            margin-right: 0 !important;
            margin-top: 0.5rem !important;
            margin-bottom: 0.25rem !important;
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }

        .dataTables_wrapper .dataTables_length {
            display: flex !important;
            align-items: center !important;
        }

        .dataTables_wrapper .dataTables_length label {
            display: flex !important;
            align-items: center !important;
            margin-bottom: 0 !important;
        }

        .dataTables_wrapper .dataTables_length label::before {
            content: "Show entries" !important;
            margin-right: 0.5rem !important;
            font-weight: normal !important;
        }

        .dataTables_wrapper .dataTables_length label select {
            margin: 0 !important;
        }

        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_processing,
        .dataTables_wrapper .dataTables_paginate {
            margin-bottom: 1rem;
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }

        /* Fix DataTable responsive issues */
        @media screen and (max-width: 767px) {
            #employeeTable {
                font-size: 0.875rem;
            }

            #employeeTable th,
            #employeeTable td {
                padding: 0.5rem 0.25rem;
            }
        }

        /* Form control improvements */
        .form-control-lg {
            font-size: 1rem;
            padding: 0.75rem 1rem;
        }

        /* Button group improvements */
        .btn-group-vertical .btn {
            border-radius: 0.375rem;
            margin-bottom: 0.5rem;
        }

        .btn-group-vertical .btn:last-child {
            margin-bottom: 0;
        }

        /* Footer improvements */
        .modal-footer {
            padding: 1rem 1.5rem;
        }

        .modal-footer .btn {
            padding: 0.5rem 1.5rem;
            font-weight: 500;
        }

        /* QR Code Modal Styling */
        #qrCodeModal .modal-dialog {
            max-width: 500px;
        }

        #qrCodeModal .modal-header {
            border-bottom: none;
            padding: 1.5rem 1.5rem 1rem 1.5rem;
        }

        #qrCodeModal .modal-body {
            padding: 1rem 1.5rem 1.5rem 1.5rem;
        }

        #qrCodeModal .modal-footer {
            border-top: 1px solid #e3e6f0;
            padding: 1rem 1.5rem;
        }

        .qr-code-container {
            border: 2px solid #e3e6f0;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        #qrCodeModal .alert-info {
            border: 1px solid #b8daff;
            background-color: #d1ecf1;
            color: #0c5460;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }

        #qrCodeModal code {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 0.25rem;
            padding: 0.5rem;
            font-size: 0.8rem;
            word-break: break-all;
            display: block;
            white-space: pre-wrap;
        }

        /* QR Code button styling */
        .btn-info {
            background-color: #17a2b8 !important;
            border-color: #17a2b8 !important;
            color: white !important;
        }

        .btn-info:hover {
            background-color: #138496 !important;
            border-color: #117a8b !important;
            color: white !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function () {
            // Initialize DataTable with Bootstrap styling and advanced features
            var table = $('#employeeTable').DataTable({
                "pageLength": 15,
                "lengthMenu": [[10, 15, 25, 50, -1], [10, 15, 25, 50, "All"]],
                "order": [[1, "asc"]],
                "columnDefs": [
                    { "orderable": false, "targets": 0 },
                    { "searchable": false, "targets": 0 },
                    { "className": "text-center", "targets": 0 },
                    { "className": "text-right", "targets": 5 }
                ],
                "language": {
                    "search": "Search:",
                    "lengthMenu": "_MENU_",
                    "info": "Showing _START_ to _END_ of _TOTAL_ employees",
                    "infoEmpty": "No employees available",
                    "infoFiltered": "(filtered from _MAX_ total employees)",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    },
                    "emptyTable": "No employees available for assignment"
                },
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                    '<"row"<"col-sm-12"tr>>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                "responsive": true,
                "stateSave": false,
                "searchHighlight": true,
                "autoWidth": false,
                "scrollX": false,
                "initComplete": function () {
                    // Add custom styling to DataTable elements
                    $('.dataTables_filter input').addClass('form-control form-control-sm');
                    $('.dataTables_length select').addClass('form-control form-control-sm');
                    $('.dataTables_info').addClass('text-muted');
                    $('.dataTables_paginate .paginate_button').addClass('btn btn-sm btn-outline-primary');
                    $('.dataTables_paginate .paginate_button.current').addClass('btn-primary');

                    // Fix table alignment
                    $('#employeeTable').css('width', '100%');
                    $('#employeeTable thead th').css('vertical-align', 'middle');
                    $('#employeeTable tbody td').css('vertical-align', 'middle');
                }
            });

            // Select All functionality
            $('#selectAllCheckbox').change(function () {
                $('.employee-checkbox').prop('checked', this.checked);
                updateAssignButton();
            });

            $('.employee-checkbox').change(function () {
                updateSelectAllCheckbox();
                updateAssignButton();
            });

            function updateSelectAllCheckbox() {
                var totalCheckboxes = $('.employee-checkbox:visible').length;
                var checkedCheckboxes = $('.employee-checkbox:visible:checked').length;
                $('#selectAllCheckbox').prop('checked', totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes);
            }

            function updateAssignButton() {
                var checkedCount = $('.employee-checkbox:checked').length;
                $('#assignBtn').prop('disabled', checkedCount === 0);

                if (checkedCount > 0) {
                    $('#assignBtn').html('<i class="fas fa-plus mr-1"></i> Assign ' + checkedCount + ' Employee(s)');
                    $('#selectionInfo').html('<i class="fas fa-check-circle text-success mr-1"></i>' + checkedCount + ' employee(s) selected');
                } else {
                    $('#assignBtn').html('<i class="fas fa-plus mr-1"></i> Assign Selected Employees');
                    $('#selectionInfo').html('Select employees to assign');
                }
            }

            // Select All / Deselect All buttons
            $('#selectAllBtn').click(function () {
                $('.employee-checkbox:visible').prop('checked', true);
                $('#selectAllCheckbox').prop('checked', true);
                updateAssignButton();
            });

            $('#deselectAllBtn').click(function () {
                $('.employee-checkbox:visible').prop('checked', false);
                $('#selectAllCheckbox').prop('checked', false);
                updateAssignButton();
            });

            // Update checkboxes when DataTable redraws (pagination, search, etc.)
            table.on('draw', function () {
                updateSelectAllCheckbox();
                updateAssignButton();

                // Fix alignment after redraw
                $('#employeeTable').css('width', '100%');
                $('#employeeTable thead th').css('vertical-align', 'middle');
                $('#employeeTable tbody td').css('vertical-align', 'middle');
            });

            // Form submission
            $('#assignEmployeeForm').submit(function (e) {
                var checkedCount = $('.employee-checkbox:checked').length;
                if (checkedCount === 0) {
                    e.preventDefault();
                    alert('Please select at least one employee to assign.');
                    return false;
                }
            });

            // Add custom search functionality
            $('#employeeTable_filter input').on('keyup', function () {
                // Update select all checkbox when search results change
                setTimeout(function () {
                    updateSelectAllCheckbox();
                }, 100);
            });

            // Function to fix table alignment
            function fixTableAlignment() {
                setTimeout(function () {
                    $('#employeeTable').css('width', '100%');
                    $('#employeeTable thead th').css('vertical-align', 'middle');
                    $('#employeeTable tbody td').css('vertical-align', 'middle');
                    table.columns.adjust().responsive.recalc();
                }, 100);
            }

            // Modal events
            $('#assignEmployeeModal').on('shown.bs.modal', function () {
                // Refresh DataTable when modal is shown
                fixTableAlignment();
            });

            $('#assignEmployeeModal').on('hidden.bs.modal', function () {
                // Reset form when modal is hidden
                $('#assignEmployeeForm')[0].reset();
                $('.employee-checkbox').prop('checked', false);
                $('#selectAllCheckbox').prop('checked', false);
                updateAssignButton();
            });

            // QR Code Modal functionality
            $('#qrCodeModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var employeeName = button.data('employee-name');
                var qrCode = button.data('qr-code');
                var projectName = button.data('project-name');

                // Update modal content
                $('#modalEmployeeName').text(employeeName);
                $('#modalProjectName').text(projectName);
                $('#qrCodeData').text(qrCode);

                // Generate QR code image URL
                var qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' + encodeURIComponent(qrCode);
                $('#qrCodeImage').attr('src', qrImageUrl);

                // Update download button
                $('#downloadQrBtn').off('click').on('click', function () {
                    var link = document.createElement('a');
                    link.href = qrImageUrl;
                    link.download = employeeName.replace(/\s+/g, '_') + '_' + projectName.replace(/\s+/g, '_') + '_QR_Code.png';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                });
            });
        });
    </script>
@endpush
@extends('layouts.app')

@section('title', 'Add Milestones - ' . $project->ProjectName)
@section('page-title', 'Add Project Milestones')

@section('content')
    <div class="container-fluid">
        <!-- Progress Summary -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="info-box bg-light">
                    <span class="info-box-icon" style="background: #87A96B; color: white;">
                        <i class="fas fa-tasks"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Milestones Added</span>
                        <span class="info-box-number">{{ $project->milestones->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box bg-light">
                    <span class="info-box-icon" style="background: #87A96B; color: white;">
                        <i class="fas fa-users"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Employees Assigned</span>
                        <span class="info-box-number">{{ $project->projectEmployees->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box bg-light">
                    <span class="info-box-icon" style="background: #87A96B; color: white;">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Est. Days Used</span>
                        <span class="info-box-number">
                            {{ $project->milestones->sum('EstimatedDays') ?? 0 }} / {{ $project->EstimatedAccomplishDays }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

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
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row">
            <!-- Milestones Panel -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header text-white" style="background: #87A96B;">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-flag-checkered mr-2"></i>Project Milestones
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm" data-toggle="modal" data-target="#addMilestoneModal"
                                style="background-color: #87A96B !important; border: 2px solid #87A96B !important; color: white !important;">
                                <i class="fas fa-plus-circle"></i> Add New Milestone
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($project->milestones->count() > 0)
                            <div class="table-responsive">
                                <table id="milestonesTable" class="table table-bordered table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%;">#</th>
                                            <th>Milestone Name</th>
                                            <th class="text-center" style="width: 15%;">Days</th>
                                            <th class="text-center" style="width: 15%;">Weight</th>
                                            <th class="text-center" style="width: 15%;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($project->milestones as $index => $milestone)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>
                                                    <strong>{{ $milestone->milestone_name }}</strong>
                                                    @if($milestone->description)
                                                        <br><small
                                                            class="text-muted">{{ Str::limit($milestone->description, 50) }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $milestone->EstimatedDays ?? 'N/A' }}</td>
                                                <td class="text-center">{{ number_format($milestone->WeightedPercentage, 1) }}%</td>
                                                <td class="text-center">
                                                    <form
                                                        action="{{ route('projects.milestones.destroy', [$project, $milestone]) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Are you sure you want to remove this milestone?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link text-danger p-0"
                                                            style="text-decoration: underline; border: none; background: none;">
                                                            <i class="fas fa-trash mr-1"></i> Remove
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer bg-light">
                                <small class="text-muted">
                                    <strong>Total Weight:</strong>
                                    {{ number_format($project->milestones->sum('WeightedPercentage'), 1) }}% |
                                    <strong>Total Days:</strong> {{ $project->milestones->sum('EstimatedDays') ?? 0 }} /
                                    {{ $project->EstimatedAccomplishDays }}
                                </small>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-3">No milestones added yet.</p>
                                <button type="button" class="btn" data-toggle="modal" data-target="#addMilestoneModal"
                                    style="background-color: #87A96B !important; border: 2px solid #87A96B !important; color: white !important;">
                                    <i class="fas fa-plus mr-1"></i> Add First Milestone
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Employees Panel -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header text-white" style="background: #87A96B;">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-users mr-2"></i>Assigned Employees
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm" data-toggle="modal" data-target="#addEmployeeModal"
                                style="background-color: #87A96B !important; border: 2px solid #87A96B !important; color: white !important;">
                                <i class="fas fa-plus-circle"></i> Add Employee
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($project->projectEmployees->count() > 0)
                            <div class="table-responsive">
                                <table id="employeesTable" class="table table-bordered table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Employee Name</th>
                                            <th>Position</th>
                                            <th class="text-center" style="width: 15%;">Status</th>
                                            <th class="text-center" style="width: 15%;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($project->projectEmployees as $assignment)
                                            <tr>
                                                <td><strong>{{ $assignment->employee->full_name }}</strong></td>
                                                <td>
                                                    @php
                                                        $emp = $assignment->employee;
                                                        $position = $emp->relationLoaded('position') ? $emp->getRelation('position') : null;
                                                        if (!$position) {
                                                            $position = $emp->position()->first();
                                                        }
                                                    @endphp
                                                    {{ ($position && is_object($position)) ? $position->PositionName : 'N/A' }}
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge badge-{{ $assignment->status == 'Active' ? 'success' : 'secondary' }}">
                                                        {{ $assignment->status }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <form
                                                        action="{{ route('projects.assignments.remove', [$project, $assignment]) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Are you sure you want to remove this employee?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link text-danger p-0"
                                                            style="text-decoration: underline; border: none; background: none;">
                                                            <i class="fas fa-trash mr-1"></i> Remove
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer bg-light">
                                <small class="text-muted">
                                    <strong>Total Assigned:</strong> {{ $project->projectEmployees->count() }} employee(s)
                                </small>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-3">No employees assigned yet.</p>
                                <button type="button" class="btn" data-toggle="modal" data-target="#addEmployeeModal"
                                    style="background-color: #87A96B !important; border: 2px solid #87A96B !important; color: white !important;">
                                    <i class="fas fa-plus mr-1"></i> Add First Employee
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> You can skip adding milestones and add them later from
                                the project details page.
                            </small>
                            <div>
                                <a href="{{ route('ProdHead.projects') }}" class="btn btn-secondary mr-2">
                                    <i class="fas fa-times mr-1"></i> Cancel
                                </a>
                                <form action="{{ route('projects.complete', $project) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn"
                                        style="background-color: #87A96B !important; border: 2px solid #87A96B !important; color: white !important;">
                                        <i class="fas fa-check-circle mr-1"></i> Complete Project Creation
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Milestone Modal -->
    <div class="modal fade" id="addMilestoneModal" tabindex="-1" role="dialog" aria-labelledby="addMilestoneModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: #87A96B;">
                    <h5 class="modal-title" id="addMilestoneModalLabel">
                        <i class="fas fa-plus-circle mr-2"></i>Add New Milestone
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('projects.milestones.store', $project) }}" method="POST" id="addMilestoneForm">
                    @csrf
                    <div class="modal-body">
                        <div id="addMilestoneErrorAlert" class="alert alert-danger" style="display: none;">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span id="addMilestoneErrorText"></span>
                        </div>

                        <!-- Milestone Information -->
                        <h6 class="text-primary mb-3"><i class="fas fa-info-circle mr-2"></i>Milestone Information</h6>
                        <div class="form-group">
                            <label for="milestone_name">Milestone Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="milestone_name" name="milestone_name"
                                placeholder="e.g., Foundation Complete" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="2"
                                placeholder="Optional description for this milestone"></textarea>
                        </div>

                        <hr class="my-3">

                        <!-- Timeline & Weight -->
                        <h6 class="text-primary mb-3"><i class="fas fa-calendar-alt mr-2"></i>Timeline & Weight</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="EstimatedDays">Estimated Days <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="EstimatedDays" name="EstimatedDays"
                                        min="1" step="1" placeholder="e.g., 7" required>
                                    <div class="invalid-feedback"></div>
                                    <small class="form-text text-muted">
                                        Days used: <span
                                            id="totalMilestoneDays">{{ $project->milestones->sum('EstimatedDays') ?? 0 }}</span>
                                        / {{ $project->EstimatedAccomplishDays }}
                                        @if($project->StartDate)
                                            <br><span id="calculatedTargetDate" class="text-info"></span>
                                        @endif
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="WeightedPercentage">Weight (%) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="WeightedPercentage"
                                        name="WeightedPercentage" min="0" max="100" step="0.01" placeholder="e.g., 25"
                                        required>
                                    <div class="invalid-feedback"></div>
                                    <small class="form-text text-muted">
                                        Total weight: <span
                                            id="totalWeight">{{ number_format($project->milestones->sum('WeightedPercentage'), 2) }}</span>%
                                    </small>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">

                        <!-- Required Items -->
                        <h6 class="text-primary mb-3"><i class="fas fa-boxes mr-2"></i>Required Items (Optional)</h6>
                        <small class="form-text text-muted mb-3">
                            Specify materials and equipment needed for this milestone. These will be the only items
                            available when foreman requests materials.
                        </small>

                        <div class="row mb-2">
                            <div class="col-md-6">
                                <select class="form-control form-control-sm" id="selectItemForMilestone">
                                    <option value="">Select Item...</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-sm">
                                    <input type="number" class="form-control" id="itemEstimatedQty"
                                        placeholder="Estimated Qty" min="0.01" step="0.01">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="selectedItemUnit">-</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-sm btn-success w-100" id="btnAddItemToMilestone">
                                    <i class="fas fa-plus"></i> Add
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Item</th>
                                        <th>Type</th>
                                        <th width="150">Est. Quantity</th>
                                        <th width="80">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="milestoneItemsList">
                                    <tr class="text-center text-muted">
                                        <td colspan="4">No items added yet</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <input type="hidden" name="required_items" id="requiredItemsData">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn"
                            style="background-color: #87A96B !important; border: 2px solid #87A96B !important; color: white !important;">
                            <i class="fas fa-plus"></i> Add Milestone
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Employee Modal -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="addEmployeeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: #87A96B;">
                    <h5 class="modal-title" id="addEmployeeModalLabel">
                        <i class="fas fa-user-plus mr-2"></i>Add Employee to Project
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('projects.assign-employee', $project) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="EmployeeID">Select Employee <span class="text-danger">*</span></label>
                            <select class="form-control select2-modal" id="EmployeeID" name="EmployeeID" required
                                style="width: 100%;">
                                <option value="">Choose an employee...</option>
                                @foreach($availableEmployees as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->full_name }}
                                        @php
                                            $position = $employee->relationLoaded('position') ? $employee->getRelation('position') : null;
                                            if (!$position) {
                                                $position = $employee->position()->first();
                                            }
                                        @endphp
                                        @if($position && is_object($position))
                                            - {{ $position->PositionName }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if($availableEmployees->count() == 0)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                All active employees are already assigned to this project.
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn"
                            style="background-color: #87A96B !important; border: 2px solid #87A96B !important; color: white !important;"
                            {{ $availableEmployees->count() == 0 ? 'disabled' : '' }}>
                            <i class="fas fa-user-plus"></i> Add Employee
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* Info box styling */
            .info-box {
                border-radius: 8px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }

            /* Card styling */
            .card-header {
                border-radius: 0 !important;
            }

            /* Modal styling */
            #addMilestoneModal .modal-content,
            #addEmployeeModal .modal-content {
                border: none;
                border-radius: 8px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            }

            #addMilestoneModal .modal-header,
            #addEmployeeModal .modal-header {
                border-radius: 8px 8px 0 0;
                border-bottom: none;
            }

            #addMilestoneModal .modal-body h6,
            #addEmployeeModal .modal-body h6 {
                color: #87A96B !important;
                font-weight: 600;
            }

            #addMilestoneModal .modal-body hr {
                border-color: #e9ecef;
            }

            #addMilestoneModal .modal-footer,
            #addEmployeeModal .modal-footer {
                border-top: 1px solid #e9ecef;
            }

            /* Form input styling */
            .form-control {
                border: 1px solid #ced4da;
                border-radius: 4px;
                padding: 0.5rem 0.75rem;
                transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            }

            .form-control:focus {
                border-color: #87A96B;
                box-shadow: 0 0 0 0.2rem rgba(135, 169, 107, 0.25);
            }

            .form-control::placeholder {
                color: #adb5bd;
                font-size: 0.9rem;
            }

            label {
                font-weight: 500;
                color: #495057;
                margin-bottom: 0.3rem;
            }

            /* DataTables Borderline Styling */
            #milestonesTable,
            #employeesTable {
                border: none !important;
            }

            #milestonesTable thead th,
            #employeesTable thead th {
                border: none !important;
                border-bottom: 1px solid #dee2e6 !important;
            }

            #milestonesTable tbody td,
            #employeesTable tbody td {
                border: none !important;
                border-bottom: 1px solid #dee2e6 !important;
                vertical-align: middle;
            }

            #milestonesTable tbody tr:last-child td,
            #employeesTable tbody tr:last-child td {
                border-bottom: none !important;
            }

            /* DataTable wrapper styling */
            #milestonesTable_wrapper,
            #employeesTable_wrapper {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }

            #milestonesTable_wrapper .dataTables_length,
            #milestonesTable_wrapper .dataTables_filter,
            #employeesTable_wrapper .dataTables_length,
            #employeesTable_wrapper .dataTables_filter {
                margin: 0.5rem 1rem !important;
                padding: 0 !important;
            }

            #milestonesTable_wrapper .dataTables_length,
            #employeesTable_wrapper .dataTables_length {
                display: flex !important;
                align-items: center !important;
            }

            #milestonesTable_wrapper .dataTables_length label,
            #employeesTable_wrapper .dataTables_length label {
                display: flex !important;
                align-items: center !important;
                margin-bottom: 0 !important;
            }

            #milestonesTable_wrapper .dataTables_length label::before,
            #employeesTable_wrapper .dataTables_length label::before {
                content: "Show entries" !important;
                margin-right: 0.5rem !important;
                font-weight: normal !important;
            }

            #milestonesTable_wrapper .dataTables_length label select,
            #employeesTable_wrapper .dataTables_length label select {
                margin: 0 !important;
            }

            #milestonesTable_wrapper .dataTables_info,
            #milestonesTable_wrapper .dataTables_paginate,
            #employeesTable_wrapper .dataTables_info,
            #employeesTable_wrapper .dataTables_paginate {
                margin: 0.5rem 1rem !important;
                padding: 0 !important;
            }

            #milestonesTable_wrapper .dataTables_paginate .paginate_button,
            #employeesTable_wrapper .dataTables_paginate .paginate_button {
                background: none !important;
                border: none !important;
                padding: 0.25rem 0.5rem !important;
                text-decoration: underline !important;
                color: #007bff !important;
            }

            #milestonesTable_wrapper .dataTables_paginate .paginate_button.current,
            #employeesTable_wrapper .dataTables_paginate .paginate_button.current {
                background: none !important;
                border: none !important;
                color: #007bff !important;
                text-decoration: underline !important;
                font-weight: bold !important;
            }

            #milestonesTable_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled),
            #employeesTable_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
                background: none !important;
                border: none !important;
                text-decoration: underline !important;
            }

            #milestonesTable_wrapper .dataTables_paginate .paginate_button.disabled,
            #employeesTable_wrapper .dataTables_paginate .paginate_button.disabled {
                background: none !important;
                border: none !important;
                text-decoration: none !important;
                color: #6c757d !important;
                opacity: 0.5 !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            $(document).ready(function () {
                // Initialize Select2 for employee dropdown in modal
                $('#addEmployeeModal').on('shown.bs.modal', function () {
                    $('.select2-modal').select2({
                        theme: 'bootstrap4',
                        placeholder: 'Choose an employee...',
                        allowClear: true,
                        dropdownParent: $('#addEmployeeModal')
                    });
                });

                // Initialize DataTables
                @if($project->milestones->count() > 0)
                    $('#milestonesTable').DataTable({
                        "responsive": true,
                        "lengthChange": false,
                        "searching": false,
                        "paging": false,
                        "info": false,
                        "autoWidth": false,
                        "order": [[0, "asc"]],
                        "columnDefs": [
                            { "orderable": false, "targets": [4] },
                            { "className": "text-center", "targets": [0, 2, 3, 4] }
                        ]
                    });
                @endif

                @if($project->projectEmployees->count() > 0)
                    $('#employeesTable').DataTable({
                        "responsive": true,
                        "lengthChange": false,
                        "searching": false,
                        "paging": false,
                        "info": false,
                        "autoWidth": false,
                        "order": [[0, "asc"]],
                        "columnDefs": [
                            { "orderable": false, "targets": [3] },
                            { "className": "text-center", "targets": [2, 3] }
                        ]
                    });
                @endif

                // Required Items Management
                let milestoneRequiredItems = [];

                // Load all inventory items
                fetch('/api/resource-catalog/items')
                    .then(response => response.json())
                    .then(data => {
                        const select = $('#selectItemForMilestone');
                        data.forEach(item => {
                            select.append(`<option value="${item.ResourceCatalogID}" data-name="${item.ItemName}" data-type="${item.Type}" data-unit="${item.Unit}">${item.ItemName} (${item.Type})</option>`);
                        });

                        // Initialize Select2 after options are loaded
                        select.select2({
                            theme: 'bootstrap4',
                            placeholder: 'Search for an item...',
                            allowClear: true,
                            width: '100%',
                            dropdownParent: $('#addMilestoneModal')
                        });

                        // Update unit when item is selected
                        select.on('change', function () {
                            const selectedOption = $(this).find(':selected');
                            const unit = selectedOption.data('unit') || '-';
                            $('#selectedItemUnit').text(unit);
                        });
                    });

                // Add item to milestone
                $('#btnAddItemToMilestone').click(function () {
                    const select = $('#selectItemForMilestone');
                    const selectedOption = select.find(':selected');
                    const itemId = select.val();
                    const qty = parseFloat($('#itemEstimatedQty').val());

                    if (!itemId) {
                        alert('Please select an item');
                        return;
                    }

                    if (!qty || qty <= 0) {
                        alert('Please enter a valid quantity');
                        return;
                    }

                    // Check if already added
                    if (milestoneRequiredItems.find(i => i.item_id == itemId)) {
                        alert('Item already added');
                        return;
                    }

                    const itemData = {
                        item_id: itemId,
                        item_name: selectedOption.data('name'),
                        item_type: selectedOption.data('type'),
                        estimated_quantity: qty,
                        unit: selectedOption.data('unit')
                    };

                    milestoneRequiredItems.push(itemData);
                    renderMilestoneItems();

                    // Reset
                    select.val('').trigger('change');
                    $('#itemEstimatedQty').val('');
                    $('#selectedItemUnit').text('-');
                });

                function renderMilestoneItems() {
                    const tbody = $('#milestoneItemsList');
                    tbody.empty();

                    if (milestoneRequiredItems.length === 0) {
                        tbody.append('<tr class="text-center text-muted"><td colspan="4">No items added yet</td></tr>');
                        $('#requiredItemsData').val('');
                        return;
                    }

                    milestoneRequiredItems.forEach((item, index) => {
                        tbody.append(`
                            <tr>
                                <td>${item.item_name}</td>
                                <td><span class="badge badge-info">${item.item_type}</span></td>
                                <td>${item.estimated_quantity} ${item.unit}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeMilestoneItem(${index})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
                    });

                    // Update hidden input
                    $('#requiredItemsData').val(JSON.stringify(milestoneRequiredItems));
                }

                window.removeMilestoneItem = function (index) {
                    milestoneRequiredItems.splice(index, 1);
                    renderMilestoneItems();
                };

                // Calculate target date and weight preview
                const projectEstimatedDays = {{ $project->EstimatedAccomplishDays }};
                let currentTotalDays = {{ $project->milestones->sum('EstimatedDays') ?? 0 }};
                let currentTotalWeight = {{ $project->milestones->sum('WeightedPercentage') ?? 0 }};

                $('#EstimatedDays').on('input', function () {
                    const estimatedDays = parseInt($(this).val()) || 0;
                    const newTotal = currentTotalDays + estimatedDays;

                    $('#totalMilestoneDays').text(newTotal);

                    if (newTotal > projectEstimatedDays) {
                        $('#totalMilestoneDays').addClass('text-danger font-weight-bold');
                        $('#calculatedTargetDate').html('<span class="text-danger">Warning: Total exceeds project days!</span>');
                        $(this).addClass('is-invalid');
                    } else {
                        $('#totalMilestoneDays').removeClass('text-danger font-weight-bold');
                        $(this).removeClass('is-invalid');

                        @if($project->StartDate)
                            const projectStartDate = new Date('{{ $project->StartDate->format('Y-m-d') }}');
                            const cumulativeDays = currentTotalDays + estimatedDays;
                            const targetDate = new Date(projectStartDate);
                            targetDate.setDate(targetDate.getDate() + cumulativeDays);

                            const formattedDate = targetDate.toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric'
                            });
                            $('#calculatedTargetDate').html('Target: <strong>' + formattedDate + '</strong>');
                        @endif
                    }
                });

                $('#WeightedPercentage').on('input', function () {
                    const weight = parseFloat($(this).val()) || 0;
                    const newTotalWeight = currentTotalWeight + weight;

                    $('#totalWeight').text(newTotalWeight.toFixed(2));

                    if (newTotalWeight > 100) {
                        $('#totalWeight').addClass('text-danger font-weight-bold');
                        $(this).addClass('is-invalid');
                    } else {
                        $('#totalWeight').removeClass('text-danger font-weight-bold');
                        $(this).removeClass('is-invalid');
                    }
                });

                // Reset modal form when closed
                $('#addMilestoneModal').on('hidden.bs.modal', function () {
                    $('#addMilestoneForm')[0].reset();
                    $('#addMilestoneErrorAlert').hide();
                    $('.is-invalid').removeClass('is-invalid');
                    $('#totalMilestoneDays').text(currentTotalDays);
                    $('#totalWeight').text(currentTotalWeight.toFixed(2));
                    $('#calculatedTargetDate').html('');
                    milestoneRequiredItems = [];
                    renderMilestoneItems();
                });

                // Handle form submission with validation
                $('#addMilestoneForm').on('submit', function (e) {
                    e.preventDefault();

                    $('#addMilestoneErrorAlert').hide();
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').text('');

                    const estimatedDays = parseInt($('#EstimatedDays').val()) || 0;
                    const weight = parseFloat($('#WeightedPercentage').val()) || 0;
                    const newTotalDays = currentTotalDays + estimatedDays;
                    const newTotalWeight = currentTotalWeight + weight;

                    if (newTotalDays > projectEstimatedDays) {
                        $('#addMilestoneErrorAlert').show();
                        $('#addMilestoneErrorText').text(`Total milestone days (${newTotalDays}) cannot exceed project days (${projectEstimatedDays}).`);
                        $('#EstimatedDays').addClass('is-invalid');
                        return false;
                    }

                    if (newTotalWeight > 100) {
                        $('#addMilestoneErrorAlert').show();
                        $('#addMilestoneErrorText').text(`Total weight (${newTotalWeight.toFixed(2)}%) cannot exceed 100%.`);
                        $('#WeightedPercentage').addClass('is-invalid');
                        return false;
                    }

                    const formData = $(this).serialize();
                    const formAction = $(this).attr('action');

                    $.ajax({
                        url: formAction,
                        type: 'POST',
                        data: formData,
                        success: function (response) {
                            if (response.success) {
                                location.reload();
                            }
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors || {};
                                const message = xhr.responseJSON.message || 'Validation failed.';

                                $('#addMilestoneErrorAlert').show();
                                $('#addMilestoneErrorText').text(message);

                                $.each(errors, function (field, messages) {
                                    const input = $('#' + field);
                                    input.addClass('is-invalid');
                                    input.next('.invalid-feedback').text(messages[0]);
                                });
                            } else {
                                $('#addMilestoneErrorAlert').show();
                                $('#addMilestoneErrorText').text(xhr.responseJSON.message || 'An error occurred.');
                            }
                        }
                    });
                });
            });
        </script>
    @endpush

@endsection
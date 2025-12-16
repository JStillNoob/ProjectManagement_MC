@extends('layouts.app')

@section('title', 'Manage Users')
@section('page-title', 'Manage Users')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">User Accounts</h3>
                        <div class="card-tools d-flex align-items-center">
                            <div class="custom-control custom-checkbox mr-3">
                                <input type="checkbox" class="custom-control-input" id="showDeactivated" {{ $showDeactivated ?? false ? 'checked' : '' }} onchange="toggleDeactivated()">
                                <label class="custom-control-label" for="showDeactivated">Show Deactivated Accounts</label>
                            </div>
                            <button type="button" class="btn btn-sm" data-toggle="modal" data-target="#createUserModal"
                                style="background-color: #87A96B !important; border: 2px solid #87A96B !important; color: white !important;">
                                <i class="fas fa-plus-circle"></i> Add New User
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="usersTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Email</th>
                                        <th class="text-center">User Type</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $user)
                                        <tr class="{{ $user->FlagDeleted ? 'table-secondary' : '' }}">
                                            <td>
                                                @if($user->employee)
                                                    @php
                                                        $position = $user->employee->relationLoaded('position')
                                                            ? $user->employee->getRelation('position')
                                                            : $user->employee->position()->first();
                                                    @endphp
                                                    <div>
                                                        <strong>{{ $user->employee->full_name }}</strong>
                                                        @if($position)
                                                            <br><small class="text-muted">{{ $position->PositionName }}</small>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">No employee linked</span>
                                                @endif
                                            </td>
                                            <td>{{ $user->Email }}</td>
                                            <td>{{ $user->userType->UserType ?? 'N/A' }}</td>
                                            <td style="white-space: nowrap;">
                                                <a href="{{ route('users.show', $user) }}" class="text-info mr-3"
                                                    style="text-decoration: underline; cursor: pointer;">
                                                    <i class="fas fa-eye mr-1"></i> View Details
                                                </a>
                                                @if($user->FlagDeleted)
                                                    <form action="{{ route('users.reactivate', $user) }}" method="POST"
                                                        style="display: inline-block;" class="swal-confirm-form"
                                                        data-title="Reactivate User?"
                                                        data-text="This user will be able to log in again."
                                                        data-icon="question"
                                                        data-confirm-text="Yes, Reactivate">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-link text-success p-0"
                                                            style="text-decoration: underline; border: none; background: none; cursor: pointer;">
                                                            <i class="fas fa-undo mr-1"></i> Reactivate
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                        style="display: inline-block;" class="swal-confirm-form"
                                                        data-title="Deactivate User?"
                                                        data-text="This user will no longer be able to log in. This action can be reversed."
                                                        data-icon="warning"
                                                        data-confirm-text="Yes, Deactivate">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link text-danger p-0"
                                                            style="text-decoration: underline; border: none; background: none; cursor: pointer;">
                                                            <i class="fas fa-ban mr-1"></i> Deactivate
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No users found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create User Modal -->
        <div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header text-white" style="background: #87A96B;">
                        <h5 class="modal-title" id="createUserModalLabel">
                            <i class="fas fa-user-plus mr-2"></i>Create New User Account
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('users.store') }}" method="POST" id="createUserForm">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="EmployeeID">Select Employee <span class="text-danger">*</span></label>
                                        <select class="form-control @error('EmployeeID') is-invalid @enderror"
                                            id="EmployeeID" name="EmployeeID" required>
                                            <option value="">Choose an employee...</option>
                                            @foreach($employees as $employee)
                                                @php
                                                    $position = $employee->relationLoaded('position')
                                                        ? $employee->getRelation('position')
                                                        : $employee->position()->first();
                                                    $positionName = $position ? $position->PositionName : null;
                                                @endphp
                                                <option value="{{ $employee->id }}" {{ old('EmployeeID') == $employee->id ? 'selected' : '' }} data-name="{{ $employee->full_name }}"
                                                    data-position="{{ $positionName }}">
                                                    {{ $employee->full_name }}
                                                    @if($positionName)
                                                        - {{ $positionName }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('EmployeeID')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @if($employees->count() == 0)
                                            <small class="text-muted">No employees available for user account creation.</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Email">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('Email') is-invalid @enderror"
                                            id="Email" name="Email" value="{{ old('Email') }}" required>
                                        @error('Email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Password">Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control @error('Password') is-invalid @enderror"
                                            id="Password" name="Password" required>
                                        @error('Password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="UserTypeID">User Type <span class="text-danger">*</span></label>
                                        <select class="form-control @error('UserTypeID') is-invalid @enderror"
                                            id="UserTypeID" name="UserTypeID" required>
                                            <option value="">Select User Type</option>
                                            @foreach($userTypes as $userType)
                                                <option value="{{ $userType->UserTypeID }}" {{ old('UserTypeID') == $userType->UserTypeID ? 'selected' : '' }}>
                                                    {{ $userType->UserType }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('UserTypeID')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Employee Information Preview -->
                            <div class="alert alert-info" id="employee-info" style="display: none;">
                                <h6><i class="fas fa-user mr-2"></i>Selected Employee Information</h6>
                                <div id="employee-details"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                            <button type="submit" class="btn"
                                style="background-color: #87A96B !important; border: 2px solid #87A96B !important; color: white !important;">
                                <i class="fas fa-save"></i> Create User Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @push('styles')
            <style>
                /* DataTables Borderline Styling - Only horizontal borders between rows */
                #usersTable {
                    border: none !important;
                }

                #usersTable thead th {
                    border: none !important;
                    border-bottom: 1px solid #dee2e6 !important;
                }

                #usersTable tbody td {
                    border: none !important;
                    border-bottom: 1px solid #e9ecef !important;
                }

                #usersTable tbody tr:last-child td {
                    border-bottom: none !important;
                }

                /* Remove margins from DataTable wrapper */
                #usersTable_wrapper {
                    margin-left: 0 !important;
                    margin-right: 0 !important;
                }

                #usersTable_wrapper .dataTables_length,
                #usersTable_wrapper .dataTables_filter {
                    margin-left: 0 !important;
                    margin-right: 0 !important;
                    margin-top: 0.5rem !important;
                    margin-bottom: 0.25rem !important;
                    padding-left: 1rem !important;
                    padding-right: 1rem !important;
                    padding-top: 0 !important;
                    padding-bottom: 0 !important;
                }

                #usersTable_wrapper .dataTables_length {
                    display: flex !important;
                    align-items: center !important;
                }

                #usersTable_wrapper .dataTables_length label {
                    display: flex !important;
                    align-items: center !important;
                    margin-bottom: 0 !important;
                }

                #usersTable_wrapper .dataTables_length label::before {
                    content: "Show entries" !important;
                    margin-right: 0.5rem !important;
                    font-weight: normal !important;
                }

                #usersTable_wrapper .dataTables_length label select {
                    margin: 0 !important;
                }

                #usersTable_wrapper .dataTables_info,
                #usersTable_wrapper .dataTables_paginate {
                    margin-left: 0 !important;
                    margin-right: 0 !important;
                    margin-top: 0.5rem !important;
                    padding-left: 1rem !important;
                    padding-right: 1rem !important;
                    padding-top: 0 !important;
                    padding-bottom: 1rem !important;
                }

                #usersTable_wrapper .dataTables_paginate .paginate_button {
                    background: none !important;
                    border: none !important;
                    padding: 0.25rem 0.5rem !important;
                    text-decoration: underline !important;
                    color: #007bff !important;
                }

                #usersTable_wrapper .dataTables_paginate .paginate_button.current {
                    background: none !important;
                    border: none !important;
                    color: #007bff !important;
                    text-decoration: underline !important;
                    font-weight: bold !important;
                }

                #usersTable_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
                    background: none !important;
                    border: none !important;
                    text-decoration: underline !important;
                }

                #usersTable_wrapper .dataTables_paginate .paginate_button.disabled {
                    background: none !important;
                    border: none !important;
                    text-decoration: none !important;
                    color: #6c757d !important;
                    opacity: 0.5 !important;
                }

                /* Deactivated row styling */
                .table-secondary {
                    opacity: 0.7;
                }

                /* Custom checkbox styling */
                .custom-control-input:checked~.custom-control-label::before {
                    background-color: #87A96B !important;
                    border-color: #87A96B !important;
                }
            </style>
        @endpush

        @push('scripts')
            <script>
                function toggleDeactivated() {
                    var checkbox = document.getElementById('showDeactivated');
                    var url = new URL(window.location.href);

                    if (checkbox.checked) {
                        url.searchParams.set('show_deactivated', '1');
                    } else {
                        url.searchParams.delete('show_deactivated');
                    }

                    window.location.href = url.toString();
                }

                $(document).ready(function () {
                    // Wait a bit to ensure DOM is fully ready
                    setTimeout(function() {
                        // Check if table exists
                        var table = $('#usersTable');
                        if (table.length === 0) {
                            console.warn('Users table not found');
                            return;
                        }

                        // Destroy existing DataTable instance if it exists
                        if ($.fn.DataTable.isDataTable('#usersTable')) {
                            try {
                                table.DataTable().destroy();
                            } catch (e) {
                                console.warn('Error destroying existing DataTable:', e);
                            }
                        }

                        // Verify table structure before initialization
                        var headerCols = table.find('thead tr th').length;
                        if (headerCols === 0) {
                            console.error('No header columns found');
                            return;
                        }

                        var bodyRows = table.find('tbody tr');
                        var validRows = 0;
                        var hasColspanRow = false;

                        bodyRows.each(function() {
                            var $row = $(this);
                            var rowCols = $row.find('td').length;
                            var $colspanCell = $row.find('td[colspan]');
                            
                            if ($colspanCell.length > 0) {
                                hasColspanRow = true;
                                // Ensure colspan matches header column count
                                var colspan = parseInt($colspanCell.attr('colspan')) || 1;
                                if (colspan !== headerCols) {
                                    $colspanCell.attr('colspan', headerCols);
                                }
                            } else if (rowCols === headerCols) {
                                validRows++;
                            } else if (rowCols > 0 && rowCols !== headerCols) {
                                console.warn('Row has incorrect column count: ' + rowCols + ' (expected: ' + headerCols + ')');
                                console.warn('Row HTML:', $row.html());
                            }
                        });

                        // Only initialize if we have valid rows or an empty state with correct colspan
                        if (validRows === 0 && !hasColspanRow) {
                            console.warn('No valid rows found in table. Header cols: ' + headerCols + ', Body rows: ' + bodyRows.length);
                            return;
                        }

                        // Initialize DataTables with error handling
                        try {
                            table.DataTable({
                                "responsive": true,
                                "lengthChange": true,
                                "autoWidth": false,
                                "pageLength": 10,
                                "order": [[0, 'asc']], // Order by Employee name
                                "columnDefs": [
                                    { "orderable": false, "targets": [3] }, // Disable sorting on Actions
                                    { "className": "text-center", "targets": [2, 3] } // Center User Type and Actions columns
                                ],
                                "language": {
                                    "search": "Search:",
                                    "lengthMenu": "_MENU_",
                                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                                    "infoFiltered": "(filtered from _MAX_ total entries)",
                                    "paginate": {
                                        "first": "First",
                                        "last": "Last",
                                        "next": "Next",
                                        "previous": "Previous"
                                    }
                                }
                            });
                        } catch (error) {
                            console.error('Error initializing DataTables:', error);
                            console.error('Table HTML:', table.html());
                        }
                    }, 100); // Small delay to ensure DOM is ready

                    // Employee selection preview in modal
                    $('#EmployeeID').on('change', function () {
                        const selectedOption = $(this).find('option:selected');
                        const employeeName = selectedOption.data('name');
                        const employeePosition = selectedOption.data('position');

                        if (selectedOption.val()) {
                            let details = `<strong>Name:</strong> ${employeeName}<br>`;
                            if (employeePosition) {
                                details += `<strong>Position:</strong> ${employeePosition}`;
                            }
                            $('#employee-details').html(details);
                            $('#employee-info').show();
                        } else {
                            $('#employee-info').hide();
                        }
                    });

                    // Reset modal on close
                    $('#createUserModal').on('hidden.bs.modal', function () {
                        $('#createUserForm')[0].reset();
                        $('#employee-info').hide();
                        $('.is-invalid').removeClass('is-invalid');
                        $('.invalid-feedback').remove();
                    });
                });
            </script>
        @endpush
@endsection
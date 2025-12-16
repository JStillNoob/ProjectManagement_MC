                    @extends('layouts.app')

                    @section('title', 'Employee Management')
                    @section('page-title', 'Employee Management')

                    @section('content')
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-list mr-2"></i>
                                            All Employees Overview
                                        </h3>
                                        <div class="card-tools">
                                            <a href="{{ route('employees.create') }}" class="btn btn-success btn-sm"
                                            style="background-color: #87A96B !important; border: 2px solid #87A96B !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                                                <i class="fas fa-plus"></i> Add Employee
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <!-- Filter Section -->
                                        <div class="row mx-3 my-3">
                                            <div class="col-md-4">
                                                <div class="form-group mb-0">
                                                    <label for="filterPosition" class="small text-muted mb-1">Filter by Position</label>
                                                    <select id="filterPosition" class="form-control form-control-sm">
                                                        <option value="">All Positions</option>
                                                        @foreach(\App\Models\Position::all() as $position)
                                                            <option value="{{ $position->PositionName }}">{{ $position->PositionName }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-0">
                                                    <label for="filterStatus" class="small text-muted mb-1">Filter by Status</label>
                                                    <select id="filterStatus" class="form-control form-control-sm">
                                                        <option value="">All Statuses</option>
                                                        @foreach(\App\Models\EmployeeStatus::all() as $status)
                                                            <option value="{{ $status->StatusName }}">{{ $status->StatusName }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 d-flex align-items-end">
                                                <button type="button" id="clearFilters" class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-times mr-1"></i> Clear Filters
                                                </button>
                                            </div>
                                        </div>

                                        <!-- All Employees Table -->
                                        <div class="table-responsive">
                                            <table id="employeesTable" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Photo</th>
                                                    <th>Name</th>
                                                    <th>Position</th>
                                                    <th>Status</th>
                                                    <th class="text-center">Actions</th>
                                                </tr>
                                            </thead>
                                                <tbody>
                                                    @forelse($employees as $employee)
                                                        <tr>
                                                            <td>
                                                                @if($employee->image_path)
                                                                    <img src="{{ $employee->image_path }}" alt="{{ $employee->full_name }}"
                                                                        class="img-circle elevation-2"
                                                                        style="width: 40px; height: 40px; object-fit: cover;">
                                                                @else
                                                                    <div class="img-circle elevation-2 bg-secondary d-flex align-items-center justify-content-center text-white"
                                                                        style="width: 40px; height: 40px;">
                                                                        <i class="fas fa-user"></i>
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            <td>{{ $employee->full_name }}</td>
                                                            <td>
                                                                @php
                                                                    $position = $employee->relationLoaded('position') 
                                                                        ? $employee->getRelation('position') 
                                                                        : $employee->position()->first();
                                                                @endphp
                                                                {{ $position ? $position->PositionName : 'N/A' }}
                                                            </td>
                                                            <td data-status="{{ strtolower($employee->effective_status) }}">
                                                                @php
                                                                    // Use effective status which considers project assignments
                                                                    $effectiveStatus = $employee->effective_status;
                                                                    $statusColor = $employee->effective_status_color;
                                                                @endphp
                                                                <div class="d-flex align-items-center">
                                                                    <span class="mr-2" style="width: 8px; height: 8px; border-radius: 50%; background-color: {{ $statusColor }};"></span>
                                                                    <span>{{ $effectiveStatus }}</span>
                                                                </div>
                                                            </td>
                                                            <td style="white-space: nowrap;">
                                                                <a href="{{ route('employees.show', $employee) }}" class="text-info mr-3" style="text-decoration: underline; cursor: pointer;">
                                                                    <i class="fas fa-eye mr-1"></i> View Details
                                                                </a>
                                                                @if($employee->employee_status_id == \App\Models\EmployeeStatus::ARCHIVED)
                                                                    <form action="{{ route('employees.unarchive', $employee) }}" method="POST"
                                                                        style="display: inline-block;" class="swal-confirm-form"
                                                                        data-title="Unarchive Employee?"
                                                                        data-text="This employee will be set to inactive status."
                                                                        data-icon="question"
                                                                        data-confirm-text="Yes, Unarchive">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="submit" class="btn btn-link text-success p-0" style="text-decoration: underline; border: none; background: none; cursor: pointer;">
                                                                            <i class="fas fa-undo mr-1"></i> Unarchive
                                                                        </button>
                                                                    </form>
                                                                @else
                                                                    <form action="{{ route('employees.destroy', $employee) }}" method="POST"
                                                                        style="display: inline-block;" class="swal-confirm-form"
                                                                        data-title="Archive Employee?"
                                                                        data-text="Are you sure you want to archive this employee?"
                                                                        data-icon="warning"
                                                                        data-confirm-text="Yes, Archive">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-link text-danger p-0" style="text-decoration: underline; border: none; background: none; cursor: pointer;">
                                                                            <i class="fas fa-archive mr-1"></i> Archive
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center">No employees found.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @push('styles')
                        <style>
                            /* DataTables Borderline Styling - Only horizontal borders between rows */
                            #employeesTable {
                                border: none !important;
                            }

                            #employeesTable thead th {
                                border: none !important;
                                border-bottom: 1px solid #dee2e6 !important;
                            }

                            #employeesTable tbody td {
                                border: none !important;
                                border-bottom: 1px solid #dee2e6 !important;
                            }

                            #employeesTable tbody tr:last-child td {
                                border-bottom: none !important;
                            }

                            /* Center Photo and Actions columns */
                            #employeesTable thead th:first-child,
                            #employeesTable thead th:last-child,
                            #employeesTable tbody td:first-child,
                            #employeesTable tbody td:last-child {
                                text-align: center !important;
                            }

                            /* Center Photo content */
                            #employeesTable tbody td:first-child {
                                vertical-align: middle !important;
                            }

                            #employeesTable tbody td:first-child img,
                            #employeesTable tbody td:first-child div {
                                margin: 0 auto !important;
                            }

                            /* Remove margins from DataTable wrapper */
                            #employeesTable_wrapper {
                                margin-left: 0 !important;
                                margin-right: 0 !important;
                            }

                            #employeesTable_wrapper .dataTables_length,
                            #employeesTable_wrapper .dataTables_filter {
                                margin-left: 0 !important;
                                margin-right: 0 !important;
                                margin-top: 0.5rem !important;
                                margin-bottom: 0.25rem !important;
                                padding-left: 1rem !important;
                                padding-right: 1rem !important;
                                padding-top: 0 !important;
                                padding-bottom: 0 !important;
                            }

                            #employeesTable_wrapper .dataTables_length {
                                display: flex !important;
                                align-items: center !important;
                            }

                            #employeesTable_wrapper .dataTables_length label {
                                display: flex !important;
                                align-items: center !important;
                                margin-bottom: 0 !important;
                            }

                            #employeesTable_wrapper .dataTables_length label::before {
                                content: "Show entries" !important;
                                margin-right: 0.5rem !important;
                                font-weight: normal !important;
                            }

                            #employeesTable_wrapper .dataTables_length label select {
                                margin: 0 !important;
                            }


                            #employeesTable_wrapper .dataTables_info,
                            #employeesTable_wrapper .dataTables_paginate {
                                margin-left: 0 !important;
                                margin-right: 0 !important;
                                margin-top: 0.5rem !important;
                                padding-left: 1rem !important;
                                padding-right: 1rem !important;
                                padding-top: 0 !important;
                                padding-bottom: 1rem !important;
                            }

                            #employeesTable_wrapper .dataTables_paginate .paginate_button {
                                background: none !important;
                                border: none !important;
                                padding: 0.25rem 0.5rem !important;
                                text-decoration: underline !important;
                                color: #007bff !important;
                            }

                            #employeesTable_wrapper .dataTables_paginate .paginate_button.current {
                                background: none !important;
                                border: none !important;
                                color: #007bff !important;
                                text-decoration: underline !important;
                                font-weight: bold !important;
                            }

                            #employeesTable_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
                                background: none !important;
                                border: none !important;
                                text-decoration: underline !important;
                            }

                            #employeesTable_wrapper .dataTables_paginate .paginate_button.disabled {
                                background: none !important;
                                border: none !important;
                                text-decoration: none !important;
                                color: #6c757d !important;
                                opacity: 0.5 !important;
                            }

                            /* Filter section styling */
                            #filterPosition, #filterStatus {
                                border: 1px solid #ced4da;
                                border-radius: 4px;
                            }

                            #filterPosition:focus, #filterStatus:focus {
                                border-color: #87A96B;
                                box-shadow: 0 0 0 0.2rem rgba(135, 169, 107, 0.25);
                            }
                        </style>
                        @endpush

                        @push('scripts')
                        <script>
                            $(document).ready(function() {
                                // Custom filter function for status
                                var statusFilter = function(settings, data, dataIndex) {
                                    var filterStatus = $('#filterStatus').val();
                                    if (!filterStatus) {
                                        return true; // Show all if no filter selected
                                    }
                                    
                                    // Get the status from the data-status attribute
                                    var row = $('#employeesTable').DataTable().row(dataIndex).node();
                                    var rowStatus = $(row).find('td[data-status]').attr('data-status') || '';
                                    
                                    // Normalize both values for comparison
                                    var filterStatusLower = filterStatus.toLowerCase();
                                    
                                    return rowStatus === filterStatusLower;
                                };

                                // Initialize DataTables
                                var table = $('#employeesTable').DataTable({
                                    "responsive": true,
                                    "lengthChange": true,
                                    "autoWidth": false,
                                    "pageLength": 10,
                                    "order": [[1, 'asc']],
                                    "columnDefs": [
                                        { "orderable": false, "targets": [0, 4] }, // Disable sorting on Photo and Actions
                                        { "className": "text-center", "targets": [0, 4] } // Center Photo and Actions columns
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

                                // Add custom status filter
                                $.fn.dataTable.ext.search.push(statusFilter);

                                // Filter by Position
                                $('#filterPosition').on('change', function() {
                                    var val = $(this).val();
                                    table.column(2).search(val ? '^' + val + '$' : '', true, false).draw();
                                });

                                // Filter by Status
                                $('#filterStatus').on('change', function() {
                                    table.draw();
                                });

                                // Clear all filters
                                $('#clearFilters').on('click', function() {
                                    $('#filterPosition').val('');
                                    $('#filterStatus').val('');
                                    table.search('').columns().search('').draw();
                                });
                            });
                        </script>
                        @endpush
                        </div>
                    </div>
                    @endsection

@extends('layouts.app')

@section('title', 'Client Details')
@section('page-title', 'Client Details')

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Client Card -->
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-secondary"
                         style="width: 100px; height: 100px;">
                        <i class="fas fa-building text-white" style="font-size: 2.5rem;"></i>
                    </div>
                </div>

                <h3 class="profile-username text-center mt-3">{{ $client->ClientName }}</h3>
                @php
                    $contactPerson = trim(($client->FirstName ?? '') . ' ' . ($client->LastName ?? ''));
                @endphp
                <p class="text-muted text-center">{{ $contactPerson ?: 'No contact person' }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Total Projects</b> 
                        <span class="float-right">
                            <span class="badge badge-info">{{ $client->projects->count() }}</span>
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>Completed</b> 
                        <span class="float-right">
                            @php
                                $completedProjects = $client->projects->filter(function($p) {
                                    return $p->status && $p->status->StatusName == 'Completed';
                                })->count();
                            @endphp
                            <span class="badge badge-success">{{ $completedProjects }}</span>
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>On Going</b> 
                        <span class="float-right">
                            @php
                                $ongoingProjects = $client->projects->filter(function($p) {
                                    return $p->status && $p->status->StatusName == 'On Going';
                                })->count();
                            @endphp
                            <span class="badge badge-primary">{{ $ongoingProjects }}</span>
                        </span>
                    </li>
                </ul>

                <button type="button" class="btn btn-warning btn-block mb-2" data-toggle="modal" data-target="#editContactModal">
                    <i class="fas fa-edit"></i> Edit Contact Person
                </button>
                <a href="{{ route('clients.index') }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Client Details -->
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link active" href="#details" data-toggle="tab">Client Information</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#projects" data-toggle="tab">Associated Projects</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Client Information Tab -->
                    <div class="active tab-pane" id="details">
                        <div class="row">
                            <div class="col-md-12">
                                <strong><i class="fas fa-building mr-1"></i> Client/Company Name</strong>
                                <p class="text-muted">{{ $client->ClientName }}</p>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-user mr-1"></i> First Name</strong>
                                <p class="text-muted">{{ $client->FirstName ?: 'Not provided' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-user mr-1"></i> Last Name</strong>
                                <p class="text-muted">{{ $client->LastName ?: 'Not provided' }}</p>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-phone mr-1"></i> Contact Number</strong>
                                <p class="text-muted">{{ $client->ContactNumber ?: 'Not provided' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-envelope mr-1"></i> Email Address</strong>
                                <p class="text-muted">{{ $client->Email ?: 'Not provided' }}</p>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-clock mr-1"></i> Date Created</strong>
                                <p class="text-muted">{{ $client->created_at ? $client->created_at->format('M d, Y') : 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-edit mr-1"></i> Last Updated</strong>
                                <p class="text-muted">{{ $client->updated_at ? $client->updated_at->format('M d, Y') : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Associated Projects Tab -->
                    <div class="tab-pane" id="projects">
                        @if($client->projects->count() > 0)
                            <div class="table-responsive">
                                <table id="projectsTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Project Name</th>
                                            <th>Status</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($client->projects as $project)
                                            <tr>
                                                <td>
                                                    <strong>{{ $project->ProjectName }}</strong>
                                                </td>
                                                <td>
                                                    @php
                                                        $badgeClass = 'secondary';
                                                        if ($project->status) {
                                                            if ($project->status->StatusName == 'Completed') {
                                                                $badgeClass = 'success';
                                                            } elseif ($project->status->StatusName == 'On Going') {
                                                                $badgeClass = 'primary';
                                                            } elseif ($project->status->StatusName == 'Pending') {
                                                                $badgeClass = 'warning';
                                                            }
                                                        }
                                                    @endphp
                                                    <span class="badge badge-{{ $badgeClass }}">
                                                        {{ $project->status->StatusName ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td>{{ $project->formatted_start_date ?? 'N/A' }}</td>
                                                <td>{{ $project->formatted_end_date ?? 'N/A' }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('projects.show', $project) }}" class="text-info" style="text-decoration: underline; cursor: pointer;">
                                                        <i class="fas fa-eye mr-1"></i> View Details
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-folder-open text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-3 mb-0">No projects associated with this client yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Contact Person Modal -->
<div class="modal fade" id="editContactModal" tabindex="-1" role="dialog" aria-labelledby="editContactModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background: #87A96B;">
                <h5 class="modal-title" id="editContactModalLabel">
                    <i class="fas fa-user-edit mr-2"></i>Edit Contact Person
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('clients.update', $client) }}" method="POST">
                @csrf
                @method('PUT')
                <!-- Hidden field to preserve ClientName -->
                <input type="hidden" name="ClientName" value="{{ $client->ClientName }}">
                
                <div class="modal-body">
                    <!-- Contact Person Section -->
                    <h6 class="text-primary mb-3"><i class="fas fa-user mr-2"></i>Contact Person Details</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_FirstName">First Name</label>
                                <input type="text" class="form-control @error('FirstName') is-invalid @enderror" 
                                       id="modal_FirstName" name="FirstName" value="{{ old('FirstName', $client->FirstName) }}"
                                       placeholder="Enter first name">
                                @error('FirstName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_LastName">Last Name</label>
                                <input type="text" class="form-control @error('LastName') is-invalid @enderror" 
                                       id="modal_LastName" name="LastName" value="{{ old('LastName', $client->LastName) }}"
                                       placeholder="Enter last name">
                                @error('LastName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <!-- Contact Details Section -->
                    <h6 class="text-primary mb-3"><i class="fas fa-address-book mr-2"></i>Contact Information</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_ContactNumber">Contact Number</label>
                                <input type="text" class="form-control @error('ContactNumber') is-invalid @enderror" 
                                       id="modal_ContactNumber" name="ContactNumber" value="{{ old('ContactNumber', $client->ContactNumber) }}"
                                       placeholder="e.g., 09123456789">
                                @error('ContactNumber')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_Email">Email Address</label>
                                <input type="email" class="form-control @error('Email') is-invalid @enderror" 
                                       id="modal_Email" name="Email" value="{{ old('Email', $client->Email) }}"
                                       placeholder="e.g., contact@email.com">
                                @error('Email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn text-white" style="background: #87A96B; border-color: #87A96B;">
                        <i class="fas fa-save"></i> Update Contact Person
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Modal styling */
    #editContactModal .modal-content {
        border: none;
        border-radius: 8px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }
    
    #editContactModal .modal-header {
        border-radius: 8px 8px 0 0;
        border-bottom: none;
    }
    
    #editContactModal .modal-body {
        padding: 1.5rem;
    }
    
    #editContactModal .modal-body h6 {
        color: #87A96B !important;
        font-weight: 600;
    }
    
    #editContactModal .modal-body hr {
        border-color: #e9ecef;
    }
    
    #editContactModal .modal-footer {
        border-top: 1px solid #e9ecef;
        padding: 1rem 1.5rem;
    }
    
    /* Form input styling */
    #editContactModal .form-control {
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 0.5rem 0.75rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    #editContactModal .form-control:focus {
        border-color: #87A96B;
        box-shadow: 0 0 0 0.2rem rgba(135, 169, 107, 0.25);
    }
    
    #editContactModal .form-control::placeholder {
        color: #adb5bd;
        font-size: 0.9rem;
    }
    
    #editContactModal label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.3rem;
    }

    /* DataTables Borderline Styling */
    #projectsTable {
        border: none !important;
    }

    #projectsTable thead th {
        border: none !important;
        border-bottom: 1px solid #dee2e6 !important;
    }

    #projectsTable tbody td {
        border: none !important;
        border-bottom: 1px solid #dee2e6 !important;
        vertical-align: middle;
    }

    #projectsTable tbody tr:last-child td {
        border-bottom: none !important;
    }

    /* Remove margins from DataTable wrapper */
    #projectsTable_wrapper {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    #projectsTable_wrapper .dataTables_length,
    #projectsTable_wrapper .dataTables_filter {
        margin-left: 0 !important;
        margin-right: 0 !important;
        margin-top: 0.5rem !important;
        margin-bottom: 0.25rem !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    #projectsTable_wrapper .dataTables_length {
        display: flex !important;
        align-items: center !important;
    }

    #projectsTable_wrapper .dataTables_length label {
        display: flex !important;
        align-items: center !important;
        margin-bottom: 0 !important;
    }

    #projectsTable_wrapper .dataTables_length label::before {
        content: "Show entries" !important;
        margin-right: 0.5rem !important;
        font-weight: normal !important;
    }

    #projectsTable_wrapper .dataTables_length label select {
        margin: 0 !important;
    }

    #projectsTable_wrapper .dataTables_info,
    #projectsTable_wrapper .dataTables_paginate {
        margin-left: 0 !important;
        margin-right: 0 !important;
        margin-top: 0.5rem !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        padding-bottom: 0.5rem !important;
    }

    #projectsTable_wrapper .dataTables_paginate .paginate_button {
        background: none !important;
        border: none !important;
        padding: 0.25rem 0.5rem !important;
        text-decoration: underline !important;
        color: #007bff !important;
    }

    #projectsTable_wrapper .dataTables_paginate .paginate_button.current {
        background: none !important;
        border: none !important;
        color: #007bff !important;
        text-decoration: underline !important;
        font-weight: bold !important;
    }

    #projectsTable_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
        background: none !important;
        border: none !important;
        text-decoration: underline !important;
    }

    #projectsTable_wrapper .dataTables_paginate .paginate_button.disabled {
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
    $(document).ready(function() {
        @if($client->projects->count() > 0)
        $('#projectsTable').DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "pageLength": 10,
            "order": [[0, 'asc']],
            "columnDefs": [
                { "orderable": false, "targets": [4] },
                { "className": "text-center", "targets": [4] }
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
        @endif

        // Auto-open modal if there are validation errors
        @if($errors->has('FirstName') || $errors->has('LastName') || $errors->has('ContactNumber') || $errors->has('Email'))
            $('#editContactModal').modal('show');
        @endif

        // Clear form validation errors when modal is closed
        $('#editContactModal').on('hidden.bs.modal', function () {
            $(this).find('.is-invalid').removeClass('is-invalid');
            $(this).find('.invalid-feedback').remove();
        });
    });
</script>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'Clients Management')
@section('page-title', 'Clients Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users mr-2"></i>
                        All Clients Overview
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createClientModal"
                           style="background-color: #87A96B !important; border: 2px solid #87A96B !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                            <i class="fas fa-plus"></i> Add New Client
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="clientsTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Client Name</th>
                                    <th>Contact Person</th>
                                    <th>Contact Number</th>
                                    <th>Email</th>
                                    <th>Projects</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clients as $client)
                                    <tr>
                                        <td>{{ $client->ClientName }}</td>
                                        <td>
                                            @php
                                                $contactPerson = trim(($client->FirstName ?? '') . ' ' . ($client->LastName ?? ''));
                                            @endphp
                                            {{ $contactPerson ?: 'N/A' }}
                                        </td>
                                        <td>{{ $client->ContactNumber ?? 'N/A' }}</td>
                                        <td>{{ $client->Email ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ $client->projects->count() }} projects</span>
                                        </td>
                                        <td style="white-space: nowrap;">
                                            <a href="{{ route('clients.show', $client) }}" class="text-info" style="text-decoration: underline; cursor: pointer;">
                                                <i class="fas fa-eye mr-1"></i> View Details
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No clients found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Client Modal -->
<div class="modal fade" id="createClientModal" tabindex="-1" role="dialog" aria-labelledby="createClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background: #87A96B;">
                <h5 class="modal-title" id="createClientModalLabel">
                    <i class="fas fa-user-plus mr-2"></i>Add New Client
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('clients.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Client Information Section -->
                    <h6 class="text-primary mb-3"><i class="fas fa-building mr-2"></i>Client Information</h6>
                    <div class="form-group">
                        <label for="modal_ClientName">Client/Company Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('ClientName') is-invalid @enderror" 
                               id="modal_ClientName" name="ClientName" value="{{ old('ClientName') }}" 
                               placeholder="Enter client or company name" required>
                        @error('ClientName')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-3">

                    <!-- Contact Person Section -->
                    <h6 class="text-primary mb-3"><i class="fas fa-user mr-2"></i>Contact Person</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_FirstName">First Name</label>
                                <input type="text" class="form-control @error('FirstName') is-invalid @enderror" 
                                       id="modal_FirstName" name="FirstName" value="{{ old('FirstName') }}"
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
                                       id="modal_LastName" name="LastName" value="{{ old('LastName') }}"
                                       placeholder="Enter last name">
                                @error('LastName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <!-- Contact Details Section -->
                    <h6 class="text-primary mb-3"><i class="fas fa-address-book mr-2"></i>Contact Details</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_ContactNumber">Contact Number</label>
                                <input type="text" class="form-control @error('ContactNumber') is-invalid @enderror" 
                                       id="modal_ContactNumber" name="ContactNumber" value="{{ old('ContactNumber') }}"
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
                                       id="modal_Email" name="Email" value="{{ old('Email') }}"
                                       placeholder="e.g., client@email.com">
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
                        <i class="fas fa-save"></i> Create Client
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Modal styling */
    #createClientModal .modal-content {
        border: none;
        border-radius: 8px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }
    
    #createClientModal .modal-header {
        border-radius: 8px 8px 0 0;
        border-bottom: none;
    }
    
    #createClientModal .modal-body {
        padding: 1.5rem;
    }
    
    #createClientModal .modal-body h6 {
        color: #87A96B !important;
        font-weight: 600;
    }
    
    #createClientModal .modal-body hr {
        border-color: #e9ecef;
    }
    
    #createClientModal .modal-footer {
        border-top: 1px solid #e9ecef;
        padding: 1rem 1.5rem;
    }
    
    /* Form input styling */
    #createClientModal .form-control {
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 0.5rem 0.75rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    #createClientModal .form-control:focus {
        border-color: #87A96B;
        box-shadow: 0 0 0 0.2rem rgba(135, 169, 107, 0.25);
    }
    
    #createClientModal .form-control::placeholder {
        color: #adb5bd;
        font-size: 0.9rem;
    }
    
    #createClientModal label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.3rem;
    }

    /* DataTables Borderline Styling - Only horizontal borders between rows */
    #clientsTable {
        border: none !important;
    }

    #clientsTable thead th {
        border: none !important;
        border-bottom: 1px solid #dee2e6 !important;
    }

    #clientsTable tbody td {
        border: none !important;
        border-bottom: 1px solid #dee2e6 !important;
    }

    #clientsTable tbody tr:last-child td {
        border-bottom: none !important;
    }

    /* Remove margins from DataTable wrapper */
    #clientsTable_wrapper {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    #clientsTable_wrapper .dataTables_length,
    #clientsTable_wrapper .dataTables_filter {
        margin-left: 0 !important;
        margin-right: 0 !important;
        margin-top: 0.5rem !important;
        margin-bottom: 0.25rem !important;
        padding-left: 1rem !important;
        padding-right: 1rem !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }

    #clientsTable_wrapper .dataTables_length {
        display: flex !important;
        align-items: center !important;
    }

    #clientsTable_wrapper .dataTables_length label {
        display: flex !important;
        align-items: center !important;
        margin-bottom: 0 !important;
    }

    #clientsTable_wrapper .dataTables_length label::before {
        content: "Show entries" !important;
        margin-right: 0.5rem !important;
        font-weight: normal !important;
    }

    #clientsTable_wrapper .dataTables_length label select {
        margin: 0 !important;
    }

    #clientsTable_wrapper .dataTables_info,
    #clientsTable_wrapper .dataTables_paginate {
        margin-left: 0 !important;
        margin-right: 0 !important;
        margin-top: 0.5rem !important;
        padding-left: 1rem !important;
        padding-right: 1rem !important;
        padding-top: 0 !important;
        padding-bottom: 1rem !important;
    }

    #clientsTable_wrapper .dataTables_paginate .paginate_button {
        background: none !important;
        border: none !important;
        padding: 0.25rem 0.5rem !important;
        text-decoration: underline !important;
        color: #007bff !important;
    }

    #clientsTable_wrapper .dataTables_paginate .paginate_button.current {
        background: none !important;
        border: none !important;
        color: #007bff !important;
        text-decoration: underline !important;
        font-weight: bold !important;
    }

    #clientsTable_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
        background: none !important;
        border: none !important;
        text-decoration: underline !important;
    }

    #clientsTable_wrapper .dataTables_paginate .paginate_button.disabled {
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
    // Clear modal form when modal is closed
    $('#createClientModal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
        $(this).find('.is-invalid').removeClass('is-invalid');
        $(this).find('.invalid-feedback').remove();
    });

    // Auto-open modal if there are validation errors
    @if($errors->has('ClientName') || $errors->has('FirstName') || $errors->has('LastName') || $errors->has('ContactNumber') || $errors->has('Email'))
        $(document).ready(function() {
            $('#createClientModal').modal('show');
        });
    @endif

    // Initialize DataTables for clients table
    $(document).ready(function() {
        $('#clientsTable').DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "pageLength": 10,
            "order": [[0, 'asc']],
            "columnDefs": [
                { "orderable": false, "targets": [5] } // Disable sorting on Actions
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
    });
</script>
@endpush
</div>
</div>
@endsection
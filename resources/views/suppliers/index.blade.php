@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-truck mr-2"></i>
                        Suppliers Management
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createSupplierModal"
                           style="background-color: #87A96B !important; border: 2px solid #87A96B !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                            <i class="fas fa-plus"></i> Add New Supplier
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show m-3">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show m-3">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    @if($suppliers->count() > 0)
                        <div class="table-responsive">
                            <table id="suppliersTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Supplier Name</th>
                                        <th>Contact Person</th>
                                        <th>Phone Number</th>
                                        <th>Email</th>
                                        <th>City</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($suppliers as $supplier)
                                        <tr>
                                            <td><strong>{{ $supplier->SupplierName }}</strong></td>
                                            <td>{{ $supplier->contact_full_name ?: 'N/A' }}</td>
                                            <td>{{ $supplier->PhoneNumber ?? 'N/A' }}</td>
                                            <td>{{ $supplier->Email ?? 'N/A' }}</td>
                                            <td>{{ $supplier->City ?? 'N/A' }}</td>
                                            <td style="white-space: nowrap;">
                                                <a href="{{ route('suppliers.show', $supplier) }}" class="text-info" style="text-decoration: underline; cursor: pointer;">
                                                    <i class="fas fa-eye mr-1"></i> View
                                                </a>
                                                <a href="#" class="text-warning ml-2" data-toggle="modal" data-target="#editSupplierModal{{ $supplier->SupplierID }}" style="text-decoration: underline; cursor: pointer;">
                                                    <i class="fas fa-edit mr-1"></i> Edit
                                                </a>
                                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline ml-2"
                                                      onsubmit="return confirm('Are you sure you want to delete this supplier?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger p-0" style="text-decoration: underline;">
                                                        <i class="fas fa-trash mr-1"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>

                                        <!-- Edit Supplier Modal -->
                                        <div class="modal fade" id="editSupplierModal{{ $supplier->SupplierID }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header text-white" style="background: #87A96B;">
                                                        <h5 class="modal-title">
                                                            <i class="fas fa-edit mr-2"></i>Edit Supplier
                                                        </h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('suppliers.update', $supplier->SupplierID) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <!-- Supplier Information Section -->
                                                            <h6 class="text-primary mb-3"><i class="fas fa-truck mr-2"></i>Supplier Information</h6>
                                                            <div class="form-group">
                                                                <label>Supplier Name <span class="text-danger">*</span></label>
                                                                <input type="text" name="SupplierName" class="form-control @error('SupplierName') is-invalid @enderror" 
                                                                       value="{{ old('SupplierName', $supplier->SupplierName) }}" required>
                                                                @error('SupplierName')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>

                                                            <hr class="my-3">

                                                            <!-- Contact Person Section -->
                                                            <h6 class="text-primary mb-3"><i class="fas fa-user mr-2"></i>Contact Person</h6>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>First Name</label>
                                                                        <input type="text" name="ContactFirstName" class="form-control @error('ContactFirstName') is-invalid @enderror"
                                                                               value="{{ old('ContactFirstName', $supplier->ContactFirstName) }}">
                                                                        @error('ContactFirstName')
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Last Name</label>
                                                                        <input type="text" name="ContactLastName" class="form-control @error('ContactLastName') is-invalid @enderror"
                                                                               value="{{ old('ContactLastName', $supplier->ContactLastName) }}">
                                                                        @error('ContactLastName')
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
                                                                        <label>Phone Number</label>
                                                                        <input type="text" name="PhoneNumber" class="form-control @error('PhoneNumber') is-invalid @enderror"
                                                                               value="{{ old('PhoneNumber', $supplier->PhoneNumber) }}" placeholder="e.g., 09123456789">
                                                                        @error('PhoneNumber')
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Email Address</label>
                                                                        <input type="email" name="Email" class="form-control @error('Email') is-invalid @enderror"
                                                                               value="{{ old('Email', $supplier->Email) }}" placeholder="e.g., supplier@email.com">
                                                                        @error('Email')
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <hr class="my-3">

                                                            <!-- Address Section -->
                                                            <h6 class="text-primary mb-3"><i class="fas fa-map-marker-alt mr-2"></i>Address</h6>
                                                            <div class="form-group">
                                                                <label>Street</label>
                                                                <input type="text" name="Street" class="form-control @error('Street') is-invalid @enderror"
                                                                       value="{{ old('Street', $supplier->Street) }}" placeholder="Street address">
                                                                @error('Street')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label>City</label>
                                                                        <input type="text" name="City" class="form-control @error('City') is-invalid @enderror"
                                                                               value="{{ old('City', $supplier->City) }}" placeholder="City">
                                                                        @error('City')
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label>Province</label>
                                                                        <input type="text" name="Province" class="form-control @error('Province') is-invalid @enderror"
                                                                               value="{{ old('Province', $supplier->Province) }}" placeholder="Province">
                                                                        @error('Province')
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label>Postal Code</label>
                                                                        <input type="text" name="PostalCode" class="form-control @error('PostalCode') is-invalid @enderror"
                                                                               value="{{ old('PostalCode', $supplier->PostalCode) }}" placeholder="Postal code">
                                                                        @error('PostalCode')
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
                                                                <i class="fas fa-save"></i> Update Supplier
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-truck" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">No suppliers found</p>
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#createSupplierModal"
                               style="background-color: #87A96B !important; border: 2px solid #87A96B !important; color: white !important;">
                                Add First Supplier
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Supplier Modal -->
<div class="modal fade" id="createSupplierModal" tabindex="-1" role="dialog" aria-labelledby="createSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background: #87A96B;">
                <h5 class="modal-title" id="createSupplierModalLabel">
                    <i class="fas fa-truck mr-2"></i>Add New Supplier
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Supplier Information Section -->
                    <h6 class="text-primary mb-3"><i class="fas fa-truck mr-2"></i>Supplier Information</h6>
                    <div class="form-group">
                        <label for="modal_SupplierName">Supplier Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('SupplierName') is-invalid @enderror" 
                               id="modal_SupplierName" name="SupplierName" value="{{ old('SupplierName') }}" 
                               placeholder="Enter supplier name" required>
                        @error('SupplierName')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-3">

                    <!-- Contact Person Section -->
                    <h6 class="text-primary mb-3"><i class="fas fa-user mr-2"></i>Contact Person</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_ContactFirstName">First Name</label>
                                <input type="text" class="form-control @error('ContactFirstName') is-invalid @enderror" 
                                       id="modal_ContactFirstName" name="ContactFirstName" value="{{ old('ContactFirstName') }}"
                                       placeholder="Enter first name">
                                @error('ContactFirstName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_ContactLastName">Last Name</label>
                                <input type="text" class="form-control @error('ContactLastName') is-invalid @enderror" 
                                       id="modal_ContactLastName" name="ContactLastName" value="{{ old('ContactLastName') }}"
                                       placeholder="Enter last name">
                                @error('ContactLastName')
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
                                <label for="modal_PhoneNumber">Phone Number</label>
                                <input type="text" class="form-control @error('PhoneNumber') is-invalid @enderror" 
                                       id="modal_PhoneNumber" name="PhoneNumber" value="{{ old('PhoneNumber') }}"
                                       placeholder="e.g., 09123456789">
                                @error('PhoneNumber')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_Email">Email Address</label>
                                <input type="email" class="form-control @error('Email') is-invalid @enderror" 
                                       id="modal_Email" name="Email" value="{{ old('Email') }}"
                                       placeholder="e.g., supplier@email.com">
                                @error('Email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <!-- Address Section -->
                    <h6 class="text-primary mb-3"><i class="fas fa-map-marker-alt mr-2"></i>Address</h6>
                    <div class="form-group">
                        <label for="modal_Street">Street</label>
                        <input type="text" class="form-control @error('Street') is-invalid @enderror" 
                               id="modal_Street" name="Street" value="{{ old('Street') }}"
                               placeholder="Street address">
                        @error('Street')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="modal_City">City</label>
                                <input type="text" class="form-control @error('City') is-invalid @enderror" 
                                       id="modal_City" name="City" value="{{ old('City') }}"
                                       placeholder="City">
                                @error('City')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="modal_Province">Province</label>
                                <input type="text" class="form-control @error('Province') is-invalid @enderror" 
                                       id="modal_Province" name="Province" value="{{ old('Province') }}"
                                       placeholder="Province">
                                @error('Province')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="modal_PostalCode">Postal Code</label>
                                <input type="text" class="form-control @error('PostalCode') is-invalid @enderror" 
                                       id="modal_PostalCode" name="PostalCode" value="{{ old('PostalCode') }}"
                                       placeholder="Postal code">
                                @error('PostalCode')
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
                        <i class="fas fa-save"></i> Create Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Modal styling */
    .modal-content {
        border: none;
        border-radius: 8px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }
    
    .modal-header {
        border-radius: 8px 8px 0 0;
        border-bottom: none;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .modal-body h6 {
        color: #87A96B !important;
        font-weight: 600;
    }
    
    .modal-body hr {
        border-color: #e9ecef;
    }
    
    .modal-footer {
        border-top: 1px solid #e9ecef;
        padding: 1rem 1.5rem;
    }
    
    /* Form input styling */
    .modal .form-control {
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 0.5rem 0.75rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    .modal .form-control:focus {
        border-color: #87A96B;
        box-shadow: 0 0 0 0.2rem rgba(135, 169, 107, 0.25);
    }
    
    .modal .form-control::placeholder {
        color: #adb5bd;
        font-size: 0.9rem;
    }
    
    .modal label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.3rem;
    }

    /* DataTables Borderline Styling */
    #suppliersTable {
        border: none !important;
    }

    #suppliersTable thead th {
        border: none !important;
        border-bottom: 1px solid #dee2e6 !important;
    }

    #suppliersTable tbody td {
        border: none !important;
        border-bottom: 1px solid #dee2e6 !important;
    }

    #suppliersTable tbody tr:last-child td {
        border-bottom: none !important;
    }

    #suppliersTable_wrapper {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    #suppliersTable_wrapper .dataTables_length,
    #suppliersTable_wrapper .dataTables_filter {
        margin-left: 0 !important;
        margin-right: 0 !important;
        margin-top: 0.5rem !important;
        margin-bottom: 0.25rem !important;
        padding-left: 1rem !important;
        padding-right: 1rem !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }

    #suppliersTable_wrapper .dataTables_length {
        display: flex !important;
        align-items: center !important;
    }

    #suppliersTable_wrapper .dataTables_length label {
        display: flex !important;
        align-items: center !important;
        margin-bottom: 0 !important;
    }

    #suppliersTable_wrapper .dataTables_length label::before {
        content: "Show entries" !important;
        margin-right: 0.5rem !important;
        font-weight: normal !important;
    }

    #suppliersTable_wrapper .dataTables_length label select {
        margin: 0 !important;
    }

    #suppliersTable_wrapper .dataTables_info,
    #suppliersTable_wrapper .dataTables_paginate {
        margin-left: 0 !important;
        margin-right: 0 !important;
        margin-top: 0.5rem !important;
        padding-left: 1rem !important;
        padding-right: 1rem !important;
        padding-top: 0 !important;
        padding-bottom: 1rem !important;
    }

    #suppliersTable_wrapper .dataTables_paginate .paginate_button {
        background: none !important;
        border: none !important;
        padding: 0.25rem 0.5rem !important;
        text-decoration: underline !important;
        color: #007bff !important;
    }

    #suppliersTable_wrapper .dataTables_paginate .paginate_button.current {
        background: none !important;
        border: none !important;
        color: #007bff !important;
        text-decoration: underline !important;
        font-weight: bold !important;
    }

    #suppliersTable_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
        background: none !important;
        border: none !important;
        text-decoration: underline !important;
    }

    #suppliersTable_wrapper .dataTables_paginate .paginate_button.disabled {
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
    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
        $(this).find('.is-invalid').removeClass('is-invalid');
        $(this).find('.invalid-feedback').remove();
    });

    // Auto-open modal if there are validation errors
    @if($errors->any())
        $(document).ready(function() {
            $('#createSupplierModal').modal('show');
        });
    @endif

    // Initialize DataTables for suppliers table
    $(document).ready(function() {
        $('#suppliersTable').DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "pageLength": 10,
            "order": [[0, 'asc']],
            "columnDefs": [
                { "orderable": false, "targets": [5] }
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
@endsection

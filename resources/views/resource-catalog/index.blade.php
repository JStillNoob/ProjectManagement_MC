@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-box-open mr-2"></i>
                            Resource Catalog
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                data-target="#createResourceModal"
                                style="background-color: #87A96B !important; border: 2px solid #87A96B !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                                <i class="fas fa-plus"></i> Add New Item
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($items->count() > 0)
                            <div class="table-responsive">
                                <table id="resourceTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Item Name</th>
                                            <th>Type</th>
                                            <th>Unit</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $item)
                                            <tr>
                                                <td><strong>{{ $item->ItemName }}</strong></td>
                                                <td>
                                                    <span class="badge bg-{{ $item->Type == 'Equipment' ? 'primary' : 'info' }}">
                                                        {{ $item->Type }}
                                                    </span>
                                                </td>
                                                <td>{{ $item->Unit }}</td>
                                                <td style="white-space: nowrap;">
                                                    <a href="{{ route('resource-catalog.show', $item->ResourceCatalogID) }}"
                                                        class="text-info" style="text-decoration: underline; cursor: pointer;">
                                                        <i class="fas fa-eye mr-1"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-box-open" style="font-size: 3rem; color: #ccc;"></i>
                                <p class="text-muted mt-3">No resources found</p>
                                <button type="button" class="btn btn-success" data-toggle="modal"
                                    data-target="#createResourceModal"
                                    style="background-color: #87A96B !important; border: 2px solid #87A96B !important; color: white !important;">
                                    Add First Resource
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Resource Modal -->
    <div class="modal fade" id="createResourceModal" tabindex="-1" role="dialog" aria-labelledby="createResourceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: #87A96B;">
                    <h5 class="modal-title" id="createResourceModalLabel">
                        <i class="fas fa-box-open mr-2"></i>Add New Item
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('resource-catalog.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="modal_ItemName">Item Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('ItemName') is-invalid @enderror"
                                id="modal_ItemName" name="ItemName" value="{{ old('ItemName') }}"
                                placeholder="Enter item name" required>
                            @error('ItemName')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="modal_Type">Type <span class="text-danger">*</span></label>
                            <select class="form-control @error('Type') is-invalid @enderror" id="modal_Type" name="Type"
                                required>
                                <option value="">Select Type</option>
                                <option value="Equipment" {{ old('Type') == 'Equipment' ? 'selected' : '' }}>Equipment
                                </option>
                                <option value="Materials" {{ old('Type') == 'Materials' ? 'selected' : '' }}>Materials
                                </option>
                            </select>
                            @error('Type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="modal_Unit">Unit <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('Unit') is-invalid @enderror"
                                id="modal_Unit_input" value="{{ old('Unit') }}" placeholder="e.g., pcs, kg, box"
                                style="display:none;">
                            <select class="form-control @error('Unit') is-invalid @enderror" id="modal_Unit_select"
                                style="display:none;">
                                <option value="">Select Unit</option>
                                <option value="kg">Kilogram (kg)</option>
                                <option value="g">Gram (g)</option>
                                <option value="lbs">Pounds (lbs)</option>
                                <option value="pcs">Pieces (pcs)</option>
                                <option value="box">Box</option>
                                <option value="bag">Bag</option>
                                <option value="sack">Sack</option>
                                <option value="pails">Pails</option>
                                <option value="rolls">Rolls</option>
                                <option value="set">Set</option>
                                <option value="liter">Liter (L)</option>
                                <option value="gallon">Gallon</option>
                                <option value="meter">Meter (m)</option>
                                <option value="feet">Feet (ft)</option>
                                <option value="sqm">Square Meter (sqm)</option>
                                <option value="cu.m">Cubic Meter (cu.m)</option>
                                <option value="sheet">Sheet</option>
                            </select>
                            @error('Unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn text-white" style="background: #87A96B; border-color: #87A96B;">
                            <i class="fas fa-save"></i> Create Resource
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
            #resourceTable {
                border: none !important;
            }

            #resourceTable thead th {
                border: none !important;
                border-bottom: 1px solid #dee2e6 !important;
            }

            #resourceTable tbody td {
                border: none !important;
                border-bottom: 1px solid #dee2e6 !important;
            }

            #resourceTable tbody tr:last-child td {
                border-bottom: none !important;
            }

            #resourceTable_wrapper {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }

            #resourceTable_wrapper .dataTables_length,
            #resourceTable_wrapper .dataTables_filter {
                margin-left: 0 !important;
                margin-right: 0 !important;
                margin-top: 0.5rem !important;
                margin-bottom: 0.25rem !important;
                padding-left: 1rem !important;
                padding-right: 1rem !important;
                padding-top: 0 !important;
                padding-bottom: 0 !important;
            }

            #resourceTable_wrapper .dataTables_length {
                display: flex !important;
                align-items: center !important;
            }

            #resourceTable_wrapper .dataTables_length label {
                display: flex !important;
                align-items: center !important;
                margin-bottom: 0 !important;
            }

            #resourceTable_wrapper .dataTables_length label::before {
                content: "Show entries" !important;
                margin-right: 0.5rem !important;
                font-weight: normal !important;
            }

            #resourceTable_wrapper .dataTables_length label select {
                margin: 0 !important;
            }

            #resourceTable_wrapper .dataTables_info,
            #resourceTable_wrapper .dataTables_paginate {
                margin-left: 0 !important;
                margin-right: 0 !important;
                margin-top: 0.5rem !important;
                padding-left: 1rem !important;
                padding-right: 1rem !important;
                padding-top: 0 !important;
                padding-bottom: 1rem !important;
            }

            #resourceTable_wrapper .dataTables_paginate .paginate_button {
                background: none !important;
                border: none !important;
                padding: 0.25rem 0.5rem !important;
                text-decoration: underline !important;
                color: #007bff !important;
            }

            #resourceTable_wrapper .dataTables_paginate .paginate_button.current {
                background: none !important;
                border: none !important;
                color: #007bff !important;
                text-decoration: underline !important;
                font-weight: bold !important;
            }

            #resourceTable_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
                background: none !important;
                border: none !important;
                text-decoration: underline !important;
            }

            #resourceTable_wrapper .dataTables_paginate .paginate_button.disabled {
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
            // Auto-fill unit based on type selection
            $('#modal_Type').on('change', function () {
                const selectedType = $(this).val();
                if (selectedType === 'Equipment') {
                    // Show readonly text input with 'unit'
                    $('#modal_Unit_input').show().val('unit').attr('readonly', true).attr('name', 'Unit').attr('required', true);
                    $('#modal_Unit_select').hide().removeAttr('name').removeAttr('required');
                } else if (selectedType === 'Materials') {
                    // Show dropdown for materials
                    $('#modal_Unit_input').hide().removeAttr('name').removeAttr('required');
                    $('#modal_Unit_select').show().val('').attr('name', 'Unit').attr('required', true);
                } else {
                    // Hide both if no type selected
                    $('#modal_Unit_input').hide().removeAttr('name').removeAttr('required');
                    $('#modal_Unit_select').hide().removeAttr('name').removeAttr('required');
                }
            });

            // Initialize modal on show
            $('#createResourceModal').on('show.bs.modal', function () {
                // Trigger change to set initial state if type is already selected
                $('#modal_Type').trigger('change');
            });

            // Clear modal form when modal is closed
            $('#createResourceModal').on('hidden.bs.modal', function () {
                $(this).find('form')[0].reset();
                $(this).find('.is-invalid').removeClass('is-invalid');
                $(this).find('.invalid-feedback').remove();
                // Reset unit fields
                $('#modal_Unit_input').hide().removeAttr('name').removeAttr('required').removeAttr('readonly');
                $('#modal_Unit_select').hide().removeAttr('name').removeAttr('required');
            });

            // Auto-open modal if there are validation errors
            @if($errors->any())
                $(document).ready(function () {
                    $('#createResourceModal').modal('show');
                });
            @endif

            // Initialize DataTables for resource table
            $(document).ready(function () {
                $('#resourceTable').DataTable({
                    "responsive": true,
                    "lengthChange": true,
                    "autoWidth": false,
                    "pageLength": 10,
                    "order": [[0, 'asc']],
                    "columnDefs": [
                        { "orderable": false, "targets": [3] }
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
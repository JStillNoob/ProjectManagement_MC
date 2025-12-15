@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-box-open mr-2"></i>
                            Resource Details
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('resource-catalog.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="text-primary mb-3"><i class="fas fa-info-circle mr-2"></i>Resource Information
                                </h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="30%">Item Name:</th>
                                        <td><strong>{{ $resourceCatalog->ItemName }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Type:</th>
                                        <td>
                                            <span
                                                class="badge bg-{{ $resourceCatalog->Type == 'Equipment' ? 'primary' : 'info' }}">
                                                {{ $resourceCatalog->Type }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Unit of Measure:</th>
                                        <td>{{ $resourceCatalog->Unit }}</td>
                                    </tr>
                                    <tr>
                                        <th>Created At:</th>
                                        <td>{{ $resourceCatalog->created_at->format('M d, Y h:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Last Updated:</th>
                                        <td>{{ $resourceCatalog->updated_at->format('M d, Y h:i A') }}</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-4">
                                <h5 class="text-primary mb-3"><i class="fas fa-warehouse mr-2"></i>Inventory Stock</h5>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Total Stock</label>
                                    <h4 class="mb-0">{{ number_format($resourceCatalog->total_stock, 2) }}
                                        {{ $resourceCatalog->Unit }}</h4>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Available Stock</label>
                                    <h4 class="mb-0 text-success">{{ number_format($resourceCatalog->available_stock, 2) }}
                                        {{ $resourceCatalog->Unit }}</h4>
                                </div>
                                <small class="text-muted">Across all inventory items</small>

                                <hr class="my-3">

                                <h5 class="text-primary mb-3"><i class="fas fa-cog mr-2"></i>Actions</h5>
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-warning" data-toggle="modal"
                                        data-target="#editResourceModal">
                                        <i class="fas fa-edit"></i> Edit Resource
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                        <i class="fas fa-trash"></i> Delete Resource
                                    </button>
                                </div>
                                <form id="delete-form"
                                    action="{{ route('resource-catalog.destroy', $resourceCatalog->ResourceCatalogID) }}"
                                    method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Resource Modal -->
    <div class="modal fade" id="editResourceModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: #87A96B;">
                    <h5 class="modal-title">
                        <i class="fas fa-edit mr-2"></i>Edit Resource
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('resource-catalog.update', $resourceCatalog->ResourceCatalogID) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Item Name <span class="text-danger">*</span></label>
                            <input type="text" name="ItemName" class="form-control @error('ItemName') is-invalid @enderror"
                                value="{{ old('ItemName', $resourceCatalog->ItemName) }}" placeholder="Enter item name"
                                required>
                            @error('ItemName')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Type <span class="text-danger">*</span></label>
                            <select name="Type" class="form-control @error('Type') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                <option value="Equipment" {{ old('Type', $resourceCatalog->Type) == 'Equipment' ? 'selected' : '' }}>Equipment</option>
                                <option value="Materials" {{ old('Type', $resourceCatalog->Type) == 'Materials' ? 'selected' : '' }}>Materials</option>
                            </select>
                            @error('Type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Unit <span class="text-danger">*</span></label>
                            <input type="text" name="Unit" class="form-control @error('Unit') is-invalid @enderror"
                                value="{{ old('Unit', $resourceCatalog->Unit) }}" placeholder="e.g., pcs, kg, box" required>
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
                            <i class="fas fa-save"></i> Update Resource
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
        </style>
    @endpush

    @push('scripts')
        <script>
            function confirmDelete() {
                if (confirm('Are you sure you want to delete this resource? This action cannot be undone.')) {
                    document.getElementById('delete-form').submit();
                }
            }

            // Auto-open modal if there are validation errors
            @if($errors->any())
                $(document).ready(function () {
                    $('#editResourceModal').modal('show');
                });
            @endif
        </script>
    @endpush
@endsection
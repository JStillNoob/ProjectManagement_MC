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
                                <i class="fas fa-arrow-left"></i> Back
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
                                    <h4 class="mb-0">
                                        @if($resourceCatalog->requiresIntegerQuantity())
                                            {{ number_format((int) $resourceCatalog->total_stock, 0) }}
                                        @else
                                            {{ number_format($resourceCatalog->total_stock, 2) }}
                                        @endif
                                        {{ $resourceCatalog->Unit }}</h4>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Available Stock</label>
                                    <h4 class="mb-0 text-success">
                                        @if($resourceCatalog->requiresIntegerQuantity())
                                            {{ number_format((int) $resourceCatalog->available_stock, 0) }}
                                        @else
                                            {{ number_format($resourceCatalog->available_stock, 2) }}
                                        @endif
                                        {{ $resourceCatalog->Unit }}</h4>
                                </div>
                                <small class="text-muted">Across all inventory items</small>
                            </div>
                        </div>
                    </div>
                </div>
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

@endsection
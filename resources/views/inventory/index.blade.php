@extends('layouts.app')

@section('title', 'Inventory Management')
@section('page-title', 'Inventory Management')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-boxes mr-2"></i>Inventory Items
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('inventory.low-stock') }}" class="btn btn-warning btn-sm"
                                style="background-color: #ffc107 !important; border: 2px solid #ffc107 !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                                <i class="fas fa-exclamation-triangle"></i> Low Stock
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">

                        <!-- Filter Section -->
                        <div class="row mx-3 my-3">
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label for="filterType" class="small text-muted mb-1">Filter by Type</label>
                                    <select id="filterType" class="form-control form-control-sm">
                                        <option value="">All Types</option>
                                        <option value="Equipment" {{ request('type') == 'Equipment' ? 'selected' : '' }}>
                                            Equipment</option>
                                        <option value="Materials" {{ request('type') == 'Materials' ? 'selected' : '' }}>
                                            Materials</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label for="filterStatus" class="small text-muted mb-1">Filter by Status</label>
                                    <select id="filterStatus" class="form-control form-control-sm">
                                        <option value="">All Statuses</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label for="filterStock" class="small text-muted mb-1">Filter by Stock</label>
                                    <select id="filterStock" class="form-control form-control-sm">
                                        <option value="">All Items</option>
                                        <option value="Low Stock">Low Stock Only</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="button" id="clearFilters" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times mr-1"></i> Clear Filters
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="inventoryTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Type</th>
                                        <th>Total Qty</th>
                                        <th>Available Qty</th>
                                        <th>Unit</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($items as $item)
                                        <tr>
                                            <td>
                                                <strong>{{ $item->resourceCatalog->ItemName ?? 'N/A' }}</strong>
                                                @if($item->AvailableQuantity < 10)
                                                    <span class="badge badge-warning ml-2">Low Stock</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $item->resourceCatalog->Type == 'Materials' ? 'info' : 'primary' }}">
                                                    {{ $item->resourceCatalog->Type ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>{{ number_format($item->TotalQuantity, 2) }}</td>
                                            <td>
                                                <span class="text-success">
                                                    {{ number_format($item->AvailableQuantity, 2) }}
                                                </span>
                                            </td>
                                            <td>{{ $item->resourceCatalog->Unit ?? 'N/A' }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="mr-2"
                                                        style="width: 8px; height: 8px; border-radius: 50%; background-color: {{ $item->Status == 'Active' ? '#28a745' : '#6c757d' }};"></span>
                                                    <span>{{ $item->Status }}</span>
                                                </div>
                                            </td>
                                            <td class="text-center" style="white-space: nowrap;">
                                                <a href="{{ route('inventory.show', $item) }}" class="text-info"
                                                    style="cursor: pointer;" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">No inventory items found.</p>
                                            </td>
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
                #inventoryTable {
                    border: none !important;
                }

                #inventoryTable thead th {
                    border: none !important;
                    border-bottom: 1px solid #dee2e6 !important;
                }

                #inventoryTable tbody td {
                    border: none !important;
                    border-bottom: 1px solid #dee2e6 !important;
                }

                #inventoryTable tbody tr:last-child td {
                    border-bottom: none !important;
                }

                /* Remove margins from DataTable wrapper */
                #inventoryTable_wrapper {
                    margin-left: 0 !important;
                    margin-right: 0 !important;
                }

                #inventoryTable_wrapper .dataTables_length,
                #inventoryTable_wrapper .dataTables_filter {
                    margin-left: 0 !important;
                    margin-right: 0 !important;
                    margin-top: 0.5rem !important;
                    margin-bottom: 0.25rem !important;
                    padding-left: 1rem !important;
                    padding-right: 1rem !important;
                    padding-top: 0 !important;
                    padding-bottom: 0 !important;
                }

                #inventoryTable_wrapper .dataTables_length {
                    display: flex !important;
                    align-items: center !important;
                }

                #inventoryTable_wrapper .dataTables_length label {
                    display: flex !important;
                    align-items: center !important;
                    margin-bottom: 0 !important;
                }

                #inventoryTable_wrapper .dataTables_length label::before {
                    content: "Show entries" !important;
                    margin-right: 0.5rem !important;
                    font-weight: normal !important;
                }

                #inventoryTable_wrapper .dataTables_length label select {
                    margin: 0 !important;
                }

                #inventoryTable_wrapper .dataTables_info,
                #inventoryTable_wrapper .dataTables_paginate {
                    margin-left: 0 !important;
                    margin-right: 0 !important;
                    margin-top: 0.5rem !important;
                    padding-left: 1rem !important;
                    padding-right: 1rem !important;
                    padding-top: 0 !important;
                    padding-bottom: 1rem !important;
                }

                #inventoryTable_wrapper .dataTables_paginate .paginate_button {
                    background: none !important;
                    border: none !important;
                    padding: 0.25rem 0.5rem !important;
                    text-decoration: underline !important;
                    color: #007bff !important;
                }

                #inventoryTable_wrapper .dataTables_paginate .paginate_button.current {
                    background: none !important;
                    border: none !important;
                    color: #007bff !important;
                    text-decoration: underline !important;
                    font-weight: bold !important;
                }

                #inventoryTable_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
                    background: none !important;
                    border: none !important;
                    text-decoration: underline !important;
                }

                #inventoryTable_wrapper .dataTables_paginate .paginate_button.disabled {
                    background: none !important;
                    border: none !important;
                    text-decoration: none !important;
                    color: #6c757d !important;
                    opacity: 0.5 !important;
                }

                /* Filter section styling */
                #filterType,
                #filterStatus,
                #filterStock {
                    border: 1px solid #ced4da;
                    border-radius: 4px;
                }

                #filterType:focus,
                #filterStatus:focus,
                #filterStock:focus {
                    border-color: #87A96B;
                    box-shadow: 0 0 0 0.2rem rgba(135, 169, 107, 0.25);
                }
            </style>
        @endpush


        @push('scripts')
            <script>
                $(document).ready(function () {
                    var table = $('#inventoryTable').DataTable({
                        "responsive": true,
                        "lengthChange": true,
                        "autoWidth": false,
                        "pageLength": 10,
                        "order": [[0, 'asc']],
                        "columnDefs": [
                            { "orderable": false, "targets": [6] },
                            { "className": "text-center", "targets": [6] }
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

                    // Filter by Type
                    $('#filterType').on('change', function () {
                        var val = $(this).val();
                        table.column(1).search(val ? val : '', true, false).draw();
                    });

                    // Filter by Status
                    $('#filterStatus').on('change', function () {
                        var val = $(this).val();
                        table.column(5).search(val ? val : '', true, false).draw();
                    });

                    // Filter by Low Stock
                    $('#filterStock').on('change', function () {
                        if ($(this).is(':checked')) {
                            table.column(3).search('text-danger', true, false).draw();
                        } else {
                            table.column(3).search('').draw();
                        }
                    });
                });
            </script>
        @endpush

@endsection
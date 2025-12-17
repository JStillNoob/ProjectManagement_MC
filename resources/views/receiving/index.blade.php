@extends('layouts.app')

@section('title', 'Receiving Records')
@section('page-title', 'Receiving Records')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-box-open mr-2"></i>
                            Receiving Records
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('receiving.create') }}" class="btn btn-success btn-sm"
                                style="background-color: #87A96B !important; border: 2px solid #87A96B !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                                <i class="fas fa-plus"></i> Receive Items
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <!-- Filter Section -->
                        <div class="row mx-3 my-3">
                            <form method="GET" action="{{ route('receiving.index') }}" class="row g-3 w-100">
                                <div class="col-md-3">
                                    <label class="form-label small text-muted mb-1">Filter by PO</label>
                                    <select name="po_id" class="form-control form-control-sm">
                                        <option value="">All Purchase Orders</option>
                                        @foreach($purchaseOrders as $po)
                                            <option value="{{ $po->POID }}" {{ request('po_id') == $po->POID ? 'selected' : '' }}>
                                                PO #{{ $po->POID }} - {{ $po->supplier->SupplierName ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small text-muted mb-1">Date From</label>
                                    <input type="date" name="date_from" class="form-control form-control-sm"
                                        value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small text-muted mb-1">Date To</label>
                                    <input type="date" name="date_to" class="form-control form-control-sm" 
                                        value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-sm text-white mr-2"
                                        style="background-color: #87A96B !important; border: 2px solid #87A96B !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                                        <i class="fas fa-filter mr-1"></i> Filter
                                    </button>
                                    <a href="{{ route('receiving.index') }}" class="btn btn-outline-secondary btn-sm"
                                        style="background-color: #6c757d !important; border: 2px solid #6c757d !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                                        <i class="fas fa-times mr-1"></i> Clear
                                    </a>
                                </div>
                            </form>
                        </div>

                        <!-- Receiving Records Table -->
                        <div class="table-responsive px-3">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Date Received</th>
                                        <th>PO Number</th>
                                        <th>Supplier</th>
                                        <th class="text-center">Items</th>
                                        <th class="text-center">Condition</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($receivingRecords as $record)
                                        <tr>
                                            <td>{{ $record->ReceivedDate->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('purchase-orders.show', $record->POID) }}" class="fw-bold">
                                                    #{{ $record->purchaseOrder->POID ?? 'N/A' }}
                                                </a>
                                            </td>
                                            <td>{{ $record->purchaseOrder->supplier->SupplierName ?? 'N/A' }}</td>
                                            <td class="text-center">
                                                {{ $record->items->count() }}
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $conditionClass = 'secondary';
                                                    $textClass = 'text-white';
                                                    if ($record->OverallCondition == 'Good') {
                                                        $conditionClass = 'success';
                                                        $textClass = 'text-white';
                                                    } elseif ($record->OverallCondition == 'Damaged') {
                                                        $conditionClass = 'danger';
                                                        $textClass = 'text-white';
                                                    } elseif ($record->OverallCondition == 'Mixed') {
                                                        $conditionClass = 'warning';
                                                        $textClass = 'text-dark';
                                                    }
                                                @endphp
                                                <span class="badge bg-{{ $conditionClass }} {{ $textClass }}">
                                                    {{ $record->OverallCondition ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('receiving.show', $record->ReceivingID) }}" 
                                                   class="text-info"
                                                   style="cursor: pointer;"
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <i class="fas fa-box-open fa-2x mb-2"></i>
                                                <p class="mb-0">No receiving records found.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="px-3 py-3">
                            {{ $receivingRecords->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* DataTables Borderline Styling - Only horizontal borders between rows */
            .table {
                border: none !important;
            }

            .table thead th {
                border: none !important;
                border-bottom: 1px solid #dee2e6 !important;
            }

            .table tbody td {
                border: none !important;
                border-bottom: 1px solid #dee2e6 !important;
            }

            .table tbody tr:last-child td {
                border-bottom: none !important;
            }

            /* Filter section styling */
            .form-control-sm {
                border: 1px solid #ced4da;
                border-radius: 4px;
            }

            .form-control-sm:focus {
                border-color: #87A96B;
                box-shadow: 0 0 0 0.2rem rgba(135, 169, 107, 0.25);
            }
        </style>
    @endpush
@endsection
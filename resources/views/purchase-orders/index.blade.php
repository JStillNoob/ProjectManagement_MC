@extends('layouts.app')

@section('title', 'Purchase Orders')
@section('page-title', 'Purchase Orders')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list mr-2"></i>
                            Purchase Orders
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('purchase-orders.create') }}" class="btn btn-success btn-sm"
                                style="background-color: #87A96B !important; border: 2px solid #87A96B !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                                <i class="fas fa-plus"></i> Create Purchase Order
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <!-- Filter Section -->
                        <div class="row mx-3 my-3">
                            <form method="GET" action="{{ route('purchase-orders.index') }}" class="row g-3 w-100">
                                <div class="col-md-2">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All Statuses</option>
                                        <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft
                                        </option>
                                        <option value="Sent" {{ request('status') == 'Sent' ? 'selected' : '' }}>Sent</option>
                                        <option value="Partially Received" {{ request('status') == 'Partially Received' ? 'selected' : '' }}>Partially Received</option>
                                        <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>
                                            Completed
                                        </option>
                                        <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>
                                            Cancelled
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Supplier</label>
                                    <select name="supplier_id" class="form-control">
                                        <option value="">All Suppliers</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->SupplierID }}" {{ request('supplier_id') == $supplier->SupplierID ? 'selected' : '' }}>
                                                {{ $supplier->SupplierName }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Date From</label>
                                    <input type="date" name="date_from" class="form-control"
                                        value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Date To</label>
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-secondary w-100">Filter</button>
                                </div>
                            </form>
                        </div>

                        <!-- Purchase Orders Table -->
                        <div class="table-responsive px-3">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>PO Number</th>
                                        <th>Supplier</th>
                                        <th>Order Date</th>
                                        <th>Status</th>
                                        <th>Created By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchaseOrders as $po)
                                        <tr>
                                            <td>
                                                <a href="{{ route('purchase-orders.show', $po->POID) }}" class="fw-bold">
                                                    #{{ $po->POID }}
                                                </a>
                                            </td>
                                            <td>{{ $po->supplier->SupplierName ?? 'N/A' }}</td>
                                            <td>{{ $po->OrderDate->format('M d, Y') }}</td>
                                            <td>
                                                @php
                                                    $statusClass = [
                                                        'Draft' => 'secondary',
                                                        'Sent' => 'info',
                                                        'Partially Received' => 'warning',
                                                        'Completed' => 'success',
                                                        'Cancelled' => 'danger'
                                                    ][$po->Status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }}">{{ $po->Status }}</span>
                                            </td>
                                            <td>{{ $po->creator->FirstName ?? '' }} {{ $po->creator->LastName ?? '' }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('purchase-orders.show', $po->POID) }}"
                                                        class="btn btn-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($po->isEditable())
                                                        <a href="{{ route('purchase-orders.edit', $po->POID) }}"
                                                            class="btn btn-warning" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('purchase-orders.pdf', $po->POID) }}"
                                                        class="btn btn-secondary" title="Download PDF">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">No purchase orders found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="px-3 py-3">
                            {{ $purchaseOrders->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
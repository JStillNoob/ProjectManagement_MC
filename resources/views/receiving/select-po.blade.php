@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2>Select Purchase Order to Receive</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('receiving.index') }}">Receiving</a></li>
                <li class="breadcrumb-item active">Select PO</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Purchase Orders Pending Receiving</h5>
        </div>
        <div class="card-body">
            @if($purchaseOrders->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No purchase orders pending receiving at this time.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>PO Number</th>
                                <th>Supplier</th>
                                <th>Order Date</th>
                                <th>Expected Delivery</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseOrders as $po)
                                <tr>
                                    <td><strong>PO #{{ $po->POID }}</strong></td>
                                    <td>{{ $po->supplier->SupplierName ?? 'N/A' }}</td>
                                    <td>{{ $po->OrderDate->format('M d, Y') }}</td>
                                    <td>{{ $po->ExpectedDeliveryDate ? $po->ExpectedDeliveryDate->format('M d, Y') : 'N/A' }}</td>
                                    <td>₱{{ number_format($po->TotalAmount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $po->Status == 'Sent' ? 'info' : 'warning' }}">
                                            {{ $po->Status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('receiving.create', ['po_id' => $po->POID]) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-box-open"></i> Receive Items
                                        </a>
                                        <a href="{{ route('purchase-orders.show', $po->POID) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View PO
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

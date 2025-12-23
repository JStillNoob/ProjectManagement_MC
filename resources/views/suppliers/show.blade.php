@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col">
                <h2>Supplier Details</h2>
            </div>
            <div class="col-auto">
                <a href="{{ route('suppliers.edit', $supplier->SupplierID) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Supplier Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Supplier Name:</th>
                                <td>{{ $supplier->SupplierName }}</td>
                            </tr>
                            <tr>
                                <th>Contact Person:</th>
                                <td>{{ $supplier->contact_full_name ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Phone Number:</th>
                                <td>{{ $supplier->PhoneNumber ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td>{{ $supplier->Email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Street:</th>
                                <td>{{ $supplier->Street ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>City:</th>
                                <td>{{ $supplier->City ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Province:</th>
                                <td>{{ $supplier->Province ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Postal Code:</th>
                                <td>{{ $supplier->PostalCode ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Full Address:</th>
                                <td>{{ $supplier->full_address ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    @if($supplier->Status === 'Active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Created At:</th>
                                <td>{{ $supplier->created_at->format('M d, Y h:i A') }}</td>
                            </tr>
                            <tr>
                                <th>Updated At:</th>
                                <td>{{ $supplier->updated_at->format('M d, Y h:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Purchase Orders</h5>
                    </div>
                    <div class="card-body">
                        @if($purchaseOrders->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>PO Number</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($purchaseOrders as $po)
                                            <tr>
                                                <td>PO #{{ $po->POID }}</td>
                                                <td>{{ \Carbon\Carbon::parse($po->PODate)->format('M d, Y') }}</td>
                                                <td>
                                                    @if($po->Status === 'Pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                    @elseif($po->Status === 'Approved')
                                                        <span class="badge bg-success">Approved</span>
                                                    @elseif($po->Status === 'Completed')
                                                        <span class="badge bg-primary">Completed</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $po->Status }}</span>
                                                    @endif
                                                </td>
                                                <td>₱{{ number_format($po->TotalAmount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-2">
                                <strong>Total Purchase Orders:</strong> {{ $purchaseOrders->count() }}
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-file-invoice fa-3x mb-3"></i>
                                <p>No purchase orders found for this supplier.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
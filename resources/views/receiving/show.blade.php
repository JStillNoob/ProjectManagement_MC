@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('receiving.index') }}">Receiving</a></li>
                    <li class="breadcrumb-item active">Receiving Record #{{ $receivingRecord->ReceivingID }}</li>
                </ol>
            </nav>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Receiving Record #{{ $receivingRecord->ReceivingID }}</h4>
                        <div>
                            @php
                                $conditionClass = [
                                    'Good' => 'success',
                                    'Damaged' => 'danger',
                                    'Mixed' => 'warning'
                                ][$receivingRecord->OverallCondition] ?? 'secondary';
                            @endphp
                            <span
                                class="badge bg-{{ $conditionClass }} fs-6">{{ $receivingRecord->OverallCondition }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted">Purchase Order Information</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>PO Number:</strong></td>
                                        <td>
                                            <a href="{{ route('purchase-orders.show', $receivingRecord->POID) }}">
                                                #{{ $receivingRecord->purchaseOrder->POID ?? 'N/A' }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Supplier:</strong></td>
                                        <td>{{ $receivingRecord->purchaseOrder->supplier->SupplierName ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>PO Status:</strong></td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $receivingRecord->purchaseOrder->Status ?? 'N/A' }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Receiving Details</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>Received Date:</strong></td>
                                        <td>{{ $receivingRecord->ReceivedDate->format('M d, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Received By:</strong></td>
                                        <td>{{ $receivingRecord->receiver->FirstName ?? '' }}
                                            {{ $receivingRecord->receiver->LastName ?? '' }}
                                        </td>
                                    </tr>
                                    @if($receivingRecord->DeliveryReceiptNumber)
                                        <tr>
                                            <td><strong>DR Number:</strong></td>
                                            <td>{{ $receivingRecord->DeliveryReceiptNumber }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td><strong>Recorded At:</strong></td>
                                        <td>{{ $receivingRecord->created_at->format('M d, Y h:i A') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($receivingRecord->Remarks)
                            <div class="mb-3">
                                <h6 class="text-muted">Remarks</h6>
                                <p class="mb-0">{{ $receivingRecord->Remarks }}</p>
                            </div>
                        @endif

                        @if($receivingRecord->AttachmentPath)
                            <div class="mb-3">
                                <h6 class="text-muted">Delivery Receipt Attachment</h6>
                                <a href="{{ Storage::url($receivingRecord->AttachmentPath) }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-file"></i> View Attachment
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Received Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-center">Qty Ordered</th>
                                        <th class="text-center">Qty Received</th>
                                        <th class="text-center">Good</th>
                                        <th class="text-center">Damaged</th>
                                        <th>Condition</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($receivingRecord->items as $item)
                                        <tr>
                                            <td>
                                                <strong>{{ $item->purchaseOrderItem->inventoryItem->ItemName ?? 'N/A' }}</strong><br>
                                                <small class="text-muted">
                                                    {{ $item->purchaseOrderItem->inventoryItem->ItemCode ?? '' }}
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                {{ $item->purchaseOrderItem->QuantityOrdered }}
                                                {{ $item->purchaseOrderItem->Unit }}
                                            </td>
                                            <td class="text-center">
                                                <strong>{{ $item->QuantityReceived }}
                                                    {{ $item->purchaseOrderItem->Unit }}</strong>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success">{{ $item->GoodQuantity }}
                                                    {{ $item->purchaseOrderItem->Unit }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if($item->QuantityDamaged > 0)
                                                    <span class="badge bg-danger">{{ $item->QuantityDamaged }}
                                                        {{ $item->purchaseOrderItem->Unit }}</span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $item->Condition == 'Good' ? 'success' : 'danger' }}">
                                                    {{ $item->Condition }}
                                                </span>
                                            </td>
                                            <td>{{ $item->ItemRemarks ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="2" class="text-end">Totals:</th>
                                        <th class="text-center">{{ $receivingRecord->items->sum('QuantityReceived') }}</th>
                                        <th class="text-center text-success">
                                            {{ $receivingRecord->items->sum('GoodQuantity') }}
                                        </th>
                                        <th class="text-center text-danger">
                                            {{ $receivingRecord->items->sum('QuantityDamaged') }}
                                        </th>
                                        <th colspan="2"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Items:</span>
                            <strong>{{ $receivingRecord->items->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Received:</span>
                            <strong>{{ $receivingRecord->items->sum('QuantityReceived') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Good Condition:</span>
                            <strong class="text-success">{{ $receivingRecord->items->sum('GoodQuantity') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Damaged:</span>
                            <strong class="text-danger">{{ $receivingRecord->items->sum('QuantityDamaged') }}</strong>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Inventory Impact</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><small class="text-muted">The following inventory items were updated:</small></p>
                        <ul class="small mb-0">
                            @foreach($receivingRecord->items as $item)
                                @if($item->GoodQuantity > 0)
                                    <li>
                                        <strong>{{ $item->purchaseOrderItem->inventoryItem->ItemName ?? 'N/A' }}</strong>
                                        <br>
                                        <span class="text-success">+{{ $item->GoodQuantity }}
                                            {{ $item->purchaseOrderItem->Unit }}</span> added to stock
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('purchase-orders.show', $receivingRecord->POID) }}" class="btn btn-info">
                                <i class="fas fa-file-invoice"></i> View Purchase Order
                            </a>
                            <a href="{{ route('receiving.index') }}" class="btn btn-secondary">
                                <i class="fas fa-list"></i> Back to Receiving List
                            </a>

                            @if(auth()->user()->UserType == 2) {{-- Admin only --}}
                                <form action="{{ route('receiving.destroy', $receivingRecord->ReceivingID) }}" method="POST"
                                    onsubmit="return confirm('Delete this receiving record? This will reverse the inventory updates.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="fas fa-trash"></i> Delete Record
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
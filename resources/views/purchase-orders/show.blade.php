@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Back Button Row -->


        <div class="row">
            <div class="col-12">
                <div class="card">
                    <!-- Card Header -->
                    <div class="card-header" style="background-color: #87A96B;">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="card-title mb-0" style="color: white; font-size: 1.25rem;">
                                    <i class="fas fa-file-invoice me-2"></i>
                                    Purchase Order #{{ $purchaseOrder->POID }}
                                </h3>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Back to List
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Info Section -->
                        <div class="row mb-4">
                            <!-- Supplier Information -->
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                                    <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.85rem;">
                                        <i class="fas fa-building me-2"></i>Supplier Information
                                    </h6>
                                    <h5 class="fw-bold mb-2" style="font-size: 1.1rem;">{{ $purchaseOrder->supplier->SupplierName ?? 'N/A' }}</h5>
                                    @if($purchaseOrder->supplier)
                                        @if($purchaseOrder->supplier->PhoneNumber)
                                            <p class="mb-1 text-muted" style="font-size: 0.95rem;">
                                                <i class="fas fa-phone me-2"></i>{{ $purchaseOrder->supplier->PhoneNumber }}
                                            </p>
                                        @endif
                                        @if($purchaseOrder->supplier->Email)
                                            <p class="mb-0 text-muted" style="font-size: 0.95rem;">
                                                <i class="fas fa-envelope me-2"></i>{{ $purchaseOrder->supplier->Email }}
                                            </p>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <!-- Order Details -->
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                                    <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.85rem;">
                                        <i class="fas fa-info-circle me-2"></i>Order Details
                                    </h6>
                                    <table class="table table-sm table-borderless mb-0" style="font-size: 0.95rem;">
                                        <tr>
                                            <td class="text-muted ps-0">Order Date</td>
                                            <td class="fw-semibold">{{ $purchaseOrder->OrderDate->format('M d, Y') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0">Created By</td>
                                            <td class="fw-semibold">
                                                @if($purchaseOrder->creator)
                                                    {{ $purchaseOrder->creator->first_name }}
                                                    {{ $purchaseOrder->creator->last_name }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        @if($purchaseOrder->DateSent)
                                            <tr>
                                                <td class="text-muted ps-0">Date Sent</td>
                                                <td class="fw-semibold">
                                                    {{ \Carbon\Carbon::parse($purchaseOrder->DateSent)->format('M d, Y') }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>

                            <!-- Linked Request -->
                            <div class="col-md-4">
                                <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                                    <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.85rem;">
                                        <i class="fas fa-link me-2"></i>Linked Request
                                    </h6>
                                    @if($purchaseOrder->inventoryRequest)
                                        <table class="table table-sm table-borderless mb-0" style="font-size: 0.95rem;">
                                            <tr>
                                                <td class="text-muted ps-0">Request ID</td>
                                                <td class="fw-bold">#{{ $purchaseOrder->inventoryRequest->RequestID }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted ps-0">Project</td>
                                                <td class="fw-semibold">
                                                    {{ $purchaseOrder->inventoryRequest->project->ProjectName ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted ps-0">Status</td>
                                                <td>
                                                    @php
                                                        $reqStatusColors = [
                                                            'Pending' => 'warning',
                                                            'Approved' => 'success',
                                                            'Rejected' => 'danger',
                                                            'Pending - To Order' => 'info',
                                                            'Ordered' => 'primary'
                                                        ];
                                                        $reqBadgeColor = $reqStatusColors[$purchaseOrder->inventoryRequest->Status] ?? 'secondary';
                                                    @endphp
                                                    <span
                                                        class="badge bg-{{ $reqBadgeColor }} text-white">{{ $purchaseOrder->inventoryRequest->Status }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    @else
                                        <p class="text-muted mb-0" style="font-size: 0.95rem;">No linked request</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Items Table -->
                       
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Supplier</th>
                                        <th class="text-center">Qty Ordered</th>
                                        <th class="text-center">Qty Received</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchaseOrder->items as $item)
                                        @php
                                            $itemName = 'N/A';
                                            $itemType = '';

                                            if ($item->inventoryItem && $item->inventoryItem->resourceCatalog) {
                                                $itemName = $item->inventoryItem->resourceCatalog->ItemName;
                                                $itemType = $item->inventoryItem->resourceCatalog->Type ?? '';
                                            }

                                            $isFullyReceived = $item->QuantityReceived >= $item->QuantityOrdered;
                                            $receivedPercentage = $item->QuantityOrdered > 0
                                                ? min(100, ($item->QuantityReceived / $item->QuantityOrdered) * 100)
                                                : 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-2"
                                                        style="width: 32px; height: 32px; background-color: {{ $itemType == 'Equipment' ? 'rgba(23, 162, 184, 0.15)' : 'rgba(135, 169, 107, 0.15)' }};">
                                                        <i class="fas {{ $itemType == 'Equipment' ? 'fa-tools' : 'fa-cube' }}"
                                                            style="color: {{ $itemType == 'Equipment' ? '#17a2b8' : '#87A96B' }}; font-size: 0.8rem;"></i>
                                                    </div>
                                                    <span>{{ $itemName }}</span>
                                                </div>
                                            </td>
                                            <td>{{ $item->supplier->SupplierName ?? '-' }}</td>
                                            <td class="text-center">{{ number_format($item->QuantityOrdered) }}
                                                {{ $item->Unit }}</td>
                                            <td class="text-center">{{ number_format($item->QuantityReceived) }}
                                                {{ $item->Unit }}</td>
                                            <td class="text-center">
                                                @if($isFullyReceived)
                                                    <span class="badge text-white" style="background-color: #87A96B;">Complete</span>
                                                @elseif($item->QuantityReceived > 0)
                                                    <span
                                                        class="badge bg-warning text-dark">{{ number_format($receivedPercentage, 0) }}%</span>
                                                @else
                                                    <span class="badge bg-secondary text-white">Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="fas fa-box-open fa-2x mb-2"></i>
                                                <p class="mb-0">No items in this purchase order</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($purchaseOrder->items->count() > 0)
                                    <tfoot>
                                        <tr class="fw-bold">
                                            <td colspan="2">Total</td>
                                            <td class="text-center">
                                                {{ number_format($purchaseOrder->items->sum('QuantityOrdered')) }}</td>
                                            <td class="text-center">
                                                {{ number_format($purchaseOrder->items->sum('QuantityReceived')) }}</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>

                        <!-- Receiving History -->
                        @if($purchaseOrder->receivingRecords && count($purchaseOrder->receivingRecords) > 0)
                            <h6 class="text-uppercase fw-bold mb-3 mt-4" style="color: #87A96B; font-size: 0.75rem;">
                                <i class="fas fa-history me-2"></i>Receiving History
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>Received Date</th>
                                            <th>Received By</th>
                                            <th class="text-center">Items</th>
                                            <th class="text-center">Condition</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($purchaseOrder->receivingRecords as $record)
                                            <tr>
                                                <td>{{ $record->ReceivedDate ? \Carbon\Carbon::parse($record->ReceivedDate)->format('M d, Y') : 'N/A' }}
                                                </td>
                                                <td>{{ $record->receiver->first_name ?? '' }}
                                                    {{ $record->receiver->last_name ?? '' }}</td>
                                                <td class="text-center">
                                                    <span class="badge text-white"
                                                        style="background-color: #87A96B;">{{ $record->items->count() ?? 0 }}</span>
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
                                                <td>{{ $record->Remarks ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <!-- Card Footer with Buttons -->
                    <div class="card-footer bg-white py-3">
                        <div class="text-right">
                            @if($purchaseOrder->Status != 'Completed' && $purchaseOrder->Status != 'Cancelled')
                                <form action="{{ route('purchase-orders.cancel', $purchaseOrder->POID) }}" method="POST" class="d-inline swal-confirm-form"
                                    data-title="Cancel Purchase Order?"
                                    data-text="This action cannot be undone. The PO will be marked as cancelled."
                                    data-icon="warning"
                                    data-confirm-text="Yes, Cancel It">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-danger mr-2">
                                        <i class="fas fa-times"></i> Cancel Order
                                    </button>
                                </form>
                            @endif

                            @if($purchaseOrder->Status == 'Draft')
                                <form action="{{ route('purchase-orders.mark-sent', $purchaseOrder->POID) }}" method="POST" class="d-inline swal-confirm-form"
                                    data-title="Send Purchase Order?"
                                    data-text="This will mark the PO as sent to the supplier. You can then receive items against this PO."
                                    data-icon="question"
                                    data-confirm-text="Yes, Send It">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-info mr-2">
                                        <i class="fas fa-paper-plane"></i> Mark as Sent
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('purchase-orders.pdf', $purchaseOrder->POID) }}" 
                               class="btn btn-secondary mr-2"
                               style="background-color: #87A96B !important; border-color: #87A96B !important; color: #fff !important;"
                               target="_blank">
                                <i class="fas fa-file-pdf btn-secondary"></i> Print PDF
                            </a>
                            @if(in_array($purchaseOrder->Status, ['Sent', 'Partially Received']))
                                <a href="{{ route('receiving.create', ['po_id' => $purchaseOrder->POID]) }}" 
                                   class="btn btn-success"
                                   style="background-color: #87A96B !important; border-color: #87A96B !important;">
                                    <i class="fas fa-box-open"></i> Receive Items
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
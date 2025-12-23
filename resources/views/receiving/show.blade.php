@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <!-- Card Header -->
                    <div class="card-header" style="background-color: #87A96B;">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="card-title mb-0" style="color: white; font-size: 1.25rem;">
                                    <i class="fas fa-box-open me-2"></i>
                                    Receiving Record #{{ $receivingRecord->ReceivingID }}
                                </h3>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('receiving.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Info Section -->
                        <div class="row mb-4">
                            <!-- Purchase Order Information -->
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                                    <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.85rem;">
                                        <i class="fas fa-file-invoice me-2"></i>Purchase Order Information
                                    </h6>
                                    <h5 class="fw-bold mb-2" style="font-size: 1.1rem;">
                                        <a href="{{ route('purchase-orders.show', $receivingRecord->POID) }}" class="text-decoration-none">
                                            PO #{{ $receivingRecord->purchaseOrder->POID ?? 'N/A' }}
                                        </a>
                                    </h5>
                                    <p class="mb-1 text-muted" style="font-size: 0.95rem;">
                                        <i class="fas fa-building me-2"></i>{{ $receivingRecord->purchaseOrder->supplier->SupplierName ?? 'N/A' }}
                                    </p>
                                    <p class="mb-0 text-muted" style="font-size: 0.95rem;">
                                        <span class="badge bg-info">{{ $receivingRecord->purchaseOrder->Status ?? 'N/A' }}</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Receiving Details -->
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                                    <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.85rem;">
                                        <i class="fas fa-info-circle me-2"></i>Receiving Details
                                    </h6>
                                    <table class="table table-sm table-borderless mb-0" style="font-size: 0.95rem;">
                                        <tr>
                                            <td class="text-muted ps-0">Received Date</td>
                                            <td class="fw-semibold">{{ $receivingRecord->ReceivedDate->format('M d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0">Received By</td>
                                            <td class="fw-semibold">
                                                {{ $receivingRecord->receiver->full_name ?? 'N/A' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0">Recorded At</td>
                                            <td class="fw-semibold">{{ $receivingRecord->created_at->format('M d, Y h:i A') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Overall Condition -->
                            <div class="col-md-4">
                                <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                                    <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.85rem;">
                                        <i class="fas fa-check-circle me-2"></i>Overall Condition
                                    </h6>
                                    <table class="table table-sm table-borderless mb-0" style="font-size: 0.95rem;">
                                        <tr>
                                            <td class="text-muted ps-0">Condition</td>
                                            <td>
                                                @php
                                                    $conditionClass = 'secondary';
                                                    $textClass = 'text-white';
                                                    if ($receivingRecord->OverallCondition == 'Good') {
                                                        $conditionClass = 'success';
                                                        $textClass = 'text-white';
                                                    } elseif ($receivingRecord->OverallCondition == 'Damaged') {
                                                        $conditionClass = 'danger';
                                                        $textClass = 'text-white';
                                                    } elseif ($receivingRecord->OverallCondition == 'Mixed') {
                                                        $conditionClass = 'warning';
                                                        $textClass = 'text-dark';
                                                    }
                                                @endphp
                                                <span class="badge bg-{{ $conditionClass }} {{ $textClass }}">
                                                    {{ $receivingRecord->OverallCondition ?? 'N/A' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0">Total Items</td>
                                            <td class="fw-semibold">{{ $receivingRecord->items->count() }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0">Total Received</td>
                                            <td class="fw-semibold">{{ $receivingRecord->items->sum('QuantityReceived') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Received Items Table -->
                        <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.75rem;">
                            <i class="fas fa-list me-2"></i>Received Items
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-center">Qty Ordered</th>
                                        <th class="text-center">Qty Received</th>
                                        <th class="text-center">Good</th>
                                        <th class="text-center">Damaged</th>
                                        <th class="text-center">Condition</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($receivingRecord->items as $item)
                                        @php
                                            $poItem = $item->purchaseOrderItem;
                                            $inventoryItem = $poItem->inventoryItem ?? null;
                                            $resourceCatalog = $inventoryItem->resourceCatalog ?? null;
                                            $itemName = $resourceCatalog->ItemName ?? ($inventoryItem->ItemName ?? 'N/A');
                                            $itemType = $resourceCatalog->Type ?? '';
                                            $goodQuantity = $item->QuantityReceived - ($item->QuantityDamaged ?? 0);
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ $itemName }}</strong>
                                            </td>
                                            <td class="text-center">
                                                @if($inventoryItem && $inventoryItem->requiresIntegerQuantity())
                                                    {{ number_format((int) ($poItem->QuantityOrdered ?? 0), 0) }}
                                                @else
                                                    {{ number_format($poItem->QuantityOrdered ?? 0, 2) }}
                                                @endif
                                                {{ $poItem->Unit ?? '' }}
                                            </td>
                                            <td class="text-center">
                                                <strong>
                                                    @if($inventoryItem && $inventoryItem->requiresIntegerQuantity())
                                                        {{ number_format((int) $item->QuantityReceived, 0) }}
                                                    @else
                                                        {{ number_format($item->QuantityReceived, 2) }}
                                                    @endif
                                                    {{ $poItem->Unit ?? '' }}
                                                </strong>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge text-white" style="background-color: #87A96B;">
                                                    @if($inventoryItem && $inventoryItem->requiresIntegerQuantity())
                                                        {{ number_format((int) $goodQuantity, 0) }}
                                                    @else
                                                        {{ number_format($goodQuantity, 2) }}
                                                    @endif
                                                    {{ $poItem->Unit ?? '' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if(($item->QuantityDamaged ?? 0) > 0)
                                                    <span class="badge bg-danger text-white">
                                                        @if($inventoryItem && $inventoryItem->requiresIntegerQuantity())
                                                            {{ number_format((int) $item->QuantityDamaged, 0) }}
                                                        @else
                                                            {{ number_format($item->QuantityDamaged, 2) }}
                                                        @endif
                                                        {{ $poItem->Unit ?? '' }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $item->Condition == 'Good' ? 'success' : 'danger' }} text-white">
                                                    {{ $item->Condition }}
                                                </span>
                                            </td>
                                            <td>{{ $item->ItemRemarks ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="fas fa-box-open fa-2x mb-2"></i>
                                                <p class="mb-0">No items in this receiving record</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($receivingRecord->items->count() > 0)
                                    <tfoot>
                                        <tr class="fw-bold">
                                            <td>Total</td>
                                            <td class="text-center">{{ number_format($receivingRecord->items->sum(function($item) { return $item->purchaseOrderItem->QuantityOrdered ?? 0; })) }}</td>
                                            <td class="text-center">{{ number_format($receivingRecord->items->sum('QuantityReceived')) }}</td>
                                            <td class="text-center">{{ number_format($receivingRecord->items->sum(function($item) { return $item->QuantityReceived - ($item->QuantityDamaged ?? 0); })) }}</td>
                                            <td class="text-center">{{ number_format($receivingRecord->items->sum('QuantityDamaged')) }}</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>

                        @if($receivingRecord->Remarks)
                            <div class="mt-4">
                                <h6 class="text-uppercase fw-bold mb-2" style="color: #87A96B; font-size: 0.75rem;">
                                    <i class="fas fa-comment me-2"></i>Remarks
                                </h6>
                                <p class="mb-0 text-muted">{{ $receivingRecord->Remarks }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Card Footer with Buttons -->
                    <div class="card-footer bg-white py-3">
                        <div class="text-right">
                            <a href="{{ route('purchase-orders.show', $receivingRecord->POID) }}" 
                               class="btn btn-info mr-2">
                                <i class="fas fa-file-invoice"></i> View Purchase Order
                            </a>
                            @if(auth()->user()->UserTypeID == 2)
                                <form action="{{ route('receiving.destroy', $receivingRecord->ReceivingID) }}" 
                                      method="POST" 
                                      class="d-inline swal-confirm-form"
                                      data-title="Delete Receiving Record?"
                                      data-text="This will reverse the inventory updates. This action cannot be undone."
                                      data-icon="warning"
                                      data-confirm-text="Yes, Delete It">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger mr-2">
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

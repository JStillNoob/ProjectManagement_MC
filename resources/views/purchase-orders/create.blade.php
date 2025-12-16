@extends('layouts.app')

@section('content')
<div class="container-fluid">


    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif


    <form action="{{ route('purchase-orders.store') }}" method="POST" id="poForm">
        @csrf
        
        @if($inventoryRequest)
            <input type="hidden" name="RequestID" value="{{ $inventoryRequest->RequestID }}">
        @endif

        <div class="row">
            <!-- Products List -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header" style="background-color: #ffffff; border-bottom: 2px solid #87A96B;">
                        <h5 class="mb-0" style="color: #87A96B;"><i class="fas fa-shopping-cart"></i> Purchase Order Details</h5>
                    </div>
                    <div class="card-body">
                        <!-- Supplier Selection -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label"><strong>Supplier</strong> <span class="text-danger">*</span></label>
                                <select name="SupplierID" class="form-select @error('SupplierID') is-invalid @enderror" required>
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->SupplierID }}" {{ old('SupplierID') == $supplier->SupplierID ? 'selected' : '' }}>
                                            {{ $supplier->SupplierName }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('SupplierID')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><i class="fas fa-list"></i> Product List</h6>
                            @if(!$requestItems || count($requestItems) == 0)
                                <button type="button" class="btn btn-sm" id="addItemBtn"
                                        style="background-color: #87A96B !important; border: 2px solid #87A96B !important; color: white !important;">
                                    <i class="fas fa-plus"></i> Add Item
                                </button>
                            @else
                                <small class="text-muted"><i class="fas fa-info-circle"></i> Items from inventory request (cannot be removed)</small>
                            @endif
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead style="background-color: #87A96B; color: white;">
                                    <tr>
                                        <th style="width: 30%">Item</th>
                                        <th style="width: 20%">Item Type</th>
                                        <th style="width: 35%" class="text-center">Unit Quantity</th>
                                        <th style="width: 10%" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                    @if($requestItems && $requestItems->isNotEmpty())
                                        @foreach($requestItems as $index => $requestItem)
                                            @php
                                                // Use item() method (same as show page uses)
                                                $inventoryItem = $requestItem->item;
                                                $resourceCatalog = $inventoryItem ? $inventoryItem->resourceCatalog : null;
                                                
                                                // Calculate quantity needed
                                                $committed = $requestItem->CommittedQuantity ?? 0;
                                                $requested = $requestItem->QuantityRequested ?? 0;
                                                $quantityNeeded = max(0, $requested - $committed);
                                                
                                                // If quantity needed is 0 or less, use the full requested quantity
                                                if ($quantityNeeded <= 0) {
                                                    $quantityNeeded = $requested;
                                                }
                                            @endphp
                                            @if($inventoryItem && $resourceCatalog && isset($resourceCatalog->ResourceCatalogID))
                                                <tr class="item-row" data-required="true">
                                                    <td>
                                                        <input type="hidden" name="items[{{ $index }}][ItemID]" value="{{ $inventoryItem->ItemID }}">
                                                        <input type="text" class="form-control" value="{{ $resourceCatalog->ItemName ?? 'N/A' }}" readonly style="background-color: #f8f9fa;">
                                                    </td>
                                                    <td class="align-middle item-type">{{ $resourceCatalog->Type ?? '-' }}</td>
                                                    <td class="align-middle">
                                                        <div class="d-flex align-items-stretch justify-content-center gap-3">
                                                            <input type="number" name="items[{{ $index }}][QuantityOrdered]" 
                                                                   class="form-control text-center quantity-input" 
                                                                   style="width: 100px; font-weight: 500; height: 38px;"
                                                                   value="{{ $quantityNeeded }}" 
                                                                   min="1" required>
                                                            <span class="badge item-unit d-flex align-items-center" style="background-color: #87A96B; color: white; padding: 0 16px; font-size: 0.875rem; height: 38px;">{{ $resourceCatalog->Unit ?? 'N/A' }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <span class="text-muted small">Required</span>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @else
                                        <tr class="item-row">
                                            <td>
                                                <select name="items[0][ItemID]" class="form-select item-select" required>
                                                    <option value="">-- Select Item --</option>
                                                    @if($resourceCatalog && is_iterable($resourceCatalog))
                                                        @foreach($resourceCatalog as $item)
                                                            @if($item && is_object($item) && isset($item->ResourceCatalogID))
                                                                <option value="{{ $item->ResourceCatalogID }}" data-unit="{{ $item->Unit ?? '' }}" data-type="{{ $item->Type ?? '' }}">
                                                                    {{ $item->ItemName ?? 'N/A' }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </td>
                                            <td class="align-middle item-type">-</td>
                                            <td class="align-middle">
                                                <div class="d-flex align-items-stretch justify-content-center gap-3">
                                                    <input type="number" name="items[0][QuantityOrdered]" 
                                                           class="form-control text-center quantity-input" 
                                                           style="width: 100px; font-weight: 500; height: 38px;"
                                                           value="1" min="1" required>
                                                    <span class="badge item-unit d-flex align-items-center" style="background-color: #87A96B; color: white; padding: 0 16px; font-size: 0.875rem; height: 38px;">-</span>
                                                </div>
                                            </td>
                                            <td class="text-center align-middle">
                                                <button type="button" class="btn btn-sm btn-link text-danger remove-item p-0">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header" style="background-color: #ffffff; border-bottom: 2px solid #87A96B;">
                        <h5 class="mb-0" style="color: #87A96B;"><i class="fas fa-clipboard-list"></i> Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2 p-2" style="background-color: #f8f9fa; border-radius: 5px;">
                                <span><i class="fas fa-boxes text-muted"></i> Total Items:</span>
                                <strong class="text-primary" id="summaryItems">0</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2 p-2" style="background-color: #f8f9fa; border-radius: 5px;">
                                <span><i class="fas fa-sort-numeric-up text-muted"></i> Total Quantity:</span>
                                <strong class="text-primary" id="summaryQuantity">0</strong>
                            </div>
                        </div>
                        <hr>
                        <button type="submit" class="btn w-100 mb-2" 
                                style="background-color: #87A96B !important; border: 2px solid #87A96B !important; color: white !important;">
                            <i class="fas fa-file-pdf"></i> Create Purchase Order
                        </button>
                        <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
let itemIndex = {{ count($requestItems) > 0 ? count($requestItems) : 1 }};

// Add new item row
document.getElementById('addItemBtn').addEventListener('click', function() {
    const tbody = document.getElementById('itemsTableBody');
    const newRow = createItemRow(itemIndex);
    tbody.insertAdjacentHTML('beforeend', newRow);
    itemIndex++;
    updateSummary();
});

// Remove item row (only for non-required items)
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-item')) {
        const row = e.target.closest('.item-row');
        // Check if this is a required item from inventory request
        if (row && row.dataset.required === 'true') {
            alert('This item is required from the inventory request and cannot be removed.');
            return;
        }
        if (document.querySelectorAll('.item-row').length > 1) {
            row.remove();
            updateSummary();
        } else {
            alert('At least one item is required');
        }
    }
});

// Update summary when quantity changes
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('quantity-input')) {
        updateSummary();
    }
});

// Update unit and type when item is selected
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('item-select')) {
        const row = e.target.closest('.item-row');
        const selectedOption = e.target.options[e.target.selectedIndex];
        const unit = selectedOption.getAttribute('data-unit') || '-';
        const type = selectedOption.getAttribute('data-type') || '-';
        const unitBadge = row.querySelector('.item-unit');
        const typeCell = row.querySelector('.item-type');
        if (unitBadge) {
            unitBadge.textContent = unit;
        }
        if (typeCell) {
            typeCell.textContent = type;
        }
    }
});

function createItemRow(index) {
    return `
        <tr class="item-row">
            <td>
                <select name="items[\${index}][ItemID]" class="form-select item-select" required>
                    <option value="">-- Select Item --</option>
                    @if($resourceCatalog && is_iterable($resourceCatalog))
                        @foreach($resourceCatalog as $item)
                            @if($item && is_object($item) && isset($item->ResourceCatalogID))
                                <option value="{{ $item->ResourceCatalogID }}" data-unit="{{ $item->Unit ?? '' }}" data-type="{{ $item->Type ?? '' }}">
                                    {{ $item->ItemName ?? 'N/A' }}
                                </option>
                            @endif
                        @endforeach
                    @endif
                </select>
            </td>
            <td class="align-middle item-type">-</td>
            <td class="align-middle">
                <div class="d-flex align-items-stretch justify-content-center gap-3">
                    <input type="number" name="items[\${index}][QuantityOrdered]" 
                           class="form-control text-center quantity-input" 
                           style="width: 100px; font-weight: 500; height: 38px;"
                           value="1" min="1" required>
                    <span class="badge item-unit d-flex align-items-center" style="background-color: #87A96B; color: white; padding: 0 16px; font-size: 0.875rem; height: 38px;">-</span>
                </div>
            </td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-sm btn-link text-danger remove-item p-0">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>
    `;
}

function updateSummary() {
    const rows = document.querySelectorAll('.item-row');
    let totalItems = rows.length;
    let totalQuantity = 0;

    rows.forEach(row => {
        const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
        totalQuantity += quantity;
    });

    // Update UI
    document.getElementById('summaryItems').textContent = totalItems;
    document.getElementById('summaryQuantity').textContent = totalQuantity;
}

// Initial update
document.addEventListener('DOMContentLoaded', function() {
    updateSummary();
});
</script>
@endpush
@endsection

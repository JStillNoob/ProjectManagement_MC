@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2>Edit Purchase Order</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('purchase-orders.index') }}">Purchase Orders</a></li>
                <li class="breadcrumb-item"><a href="{{ route('purchase-orders.show', $purchaseOrder->POID) }}">PO #{{ $purchaseOrder->POID }}</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('purchase-orders.update', $purchaseOrder->POID) }}" method="POST" id="poForm">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Purchase Order Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">PO Number</label>
                                <input type="text" class="form-control" value="PO #{{ $purchaseOrder->POID }}" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Supplier <span class="text-danger">*</span></label>
                                <select name="SupplierID" class="form-select @error('SupplierID') is-invalid @enderror" required>
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->SupplierID }}" 
                                            {{ (old('SupplierID') ?? $purchaseOrder->SupplierID) == $supplier->SupplierID ? 'selected' : '' }}>
                                            {{ $supplier->SupplierName }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('SupplierID')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Order Date</label>
                                <input type="date" name="OrderDate" class="form-control @error('OrderDate') is-invalid @enderror" 
                                    value="{{ $purchaseOrder->OrderDate->format('Y-m-d') }}" readonly style="background-color: #e9ecef;">
                                @error('OrderDate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Items</h5>
                        <button type="button" class="btn btn-sm btn-primary" id="addItemBtn">
                            <i class="fas fa-plus"></i> Add Item
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th width="35%">Item</th>
                                        <th width="15%">Quantity</th>
                                        <th width="15%">Unit Price</th>
                                        <th width="15%">Total</th>
                                        <th width="15%">Specifications</th>
                                        <th width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                    @foreach($purchaseOrder->items as $index => $item)
                                        <tr class="item-row">
                                            <td>
                                                <select name="items[{{ $index }}][ItemID]" class="form-select item-select" required>
                                                    <option value="">Select Item</option>
                                                    @foreach($inventoryItems as $invItem)
                                                        <option value="{{ $invItem->ItemID }}" 
                                                            data-unit="{{ $invItem->Unit }}" 
                                                            data-price="{{ $invItem->UnitPrice }}"
                                                            {{ $item->ItemID == $invItem->ItemID ? 'selected' : '' }}>
                                                            {{ $invItem->ItemName }} ({{ $invItem->ItemCode }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $index }}][QuantityOrdered]" 
                                                    class="form-control quantity-input" 
                                                    value="{{ $item->QuantityOrdered }}" 
                                                    min="1" required>
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $index }}][UnitPrice]" 
                                                    class="form-control unit-price-input" 
                                                    value="{{ $item->UnitPrice }}" 
                                                    step="0.01" min="0" required>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control total-price" readonly value="{{ $item->TotalPrice }}">
                                            </td>
                                            <td>
                                                <input type="text" name="items[{{ $index }}][Specifications]" 
                                                    class="form-control" 
                                                    value="{{ $item->Specifications }}" 
                                                    placeholder="Optional">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger remove-item">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
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
                            <strong id="totalItems">0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Quantity:</span>
                            <strong id="totalQuantity">0</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <h5>Total Amount:</h5>
                            <h5 class="text-primary" id="grandTotal">₱0.00</h5>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg"
                            style="background-color: #87A96B !important; border: 2px solid #87A96B !important; color: white !important;">
                        <i class="fas fa-save"></i> Update Purchase Order
                    </button>
                    <a href="{{ route('purchase-orders.show', $purchaseOrder->POID) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
let itemIndex = {{ count($purchaseOrder->items) }};

// Add new item row
document.getElementById('addItemBtn').addEventListener('click', function() {
    const tbody = document.getElementById('itemsTableBody');
    const newRow = createItemRow(itemIndex);
    tbody.insertAdjacentHTML('beforeend', newRow);
    itemIndex++;
    updateSummary();
});

// Remove item row
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-item')) {
        const row = e.target.closest('.item-row');
        if (document.querySelectorAll('.item-row').length > 1) {
            row.remove();
            updateSummary();
        } else {
            alert('At least one item is required');
        }
    }
});

// Update total when quantity or price changes
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('quantity-input') || e.target.classList.contains('unit-price-input')) {
        const row = e.target.closest('.item-row');
        updateRowTotal(row);
        updateSummary();
    }
});

// Auto-fill price when item selected
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('item-select')) {
        const selectedOption = e.target.options[e.target.selectedIndex];
        const price = selectedOption.dataset.price || 0;
        const row = e.target.closest('.item-row');
        const priceInput = row.querySelector('.unit-price-input');
        priceInput.value = price;
        updateRowTotal(row);
        updateSummary();
    }
});

function createItemRow(index) {
    return `
        <tr class="item-row">
            <td>
                <select name="items[${index}][ItemID]" class="form-select item-select" required>
                    <option value="">Select Item</option>
                    @foreach($inventoryItems as $item)
                        <option value="{{ $item->ItemID }}" data-unit="{{ $item->Unit }}" data-price="{{ $item->UnitPrice }}">
                            {{ $item->ItemName }} ({{ $item->ItemCode }})
                        </option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="items[${index}][QuantityOrdered]" class="form-control quantity-input" 
                    value="1" min="1" required>
            </td>
            <td>
                <input type="number" name="items[${index}][UnitPrice]" class="form-control unit-price-input" 
                    value="0" step="0.01" min="0" required>
            </td>
            <td>
                <input type="text" class="form-control total-price" readonly value="0.00">
            </td>
            <td>
                <input type="text" name="items[${index}][Specifications]" class="form-control" placeholder="Optional">
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-item">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
}

function updateRowTotal(row) {
    const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
    const unitPrice = parseFloat(row.querySelector('.unit-price-input').value) || 0;
    const total = quantity * unitPrice;
    row.querySelector('.total-price').value = total.toFixed(2);
}

function updateSummary() {
    const rows = document.querySelectorAll('.item-row');
    let totalItems = rows.length;
    let totalQuantity = 0;
    let grandTotal = 0;

    rows.forEach(row => {
        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const total = parseFloat(row.querySelector('.total-price').value) || 0;
        totalQuantity += quantity;
        grandTotal += total;
    });

    document.getElementById('totalItems').textContent = totalItems;
    document.getElementById('totalQuantity').textContent = totalQuantity;
    document.getElementById('grandTotal').textContent = '₱' + grandTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

// Initial update
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.item-row').forEach(row => {
        updateRowTotal(row);
    });
    updateSummary();
});
</script>
@endpush
@endsection

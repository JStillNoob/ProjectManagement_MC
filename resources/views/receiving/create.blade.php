@extends('layouts.app')

@section('title', 'Receive Items')
@section('page-title', 'Receive Items')

@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <form action="{{ route('receiving.store') }}" method="POST" enctype="multipart/form-data"
                    id="receivingForm">
                    @csrf
                    <input type="hidden" name="POID" value="{{ $purchaseOrder->POID }}">

                    <div class="card">
                        <div class="card-header" style="background-color: #87A96B;">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title mb-0" style="color: white; font-size: 1.25rem;">
                                    <i class="fas fa-box-open me-2"></i>
                                    Purchase Order #{{ $purchaseOrder->POID }}
                                </h3>
                                <span class="badge bg-info">{{ $purchaseOrder->Status }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <!-- 3 column layout: Supplier | Receiving Details | Summary -->
                            <div class="row mb-4">
                                <!-- Supplier Information -->
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                                        <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.85rem;">
                                            <i class="fas fa-building me-2"></i>Supplier Information
                                        </h6>
                                        <h5 class="fw-bold mb-2">{{ $purchaseOrder->supplier->SupplierName ?? 'N/A' }}</h5>
                                        <table class="table table-sm table-borderless mb-0" style="font-size: 0.95rem;">
                                            <tr>
                                                <td class="text-muted ps-0">Order Date</td>
                                                <td class="fw-semibold">{{ $purchaseOrder->OrderDate->format('M d, Y') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted ps-0">Status</td>
                                                <td><span class="badge bg-info">{{ $purchaseOrder->Status }}</span></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <!-- Receiving Details Form -->
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                                        <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.85rem;">
                                            <i class="fas fa-clipboard-list me-2"></i>Receiving Details
                                        </h6>
                                        <div class="mb-2">
                                            <label class="form-label small mb-1">Received Date <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="ReceivedDate"
                                                class="form-control form-control-sm @error('ReceivedDate') is-invalid @enderror"
                                                value="{{ date('Y-m-d') }}" readonly
                                                style="background-color: #e9ecef; cursor: not-allowed;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Summary -->
                                <div class="col-md-4">
                                    <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                                        <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.85rem;">
                                            <i class="fas fa-chart-bar me-2"></i>Summary
                                        </h6>
                                        <table class="table table-sm table-borderless mb-0" style="font-size: 0.95rem;">
                                            <tr>
                                                <td class="text-muted ps-0">Total Items</td>
                                                <td class="fw-semibold text-end" id="totalItems">
                                                    {{ $purchaseOrder->items->count() }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted ps-0">Total Receiving</td>
                                                <td class="fw-semibold text-end" id="totalQuantity">0</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted ps-0">Good Condition</td>
                                                <td class="fw-semibold text-end text-success" id="goodQuantity">0</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted ps-0">Damaged</td>
                                                <td class="fw-semibold text-end text-danger" id="damagedQuantity">0</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Items to Receive -->
                            <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.85rem;">
                                <i class="fas fa-boxes me-2"></i>Items to Receive
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="25%">Item</th>
                                            <th width="10%" class="text-center">Ordered</th>
                                            <th width="10%" class="text-center">Already Received</th>
                                            <th width="10%" class="text-center">Remaining</th>
                                            <th width="12%" class="text-center">Qty Receiving <span
                                                    class="text-danger">*</span></th>
                                            <th width="10%">Condition</th>
                                            <th width="10%" class="text-center">Qty Damaged</th>
                                            <th width="13%">Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($purchaseOrder->items as $index => $item)
                                            @php
                                                $remaining = $item->QuantityOrdered - $item->QuantityReceived;
                                                $itemName = 'N/A';
                                                if ($item->inventoryItem && $item->inventoryItem->resourceCatalog) {
                                                    $itemName = $item->inventoryItem->resourceCatalog->ItemName;
                                                }
                                            @endphp
                                            @if($remaining > 0)
                                                <tr class="item-row">
                                                    <td>
                                                        <input type="hidden" name="items[{{ $index }}][POItemID]"
                                                            value="{{ $item->POItemID }}">
                                                        <strong>{{ $itemName }}</strong>
                                                        @if($item->Specifications)
                                                            <br><small class="text-info">{{ $item->Specifications }}</small>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">{{ $item->QuantityOrdered }} {{ $item->Unit }}</td>
                                                    <td class="text-center">{{ $item->QuantityReceived }} {{ $item->Unit }}</td>
                                                    <td class="text-center">
                                                        <strong class="text-primary">{{ $remaining }} {{ $item->Unit }}</strong>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="items[{{ $index }}][QuantityReceived]"
                                                            class="form-control text-center qty-receiving"
                                                            value="{{ old("items.$index.QuantityReceived", $remaining) }}" min="1"
                                                            max="{{ $remaining }}" data-remaining="{{ $remaining }}" required>
                                                    </td>
                                                    <td>
                                                        <select name="items[{{ $index }}][Condition]"
                                                            class="form-control form-control-sm condition-select">
                                                            <option value="Good" {{ old("items.$index.Condition") == 'Good' ? 'selected' : '' }}>Good</option>
                                                            <option value="Damaged" {{ old("items.$index.Condition") == 'Damaged' ? 'selected' : '' }}>Damaged</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="items[{{ $index }}][QuantityDamaged]"
                                                            class="form-control form-control-sm text-center qty-damaged"
                                                            value="{{ old("items.$index.QuantityDamaged", 0) }}" min="0">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="items[{{ $index }}][ItemRemarks]"
                                                            class="form-control form-control-sm" placeholder="Optional">
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="card-footer bg-white">
                            <div class="text-right">
                                <a href="{{ route('purchase-orders.show', $purchaseOrder->POID) }}"
                                    class="btn btn-secondary mr-2">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-success"
                                    style="background-color: #87A96B !important; border-color: #87A96B !important;">
                                    <i class="fas fa-check"></i> Confirm Receiving
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Update summary when quantities change
            document.addEventListener('input', function (e) {
                if (e.target.classList.contains('qty-receiving') ||
                    e.target.classList.contains('qty-damaged') ||
                    e.target.classList.contains('condition-select')) {
                    updateSummary();
                    validateDamagedQuantity(e.target);
                }
            });

            // When condition changes to "Damaged", auto-fill damaged quantity
            document.addEventListener('change', function (e) {
                if (e.target.classList.contains('condition-select')) {
                    const row = e.target.closest('.item-row');
                    const qtyReceiving = row.querySelector('.qty-receiving');
                    const qtyDamaged = row.querySelector('.qty-damaged');

                    if (e.target.value === 'Damaged') {
                        qtyDamaged.value = qtyReceiving.value;
                    } else {
                        qtyDamaged.value = 0;
                    }
                    updateSummary();
                }
            });

            function validateDamagedQuantity(element) {
                const row = element.closest('.item-row');
                if (!row) return;

                const qtyReceiving = parseFloat(row.querySelector('.qty-receiving').value) || 0;
                const qtyDamaged = parseFloat(row.querySelector('.qty-damaged').value) || 0;
                const damagedInput = row.querySelector('.qty-damaged');

                if (qtyDamaged > qtyReceiving) {
                    damagedInput.value = qtyReceiving;
                    alert('Damaged quantity cannot exceed quantity receiving');
                }
            }

            function updateSummary() {
                const rows = document.querySelectorAll('.item-row');
                let totalQuantity = 0;
                let goodQuantity = 0;
                let damagedQuantity = 0;

                rows.forEach(row => {
                    const receiving = parseFloat(row.querySelector('.qty-receiving').value) || 0;
                    const damaged = parseFloat(row.querySelector('.qty-damaged').value) || 0;

                    totalQuantity += receiving;
                    damagedQuantity += damaged;
                    goodQuantity += (receiving - damaged);
                });

                document.getElementById('totalQuantity').textContent = totalQuantity;
                document.getElementById('goodQuantity').textContent = goodQuantity;
                document.getElementById('damagedQuantity').textContent = damagedQuantity;
            }

            // Form validation before submit
            document.getElementById('receivingForm').addEventListener('submit', function (e) {
                const rows = document.querySelectorAll('.item-row');
                let hasItems = false;

                rows.forEach(row => {
                    const receiving = parseFloat(row.querySelector('.qty-receiving').value) || 0;
                    const remaining = parseFloat(row.querySelector('.qty-receiving').dataset.remaining) || 0;

                    if (receiving > 0) {
                        hasItems = true;
                    }

                    if (receiving > remaining) {
                        e.preventDefault();
                        alert(`Quantity receiving cannot exceed remaining quantity (${remaining}) for one of the items`);
                        return false;
                    }
                });

                if (!hasItems) {
                    e.preventDefault();
                    alert('Please enter at least one item to receive');
                    return false;
                }
            });

            // Initial summary update
            document.addEventListener('DOMContentLoaded', function () {
                updateSummary();
            });
        </script>
    @endpush
@endsection
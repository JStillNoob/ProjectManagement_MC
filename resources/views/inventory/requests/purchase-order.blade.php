@extends('layouts.app')

@section('title', 'Create Purchase Order')
@section('page-title', 'Create Purchase Order')

@section('content')
    <div class="container-fluid">


        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-3">
                    <div class="card-header" style="background-color: #ffffff; border-bottom: 2px solid #87A96B;">
                        <h5 class="mb-0" style="color: #87A96B;"><i class="fas fa-info-circle"></i> Request Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-uppercase text-muted mb-2">Project Details</h6>
                                <p class="mb-1"><strong>Project:</strong> {{ $inventoryRequest->project->ProjectName }}</p>
                                <p class="mb-1"><strong>Milestone:</strong>
                                    {{ optional($inventoryRequest->milestone)->milestone_name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-uppercase text-muted mb-2">Requester</h6>
                                <p class="mb-1"><strong>Name:</strong> {{ $inventoryRequest->employee->full_name }}</p>
                                <p class="mb-1"><strong>Request Type:</strong> {{ $inventoryRequest->RequestType }}</p>
                            </div>
                        </div>


                    </div>
                </div>

                <div class="card">
                    <div class="card-header" style="background-color: #ffffff; border-bottom: 2px solid #87A96B;">
                        <h5 class="mb-0" style="color: #87A96B;"><i class="fas fa-shopping-cart"></i> Items to Order</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('inventory.requests.save-purchase-order', $inventoryRequest) }}"
                            method="POST" id="purchaseOrderForm">
                            @csrf
                            <div class="mb-3">
                                <label for="global-supplier" class="form-label fw-bold">Apply Supplier to All Items
                                    (Optional)</label>
                                <div class="input-group">
                                    <select id="global-supplier" class="form-select" style="height: 38px;">
                                        <option value="" selected>-- Select supplier for all items --</option>
                                        @foreach($suppliers ?? [] as $supplier)
                                            <option value="{{ $supplier->SupplierID }}">{{ $supplier->SupplierName }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-outline-secondary" onclick="applyToAll()">
                                        <i class="fas fa-check"></i> Apply to All
                                    </button>
                                </div>
                                <small class="text-muted">Select a supplier and click "Apply to All" to set the same
                                    supplier for all items, or leave blank to choose different suppliers below.</small>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead style="background-color: #87A96B; color: white;">
                                        <tr>
                                            <th style="width: 5%">#</th>
                                            <th style="width: 35%">Material</th>
                                            <th style="width: 12%" class="text-center">Requested</th>
                                            <th style="width: 12%" class="text-center">Available</th>
                                            <th style="width: 12%" class="text-center">To Order</th>
                                            <th style="width: 24%">Preferred Supplier</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($shortageItems as $index => $item)
                                            @php
                                                $available = $item->item ? $item->item->available_stock : 0;
                                                $toOrder = max($item->QuantityRequested - $available, 0);
                                            @endphp
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>
                                                    <strong>{{ $item->item->resourceCatalog->ItemName ?? 'Item removed' }}</strong>
                                                    <div class="text-muted small">UNIT:
                                                        {{ $item->UnitOfMeasure ?? ($item->item->resourceCatalog->Unit ?? 'UNITS') }}
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    @if($item->item && $item->item->requiresIntegerQuantity())
                                                        {{ number_format((int) $item->QuantityRequested, 0) }}
                                                    @else
                                                        {{ number_format($item->QuantityRequested, 2) }}
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($item->item && $item->item->requiresIntegerQuantity())
                                                        {{ number_format((int) $available, 0) }}
                                                    @else
                                                        {{ number_format($available, 2) }}
                                                    @endif
                                                </td>
                                                <td class="text-center"><strong class="text-danger">
                                                    @if($item->item && $item->item->requiresIntegerQuantity())
                                                        {{ number_format((int) $toOrder, 0) }}
                                                    @else
                                                        {{ number_format($toOrder, 2) }}
                                                    @endif
                                                </strong></td>
                                                <td>
                                                    <label for="supplier-{{ $index }}" class="visually-hidden">Supplier for
                                                        {{ $item->item->resourceCatalog->ItemName ?? 'Item' }}</label>
                                                    <select class="form-select supplier-input" style="height: 38px;"
                                                        id="supplier-{{ $index }}" name="suppliers[{{ $index }}]"
                                                        data-item-id="{{ $item->item->ItemID }}" data-quantity="{{ $toOrder }}"
                                                        data-unit="{{ $item->UnitOfMeasure ?? ($item->item->resourceCatalog->Unit ?? 'UNITS') }}"
                                                        required
                                                        aria-label="Select supplier for {{ $item->item->resourceCatalog->ItemName ?? 'item' }}">
                                                        <option value="" selected>-- Select supplier --</option>
                                                        @foreach($suppliers ?? [] as $supplier)
                                                            <option value="{{ $supplier->SupplierID }}">
                                                                {{ $supplier->SupplierName }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="text-end mt-4">
                                <button type="button" class="btn btn-secondary" onclick="window.print()">
                                    <i class="fas fa-print"></i> Print Draft
                                </button>
                                <button type="submit" class="btn"
                                    style="background-color: #87A96B !important; border: 2px solid #87A96B !important; color: white !important;">
                                    <i class="fas fa-save"></i> Save & Generate PDF
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function applyToAll() {
            const globalSupplier = document.getElementById('global-supplier');
            const supplierInputs = document.querySelectorAll('.supplier-input');

            if (globalSupplier.value) {
                supplierInputs.forEach(input => {
                    input.value = globalSupplier.value;
                });

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Supplier applied to all items!',
                    confirmButtonColor: '#87A96B',
                    timer: 2000,
                    timerProgressBar: true
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Supplier Selected',
                    text: 'Please select a supplier first.',
                    confirmButtonColor: '#87A96B'
                });
            }
        }

        // Handle form submission
        document.getElementById('purchaseOrderForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const supplierInputs = document.querySelectorAll('.supplier-input');
            const items = [];
            let hasError = false;

            supplierInputs.forEach((input, index) => {
                if (!input.value) {
                    hasError = true;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                    items.push({
                        item_id: input.dataset.itemId,
                        supplier_id: input.value,
                        quantity: input.dataset.quantity,
                        unit: input.dataset.unit
                    });
                }
            });

            if (hasError) {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Suppliers',
                    text: 'Please select a supplier for all items!',
                    confirmButtonColor: '#87A96B'
                });
                return;
            }

            // Add items to form as hidden inputs
            const form = this;
            items.forEach((item, index) => {
                Object.keys(item).forEach(key => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `items[${index}][${key}]`;
                    input.value = item[key];
                    form.appendChild(input);
                });
            });

            // Submit form
            form.submit();
        });
    </script>
@endsection
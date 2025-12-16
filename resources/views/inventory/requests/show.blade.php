@extends('layouts.app')

@section('title', 'Inventory Request Details')
@section('page-title', 'Inventory Request Details')

@section('content')
    @php
        $currentUser = Auth::user();
        $isAdmin = !$currentUser->EmployeeID || in_array($currentUser->UserTypeID, [1, 2]);
    @endphp
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-clipboard-list mr-2"></i>Request Details
                        </h3>
                        <a href="{{ route('inventory.requests.index') }}" class="btn btn-secondary btn-sm ml-auto">
                            <i class="fas fa-arrow-left"></i> Back to Requests
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="35%">Request ID</th>
                                        <td class="font-weight-bold">
                                            REQ-{{ str_pad($inventoryRequest->RequestID, 4, '0', STR_PAD_LEFT) }}
                                            @if($inventoryRequest->IsAdditionalRequest)
                                                <span class="badge badge-warning ml-2" title="Additional Request - Items beyond required quantities">
                                                    <i class="fas fa-plus-circle mr-1"></i>Additional Request
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Request Date</th>
                                        <td>{{ $inventoryRequest->created_at->format('M d, Y g:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Project</th>
                                        <td class="text-uppercase font-weight-bold">
                                            {{ $inventoryRequest->project->ProjectName }}</td>
                                    </tr>
                                    <tr>
                                        <th>Milestone</th>
                                        <td>{{ optional($inventoryRequest->milestone)->milestone_name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Requested By</th>
                                        <td>{{ optional($inventoryRequest->employee)->full_name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Request Type</th>
                                        <td>
                                            @php
                                                $requestTypeClass = [
                                                    'Material' => 'badge-info',
                                                    'Equipment' => 'badge-primary',
                                                    'Mixed' => 'badge-secondary',
                                                ][$inventoryRequest->RequestType] ?? 'badge-info';
                                            @endphp
                                            <span class="badge {{ $requestTypeClass }}">
                                                {{ $inventoryRequest->RequestType }}
                                            </span>
                                        </td>
                                    </tr>
                                    @if($inventoryRequest->Reason)
                                        <tr>
                                            <th>Reason</th>
                                            <td>{{ $inventoryRequest->Reason }}</td>
                                        </tr>
                                    @endif
                                </table>

                                {{-- Alerts kept in Body for visibility --}}
                                @if($isAdmin)
                                    @if(in_array($inventoryRequest->Status, ['Pending', 'Pending - To Order', 'Ordered']))
                                        @if($hasInsufficientStock && $inventoryRequest->Status !== 'Ordered')
                                            <div class="alert alert-warning mb-3">
                                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                                <strong>Action Required:</strong> Some items have insufficient stock. You must create a
                                                Purchase Order before approving.
                                            </div>
                                        @elseif($inventoryRequest->Status === 'Ordered')
                                            <div class="alert alert-info mb-3">
                                                <i class="fas fa-info-circle mr-2"></i>
                                                <strong>Purchase Order Created:</strong> You can now approve this request.
                                            </div>
                                        @endif
                                    @elseif($inventoryRequest->Status === 'Approved')
                                        <div class="alert alert-info mb-3">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            This request has been approved. Stock has been reserved. Click below to issue items.
                                        </div>
                                    @elseif($inventoryRequest->Status === 'Fulfilled')
                                        <div class="alert alert-success mb-0 mt-3">
                                            <i class="fas fa-check mr-2"></i>
                                            This request has been fulfilled. All items have been issued from inventory.
                                        </div>
                                    @endif
                                @endif
                            </div>
                            <div class="col-lg-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="35%">Status</th>
                                        @php
                                            $statusClass = [
                                                'Pending' => 'badge-warning',
                                                'Pending - To Order' => 'badge-warning text-dark',
                                                'Approved' => 'badge-primary',
                                                'Rejected' => 'badge-danger',
                                                'Fulfilled' => 'badge-success',
                                                'Needs PO' => 'badge-secondary',
                                            ][$inventoryRequest->Status] ?? 'badge-info';
                                        @endphp
                                        <td><span class="badge {{ $statusClass }}">{{ $inventoryRequest->Status }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Approved/Rejected By</th>
                                        <td>{{ optional($inventoryRequest->approver)->FirstName ? $inventoryRequest->approver->FirstName . ' ' . $inventoryRequest->approver->LastName : '—' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Decision Date</th>
                                        <td>{{ $inventoryRequest->ApprovedAt ? $inventoryRequest->ApprovedAt->format('M d, Y g:i A') : '—' }}
                                        </td>
                                    </tr>
                                    @if($inventoryRequest->RejectionReason)
                                        <tr>
                                            <th>Rejection Reason</th>
                                            <td class="text-danger">{{ $inventoryRequest->RejectionReason }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        @if($inventoryRequest->Status === 'Pending - To Order')
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                This request contains items that are below available stock. Please create a Purchase Order
                                before approving.
                                <ul class="mb-0 mt-2 pl-3">
                                    @foreach($shortageItems as $line)
                                        <li>
                                            {{ $line->item->resourceCatalog->ItemName ?? 'Item removed' }} — Requested:
                                            {{ number_format($line->QuantityRequested, 2) }}
                                            {{ $line->UnitOfMeasure ?? $line->item->resourceCatalog->Unit ?? 'units' }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Requested Items</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Item</th>
                                                <th class="text-center">Type</th>
                                                <th class="text-center">Unit</th>
                                                <th class="text-center">Quantity Requested</th>
                                                @if($isAdmin && in_array($inventoryRequest->Status, ['Pending', 'Pending - To Order']))
                                                    <th class="text-center">Available Stock</th>
                                                @endif
                                                <th class="text-center">Stock Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($inventoryRequest->items as $line)
                                                @php
                                                    $itemType = $line->item->resourceCatalog->Type ?? 'Materials';
                                                    $stockInfo = $stockVerification[$line->InventoryItemID] ?? null;
                                                    
                                                    // For approved/ordered/fulfilled requests, don't show "Low Stock"
                                                    // because stock has been reserved or the request is being processed
                                                    $isProcessed = in_array($inventoryRequest->Status, ['Approved', 'Ordered', 'Fulfilled']);
                                                    
                                                    if ($isProcessed) {
                                                        // Don't show "Low Stock" for processed requests
                                                        $needsPurchase = false;
                                                    } else {
                                                        // For pending requests, check if stock is sufficient
                                                        $needsPurchase = $stockInfo ? !$stockInfo['sufficient'] : $line->NeedsPurchase;
                                                    }
                                                @endphp
                                                <tr
                                                    class="{{ $needsPurchase ? 'table-warning' : '' }}">
                                                    <td>{{ $line->item->resourceCatalog->ItemName ?? 'Item removed' }}</td>
                                                    <td class="text-center">
                                                        <span
                                                            class="badge badge-{{ $itemType === 'Materials' ? 'info' : 'primary' }}">
                                                            {{ $itemType }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $line->UnitOfMeasure ?? $line->item->resourceCatalog->Unit ?? 'units' }}
                                                    </td>
                                                    <td class="text-center font-weight-bold">
                                                        {{ number_format($line->QuantityRequested, 2) }}</td>
                                                    @if($isAdmin && in_array($inventoryRequest->Status, ['Pending', 'Pending - To Order']))
                                                        <td class="text-center">
                                                            @if($stockInfo)
                                                                <span
                                                                    class="{{ $stockInfo['sufficient'] ? 'text-success' : 'text-danger font-weight-bold' }}">
                                                                    {{ number_format($stockInfo['available'], 2) }}
                                                                    {{ $stockInfo['unit'] }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted">—</span>
                                                            @endif
                                                        </td>
                                                    @endif
                                                    <td class="text-center">
                                                        @if($needsPurchase)
                                                            <span class="badge badge-warning text-dark">
                                                                <i class="fas fa-exclamation-triangle mr-1"></i>Low Stock
                                                            </span>
                                                        @else
                                                            <span class="badge badge-success">In Stock</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Reject Modal --}}
                        @if($isAdmin && in_array($inventoryRequest->Status, ['Pending', 'Pending - To Order', 'Ordered']))
                            <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Reject Request</h5>
                                            <button type="button" class="close text-white" data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>
                                        <form action="{{ route('inventory.requests.reject', $inventoryRequest) }}"
                                            method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="RejectionReason">Rejection Reason <span
                                                            class="text-danger">*</span></label>
                                                    <textarea class="form-control" id="RejectionReason" name="RejectionReason"
                                                        rows="4" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Reject Request</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="card-footer bg-white">
                        <div class="d-flex flex-wrap justify-content-end">
                            @if($isAdmin)
                                @if(in_array($inventoryRequest->Status, ['Pending', 'Pending - To Order', 'Ordered']))
                                    @if($inventoryRequest->Status === 'Ordered')
                                        @php $purchaseOrder = $inventoryRequest->purchaseOrders->first(); @endphp
                                        @if($purchaseOrder)
                                            <a href="{{ route('purchase-orders.show', $purchaseOrder->POID) }}"
                                                class="btn btn-info mr-2 mb-2">
                                                <i class="fas fa-file-invoice-dollar mr-1"></i> View Purchase Order
                                            </a>
                                        @endif
                                        <form action="{{ route('inventory.requests.approve', $inventoryRequest) }}" method="POST"
                                            class="d-inline mr-2 mb-2 swal-confirm-form"
                                            data-title="Approve Request?"
                                            data-text="Approve this request? Stock will be reserved."
                                            data-icon="question"
                                            data-confirm-text="Yes, Approve">
                                            @csrf
                                            <button type="submit" class="btn text-white"
                                                style="background-color: #87A96B; border-color: #87A96B;">
                                                <i class="fas fa-check mr-1"></i> Approve Request
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-secondary mb-2" data-toggle="modal"
                                            data-target="#rejectModal">
                                            <i class="fas fa-times mr-1"></i> Reject Request
                                        </button>
                                    @elseif($hasInsufficientStock)
                                        @php $purchaseOrder = $inventoryRequest->purchaseOrders->first(); @endphp
                                        @if($purchaseOrder)
                                            <a href="{{ route('purchase-orders.show', $purchaseOrder->POID) }}"
                                                class="btn btn-info mr-2 mb-2">
                                                <i class="fas fa-file-invoice-dollar mr-1"></i> View Purchase Order
                                            </a>
                                            <form action="{{ route('inventory.requests.approve', $inventoryRequest) }}" method="POST"
                                                class="d-inline mr-2 mb-2 swal-confirm-form"
                                                data-title="Approve Request?"
                                                data-text="Approve this request? Stock will be reserved."
                                                data-icon="question"
                                                data-confirm-text="Yes, Approve">
                                                @csrf
                                                <button type="submit" class="btn text-white"
                                                    style="background-color: #87A96B; border-color: #87A96B;">
                                                    <i class="fas fa-check mr-1"></i> Approve Request
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-secondary mb-2" data-toggle="modal"
                                                data-target="#rejectModal">
                                                <i class="fas fa-times mr-1"></i> Reject Request
                                            </button>
                                        @else
                                            <a href="{{ route('purchase-orders.create', ['request_id' => $inventoryRequest->RequestID]) }}"
                                                class="btn btn-warning mr-2 mb-2">
                                                <i class="fas fa-file-invoice-dollar mr-1"></i> Create Purchase Order
                                            </a>
                                            <button type="button" class="btn btn-secondary mb-2" data-toggle="modal"
                                                data-target="#rejectModal">
                                                <i class="fas fa-times mr-1"></i> Reject Request
                                            </button>
                                        @endif
                                    @else
                                        <form action="{{ route('inventory.requests.approve', $inventoryRequest) }}" method="POST"
                                            class="d-inline mr-2 mb-2 swal-confirm-form"
                                            data-title="Approve Request?"
                                            data-text="Approve this request? Stock will be reserved."
                                            data-icon="question"
                                            data-confirm-text="Yes, Approve">
                                            @csrf
                                            <button type="submit" class="btn text-white"
                                                style="background-color: #87A96B; border-color: #87A96B;">
                                                <i class="fas fa-check mr-1"></i> Approve Request
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-secondary mb-2" data-toggle="modal"
                                            data-target="#rejectModal">
                                            <i class="fas fa-times mr-1"></i> Reject Request
                                        </button>
                                    @endif
                                @elseif($inventoryRequest->Status === 'Approved')
                                    <a href="{{ route('issuance.create', ['request_id' => $inventoryRequest->RequestID]) }}"
                                        class="btn text-white" style="background-color: #87A96B; border-color: #87A96B;">
                                        <i class="fas fa-truck-loading mr-1"></i> Issue Items
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
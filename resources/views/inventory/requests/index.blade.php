@extends('layouts.app')

@section('title', 'Inventory Requests')
@section('page-title', 'Inventory Requests')

@php
    $hasSingleProject = $userProjects instanceof \Illuminate\Support\Collection ? $userProjects->count() === 1 : (count($userProjects) === 1);
    $singleProject = $hasSingleProject ? ($userProjects instanceof \Illuminate\Support\Collection ? $userProjects->first() : $userProjects[0]) : null;
    $isAdmin = !Auth::user()->EmployeeID || in_array(Auth::user()->UserTypeID, [1, 2]); // Admin/Production Head or Admin/GM
    $canSubmitRequest = Auth::user()->EmployeeID && !in_array(Auth::user()->UserTypeID, [1, 2]); // Only employees/foremen can submit
@endphp

@section('content')
    <div class="container-fluid">

    {{-- Admin Inventory Requests Table --}}
    @if($isAdmin)
        <div class="row mb-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-success rounded-circle d-flex align-items-center justify-content-center mr-2"
                                    style="width: 36px; height: 36px; background-color: #87A96B !important;">
                                    <i class="fas fa-clipboard-list text-white"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 font-weight-bold text-dark">Inventory Requests</h5>
                                    <small class="text-muted">Review and approve inventory requests from project teams</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($requests->count())
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Request ID</th>
                                            <th>Project</th>
                                            <th>Milestone</th>
                                            <th>Requested By</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($requests as $request)
                                            <tr>
                                                <td><strong>#{{ $request->RequestID }}</strong></td>
                                                <td>{{ $request->project->ProjectName ?? 'N/A' }}</td>
                                                <td>{{ $request->milestone->milestone_name ?? 'N/A' }}</td>
                                                <td>{{ $request->employee->FirstName ?? '' }} {{ $request->employee->LastName ?? '' }}</td>
                                                <td><span class="badge badge-info">{{ $request->RequestType }}</span></td>
                                                <td>
                                                    @if($request->Status === 'Pending')
                                                        <span class="badge badge-warning">Pending</span>
                                                    @elseif($request->Status === 'Approved')
                                                        <span class="badge badge-success">Approved</span>
                                                    @elseif($request->Status === 'Rejected')
                                                        <span class="badge badge-danger">Rejected</span>
                                                    @elseif($request->Status === 'Pending - To Order')
                                                        <span class="badge badge-warning">To Order</span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ $request->Status }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $request->created_at->format('M d, Y') }}</td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('inventory.requests.show', $request) }}"
                                                            class="btn btn-outline-info" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if(in_array($request->Status, ['Pending', 'Pending - To Order']))
                                                            <form action="{{ route('inventory.requests.approve', $request) }}" method="POST"
                                                                style="display:inline;" onsubmit="return confirm('Are you sure you want to approve this request?');">
                                                                @csrf
                                                                <button type="submit" class="btn btn-outline-success" title="Approve">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                        @if($request->Status === 'Pending - To Order')
                                                            <a href="{{ route('inventory.requests.purchase-order', $request) }}"
                                                                class="btn btn-outline-warning" title="Create Purchase Order">
                                                                <i class="fas fa-shopping-cart"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $requests->links() }}
                            </div>
                        @else
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <h5>No inventory requests found</h5>
                                <p>Inventory requests from project teams will appear here</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($canSubmitRequest)
        <div class="modal fade" id="createRequestModal" tabindex="-1" role="dialog" aria-labelledby="createRequestModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header text-white" style="background:#7fb069;">
                        <h5 class="modal-title">
                            <i class="fas fa-cart-plus mr-2"></i>Bulk Inventory Requisition
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('inventory.requests.store') }}" method="POST" id="bulkRequestForm">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-uppercase small">Project</label>
                                        @if($hasSingleProject)
                                            <input type="hidden" id="projectSelect" name="ProjectID"
                                                value="{{ $singleProject->ProjectID }}">
                                            <div class="form-control-plaintext font-weight-bold">{{ $singleProject->ProjectName }}
                                            </div>
                                        @else
                                            <select class="form-control" id="projectSelect" name="ProjectID" required>
                                                <option value="">Select Project</option>
                                                @foreach($userProjects as $project)
                                                    <option value="{{ $project->ProjectID }}">{{ $project->ProjectName }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                        <small class="text-muted">Engineers can choose any assigned project. Foreman requests
                                            are auto-bound to their project.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-uppercase small">Milestone</label>
                                        <select class="form-control" id="milestoneSelect" name="MilestoneID" required disabled>
                                            <option value="">Select Milestone</option>
                                        </select>
                                        <small class="text-muted">Only milestones for the selected project are shown.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-uppercase small">Reason / Purpose</label>
                                        <textarea class="form-control" name="Reason" rows="2"
                                            placeholder="Provide context for this requisition (optional)"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="border rounded p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="mb-0 font-weight-bold text-uppercase">Add Items</h6>
                                        <small class="text-muted">Available stock is calculated as Physical Qty minus Committed
                                            Qty. You can add both materials and equipment.</small>
                                    </div>
                                </div>

                                <div class="form-row align-items-end">
                                    <div class="col-md-6 mb-2">
                                        <label class="font-weight-bold small text-uppercase">Item</label>
                                        <select class="form-control" id="itemSelect">
                                            <option value="">Select Item</option>
                                        </select>
                                        <div class="mt-1 text-muted" id="availableStockText"></div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="font-weight-bold small text-uppercase">Quantity</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="itemQuantity" min="0.01" step="0.01"
                                                placeholder="0.00">
                                            <div class="input-group-append">
                                                <span class="input-group-text bg-light" id="quantityUnit">—</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <button type="button" class="btn btn-outline-success btn-block" id="addToCartBtn">
                                            <i class="fas fa-plus-circle mr-1"></i>Add to Cart
                                        </button>
                                    </div>
                                </div>
                                <div class="alert alert-danger d-none mt-2" id="cartError"></div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="cartTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width:50px;">#</th>
                                            <th>Item</th>
                                            <th class="text-center" style="width:100px;">Type</th>
                                            <th class="text-center" style="width:150px;">Available</th>
                                            <th class="text-center" style="width:150px;">Qty Requested</th>
                                            <th class="text-center" style="width:120px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cartTableBody">
                                        <tr class="cart-empty-row">
                                            <td colspan="6" class="text-center text-muted py-3">
                                                <i class="fas fa-info-circle mr-1"></i>No items added yet.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="mr-auto text-muted small">
                                <i class="fas fa-shield-alt mr-1"></i>Request is routed to Admin/GM for approval.
                            </div>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-success" id="submitBulkRequest" disabled>
                                <i class="fas fa-paper-plane"></i> Submit Request
                            </button>
                        </div>
                        <div id="cartHiddenInputs"></div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Milestone Required Items for Foreman Assigned Project --}}
    @if(Auth::user()->UserTypeID == 3 && isset($foremanProject) && $foremanProject->milestones && $foremanProject->milestones->count())
        <div class="row mt-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0">
                        <div class="d-flex align-items-center">
                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center mr-2"
                                style="width: 36px; height: 36px; background-color: #87A96B !important;">
                                <i class="fas fa-boxes text-white"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold text-dark">Milestone Required Items</h5>
                                <small class="text-muted">Grouped by milestone</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($foremanProject->milestones as $milestone)
                                @php
                                    $items = $milestone->requiredItems;
                                    $displayItems = $items->take(4);
                                    $remaining = $items->count() - $displayItems->count();
                                    $status = strtolower($milestone->status ?? 'Pending');
                                    $statusClass = $status === 'completed' ? 'success' : ($status === 'in progress' ? 'warning' : 'secondary');
                                @endphp
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100 border-0 shadow-sm milestone-req-card"
                                        style="border-radius: 14px; cursor: pointer;"
                                        data-milestone-id="{{ $milestone->milestone_id }}"
                                        data-milestone-name="{{ $milestone->milestone_name }}">
                                        <div class="card-body pb-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                @php $targetDate = $milestone->formatted_target_date ?? null; @endphp
                                                <div>
                                                    <h6 class="mb-0 font-weight-bold text-dark">
                                                        {{ $milestone->milestone_name ?? 'Milestone' }}</h6>
                                                    @if($targetDate && $targetDate !== 'N/A')
                                                        <small class="text-muted">{{ $targetDate }}</small>
                                                    @endif
                                                </div>
                                                <span
                                                    class="badge milestone-req-status badge-{{ $statusClass }} text-capitalize">{{ $milestone->status ?? 'Pending' }}</span>
                                            </div>
                                            @if($displayItems->count())
                                                <div class="list-group list-group-flush">
                                                    @foreach($displayItems as $req)
                                                        <div
                                                            class="list-group-item px-0 py-1 d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <div class="font-weight-semibold text-dark">
                                                                    {{ $req->item->resourceCatalog->ItemName ?? '' }}</div>
                                                                <small class="text-muted">Qty:
                                                                    {{ number_format($req->estimated_quantity, 2) }}
                                                                    {{ $req->unit ?? ($req->item->resourceCatalog->Unit ?? '') }}</small>
                                                            </div>
                                                            <span
                                                                class="badge badge-light border text-muted">{{ $req->item->resourceCatalog->Type ?? '' }}</span>
                                                        </div>
                                                    @endforeach
                                                    @if($remaining > 0)
                                                        <div class="list-group-item px-0 py-1 text-muted small">+{{ $remaining }} more
                                                            item(s)</div>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="text-muted small">No required items defined for this milestone.</div>
                                            @endif
                                        </div>
                                        <div class="card-footer bg-white border-0 pt-0">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <span class="badge badge-light text-muted">Required Items</span>
                                                    <span class="text-muted small">{{ $items->count() }} total</span>
                                                </div>
                                                <span class="text-muted small"><i class="fas fa-edit mr-1"></i>Click card to
                                                    edit</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal for Milestone Request --}}
    <div class="modal fade" id="milestoneRequestModal" tabindex="-1" role="dialog"
        aria-labelledby="milestoneRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:#87A96B;">
                    <h5 class="modal-title" id="milestoneRequestModalLabel">
                        <i class="fas fa-clipboard-list mr-2"></i>Request Items for Milestone: <span
                            id="milestoneName"></span>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('inventory.requests.store') }}" method="POST" id="milestoneRequestForm">
                    @csrf
                    <input type="hidden" name="ProjectID" id="modalProjectID">
                    <input type="hidden" name="MilestoneID" id="modalMilestoneID">
                    <div class="modal-body">
                        <div id="milestoneRequestError" class="alert alert-danger" style="display:none;"></div>
                        <div class="mb-3">
                            <label class="font-weight-bold">Required Items for this Milestone</label>
                            <div id="requiredItemsList"></div>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="font-weight-bold">Add Additional Items</label>
                            <div class="form-row align-items-end">
                                <div class="col-md-6 mb-2">
                                    <label class="small">Item</label>
                                    <select class="form-control" id="additionalItemSelect">
                                        <option value="">Select Item</option>
                                        @foreach($materials as $item)
                                            <option value="{{ $item['id'] }}" data-unit="{{ $item['unit'] }}"
                                                data-type="Materials">{{ $item['name'] }} ({{ $item['unit'] }})</option>
                                        @endforeach
                                        @foreach($equipment as $item)
                                            <option value="{{ $item['id'] }}" data-unit="{{ $item['unit'] }}"
                                                data-type="Equipment">{{ $item['name'] }} ({{ $item['unit'] }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="small">Quantity</label>
                                    <input type="number" class="form-control" id="additionalItemQty" min="0.01" step="0.01"
                                        placeholder="0.00">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <button type="button" class="btn btn-outline-success btn-block"
                                        id="addAdditionalItemBtn">
                                        <i class="fas fa-plus-circle mr-1"></i>Add Item
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive mt-2">
                                <table class="table table-bordered" id="additionalItemsTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Item</th>
                                            <th>Type</th>
                                            <th>Quantity</th>
                                            <th>Unit</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane"></i> Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const milestonesEndpoint = '/api/projects';
                const itemsEndpoint = "{{ route('inventory.requests.items') }}";
                const cartTableBody = document.getElementById('cartTableBody');
                const cartError = document.getElementById('cartError');
                const submitBtn = document.getElementById('submitBulkRequest');
                const hiddenInputs = document.getElementById('cartHiddenInputs');
                const itemSelect = document.getElementById('itemSelect');
                const availableText = document.getElementById('availableStockText');
                const quantityInput = document.getElementById('itemQuantity');
                const projectSelect = document.getElementById('projectSelect');
                const milestoneSelect = document.getElementById('milestoneSelect');
                const addBtn = document.getElementById('addToCartBtn');
                const cartItems = [];
                let cachedMaterials = @json($materials);
                let cachedEquipment = @json($equipment);
                let allCachedItems = [];

                const hasSingleProject = {{ $hasSingleProject ? 'true' : 'false' }};

                function toggleSubmitButton() {
                    submitBtn.disabled = cartItems.length === 0;
                }

                function renderCart() {
                    cartTableBody.innerHTML = '';

                    if (!cartItems.length) {
                        cartTableBody.innerHTML = `
                            <tr class="cart-empty-row">
                                <td colspan="6" class="text-center text-muted py-3">
                                    <i class="fas fa-info-circle mr-1"></i>No items added yet.
                                </td>
                            </tr>`;
                        hiddenInputs.innerHTML = '';
                        toggleSubmitButton();
                        return;
                    }

                    hiddenInputs.innerHTML = '';
                    cartItems.forEach((item, index) => {
                        // Recalculate needs_purchase flag
                        item.needs_purchase = item.quantity > item.available;
                        const needsPurchase = item.needs_purchase;
                        const row = document.createElement('tr');
                        if (needsPurchase) {
                            row.classList.add('table-warning');
                        }
                        row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>
                                <div class="font-weight-bold">${item.name}</div>
                                <small class="text-muted">Unit: ${item.unit}</small>
                                ${needsPurchase ? '<div class="text-warning font-weight-bold small mt-1"><i class="fas fa-exclamation-triangle mr-1"></i>Low Stock - Will Require Purchasing</div>' : ''}
                            </td>
                            <td class="text-center">
                                <span class="badge badge-${item.type === 'Materials' ? 'info' : 'primary'}">${item.type}</span>
                            </td>
                            <td class="text-center">${item.available.toFixed(2)} ${item.unit}</td>
                            <td class="text-center font-weight-bold">${item.quantity.toFixed(2)}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-link text-danger p-0" data-index="${index}">
                                    <i class="fas fa-trash-alt"></i> Remove
                                </button>
                            </td>
                        `;
                        cartTableBody.appendChild(row);

                        hiddenInputs.insertAdjacentHTML('beforeend', `
                            <input type="hidden" name="items[${index}][inventory_item_id]" value="${item.id}">
                            <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
                            <input type="hidden" name="items[${index}][needs_purchase]" value="${needsPurchase ? 1 : 0}">
                        `);
                    });

                    cartTableBody.querySelectorAll('button[data-index]').forEach(btn => {
                        btn.addEventListener('click', function () {
                            const removeIndex = parseInt(this.getAttribute('data-index'));
                            cartItems.splice(removeIndex, 1);
                            renderCart();
                        });
                    });

                    toggleSubmitButton();
                }

                function showCartError(message) {
                    cartError.textContent = message;
                    cartError.classList.remove('d-none');
                }

                function clearCartError() {
                    cartError.classList.add('d-none');
                    cartError.textContent = '';
                }

                function populateItemSelect(items) {
                    itemSelect.innerHTML = '<option value="">Select Item</option>';
                    items.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.dataset.available = item.available_stock;
                        option.dataset.unit = item.unit;
                        option.dataset.name = item.name;
                        option.dataset.type = item.type || 'Materials';
                        option.textContent = `${item.name} — Available: ${parseFloat(item.available_stock).toFixed(2)} ${item.unit}`;
                        itemSelect.appendChild(option);
                    });
                }

                function loadItems(typeFilter = 'all') {
                    if (typeFilter === 'all') {
                        // Combine materials and equipment
                        const allItems = [];
                        if (cachedMaterials && cachedMaterials.length) {
                            cachedMaterials.forEach(item => {
                                allItems.push({ ...item, type: 'Materials' });
                            });
                        }
                        if (cachedEquipment && cachedEquipment.length) {
                            cachedEquipment.forEach(item => {
                                allItems.push({ ...item, type: 'Equipment' });
                            });
                        }
                        if (allItems.length) {
                            allCachedItems = allItems;
                            populateItemSelect(allItems);
                            return;
                        }
                        // If not cached, fetch both
                        Promise.all([
                            fetch(itemsEndpoint + '?type=Materials').then(r => r.json()),
                            fetch(itemsEndpoint + '?type=Equipment').then(r => r.json())
                        ]).then(([materials, equipment]) => {
                            cachedMaterials = materials;
                            cachedEquipment = equipment;
                            const allItems = [
                                ...materials.map(item => ({ ...item, type: 'Materials' })),
                                ...equipment.map(item => ({ ...item, type: 'Equipment' }))
                            ];
                            allCachedItems = allItems;
                            populateItemSelect(allItems);
                        }).catch(() => showCartError('Unable to load items list.'));
                    } else {
                        // Load specific type
                        const cached = typeFilter === 'Materials' ? cachedMaterials : cachedEquipment;
                        if (cached && cached.length) {
                            const items = cached.map(item => ({ ...item, type: typeFilter }));
                            populateItemSelect(items);
                            return;
                        }
                        fetch(itemsEndpoint + '?type=' + typeFilter)
                            .then(response => response.json())
                            .then(data => {
                                if (typeFilter === 'Materials') {
                                    cachedMaterials = data;
                                } else {
                                    cachedEquipment = data;
                                }
                                const items = data.map(item => ({ ...item, type: typeFilter }));
                                populateItemSelect(items);
                            })
                            .catch(() => showCartError('Unable to load items list.'));
                    }
                }

                function loadMaterials() {
                    loadItems('Materials');
                }

                function loadMilestones(projectId) {
                    milestoneSelect.innerHTML = '<option value="">Select Milestone</option>';
                    milestoneSelect.disabled = true;

                    if (!projectId) {
                        return;
                    }

                    fetch(`${milestonesEndpoint}/${projectId}/milestones`)
                        .then(response => response.json())
                        .then(data => {
                            if (!Array.isArray(data) || !data.length) {
                                milestoneSelect.innerHTML = '<option value="">No milestones found</option>';
                                return;
                            }

                            // Filter milestones: only show "In Progress" status for foreman
                            const isForeman = {{ Auth::user()->UserTypeID == 3 ? 'true' : 'false' }};
                            const filteredData = isForeman
                                ? data.filter(ms => ms.status === 'In Progress')
                                : data;

                            if (filteredData.length === 0) {
                                milestoneSelect.innerHTML = '<option value="">No in-progress milestones available</option>';
                                return;
                            }

                            filteredData.forEach(ms => {
                                const option = document.createElement('option');
                                option.value = ms.milestone_id;
                                option.textContent = ms.milestone_name;
                                milestoneSelect.appendChild(option);
                            });
                            milestoneSelect.disabled = false;
                        })
                        .catch(() => {
                            milestoneSelect.innerHTML = '<option value="">Error loading milestones</option>';
                        });
                }

                // When milestone changes, load its required items
                milestoneSelect.addEventListener('change', function () {
                    const milestoneId = this.value;
                    if (!milestoneId) {
                        loadItems('all'); // Load all items if no milestone selected
                        return;
                    }

                    // Load milestone-specific required items
                    fetch(`/api/milestones/${milestoneId}/required-items`)
                        .then(response => response.json())
                        .then(data => {
                            if (!data || data.length === 0) {
                                // If no required items defined, show all items
                                loadItems('all');
                                return;
                            }

                            // Populate with only required items
                            allCachedItems = data.map(item => ({
                                ...item,
                                type: item.ItemType
                            }));
                            populateItemSelect(allCachedItems);
                        })
                        .catch(() => {
                            showCartError('Unable to load milestone required items.');
                            loadItems('all'); // Fallback to all items
                        });
                });

                const quantityUnit = document.getElementById('quantityUnit');

                itemSelect.addEventListener('change', function () {
                    clearCartError();
                    const selected = this.options[this.selectedIndex];
                    const available = selected ? parseFloat(selected.dataset.available) : null;
                    const unit = selected ? selected.dataset.unit : '';

                    // Update quantity unit display
                    if (unit) {
                        quantityUnit.textContent = unit;
                        quantityUnit.classList.remove('text-muted');
                    } else {
                        quantityUnit.textContent = '—';
                        quantityUnit.classList.add('text-muted');
                    }

                    if (available !== null && !isNaN(available)) {
                        availableText.textContent = `Available: ${available.toFixed(2)} ${unit}`;
                    } else {
                        availableText.textContent = '';
                    }
                });

                addBtn.addEventListener('click', function () {
                    clearCartError();

                    const selectedOption = itemSelect.options[itemSelect.selectedIndex];
                    if (!selectedOption || !selectedOption.value) {
                        showCartError('Please select an item.');
                        return;
                    }

                    const available = parseFloat(selectedOption.dataset.available);
                    const unit = selectedOption.dataset.unit;
                    const name = selectedOption.dataset.name;
                    const quantity = parseFloat(quantityInput.value);

                    if (!quantity || quantity <= 0) {
                        showCartError('Please enter a valid quantity.');
                        return;
                    }

                    const itemType = selectedOption.dataset.type || 'Materials';
                    const existing = cartItems.find(item => item.id === parseInt(selectedOption.value, 10));
                    if (existing) {
                        existing.quantity += quantity;
                        existing.needs_purchase = existing.quantity > existing.available;
                    } else {
                        cartItems.push({
                            id: parseInt(selectedOption.value, 10),
                            name,
                            unit,
                            available,
                            quantity,
                            type: itemType,
                            needs_purchase: quantity > available
                        });
                    }

                    quantityInput.value = '';
                    itemSelect.selectedIndex = 0;
                    availableText.textContent = '';
                    quantityUnit.textContent = '—';
                    quantityUnit.classList.add('text-muted');
                    renderCart();
                });

                if (projectSelect) {
                    projectSelect.addEventListener('change', function () {
                        loadMilestones(this.value);
                    });
                }

                $('#createRequestModal').on('shown.bs.modal', function () {
                    loadItems('all');
                    if (hasSingleProject && projectSelect) {
                        loadMilestones(projectSelect.value);
                    }
                }).on('hidden.bs.modal', function () {
                    document.getElementById('bulkRequestForm').reset();
                    cartItems.length = 0;
                    renderCart();
                    milestoneSelect.innerHTML = '<option value="">Select Milestone</option>';
                    milestoneSelect.disabled = true;
                    availableText.textContent = '';
                    quantityUnit.textContent = '—';
                    quantityUnit.classList.add('text-muted');
                    clearCartError();
                });

                document.getElementById('bulkRequestForm').addEventListener('submit', function (e) {
                    if (!cartItems.length) {
                        e.preventDefault();
                        showCartError('Please add at least one item to the request.');
                    }
                });

                // Milestone request modal
                let additionalItems = [];
                let requiredItems = [];

                // Open modal and populate with milestone data
                $('.milestone-req-card').on('click', function () {
                    const milestoneId = $(this).data('milestone-id');
                    const milestoneName = $(this).data('milestone-name');
                    const projectId = @json($foremanProject ? $foremanProject->ProjectID : null);
                    
                    $('#milestoneName').text(milestoneName);
                    $('#modalProjectID').val(projectId);
                    $('#modalMilestoneID').val(milestoneId);
                    
                    // Show loading state
                    $('#requiredItemsList').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading required items...</div>');
                    
                    // Fetch required items from API
                    requiredItems = [];
                    fetch(`/api/milestones/${milestoneId}/required-items`)
                        .then(response => response.json())
                        .then(data => {
                            console.log('Raw API data:', data);
                            if (data && data.length > 0) {
                                requiredItems = data.map(item => ({
                                    id: item.ItemID || '',
                                    resource_catalog_id: item.ResourceCatalogID || '',
                                    name: item.ItemName || '',
                                    quantity: item.estimated_quantity || 0,
                                    unit: item.Unit || '',
                                    type: item.ItemType || ''
                                }));
                                console.log('Mapped requiredItems:', requiredItems);
                                
                                // Check for items without inventory IDs (they'll be auto-created)
                                const itemsWithoutId = requiredItems.filter(item => !item.id && item.resource_catalog_id);
                                if (itemsWithoutId.length > 0) {
                                    console.info('Items to be auto-created in inventory:', itemsWithoutId);
                                }
                            }
                            renderRequiredItems();
                        })
                        .catch(error => {
                            console.error('Error fetching required items:', error);
                            $('#requiredItemsList').html('<div class="text-danger small">Error loading required items.</div>');
                        });
                    
                    additionalItems = [];
                    renderAdditionalItems();
                    $('#milestoneRequestModal').modal('show');
                });

                function renderRequiredItems() {
                    let html = '';
                    if (!requiredItems.length) {
                        html = '<div class="text-muted small">No required items for this milestone.</div>';
                    } else {
                        html = '<ul class="list-group">';
                        requiredItems.forEach(item => {
                            html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>${item.name}</span>
                                <span class="text-muted small">Qty: ${item.quantity} ${item.unit} <span class="badge badge-light ml-2">${item.type}</span></span>
                            </li>`;
                        });
                        html += '</ul>';
                    }
                    $('#requiredItemsList').html(html);
                }

                function renderAdditionalItems() {
                    const tbody = $('#additionalItemsTable tbody');
                    tbody.empty();
                    if (!additionalItems.length) {
                        tbody.append('<tr><td colspan="5" class="text-center text-muted">No additional items added.</td></tr>');
                    } else {
                        additionalItems.forEach((item, idx) => {
                            tbody.append(`<tr>
                                <td>${item.name}</td>
                                <td>${item.type}</td>
                                <td>${item.quantity}</td>
                                <td>${item.unit}</td>
                                <td><button type="button" class="btn btn-link text-danger p-0" data-idx="${idx}"><i class="fas fa-trash-alt"></i> Remove</button></td>
                            </tr>`);
                        });
                    }
                }

                $('#addAdditionalItemBtn').on('click', function () {
                    const select = $('#additionalItemSelect');
                    const selected = select.find('option:selected');
                    const id = select.val();
                    const name = selected.text();
                    const type = selected.data('type');
                    const unit = selected.data('unit');
                    const qty = parseFloat($('#additionalItemQty').val());
                    if (!id || !qty || qty <= 0) return;
                    additionalItems.push({ id, name, type, unit, quantity: qty });
                    renderAdditionalItems();
                    $('#additionalItemQty').val('');
                    select.val('');
                });

                $('#additionalItemsTable').on('click', 'button[data-idx]', function () {
                    const idx = $(this).data('idx');
                    additionalItems.splice(idx, 1);
                    renderAdditionalItems();
                });

                // On submit, build complete items array (required + additional)
                $('#milestoneRequestForm').on('submit', function (e) {
                    e.preventDefault();
                    
                    // Hide previous errors
                    $('#milestoneRequestError').hide();
                    
                    let itemsData = [];
                    
                    // Add required items from milestone
                    requiredItems.forEach(item => {
                        // Include item if it has inventory_item_id OR resource_catalog_id
                        if (item.id || item.resource_catalog_id) {
                            itemsData.push({
                                inventory_item_id: item.id || null,
                                resource_catalog_id: item.resource_catalog_id || null,
                                quantity: item.quantity,
                                needs_purchase: item.id ? 0 : 1  // Auto-mark as needs_purchase if no inventory item exists
                            });
                        }
                    });
                    
                    // Add additional items (optional)
                    additionalItems.forEach(item => {
                        itemsData.push({
                            inventory_item_id: item.id,
                            quantity: item.quantity,
                            needs_purchase: 0
                        });
                    });
                    
                    // Validate that at least one item is being requested
                    if (itemsData.length === 0) {
                        $('#milestoneRequestError').text('Cannot submit request. This milestone has no required items defined. Please add at least one item.').show();
                        return false;
                    }
                    
                    const projectId = $('#modalProjectID').val();
                    const milestoneId = $('#modalMilestoneID').val();
                    
                    // Debug log
                    console.log('Submitting request:', {
                        ProjectID: projectId,
                        MilestoneID: milestoneId,
                        items: itemsData
                    });
                    
                    // Disable submit button
                    const $submitBtn = $(this).find('button[type="submit"]');
                    $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Submitting...');
                    
                    // Submit via AJAX to catch errors
                    $.ajax({
                        url: $(this).attr('action'),
                        method: 'POST',
                        data: {
                            _token: $('input[name="_token"]').val(),
                            ProjectID: projectId,
                            MilestoneID: milestoneId,
                            items: itemsData
                        },
                        success: function(response) {
                            console.log('Success:', response);
                            $('#milestoneRequestModal').modal('hide');
                            location.reload();
                        },
                        error: function(xhr) {
                            console.error('Error:', xhr);
                            let errorMsg = 'Failed to submit request. ';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg += xhr.responseJSON.message;
                            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                                errorMsg += Object.values(xhr.responseJSON.errors).flat().join(', ');
                            } else {
                                errorMsg += 'Please try again.';
                            }
                            $('#milestoneRequestError').text(errorMsg).show();
                            $submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Submit Request');
                        }
                    });
                });

                renderCart();
                loadItems('all');
                if (hasSingleProject && projectSelect) {
                    loadMilestones(projectSelect.value);
                }
            });
        </script>
    @endpush
@endsection
@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('issuance.store') }}" method="POST" id="issuanceForm">
            @csrf

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <!-- Card Header -->
                        <div class="card-header" style="background-color: #87A96B;">
                            <div class="row align-items-center">
                                <div class="col-auto mb-3 mb-md-0">
                                    <a href="{{ route('issuance.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i> Back to List
                                    </a>
                                </div>
                                <div class="col">
                                    <h3 class="card-title mb-0" style="color: white; font-size: 1.25rem;">
                                        <i class="fas fa-truck-loading me-2"></i>
                                        New Issuance Record
                                    </h3>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Info Section -->
                            <div class="row mb-4">
                                <!-- Issuance Details -->
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                                        <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.85rem;">
                                            <i class="fas fa-info-circle me-2"></i>Issuance Details
                                        </h6>
                                        
                                        <div class="mb-3">
                                            <label for="IssuanceDate" class="form-label small text-muted mb-1">Issuance Date <span class="text-danger">*</span></label>
                                            <input type="date" name="IssuanceDate" id="IssuanceDate" class="form-control"
                                                value="{{ old('IssuanceDate', date('Y-m-d')) }}" required readonly style="background-color: #e9ecef;">
                                            @error('IssuanceDate')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="ReceivedBy" class="form-label small text-muted mb-1">Received By <span class="text-danger">*</span></label>
                                            @php
                                                $defaultReceivedBy = null;
                                                if (isset($inventoryRequest) && $inventoryRequest && $inventoryRequest->employee) {
                                                    $defaultReceivedBy = $inventoryRequest->employee->id;
                                                }
                                            @endphp
                                            <select name="ReceivedBy" id="ReceivedBy" class="form-select" required>
                                                <option value="">Select Employee</option>
                                                @foreach($employees as $employee)
                                                    <option value="{{ $employee->id }}" {{ old('ReceivedBy', $defaultReceivedBy) == $employee->id ? 'selected' : '' }}>
                                                        {{ $employee->FullName }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('ReceivedBy')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-0">
                                            <label for="Notes" class="form-label small text-muted mb-1">Notes</label>
                                            <textarea name="Notes" id="Notes" class="form-control" rows="2" placeholder="Optional notes...">{{ old('Notes') }}</textarea>
                                            @error('Notes')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Project & Milestone -->
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                                        <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.85rem;">
                                            <i class="fas fa-project-diagram me-2"></i>Project Details
                                        </h6>
                                        
                                        @if(isset($inventoryRequest) && $inventoryRequest)
                                            {{-- Pre-filled from approved request --}}
                                            <input type="hidden" name="ProjectID" value="{{ $inventoryRequest->ProjectID }}">
                                            <input type="hidden" name="MilestoneID" value="{{ $inventoryRequest->MilestoneID }}">
                                            <table class="table table-sm table-borderless mb-0">
                                                <tr>
                                                    <td class="text-muted ps-0">Project</td>
                                                    <td class="fw-bold">{{ $inventoryRequest->project->ProjectName ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted ps-0">Milestone</td>
                                                    <td class="fw-semibold">{{ $inventoryRequest->milestone->milestone_name ?? 'N/A' }}</td>
                                                </tr>
                                            </table>
                                        @else
                                            <div class="mb-3">
                                                <label for="ProjectID" class="form-label small text-muted mb-1">Project <span class="text-danger">*</span></label>
                                                <select name="ProjectID" id="ProjectID" class="form-select" required>
                                                    <option value="">Select Project</option>
                                                    @foreach($projects as $project)
                                                        <option value="{{ $project->ProjectID }}" {{ old('ProjectID', request('project_id')) == $project->ProjectID ? 'selected' : '' }}>
                                                            {{ $project->ProjectName }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('ProjectID')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-0">
                                                <label for="MilestoneID" class="form-label small text-muted mb-1">Milestone</label>
                                                <select name="MilestoneID" id="MilestoneID" class="form-select">
                                                    <option value="">Select Milestone (optional)</option>
                                                </select>
                                                @error('MilestoneID')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Linked Request -->
                                <div class="col-md-4">
                                    <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                                        <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.85rem;">
                                            <i class="fas fa-link me-2"></i>Linked Request
                                        </h6>
                                        
                                        @if(isset($inventoryRequest) && $inventoryRequest)
                                            {{-- Pre-selected from approved request --}}
                                            <input type="hidden" name="RequestID" value="{{ $inventoryRequest->RequestID }}">
                                            <table class="table table-sm table-borderless mb-0">
                                                <tr>
                                                    <td class="text-muted ps-0">Request ID</td>
                                                    <td class="fw-bold">REQ-{{ str_pad($inventoryRequest->RequestID, 4, '0', STR_PAD_LEFT) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted ps-0">Requested By</td>
                                                    <td class="fw-semibold">{{ $inventoryRequest->employee->full_name ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted ps-0">Status</td>
                                                    <td><span class="badge bg-success">{{ $inventoryRequest->Status }}</span></td>
                                                </tr>
                                            </table>
                                        @else
                                            <div class="mb-0">
                                                <label for="RequestID" class="form-label small text-muted mb-1">Request (Optional)</label>
                                                <select name="RequestID" id="RequestID" class="form-select">
                                                    <option value="">Select Request (optional)</option>
                                                    @foreach($requests as $request)
                                                        <option value="{{ $request->RequestID }}" 
                                                            data-project="{{ $request->ProjectID }}"
                                                            data-milestone="{{ $request->MilestoneID }}"
                                                            {{ old('RequestID', request('request_id')) == $request->RequestID ? 'selected' : '' }}>
                                                            REQ-{{ str_pad($request->RequestID, 4, '0', STR_PAD_LEFT) }} -
                                                            {{ $request->project->ProjectName ?? 'N/A' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('RequestID')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted mt-2 d-block">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Selecting a request will auto-fill project and milestone.
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Items Table -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-uppercase fw-bold mb-0" style="color: #87A96B; font-size: 0.85rem;">
                                    <i class="fas fa-boxes me-2"></i>Items to Issue
                                </h6>
                                @if(!isset($inventoryRequest) || !$inventoryRequest)
                                    <button type="button" class="btn btn-sm text-white" style="background-color: #87A96B;" onclick="addItemRow()">
                                        <i class="fas fa-plus me-1"></i> Add Item
                                    </button>
                                @endif
                            </div>

                            @if(isset($inventoryRequest) && $inventoryRequest)
                                {{-- Pre-populated items from the request --}}
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="itemsTable">
                                        <thead>
                                            <tr>
                                                <th style="width: 35%;">Item</th>
                                                <th class="text-center" style="width: 15%;">Type</th>
                                                <th class="text-center" style="width: 15%;">Requested Qty</th>
                                                <th class="text-center" style="width: 10%;">Unit</th>
                                                <th class="text-center" style="width: 15%;">Available</th>
                                                <th class="text-center" style="width: 10%;">Qty to Issue</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($inventoryRequest->items as $index => $reqItem)
                                                @php
                                                    $item = $reqItem->item;
                                                    $catalog = $item->resourceCatalog ?? null;
                                                    $available = $item->AvailableQuantity ?? 0;
                                                    $qtyToIssue = min($reqItem->QuantityRequested, $available);
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <strong>{{ $catalog->ItemName ?? 'Unknown Item' }}</strong>
                                                        <input type="hidden" name="items[{{ $index }}][ItemID]" value="{{ $item->ItemID }}">
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-{{ ($catalog->Type ?? 'Materials') === 'Equipment' ? 'info' : 'primary' }}">
                                                            {{ $catalog->Type ?? 'Materials' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">{{ number_format($reqItem->QuantityRequested, 2) }}</td>
                                                    <td class="text-center">{{ $catalog->Unit ?? 'unit' }}</td>
                                                    <td class="text-center">
                                                        <span class="{{ $available >= $reqItem->QuantityRequested ? 'text-success' : 'text-warning' }}">
                                                            {{ number_format($available, 2) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <input type="number" 
                                                               name="items[{{ $index }}][Quantity]" 
                                                               class="form-control text-center" 
                                                               value="{{ $qtyToIssue }}"
                                                               min="0.01" 
                                                               max="{{ $available }}"
                                                               step="0.01" 
                                                               required>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                {{-- Manual item selection --}}
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="itemsTable">
                                        <thead>
                                            <tr>
                                                <th style="width: 40%;">Item</th>
                                                <th class="text-center" style="width: 15%;">Available</th>
                                                <th class="text-center" style="width: 15%;">Unit</th>
                                                <th class="text-center" style="width: 20%;">Quantity</th>
                                                <th class="text-center" style="width: 10%;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemsTableBody">
                                            <!-- Item rows will be added here -->
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                            @error('items')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Card Footer -->
                        <div class="card-footer bg-white d-flex justify-content-end">
                            <a href="{{ route('issuance.index') }}" class="btn btn-secondary mr-2">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn text-white" style="background-color: #87A96B;">
                                <i class="fas fa-truck-loading me-1"></i> Issue Items
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        let rowIndex = 0;
        const inventoryItems = @json($inventoryItems);

        @if(!isset($inventoryRequest) || !$inventoryRequest)
        // Auto-fill project and milestone when request is selected (only when not pre-filled)
        const requestSelect = document.getElementById('RequestID');
        if (requestSelect) {
            requestSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const projectId = selectedOption.getAttribute('data-project');
                const milestoneId = selectedOption.getAttribute('data-milestone');

                if (projectId) {
                    document.getElementById('ProjectID').value = projectId;
                    // Trigger milestone load
                    loadMilestones(projectId, milestoneId);
                }
            });
        }

        // Load milestones when project changes
        const projectSelect = document.getElementById('ProjectID');
        if (projectSelect) {
            projectSelect.addEventListener('change', function() {
                loadMilestones(this.value, null);
            });
        }
        @endif

        function loadMilestones(projectId, selectedMilestoneId) {
            const milestoneSelect = document.getElementById('MilestoneID');
            milestoneSelect.innerHTML = '<option value="">Select Milestone (optional)</option>';

            if (projectId) {
                fetch(`/api/projects/${projectId}/milestones`)
                    .then(response => response.json())
                    .then(milestones => {
                        milestones.forEach(milestone => {
                            const selected = selectedMilestoneId && milestone.milestone_id == selectedMilestoneId ? 'selected' : '';
                            milestoneSelect.innerHTML += `<option value="${milestone.milestone_id}" ${selected}>${milestone.milestone_name}</option>`;
                        });
                    })
                    .catch(error => console.error('Error loading milestones:', error));
            }
        }

        function addItemRow() {
            const tbody = document.getElementById('itemsTableBody');
            const row = document.createElement('tr');
            row.id = `itemRow${rowIndex}`;

            row.innerHTML = `
                <td>
                    <select name="items[${rowIndex}][ItemID]" class="form-select item-select" onchange="updateAvailability(${rowIndex})" required>
                        <option value="">Select Item</option>
                        ${inventoryItems.map(item =>
                            `<option value="${item.ItemID}" data-available="${item.AvailableQuantity}" data-unit="${item.Unit}">
                                ${item.ItemName} (${item.ItemType})
                            </option>`
                        ).join('')}
                    </select>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control text-center available-qty" id="available${rowIndex}" readonly style="background-color: #e9ecef;">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control text-center unit-field" id="unit${rowIndex}" readonly style="background-color: #e9ecef;">
                </td>
                <td>
                    <input type="number" name="items[${rowIndex}][Quantity]" class="form-control text-center" 
                           min="0.01" step="0.01" required onchange="validateQuantity(${rowIndex})">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeItemRow(${rowIndex})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;

            tbody.appendChild(row);
            rowIndex++;
        }

        function updateAvailability(index) {
            const select = document.querySelector(`select[name="items[${index}][ItemID]"]`);
            const selectedOption = select.options[select.selectedIndex];

            const available = selectedOption.getAttribute('data-available') || '';
            const unit = selectedOption.getAttribute('data-unit') || '';

            document.getElementById(`available${index}`).value = available;
            document.getElementById(`unit${index}`).value = unit;
        }

        function validateQuantity(index) {
            const qtyInput = document.querySelector(`input[name="items[${index}][Quantity]"]`);
            const available = parseFloat(document.getElementById(`available${index}`).value) || 0;
            const quantity = parseFloat(qtyInput.value) || 0;

            if (quantity > available) {
                alert(`Quantity cannot exceed available stock (${available})`);
                qtyInput.value = available;
            }
        }

        function removeItemRow(index) {
            const row = document.getElementById(`itemRow${index}`);
            if (row) {
                row.remove();
            }
        }

        // On page load
        window.addEventListener('DOMContentLoaded', function() {
            @if(!isset($inventoryRequest) || !$inventoryRequest)
                addItemRow();

                // If request_id is pre-selected, trigger the change event
                const requestSelect = document.getElementById('RequestID');
                if (requestSelect && requestSelect.value) {
                    requestSelect.dispatchEvent(new Event('change'));
                }
            @endif
        });
    </script>
@endsection
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2>Add Resource to Milestone</h2>
            <p class="text-muted">{{ $milestone->project->ProjectName ?? 'N/A' }} - {{ $milestone->milestone_name }}</p>
        </div>
    </div>

    <form action="{{ route('milestones.resources.store', $milestone) }}" method="POST">
        @csrf
        
        <div class="row">
            <!-- Left Column: Resource Details -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Resource Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="ItemID" class="form-label">Item <span class="text-danger">*</span></label>
                            <select name="ItemID" id="ItemID" class="form-select" required onchange="updateItemDetails()">
                                <option value="">Select Item</option>
                                @foreach($inventoryItems as $item)
                                    <option value="{{ $item->ItemID }}" 
                                            data-available="{{ $item->AvailableQuantity }}"
                                            data-unit="{{ $item->Unit }}"
                                            data-type="{{ $item->ItemType }}"
                                            {{ old('ItemID') == $item->ItemID ? 'selected' : '' }}>
                                        {{ $item->ItemName }} ({{ $item->ItemType }})
                                    </option>
                                @endforeach
                            </select>
                            @error('ItemID')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Item Type</label>
                            <input type="text" id="itemType" class="form-control" readonly placeholder="Select an item">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Available Stock</label>
                            <div class="input-group">
                                <input type="text" id="availableStock" class="form-control" readonly placeholder="0.00">
                                <span class="input-group-text" id="stockUnit">Unit</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="PlannedQuantity" class="form-label">Planned Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="PlannedQuantity" id="PlannedQuantity" class="form-control" 
                                   step="0.01" min="0.01" value="{{ old('PlannedQuantity') }}" required onchange="calculateCost()">
                            <div id="stockWarning" class="text-danger small" style="display: none;">
                                <i class="bi bi-exclamation-triangle"></i> Warning: Insufficient stock available
                            </div>
                            @error('PlannedQuantity')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="EstimatedCost" class="form-label">Estimated Cost (₱)</label>
                            <input type="number" name="EstimatedCost" id="EstimatedCost" class="form-control" 
                                   step="0.01" min="0" value="{{ old('EstimatedCost') }}" placeholder="0.00">
                            @error('EstimatedCost')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Planning Notes -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Milestone Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th style="width: 40%;">Project:</th>
                                <td>{{ $milestone->project->ProjectName ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Milestone:</th>
                                <td>{{ $milestone->milestone_name }}</td>
                            </tr>
                            <tr>
                                <th>Start Date:</th>
                                <td>{{ $milestone->StartDate ? $milestone->StartDate->format('M d, Y') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>End Date:</th>
                                <td>{{ $milestone->EndDate ? $milestone->EndDate->format('M d, Y') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    <span class="badge bg-{{ $milestone->Status == 'Completed' ? 'success' : ($milestone->Status == 'In Progress' ? 'primary' : 'secondary') }}">
                                        {{ $milestone->Status }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Planning Notes</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="Notes" class="form-label">Notes</label>
                            <textarea name="Notes" id="Notes" class="form-control" rows="5" placeholder="Add any notes about this resource requirement...">{{ old('Notes') }}</textarea>
                            @error('Notes')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check">
                            <input type="checkbox" name="IsAllocated" id="IsAllocated" class="form-check-input" 
                                   value="1" {{ old('IsAllocated') ? 'checked' : '' }}>
                            <label for="IsAllocated" class="form-check-label">
                                Mark as allocated (reserved for this milestone)
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('milestones.resources.index', $milestone) }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Add Resource</button>
        </div>
    </form>
</div>

<script>
function updateItemDetails() {
    const select = document.getElementById('ItemID');
    const selectedOption = select.options[select.selectedIndex];
    
    const available = selectedOption.getAttribute('data-available') || '0';
    const unit = selectedOption.getAttribute('data-unit') || 'Unit';
    const type = selectedOption.getAttribute('data-type') || '';
    
    document.getElementById('itemType').value = type;
    document.getElementById('availableStock').value = parseFloat(available).toFixed(2);
    document.getElementById('stockUnit').textContent = unit;
    
    // Check stock when quantity changes
    checkStock();
}

function checkStock() {
    const available = parseFloat(document.getElementById('availableStock').value) || 0;
    const planned = parseFloat(document.getElementById('PlannedQuantity').value) || 0;
    const warning = document.getElementById('stockWarning');
    
    if (planned > available) {
        warning.style.display = 'block';
    } else {
        warning.style.display = 'none';
    }
}

function calculateCost() {
    checkStock();
    // Could auto-calculate cost based on quantity if unit price is available
}

// Initialize on page load
window.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('ItemID').value) {
        updateItemDetails();
    }
});
</script>
@endsection

@extends('layouts.app')

@section('title', 'Edit Inventory Item')
@section('page-title', 'Edit Inventory Item')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit mr-2"></i>Edit Inventory Item: {{ $inventory->ItemName }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('inventory.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('inventory.update', $inventory) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ItemTypeID">Item Type <span class="text-danger">*</span></label>
                                    <select class="form-control @error('ItemTypeID') is-invalid @enderror" 
                                            id="ItemTypeID" name="ItemTypeID" required>
                                        <option value="">Select Type</option>
                                        @foreach($itemTypes as $type)
                                            <option value="{{ $type->ItemTypeID }}" 
                                                    data-type-name="{{ $type->TypeName }}"
                                                    {{ old('ItemTypeID', $inventory->ItemTypeID) == $type->ItemTypeID ? 'selected' : '' }}>
                                                {{ $type->TypeName }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('ItemTypeID')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ItemName">Item Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ItemName') is-invalid @enderror" 
                                           id="ItemName" name="ItemName" value="{{ old('ItemName', $inventory->ItemName) }}" required>
                                    @error('ItemName')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Unit">Unit <span class="text-danger">*</span></label>
                                    <select class="form-control @error('Unit') is-invalid @enderror" 
                                            id="Unit" name="Unit" required>
                                        <option value="">Select Unit</option>
                                        <option value="units" {{ old('Unit', $inventory->Unit) == 'units' ? 'selected' : '' }}>Units</option>
                                        <option value="kg" {{ old('Unit', $inventory->Unit) == 'kg' ? 'selected' : '' }}>Kilograms (kg)</option>
                                        <option value="g" {{ old('Unit', $inventory->Unit) == 'g' ? 'selected' : '' }}>Grams (g)</option>
                                        <option value="pieces" {{ old('Unit', $inventory->Unit) == 'pieces' ? 'selected' : '' }}>Pieces</option>
                                        <option value="bags" {{ old('Unit', $inventory->Unit) == 'bags' ? 'selected' : '' }}>Bags</option>
                                        <option value="sacks" {{ old('Unit', $inventory->Unit) == 'sacks' ? 'selected' : '' }}>Sacks</option>
                                        <option value="boxes" {{ old('Unit', $inventory->Unit) == 'boxes' ? 'selected' : '' }}>Boxes</option>
                                        <option value="crates" {{ old('Unit', $inventory->Unit) == 'crates' ? 'selected' : '' }}>Crates</option>
                                        <option value="bundles" {{ old('Unit', $inventory->Unit) == 'bundles' ? 'selected' : '' }}>Bundles</option>
                                        <option value="rolls" {{ old('Unit', $inventory->Unit) == 'rolls' ? 'selected' : '' }}>Rolls</option>
                                        <option value="meters" {{ old('Unit', $inventory->Unit) == 'meters' ? 'selected' : '' }}>Meters (m)</option>
                                        <option value="liters" {{ old('Unit', $inventory->Unit) == 'liters' ? 'selected' : '' }}>Liters (L)</option>
                                        <option value="gallons" {{ old('Unit', $inventory->Unit) == 'gallons' ? 'selected' : '' }}>Gallons</option>
                                        <option value="pcs" {{ old('Unit', $inventory->Unit) == 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                                        <option value="sets" {{ old('Unit', $inventory->Unit) == 'sets' ? 'selected' : '' }}>Sets</option>
                                    </select>
                                    @error('Unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="TotalQuantity">Total Quantity <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control @error('TotalQuantity') is-invalid @enderror" 
                                           id="TotalQuantity" name="TotalQuantity" value="{{ old('TotalQuantity', $inventory->TotalQuantity) }}" min="0" required>
                                    <small class="form-text text-muted">For materials: current stock. For equipment: total owned.</small>
                                    @error('TotalQuantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="AvailableQuantity">Available Quantity</label>
                                    <input type="number" step="0.01" class="form-control" 
                                           id="AvailableQuantity" value="{{ $inventory->AvailableQuantity }}" disabled>
                                    <small class="form-text text-muted">Auto-calculated for materials</small>
                                </div>
                            </div>
                            <div class="col-md-4" id="minimumStockLevelContainer" style="display: none;">
                                <div class="form-group">
                                    <label for="MinimumStockLevel">Minimum Stock Level</label>
                                    <input type="number" step="0.01" class="form-control @error('MinimumStockLevel') is-invalid @enderror" 
                                           id="MinimumStockLevel" name="MinimumStockLevel" value="{{ old('MinimumStockLevel', $inventory->MinimumStockLevel) }}" min="0">
                                    <small class="form-text text-muted">For materials only</small>
                                    @error('MinimumStockLevel')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" style="background-color: #7fb069; border-color: #7fb069;">
                                <i class="fas fa-save"></i> Update Item
                            </button>
                            <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Function to toggle Minimum Stock Level visibility and auto-set Unit for Equipment
        function toggleMinimumStockLevel() {
            const selectedOption = $('#ItemTypeID option:selected');
            const typeName = selectedOption.data('type-name');
            const minimumStockContainer = $('#minimumStockLevelContainer');
            const unitSelect = $('#Unit');
            
            // Show for Materials, hide for Equipment
            if (typeName === 'Materials') {
                minimumStockContainer.show();
                // Enable Unit dropdown for Materials
                unitSelect.prop('disabled', false);
            } else if (typeName === 'Equipment') {
                minimumStockContainer.hide();
                // Auto-set Unit to "units" for Equipment and disable dropdown
                unitSelect.val('units');
                unitSelect.prop('disabled', true);
            } else {
                minimumStockContainer.hide();
                // Enable Unit dropdown if no type selected
                unitSelect.prop('disabled', false);
            }
        }
        
        // Check on page load
        toggleMinimumStockLevel();
        
        // Check when item type changes
        $('#ItemTypeID').on('change', function() {
            toggleMinimumStockLevel();
        });
    });
</script>
@endpush

@endsection


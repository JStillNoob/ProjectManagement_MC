@extends('layouts.app')

@section('title', 'Create Inventory Item')
@section('page-title', 'Create Inventory Item')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus mr-2"></i>Create New Inventory Item
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('inventory.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('inventory.store') }}" method="POST">
                        @csrf
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
                                                    {{ old('ItemTypeID') == $type->ItemTypeID ? 'selected' : '' }}>
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
                                           id="ItemName" name="ItemName" value="{{ old('ItemName') }}" required>
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
                                        <option value="units" {{ old('Unit', 'units') == 'units' ? 'selected' : '' }}>Units</option>
                                        <option value="kg" {{ old('Unit') == 'kg' ? 'selected' : '' }}>Kilograms (kg)</option>
                                        <option value="g" {{ old('Unit') == 'g' ? 'selected' : '' }}>Grams (g)</option>
                                        <option value="pieces" {{ old('Unit') == 'pieces' ? 'selected' : '' }}>Pieces</option>
                                        <option value="bags" {{ old('Unit') == 'bags' ? 'selected' : '' }}>Bags</option>
                                        <option value="sacks" {{ old('Unit') == 'sacks' ? 'selected' : '' }}>Sacks</option>
                                        <option value="boxes" {{ old('Unit') == 'boxes' ? 'selected' : '' }}>Boxes</option>
                                        <option value="crates" {{ old('Unit') == 'crates' ? 'selected' : '' }}>Crates</option>
                                        <option value="bundles" {{ old('Unit') == 'bundles' ? 'selected' : '' }}>Bundles</option>
                                        <option value="rolls" {{ old('Unit') == 'rolls' ? 'selected' : '' }}>Rolls</option>
                                        <option value="meters" {{ old('Unit') == 'meters' ? 'selected' : '' }}>Meters (m)</option>
                                        <option value="liters" {{ old('Unit') == 'liters' ? 'selected' : '' }}>Liters (L)</option>
                                        <option value="gallons" {{ old('Unit') == 'gallons' ? 'selected' : '' }}>Gallons</option>
                                        <option value="pcs" {{ old('Unit') == 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                                        <option value="sets" {{ old('Unit') == 'sets' ? 'selected' : '' }}>Sets</option>
                                    </select>
                                    @error('Unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="TotalQuantity">Total Quantity <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control @error('TotalQuantity') is-invalid @enderror" 
                                           id="TotalQuantity" name="TotalQuantity" value="{{ old('TotalQuantity', 0) }}" min="0" required>
                                    <small class="form-text text-muted">For materials: current stock. For equipment: total owned.</small>
                                    @error('TotalQuantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6" id="minimumStockLevelContainer" style="display: none;">
                                <div class="form-group">
                                    <label for="MinimumStockLevel">Minimum Stock Level</label>
                                    <input type="number" step="0.01" class="form-control @error('MinimumStockLevel') is-invalid @enderror" 
                                           id="MinimumStockLevel" name="MinimumStockLevel" value="{{ old('MinimumStockLevel') }}" min="0">
                                    <small class="form-text text-muted">For materials only</small>
                                    @error('MinimumStockLevel')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" style="background-color: #7fb069; border-color: #7fb069;">
                                <i class="fas fa-save"></i> Create Item
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
                $('#MinimumStockLevel').val(''); // Clear value when hidden
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


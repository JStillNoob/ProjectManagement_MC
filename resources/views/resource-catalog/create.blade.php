@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h2><i class="fas fa-plus-circle"></i> Add New Resource</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('resource-catalog.index') }}">Resource Catalog</a></li>
                    <li class="breadcrumb-item active">Add New</li>
                </ol>
            </nav>
        </div>

        <form action="{{ route('resource-catalog.store') }}" method="POST">
            @csrf

            <div class="card">
                <div class="card-header" style="background-color: #87A96B; color: white;">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Resource Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><strong>Item Name</strong> <span class="text-danger">*</span></label>
                            <input type="text" name="ItemName" class="form-control @error('ItemName') is-invalid @enderror"
                                value="{{ old('ItemName') }}" required placeholder="e.g., Cement, Drill Machine">
                            @error('ItemName')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><strong>Type</strong> <span class="text-danger">*</span></label>
                            <select name="Type" class="form-select @error('Type') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                <option value="Equipment" {{ old('Type') == 'Equipment' ? 'selected' : '' }}>Equipment
                                </option>
                                <option value="Materials" {{ old('Type') == 'Materials' ? 'selected' : '' }}>Materials
                                </option>
                            </select>
                            @error('Type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label"><strong>Unit of Measure</strong> <span
                                    class="text-danger">*</span></label>
                            <select name="Unit" class="form-select @error('Unit') is-invalid @enderror" required>
                                <option value="">Select Unit</option>
                                <option value="pcs" {{ old('Unit') == 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                                <option value="bags" {{ old('Unit') == 'bags' ? 'selected' : '' }}>Bags</option>
                                <option value="boxes" {{ old('Unit') == 'boxes' ? 'selected' : '' }}>Boxes</option>
                                <option value="units" {{ old('Unit') == 'units' ? 'selected' : '' }}>Units</option>
                                <option value="sets" {{ old('Unit') == 'sets' ? 'selected' : '' }}>Sets</option>
                                <option value="kg" {{ old('Unit') == 'kg' ? 'selected' : '' }}>Kilograms (kg)</option>
                                <option value="lbs" {{ old('Unit') == 'lbs' ? 'selected' : '' }}>Pounds (lbs)</option>
                                <option value="liters" {{ old('Unit') == 'liters' ? 'selected' : '' }}>Liters</option>
                                <option value="gallons" {{ old('Unit') == 'gallons' ? 'selected' : '' }}>Gallons</option>
                                <option value="meters" {{ old('Unit') == 'meters' ? 'selected' : '' }}>Meters (m)</option>
                                <option value="feet" {{ old('Unit') == 'feet' ? 'selected' : '' }}>Feet (ft)</option>
                            </select>
                            @error('Unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('resource-catalog.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn"
                            style="background-color: #87A96B !important; border: 2px solid #87A96B !important; color: white !important;">
                            <i class="fas fa-save"></i> Add Resource
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
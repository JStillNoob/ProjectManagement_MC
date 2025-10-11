@extends('layouts.app')

@section('title', 'Create Position')
@section('page-title', 'Create Position')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus text-success mr-2"></i>
                        Create New Position
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('positions.store') }}" method="POST">
                        @csrf

                        <!-- Position Information -->
                        <h5 class="text-primary mb-3"><i class="fas fa-briefcase mr-2"></i>Position Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="PositionName">Position Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('PositionName') is-invalid @enderror"
                                        id="PositionName" name="PositionName" value="{{ old('PositionName') }}" required>
                                    @error('PositionName')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Salary">Salary <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">₱</span>
                                        </div>
                                        <input type="number" class="form-control @error('Salary') is-invalid @enderror"
                                            id="Salary" name="Salary" value="{{ old('Salary') }}" 
                                            step="0.01" min="0" required>
                                        @error('Salary')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Create Position
                            </button>
                            <a href="{{ route('positions.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Positions
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle text-info mr-2"></i>
                        Position Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-lightbulb mr-2"></i>Tips for Creating Positions</h6>
                        <ul class="list-unstyled mb-0">
                            <li><i class="fas fa-check text-success mr-1"></i> Use clear, descriptive position names</li>
                            <li><i class="fas fa-check text-success mr-1"></i> Set competitive salary rates</li>
                            <li><i class="fas fa-check text-success mr-1"></i> Consider market standards</li>
                            <li><i class="fas fa-check text-success mr-1"></i> Position names must be unique</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle mr-2"></i>Important Notes</h6>
                        <ul class="list-unstyled mb-0">
                            <li><i class="fas fa-info text-warning mr-1"></i> Salary will be used for all employees in this position</li>
                            <li><i class="fas fa-info text-warning mr-1"></i> You can edit the salary later if needed</li>
                            <li><i class="fas fa-info text-warning mr-1"></i> Position names cannot be duplicated</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

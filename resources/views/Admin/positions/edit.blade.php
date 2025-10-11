@extends('layouts.app')

@section('title', 'Edit Position')
@section('page-title', 'Edit Position')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit text-warning mr-2"></i>
                        Edit Position: {{ $position->RoleName }}
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('positions.update', $position) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Position Information -->
                        <h5 class="text-primary mb-3"><i class="fas fa-briefcase mr-2"></i>Position Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="PositionName">Position Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('PositionName') is-invalid @enderror"
                                        id="PositionName" name="PositionName" value="{{ old('PositionName', $position->PositionName) }}" required>
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
                                            id="Salary" name="Salary" value="{{ old('Salary', $position->Salary) }}" 
                                            step="0.01" min="0" required>
                                        @error('Salary')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Update Position
                            </button>
                            <a href="{{ route('positions.show', $position) }}" class="btn btn-info">
                                <i class="fas fa-eye"></i> View Details
                            </a>
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
                        Position Details
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-briefcase mr-2"></i>Current Information</h6>
                        <ul class="list-unstyled mb-0">
                            <li><strong>Position ID:</strong> {{ $position->PositionID }}</li>
                            <li><strong>Current Name:</strong> {{ $position->PositionName }}</li>
                            <li><strong>Current Salary:</strong> ₱{{ number_format($position->Salary, 2) }}</li>
                            <li><strong>Created:</strong> {{ $position->created_at->format('M d, Y') }}</li>
                            <li><strong>Last Updated:</strong> {{ $position->updated_at->format('M d, Y') }}</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle mr-2"></i>Important Notes</h6>
                        <ul class="list-unstyled mb-0">
                            <li><i class="fas fa-info text-warning mr-1"></i> Changing salary will affect all employees in this position</li>
                            <li><i class="fas fa-info text-warning mr-1"></i> Position names must be unique</li>
                            <li><i class="fas fa-info text-warning mr-1"></i> Changes will be reflected immediately</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Create New Project')
@section('page-title', 'Create New Project')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Create New Project
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('ProdHead.projects') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i>Back to Projects
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form action="{{ route('projects.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ProjectName">Project Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ProjectName') is-invalid @enderror"
                                        id="ProjectName" name="ProjectName" value="{{ old('ProjectName') }}" required>
                                    @error('ProjectName')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ClientID">Client</label>
                                    <select class="form-control @error('ClientID') is-invalid @enderror" id="ClientID" name="ClientID">
                                        <option value="">Select a client...</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->ClientID }}" {{ old('ClientID') == $client->ClientID ? 'selected' : '' }}>
                                                {{ $client->ClientName }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('ClientID')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="ProjectDescription">Project Description</label>
                            <textarea class="form-control @error('ProjectDescription') is-invalid @enderror"
                                id="ProjectDescription" name="ProjectDescription" rows="4"
                                placeholder="Enter project description...">{{ old('ProjectDescription') }}</textarea>
                            @error('ProjectDescription')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="StartDate">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('StartDate') is-invalid @enderror"
                                        id="StartDate" name="StartDate" value="{{ old('StartDate') }}" required>
                                    @error('StartDate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="EndDate">End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('EndDate') is-invalid @enderror"
                                        id="EndDate" name="EndDate" value="{{ old('EndDate') }}" required>
                                    @error('EndDate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="WarrantyEndDate">Warranty End Date</label>
                                    <input type="date" class="form-control @error('WarrantyEndDate') is-invalid @enderror"
                                        id="WarrantyEndDate" name="WarrantyEndDate" value="{{ old('WarrantyEndDate') }}">
                                    @error('WarrantyEndDate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="StreetAddress">Street Address</label>
                                    <input type="text" class="form-control @error('StreetAddress') is-invalid @enderror"
                                        id="StreetAddress" name="StreetAddress" value="{{ old('StreetAddress') }}">
                                    @error('StreetAddress')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Barangay">Barangay</label>
                                    <input type="text" class="form-control @error('Barangay') is-invalid @enderror"
                                        id="Barangay" name="Barangay" value="{{ old('Barangay') }}">
                                    @error('Barangay')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="City">City</label>
                                    <input type="text" class="form-control @error('City') is-invalid @enderror" id="City"
                                        name="City" value="{{ old('City') }}">
                                    @error('City')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="StateProvince">State/Province</label>
                                    <input type="text" class="form-control @error('StateProvince') is-invalid @enderror"
                                        id="StateProvince" name="StateProvince" value="{{ old('StateProvince') }}">
                                    @error('StateProvince')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ZipCode">Zip Code</label>
                                    <input type="text" class="form-control @error('ZipCode') is-invalid @enderror"
                                        id="ZipCode" name="ZipCode" value="{{ old('ZipCode') }}">
                                    @error('ZipCode')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Employee Assignment -->
                        <div class="form-group">
                            <label>Assign Employees to Project</label>
                            <div class="row">
                                @foreach($employees as $employee)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="employee_ids[]" 
                                                   value="{{ $employee->id }}" 
                                                   id="employee_{{ $employee->id }}"
                                                   {{ in_array($employee->id, old('employee_ids', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="employee_{{ $employee->id }}">
                                                {{ $employee->full_name }}
                                                @if($employee->position)
                                                    <small class="text-muted">({{ $employee->position }})</small>
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if($employees->count() == 0)
                                <p class="text-muted">No employees available for assignment.</p>
                            @endif
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>Create Project
                            </button>
                            <a href="{{ route('ProdHead.projects') }}" class="btn btn-secondary">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Form validation
        $('form').on('submit', function(e) {
            const startDate = new Date($('#StartDate').val());
            const endDate = new Date($('#EndDate').val());
            const warrantyDate = $('#WarrantyEndDate').val() ? new Date($('#WarrantyEndDate').val()) : null;
            
            if (endDate <= startDate) {
                e.preventDefault();
                alert('End date must be after start date.');
                $('#EndDate').focus();
                return false;
            }
            
            if (warrantyDate && warrantyDate <= endDate) {
                e.preventDefault();
                alert('Warranty end date must be after project end date.');
                $('#WarrantyEndDate').focus();
                return false;
            }
        });
    });
</script>
@endpush


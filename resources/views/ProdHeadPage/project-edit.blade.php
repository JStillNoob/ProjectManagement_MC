@extends('layouts.app')

@section('title', 'Edit Project - ' . $project->ProjectName)
@section('page-title', 'Edit Project')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Project: {{ $project->ProjectName }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i>Back to Project
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

                    <form action="{{ route('projects.update', $project) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ProjectName">Project Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('ProjectName') is-invalid @enderror" 
                                           id="ProjectName" 
                                           name="ProjectName" 
                                           value="{{ old('ProjectName', $project->ProjectName) }}" 
                                           required>
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
                                            <option value="{{ $client->ClientID }}" 
                                                    {{ old('ClientID', $project->ClientID) == $client->ClientID ? 'selected' : '' }}>
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
                                      id="ProjectDescription" 
                                      name="ProjectDescription" 
                                      rows="4">{{ old('ProjectDescription', $project->ProjectDescription) }}</textarea>
                            @error('ProjectDescription')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="StartDate">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           class="form-control @error('StartDate') is-invalid @enderror" 
                                           id="StartDate" 
                                           name="StartDate" 
                                           value="{{ old('StartDate', $project->StartDate->format('Y-m-d')) }}" 
                                           required>
                                    @error('StartDate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="EndDate">End Date <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           class="form-control @error('EndDate') is-invalid @enderror" 
                                           id="EndDate" 
                                           name="EndDate" 
                                           value="{{ old('EndDate', $project->EndDate->format('Y-m-d')) }}" 
                                           required>
                                    @error('EndDate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="WarrantyEndDate">Warranty End Date</label>
                                    <input type="date" 
                                           class="form-control @error('WarrantyEndDate') is-invalid @enderror" 
                                           id="WarrantyEndDate" 
                                           name="WarrantyEndDate" 
                                           value="{{ old('WarrantyEndDate', $project->WarrantyEndDate ? $project->WarrantyEndDate->format('Y-m-d') : '') }}">
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
                                    <input type="text" 
                                           class="form-control @error('StreetAddress') is-invalid @enderror" 
                                           id="StreetAddress" 
                                           name="StreetAddress" 
                                           value="{{ old('StreetAddress', $project->StreetAddress) }}">
                                    @error('StreetAddress')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Barangay">Barangay</label>
                                    <input type="text" 
                                           class="form-control @error('Barangay') is-invalid @enderror" 
                                           id="Barangay" 
                                           name="Barangay" 
                                           value="{{ old('Barangay', $project->Barangay) }}">
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
                                    <input type="text" 
                                           class="form-control @error('City') is-invalid @enderror" 
                                           id="City" 
                                           name="City" 
                                           value="{{ old('City', $project->City) }}">
                                    @error('City')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="StateProvince">State/Province</label>
                                    <input type="text" 
                                           class="form-control @error('StateProvince') is-invalid @enderror" 
                                           id="StateProvince" 
                                           name="StateProvince" 
                                           value="{{ old('StateProvince', $project->StateProvince) }}">
                                    @error('StateProvince')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ZipCode">Zip Code</label>
                                    <input type="text" 
                                           class="form-control @error('ZipCode') is-invalid @enderror" 
                                           id="ZipCode" 
                                           name="ZipCode" 
                                           value="{{ old('ZipCode', $project->ZipCode) }}">
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
                                                   {{ in_array($employee->id, $projectEmployees) ? 'checked' : '' }}>
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

                        <!-- Current Status Info -->
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Status Information:</strong> The project status is automatically calculated based on the dates you set. 
                            Current status: <span class="badge badge-primary">{{ $project->status->StatusName }}</span>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>Update Project
                            </button>
                            <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                        </div>
                    </form>
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
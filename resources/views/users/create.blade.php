@extends('layouts.app')

@section('title', 'Add New User')
@section('page-title', 'Add New User')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create User Account</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="EmployeeID">Select Employee <span class="text-danger">*</span></label>
                                <select class="form-control @error('EmployeeID') is-invalid @enderror" 
                                        id="EmployeeID" name="EmployeeID" required>
                                    <option value="">Choose an employee...</option>
                                    @foreach($employees as $employee)
                                        @php
                                            $position = $employee->relationLoaded('position') 
                                                ? $employee->getRelation('position') 
                                                : $employee->position()->first();
                                            $positionName = $position ? $position->PositionName : null;
                                        @endphp
                                        <option value="{{ $employee->id }}" 
                                                {{ old('EmployeeID') == $employee->id ? 'selected' : '' }}
                                                data-name="{{ $employee->full_name }}"
                                                data-position="{{ $positionName }}">
                                            {{ $employee->full_name }} 
                                            @if($positionName)
                                                - {{ $positionName }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('EmployeeID')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($employees->count() == 0)
                                    <small class="text-muted">No employees available for user account creation.</small>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Password">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('Password') is-invalid @enderror" 
                                       id="Password" name="Password" required>
                                @error('Password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Email">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('Email') is-invalid @enderror" 
                                       id="Email" name="Email" value="{{ old('Email') }}" required>
                                @error('Email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="UserTypeID">User Type <span class="text-danger">*</span></label>
                                <select class="form-control @error('UserTypeID') is-invalid @enderror" 
                                        id="UserTypeID" name="UserTypeID" required>
                                    <option value="">Select User Type</option>
                                    @foreach($userTypes as $userType)
                                        <option value="{{ $userType->UserTypeID }}" 
                                                {{ old('UserTypeID') == $userType->UserTypeID ? 'selected' : '' }}>
                                            {{ $userType->UserType }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('UserTypeID')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>


                    <!-- Employee Information Preview -->
                    <div class="alert alert-info" id="employee-info" style="display: none;">
                        <h6><i class="fas fa-user mr-2"></i>Selected Employee Information</h6>
                        <div id="employee-details"></div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create User Account
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Instructions</h3>
            </div>
            <div class="card-body">
                <ol>
                    <li><strong>Select Employee:</strong> Choose from the list of employees who don't have user accounts yet.</li>
                    <li><strong>Set Email:</strong> Enter a unique email address for the user account.</li>
                    <li><strong>Set Password:</strong> Create a secure password for the user.</li>
                    <li><strong>Choose User Type:</strong> Select the appropriate user type (Admin, Production Head, etc.).</li>
                </ol>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle"></i>
                    <strong>Note:</strong> Only employees without existing user accounts are shown in the dropdown.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#EmployeeID').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const employeeName = selectedOption.data('name');
        const employeePosition = selectedOption.data('position');
        
        if (selectedOption.val()) {
            let details = `<strong>Name:</strong> ${employeeName}<br>`;
            if (employeePosition) {
                details += `<strong>Position:</strong> ${employeePosition}`;
            }
            $('#employee-details').html(details);
            $('#employee-info').show();
        } else {
            $('#employee-info').hide();
        }
    });
});
</script>
@endpush
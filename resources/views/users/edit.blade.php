@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit User Account</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="EmployeeID">Select Employee <span class="text-danger">*</span></label>
                                <select class="form-control @error('EmployeeID') is-invalid @enderror" 
                                        id="EmployeeID" name="EmployeeID" required>
                                    <option value="">Choose an employee...</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" 
                                                {{ old('EmployeeID', $user->EmployeeID) == $employee->id ? 'selected' : '' }}
                                                data-name="{{ $employee->full_name }}"
                                                data-position="{{ $employee->position }}">
                                            {{ $employee->full_name }} 
                                            @if($employee->position)
                                                - {{ $employee->position }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('EmployeeID')
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
                                                {{ old('UserTypeID', $user->UserTypeID) == $userType->UserTypeID ? 'selected' : '' }}>
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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Email">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('Email') is-invalid @enderror" 
                                       id="Email" name="Email" value="{{ old('Email', $user->Email) }}" required>
                                @error('Email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Password">New Password</label>
                                <input type="password" class="form-control @error('Password') is-invalid @enderror" 
                                       id="Password" name="Password">
                                @error('Password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Leave blank to keep current password</small>
                            </div>
                        </div>
                    </div>

                    <!-- Current Employee Information -->
                    @if($user->employee)
                        <div class="alert alert-info">
                            <h6><i class="fas fa-user mr-2"></i>Current Employee Information</h6>
                            <strong>Name:</strong> {{ $user->employee->full_name }}<br>
                            @if($user->employee->position)
                                <strong>Position:</strong> {{ $user->employee->position }}
                            @endif
                        </div>
                    @endif

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update User Account
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
                <h3 class="card-title">User Information</h3>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td><strong>User ID:</strong></td>
                        <td>{{ $user->id }}</td>
                    </tr>
                    <tr>
                        <td><strong>Username:</strong></td>
                        <td>{{ $user->Username }}</td>
                    </tr>
                    <tr>
                        <td><strong>User Type:</strong></td>
                        <td>{{ $user->userType->UserType ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Role:</strong></td>
                        <td>{{ $user->role->RoleName ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Created:</strong></td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                    </tr>
                </table>
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
            // You can add a preview here if needed
        }
    });
});
</script>
@endpush



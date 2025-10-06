@extends('layouts.app')

@section('title', 'Employee Profile')
@section('page-title', 'Employee Profile')

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Employee Card -->
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle" 
                         src="{{ $employee->image_path }}" 
                         alt="{{ $employee->full_name }}">
                </div>

                <h3 class="profile-username text-center">{{ $employee->full_name }}</h3>
                <p class="text-muted text-center">{{ $employee->position }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Employee ID</b> <a class="float-right">{{ $employee->id }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Employee Type</b> 
                        <span class="float-right">
                            @if($employee->employeeType)
                                <span class="badge badge-{{ 
                                    $employee->employeeType->EmployeeTypeName == 'Regular' ? 'success' : 
                                    ($employee->employeeType->EmployeeTypeName == 'On-call' ? 'info' : 
                                    ($employee->employeeType->EmployeeTypeName == 'Contract' ? 'warning' : 'primary')) 
                                }}">
                                    {{ $employee->employeeType->EmployeeTypeName }}
                                </span>
                            @else
                                <span class="text-muted">No type assigned</span>
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>Status</b> 
                        <span class="float-right">
                            <span class="badge badge-{{ $employee->status == 'Active' ? 'success' : 'danger' }}">
                                {{ $employee->status }}
                            </span>
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>Start Date</b> <a class="float-right">{{ $employee->start_date->format('M d, Y') }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Age</b> <a class="float-right">{{ $employee->age }} years old</a>
                    </li>
                </ul>

                <div class="row">
                    <div class="col-6">
                        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning btn-block">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Employee Details -->
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link active" href="#personal" data-toggle="tab">Personal Information</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#employment" data-toggle="tab">Employment Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#qrcode" data-toggle="tab">QR Code</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Personal Information Tab -->
                    <div class="active tab-pane" id="personal">
                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-user mr-1"></i> First Name</strong>
                                <p class="text-muted">{{ $employee->first_name }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-user mr-1"></i> Last Name</strong>
                                <p class="text-muted">{{ $employee->last_name }}</p>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-user mr-1"></i> Middle Name</strong>
                                <p class="text-muted">{{ $employee->middle_name ?: 'Not provided' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-birthday-cake mr-1"></i> Birthday</strong>
                                <p class="text-muted">{{ $employee->birthday->format('F d, Y') }}</p>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-calendar mr-1"></i> Age</strong>
                                <p class="text-muted">{{ $employee->age }} years old</p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-map-marker-alt mr-1"></i> Address</strong>
                                <p class="text-muted">{{ $employee->address }}</p>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-clock mr-1"></i> Date Created</strong>
                                <p class="text-muted">{{ $employee->created_at->format('F d, Y \a\t g:i A') }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-edit mr-1"></i> Last Updated</strong>
                                <p class="text-muted">{{ $employee->updated_at->format('F d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Employment Details Tab -->
                    <div class="tab-pane" id="employment">
                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-briefcase mr-1"></i> Position</strong>
                                <p class="text-muted">{{ $employee->position }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-tag mr-1"></i> Employee Type</strong>
                                <p class="text-muted">
                                    @if($employee->employeeType)
                                        <span class="badge badge-{{ 
                                            $employee->employeeType->EmployeeTypeName == 'Regular' ? 'success' : 
                                            ($employee->employeeType->EmployeeTypeName == 'On-call' ? 'info' : 
                                            ($employee->employeeType->EmployeeTypeName == 'Contract' ? 'warning' : 'primary')) 
                                        }}">
                                            {{ $employee->employeeType->EmployeeTypeName }}
                                        </span>
                                        <br><small class="text-muted">
                                            @if($employee->employeeType->EmployeeTypeName == 'Regular')
                                                Full-time employee with regular benefits
                                            @elseif($employee->employeeType->EmployeeTypeName == 'On-call')
                                                Part-time employee called as needed
                                            @elseif($employee->employeeType->EmployeeTypeName == 'Contract')
                                                Fixed-term contract employee
                                            @elseif($employee->employeeType->EmployeeTypeName == 'Part-time')
                                                Part-time employee with limited hours
                                            @endif
                                        </small>
                                    @else
                                        <span class="text-muted">No employee type assigned</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-calendar-check mr-1"></i> Start Date</strong>
                                <p class="text-muted">{{ $employee->start_date->format('F d, Y') }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-toggle-on mr-1"></i> Status</strong>
                                <p class="text-muted">
                                    <span class="badge badge-{{ $employee->status == 'Active' ? 'success' : 'danger' }}">
                                        {{ $employee->status }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-calendar-alt mr-1"></i> Employment Duration</strong>
                                <p class="text-muted">
                                    @php
                                        $startDate = $employee->start_date;
                                        $today = now();
                                        $duration = $startDate->diffInDays($today);
                                        $years = floor($duration / 365);
                                        $months = floor(($duration % 365) / 30);
                                        $days = $duration % 30;
                                    @endphp
                                    {{ $years }} year(s), {{ $months }} month(s), {{ $days }} day(s)
                                </p>
                            </div>
                        </div>
                        <hr>

                        @if($employee->image_name)
                        <div class="row">
                            <div class="col-12">
                                <strong><i class="fas fa-image mr-1"></i> Employee Photo</strong>
                                <div class="mt-2">
                                    <img src="{{ $employee->image_path }}" alt="{{ $employee->full_name }}" 
                                         class="img-thumbnail" style="max-width: 200px;">
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- QR Code Tab -->
                    <div class="tab-pane" id="qrcode">
                        <div class="row">
                            <div class="col-12 text-center">
                                <h4><i class="fas fa-qrcode mr-2"></i>Employee QR Code</h4>
                                <hr>
                                
                                @if($employee->qr_code)
                                    <div class="mb-4">
                                        <img src="{{ $employee->qr_code }}" alt="Employee QR Code" 
                                             class="img-fluid" style="max-width: 300px; border: 2px solid #dee2e6; border-radius: 8px;">
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <h5><i class="fas fa-info-circle"></i> QR Code Information</h5>
                                        <p class="mb-2">This QR code contains the following employee information:</p>
                                        <ul class="list-unstyled text-left">
                                            <li><strong>Name:</strong> {{ $employee->full_name }}</li>
                                            <li><strong>Position:</strong> {{ $employee->position }}</li>
                                            <li><strong>Status:</strong> {{ $employee->status }}</li>
                                            <li><strong>Start Date:</strong> {{ $employee->start_date->format('M d, Y') }}</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <a href="{{ $employee->qr_code }}" download="employee_{{ $employee->id }}_qrcode.svg" 
                                           class="btn btn-success">
                                            <i class="fas fa-download"></i> Download QR Code
                                        </a>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <h5><i class="fas fa-exclamation-triangle"></i> No QR Code Available</h5>
                                        <p>This employee doesn't have a QR code generated yet. Please contact the administrator.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
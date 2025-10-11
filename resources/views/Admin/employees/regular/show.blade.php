@extends('layouts.app')

@section('title', 'Regular Employee Details')
@section('page-title', 'Regular Employee Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-tie text-success mr-2"></i>
                        Employee Information
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('regular-employees.index') }}" class="btn btn-sm btn-secondary"
                           style="background-color: #6c757d !important; border: 2px solid #6c757d !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            @if($employee->image_path)
                                <img src="{{ $employee->image_path }}" alt="{{ $employee->full_name }}"
                                    class="img-circle elevation-2 mb-3"
                                    style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <div class="img-circle elevation-2 bg-secondary d-flex align-items-center justify-content-center text-white mb-3 mx-auto"
                                     style="width: 150px; height: 150px;">
                                    <i class="fas fa-user fa-3x"></i>
                                </div>
                            @endif
                            
                            <h4>{{ $employee->full_name }}</h4>
                            <p class="text-muted">{{ $employee->position }}</p>
                            
                            <div class="mt-3">
                                <span class="badge badge-{{ $employee->status == 'Active' ? 'success' : 'danger' }} badge-lg">
                                    {{ $employee->status }}
                                </span>
                                <span class="badge badge-info badge-lg">
                                    {{ $employee->employeeType->EmployeeTypeName }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <h5 class="text-primary mb-3"><i class="fas fa-user mr-2"></i>Personal Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Full Name:</strong></td>
                                    <td>{{ $employee->full_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Birthday:</strong></td>
                                    <td>{{ $employee->birthday->format('F d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Age:</strong></td>
                                    <td>{{ $employee->age }} years old</td>
                                </tr>
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td>{{ $employee->full_address }}</td>
                                </tr>
                            </table>

                            <h5 class="text-primary mb-3 mt-4"><i class="fas fa-briefcase mr-2"></i>Employment Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Position:</strong></td>
                                    <td>{{ $employee->position->PositionName ?? 'Not assigned' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Employee Type:</strong></td>
                                    <td>
                                        {{ $employee->employeeType->EmployeeTypeName }}
                                        @if($employee->employeeType->hasBenefits)
                                            <span class="badge badge-success ml-2">Eligible for Benefits</span>
                                        @else
                                            <span class="badge badge-warning ml-2">Not Eligible for Benefits</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Start Date:</strong></td>
                                    <td>{{ $employee->start_date->format('F d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Employment Duration:</strong></td>
                                    <td>{{ $employee->start_date->diffForHumans() }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $employee->status == 'Active' ? 'success' : 'danger' }}">
                                            {{ $employee->status }}
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            @if($employee->base_salary)
                                <h5 class="text-primary mb-3 mt-4"><i class="fas fa-money-bill-wave mr-2"></i>Salary Information</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="30%"><strong>Base Salary:</strong></td>
                                        <td>{{ $employee->formatted_salary }}</td>
                                    </tr>
                                </table>
                            @endif

                            <h5 class="text-info mb-3 mt-4"><i class="fas fa-phone mr-2"></i>Contact Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Contact Number:</strong></td>
                                    <td>{{ $employee->contact_number ?? 'Not provided' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="btn-group" role="group">
                        <a href="{{ route('employees.benefits', $employee) }}" class="btn btn-success">
                            <i class="fas fa-gift"></i> Manage Benefits
                        </a>
                        <a href="{{ route('regular-employees.edit', $employee) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Employee
                        </a>
                        <a href="{{ route('regular-employees.index') }}" class="btn btn-secondary">
                            <i class="fas fa-list"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Benefits Summary Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-gift text-success mr-2"></i>
                        Benefits Summary
                    </h3>
                </div>
                <div class="card-body">
                    @if($employee->employeeType->hasBenefits)
                        @php
                            $currentBenefits = $employee->getCurrentBenefits();
                        @endphp
                        
                        @if($currentBenefits->count() > 0)
                            <div class="alert alert-success">
                                <h6><i class="fas fa-check-circle mr-2"></i>Assigned Benefits</h6>
                                <p class="mb-2">This employee has <strong>{{ $currentBenefits->count() }}</strong> benefits assigned by admin:</p>
                                <ul class="list-unstyled mb-0 small">
                                    @foreach($currentBenefits->take(5) as $employeeBenefit)
                                        <li><i class="fas fa-check text-success mr-1"></i> {{ $employeeBenefit->benefit->BenefitName }}</li>
                                    @endforeach
                                    @if($currentBenefits->count() > 5)
                                        <li class="text-muted">... and {{ $currentBenefits->count() - 5 }} more</li>
                                    @endif
                                </ul>
                            </div>
                            
                            <a href="{{ route('employees.benefits', $employee) }}" class="btn btn-success btn-block">
                                <i class="fas fa-gift"></i> Manage Benefits
                            </a>
                        @else
                            <div class="alert alert-warning">
                                <h6><i class="fas fa-exclamation-triangle mr-2"></i>No Benefits Assigned</h6>
                                <p class="mb-0">This employee is eligible for benefits but none have been assigned by admin yet.</p>
                            </div>
                            
                            <a href="{{ route('employees.benefits', $employee) }}" class="btn btn-primary btn-block">
                                <i class="fas fa-plus"></i> Assign Benefits
                            </a>
                        @endif
                    @else
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle mr-2"></i>Not Eligible for Benefits</h6>
                            <p class="mb-0">This employee type is not eligible for company benefits.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt text-warning mr-2"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('employees.benefits', $employee) }}" class="btn btn-success">
                            <i class="fas fa-gift"></i> Manage Benefits
                        </a>
                        <a href="{{ route('regular-employees.edit', $employee) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Employee
                        </a>
                        @if($employee->qr_code)
                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#qrModal">
                                <i class="fas fa-qrcode"></i> View QR Code
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
@if($employee->qr_code)
<div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrModalLabel">Employee QR Code</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img src="{{ $employee->qr_code }}" alt="Employee QR Code" class="img-fluid">
                <p class="mt-3 text-muted">{{ $employee->full_name }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

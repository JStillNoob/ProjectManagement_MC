@extends('layouts.app')

@section('title', 'On-Call Employee Details')
@section('page-title', 'On-Call Employee Details')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-phone text-info mr-2"></i>
                        {{ $employee->full_name }}
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-info">
                            <i class="fas fa-phone mr-1"></i>On-Call Employee
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            @if($employee->image_name)
                                <img src="{{ asset('storage/' . $employee->image_name) }}" 
                                     alt="{{ $employee->full_name }}" 
                                     class="img-fluid rounded-circle mb-3" 
                                     style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <div class="bg-info rounded-circle d-flex align-items-center justify-content-center mb-3 mx-auto" 
                                     style="width: 150px; height: 150px;">
                                    <i class="fas fa-user text-white" style="font-size: 4rem;"></i>
                                </div>
                            @endif
                            
                            @if($employee->qr_code)
                                <div class="mt-3">
                                    <img src="{{ $employee->qr_code }}" alt="QR Code" class="img-fluid" style="max-width: 100px;">
                                    <p class="text-muted small mt-1">Employee QR Code</p>
                                </div>
                            @endif
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
                                        <span class="badge badge-info">
                                            <i class="fas fa-phone mr-1"></i>{{ $employee->employeeType->EmployeeTypeName ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Start Date:</strong></td>
                                    <td>{{ $employee->start_date->format('F d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge {{ $employee->status == 'Active' ? 'badge-success' : 'badge-danger' }}">
                                            <i class="fas fa-circle mr-1"></i>{{ $employee->status }}
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

                            @if($employee->employeeType && !$employee->employeeType->hasBenefits)
                                <div class="alert alert-info mt-4">
                                    <h6><i class="fas fa-info-circle mr-2"></i>Benefits Information</h6>
                                    <p class="mb-0">On-call employees are not eligible for company benefits as per employment type policy.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="btn-group" role="group">
                        <a href="{{ route('oncall-employees.edit', $employee) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('oncall-employees.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        <form action="{{ route('oncall-employees.destroy', $employee) }}" method="POST" 
                              style="display: inline-block;" 
                              onsubmit="return confirm('Are you sure you want to delete this on-call employee?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle text-info mr-2"></i>
                        On-Call Employee Info
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-phone mr-2"></i>On-Call Employment</h6>
                        <ul class="list-unstyled mb-0">
                            <li><i class="fas fa-clock text-warning mr-1"></i> Flexible Schedule</li>
                            <li><i class="fas fa-phone text-primary mr-1"></i> Called as Needed</li>
                            <li><i class="fas fa-calendar text-info mr-1"></i> No Fixed Hours</li>
                            <li><i class="fas fa-handshake text-success mr-1"></i> Project-Based Work</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-success">
                        <h6><i class="fas fa-check-circle mr-2"></i>Advantages</h6>
                        <ul class="list-unstyled mb-0">
                            <li><i class="fas fa-calendar-alt text-success mr-1"></i> Flexible Schedule</li>
                            <li><i class="fas fa-handshake text-success mr-1"></i> Project-Based Work</li>
                            <li><i class="fas fa-clock text-success mr-1"></i> Work When Available</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle mr-2"></i>Note</h6>
                        <p class="mb-0">On-call employees are typically paid per project or per hour worked, with no fixed monthly salary or benefits.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

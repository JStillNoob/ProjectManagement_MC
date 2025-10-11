@extends('layouts.app')

@section('title', 'Position Details')
@section('page-title', 'Position Details')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-briefcase text-primary mr-2"></i>
                        Position Details: {{ $position->PositionName }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('positions.edit', $position) }}" class="btn btn-warning btn-sm"
                           style="background-color: #ffc107 !important; border: 2px solid #ffc107 !important; color: #212529 !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                            <i class="fas fa-edit"></i> Edit Position
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Position Information -->
                    <h5 class="text-primary mb-3"><i class="fas fa-info-circle mr-2"></i>Position Information</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-id-badge"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Position ID</span>
                                    <span class="info-box-number">{{ $position->PositionID }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-briefcase"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Position Name</span>
                                    <span class="info-box-number">{{ $position->PositionName }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-money-bill-wave"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Salary</span>
                                    <span class="info-box-number">₱{{ number_format($position->Salary, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Employees</span>
                                    <span class="info-box-number">{{ $position->employees->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <h5 class="text-secondary mb-3 mt-4"><i class="fas fa-clock mr-2"></i>Timestamps</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-secondary"><i class="fas fa-calendar-plus"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Created</span>
                                    <span class="info-box-number">{{ $position->created_at->format('M d, Y g:i A') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-dark"><i class="fas fa-calendar-edit"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Last Updated</span>
                                    <span class="info-box-number">{{ $position->updated_at->format('M d, Y g:i A') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Employees in this Position -->
                    @if($position->employees->count() > 0)
                        <h5 class="text-success mb-3 mt-4"><i class="fas fa-users mr-2"></i>Employees in this Position</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Employee ID</th>
                                        <th>Name</th>
                                        <th>Employee Type</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($position->employees as $employee)
                                        <tr>
                                            <td>{{ $employee->EmployeeID }}</td>
                                            <td>
                                                <strong>{{ $employee->first_name }} {{ $employee->last_name }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">
                                                    {{ $employee->employeeType->EmployeeTypeName ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($employee->status == 'Active')
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ $employee->status }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('employees.show', $employee) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            No employees are currently assigned to this position.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs text-secondary mr-2"></i>
                        Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('positions.edit', $position) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Position
                        </a>
                        <a href="{{ route('positions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Positions
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie text-info mr-2"></i>
                        Statistics
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Employees</span>
                                    <span class="info-box-number">{{ $position->employees->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-user-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Active Employees</span>
                                    <span class="info-box-number">{{ $position->employees->where('status', 'Active')->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

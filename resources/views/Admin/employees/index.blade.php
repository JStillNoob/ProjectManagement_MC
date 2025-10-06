@extends('layouts.app')

@section('title', 'Employee Management')
@section('page-title', 'Employee Management')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users mr-2"></i>
                        Employee Management
                    </h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Employee Type Selection Cards -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-user-tie mr-2"></i>
                                        Regular Employees
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">Full-time employees with complete benefits package including health insurance, retirement plan, vacation days, and sick leave.</p>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success mr-1"></i> Health Insurance</li>
                                        <li><i class="fas fa-check text-success mr-1"></i> Retirement Plan</li>
                                        <li><i class="fas fa-check text-success mr-1"></i> Paid Vacation</li>
                                        <li><i class="fas fa-check text-success mr-1"></i> Sick Leave</li>
                                        <li><i class="fas fa-check text-success mr-1"></i> Monthly Salary</li>
                                    </ul>
                                    <div class="mt-3">
                                        <a href="{{ route('regular-employees.index') }}" class="btn btn-success">
                                            <i class="fas fa-users"></i> Manage Regular Employees
                                        </a>
                                        <a href="{{ route('regular-employees.create') }}" class="btn btn-outline-success">
                                            <i class="fas fa-plus"></i> Add Regular Employee
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-phone mr-2"></i>
                                        On-Call Employees
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">Part-time employees called as needed with flexible scheduling and hourly compensation. No benefits package.</p>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-clock text-info mr-1"></i> Flexible Schedule</li>
                                        <li><i class="fas fa-phone text-info mr-1"></i> Called as Needed</li>
                                        <li><i class="fas fa-dollar-sign text-info mr-1"></i> Hourly Rate</li>
                                        <li><i class="fas fa-calendar text-info mr-1"></i> No Fixed Hours</li>
                                        <li><i class="fas fa-exclamation-triangle text-warning mr-1"></i> No Benefits</li>
                                    </ul>
                                    <div class="mt-3">
                                        <a href="{{ route('oncall-employees.index') }}" class="btn btn-info">
                                            <i class="fas fa-users"></i> Manage On-Call Employees
                                        </a>
                                        <a href="{{ route('oncall-employees.create') }}" class="btn btn-outline-info">
                                            <i class="fas fa-plus"></i> Add On-Call Employee
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-user-tie"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Regular Employees</span>
                                    <span class="info-box-number">
                                        {{ \App\Models\Employee::whereHas('employeeType', function($query) {
                                            $query->where('EmployeeTypeName', 'Regular');
                                        })->where('flag_deleted', 0)->count() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">On-Call Employees</span>
                                    <span class="info-box-number">
                                        {{ \App\Models\Employee::whereHas('employeeType', function($query) {
                                            $query->where('EmployeeTypeName', 'On-call');
                                        })->where('flag_deleted', 0)->count() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning">
                                    <i class="fas fa-file-contract"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Contract Employees</span>
                                    <span class="info-box-number">
                                        {{ \App\Models\Employee::whereHas('employeeType', function($query) {
                                            $query->where('EmployeeTypeName', 'Contract');
                                        })->where('flag_deleted', 0)->count() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Part-time Employees</span>
                                    <span class="info-box-number">
                                        {{ \App\Models\Employee::whereHas('employeeType', function($query) {
                                            $query->where('EmployeeTypeName', 'Part-time');
                                        })->where('flag_deleted', 0)->count() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- All Employees Table -->
                    <div class="mt-4">
                        <h5><i class="fas fa-list mr-2"></i>All Employees Overview</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Photo</th>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Employee Type</th>
                                        <th>Status</th>
                                        <th>Start Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($employees as $employee)
                                        <tr>
                                            <td>{{ $employee->id }}</td>
                                            <td>
                                                @if($employee->image_path)
                                                    <img src="{{ $employee->image_path }}" alt="{{ $employee->full_name }}"
                                                        class="img-circle elevation-2"
                                                        style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <div class="img-circle elevation-2 bg-secondary d-flex align-items-center justify-content-center text-white"
                                                         style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $employee->full_name }}</td>
                                            <td>{{ $employee->position }}</td>
                                            <td>
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
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $employee->status == 'Active' ? 'success' : 'danger' }}">
                                                    {{ $employee->status }}
                                                </span>
                                            </td>
                                            <td>{{ $employee->start_date->format('M d, Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('employees.show', $employee) }}" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('employees.destroy', $employee) }}" method="POST"
                                                        style="display: inline-block;"
                                                        onsubmit="return confirm('Are you sure you want to delete this employee?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No employees found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $employees->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
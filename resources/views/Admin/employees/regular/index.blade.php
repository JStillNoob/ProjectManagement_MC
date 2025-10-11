@extends('layouts.app')

@section('title', 'Regular Employees')
@section('page-title', 'Regular Employees')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-tie text-success mr-2"></i>
                        Regular Employees
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-sm me-2"
                           style="background-color: #6c757d !important; border: 2px solid #6c757d !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                            <i class="fas fa-arrow-left"></i> Back to Employee Management
                        </a>
                        <a href="{{ route('regular-employees.create') }}" class="btn btn-success btn-sm"
                           style="background-color: #28a745 !important; border: 2px solid #28a745 !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                            <i class="fas fa-plus"></i> Add Regular Employee
                        </a>
                    </div>
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

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Status</th>
                                    <th>Benefits</th>
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
                                            <span class="badge badge-{{ $employee->status == 'Active' ? 'success' : 'danger' }}">
                                                {{ $employee->status }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $benefitCount = $employee->employeeBenefits()->where('IsActive', true)->count();
                                            @endphp
                                            @if($benefitCount > 0)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-gift mr-1"></i>{{ $benefitCount }} Benefits
                                                </span>
                                            @else
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>No Benefits
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $employee->start_date->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('regular-employees.show', $employee) }}" class="btn btn-info btn-sm" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('employees.benefits', $employee) }}" class="btn btn-success btn-sm" title="Manage Benefits">
                                                    <i class="fas fa-gift"></i>
                                                </a>
                                                <a href="{{ route('regular-employees.edit', $employee) }}" class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('regular-employees.destroy', $employee) }}" method="POST"
                                                    style="display: inline-block;"
                                                    onsubmit="return confirm('Are you sure you want to archive this regular employee?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Archive">
                                                        <i class="fas fa-archive"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No regular employees found.</td>
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
@endsection
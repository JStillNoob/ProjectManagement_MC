@extends('layouts.app')

@section('title', 'On-Call Employees')
@section('page-title', 'On-Call Employees')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-phone text-info mr-2"></i>
                        On-Call Employees
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-sm me-2"
                           style="background-color: #6c757d !important; border: 2px solid #6c757d !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                            <i class="fas fa-arrow-left"></i> Back to Employee Management
                        </a>
                        <a href="{{ route('oncall-employees.create') }}" class="btn btn-info btn-sm"
                           style="background-color: #17a2b8 !important; border: 2px solid #17a2b8 !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                            <i class="fas fa-plus"></i> Add On-Call Employee
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
                                    <th>Salary</th>
                                    <th>Contact</th>
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
                                        <td>
                                            @php
                                                $position = $employee->relationLoaded('position') 
                                                    ? $employee->getRelation('position') 
                                                    : $employee->position()->first();
                                            @endphp
                                            {{ $position ? $position->PositionName : 'N/A' }}
                                        </td>
                                        <td>
                                            @if($employee->base_salary)
                                                <small class="text-success">
                                                    <i class="fas fa-money-bill-wave mr-1"></i>
                                                    {{ $employee->formatted_salary }}
                                                </small>
                                            @else
                                                <span class="text-muted">Not set</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($employee->contact_number)
                                                <small class="text-muted">{{ $employee->contact_number }}</small>
                                            @else
                                                <span class="text-muted">No contact</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $employee->status == 'Active' ? 'success' : 'danger' }}">
                                                {{ $employee->status }}
                                            </span>
                                        </td>
                                        <td>{{ $employee->formatted_start_date }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('oncall-employees.show', $employee) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('oncall-employees.edit', $employee) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('oncall-employees.destroy', $employee) }}" method="POST"
                                                    style="display: inline-block;"
                                                    onsubmit="return confirm('Are you sure you want to archive this on-call employee?')">
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
                                        <td colspan="9" class="text-center">No on-call employees found.</td>
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

    <!-- Include QR Code Modal -->
    @include('components.qr-code-modal')

    @if(session('show_qr_modal') && session('employee_data'))
        @push('scripts')
        <script>
        $(document).ready(function() {
            // Show QR code modal after employee creation
            const employeeData = @json(session('employee_data'));
            showQrCodeModal(employeeData);
        });
        </script>
        @endpush
    @endif
@endsection
@extends('layouts.app')

@section('title', 'Position Management')
@section('page-title', 'Position Management')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-briefcase text-primary mr-2"></i>
                        Position Management
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('positions.create') }}" class="btn btn-primary btn-sm"
                           style="background-color: #52b788 !important; border: 2px solid #52b788 !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                            <i class="fas fa-plus"></i> Add New Position
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

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
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
                                    <th>Position Name</th>
                                    <th>Salary</th>
                                    <th>Employees Count</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($positions as $position)
                                    <tr>
                                        <td>{{ $position->PositionID }}</td>
                                        <td>
                                            <strong>{{ $position->PositionName }}</strong>
                                        </td>
                                        <td>
                                            @if($position->Salary)
                                                <span class="text-success">
                                                    <i class="fas fa-money-bill-wave mr-1"></i>
                                                    ₱{{ number_format($position->Salary, 2) }}
                                                </span>
                                            @else
                                                <span class="text-muted">Not set</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                <i class="fas fa-users mr-1"></i>
                                                {{ $position->employees_count }} employee(s)
                                            </span>
                                        </td>
                                        <td>{{ $position->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('positions.show', $position) }}" class="btn btn-info btn-sm"
                                                   style="background-color: #17a2b8 !important; border: 2px solid #17a2b8 !important; color: white !important; opacity: 1 !important; visibility: visible !important;">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('positions.edit', $position) }}" class="btn btn-warning btn-sm"
                                                   style="background-color: #ffc107 !important; border: 2px solid #ffc107 !important; color: #212529 !important; opacity: 1 !important; visibility: visible !important;">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('positions.destroy', $position) }}" method="POST"
                                                    style="display: inline-block;"
                                                    onsubmit="return confirm('Are you sure you want to delete this position? This action cannot be undone.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                            style="background-color: #dc3545 !important; border: 2px solid #dc3545 !important; color: white !important; opacity: 1 !important; visibility: visible !important;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No positions found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $positions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    
@endsection

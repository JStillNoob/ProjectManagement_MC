@extends('layouts.app')

@section('title', 'Manage Users')
@section('page-title', 'Manage Users')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">User Accounts</h3>
                <div class="card-tools">
                    <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm"
                       style="background-color: #52b788 !important; border: 2px solid #52b788 !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                        <i class="fas fa-plus"></i> Add New User
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
                                <th>Employee</th>
                                <th>Email</th>
                                <th>User Type</th>
                                <th>Date Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>
                                        @if($user->employee)
                                            <div>
                                                <strong>{{ $user->employee->full_name }}</strong>
                                                @if($user->employee->position)
                                                    <br><small class="text-muted">{{ $user->employee->position }}</small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">No employee linked</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->Email }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $user->userType->UserType ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('users.show', $user) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" 
                                                  style="display: inline-block;" 
                                                  onsubmit="return confirm('Are you sure you want to delete this user?')">
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
                                    <td colspan="6" class="text-center">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
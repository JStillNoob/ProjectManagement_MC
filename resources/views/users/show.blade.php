@extends('layouts.app')

@section('title', 'User Profile')
@section('page-title', 'User Profile')

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- User Card -->
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    @if($user->employee)
                        <img class="profile-user-img img-fluid img-circle" 
                             src="{{ $user->employee->image_path }}" 
                             alt="{{ $user->employee->full_name }}">
                    @else
                        <img class="profile-user-img img-fluid img-circle" 
                             src="https://adminlte.io/themes/v3/dist/img/user4-128x128.jpg" 
                             alt="{{ $user->full_name }}">
                    @endif
                </div>

                <h3 class="profile-username text-center">
                    {{ $user->employee ? $user->employee->full_name : $user->full_name }}
                </h3>
                <p class="text-muted text-center">
                    @if($user->employee)
                        @php
                            $position = $user->employee->relationLoaded('position') 
                                ? $user->employee->getRelation('position') 
                                : $user->employee->position()->first();
                        @endphp
                        @if($position)
                            {{ $position->PositionName }}
                        @else
                            {{ $user->Position ?? 'N/A' }}
                        @endif
                    @else
                        {{ $user->Position ?? 'N/A' }}
                    @endif
                </p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>User ID</b> <a class="float-right">{{ $user->id }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Username</b> <a class="float-right">{{ $user->Username }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>User Type</b> 
                        <span class="float-right">
                            <span class="badge badge-info">{{ $user->userType->UserType ?? 'N/A' }}</span>
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>Role</b> 
                        <span class="float-right">
                            @if($user->role)
                                <span class="badge badge-success">{{ $user->role->RoleName }}</span>
                            @else
                                <span class="text-muted">No role assigned</span>
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>Date Created</b> <a class="float-right">{{ $user->formatted_created_at }}</a>
                    </li>
                </ul>

                <a href="{{ route('users.index') }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- User Details -->
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link active" href="#personal" data-toggle="tab">Employee Information</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#account" data-toggle="tab">Account Details</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Employee Information Tab -->
                    <div class="active tab-pane" id="personal">
                        @if($user->employee)
                            <div class="row">
                                <div class="col-md-6">
                                    <strong><i class="fas fa-user mr-1"></i> Employee Name</strong>
                                    <p class="text-muted">{{ $user->employee->full_name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong><i class="fas fa-briefcase mr-1"></i> Position</strong>
                                    @php
                                        $position = $user->employee->relationLoaded('position') 
                                            ? $user->employee->getRelation('position') 
                                            : $user->employee->position()->first();
                                    @endphp
                                    <p class="text-muted">{{ $position ? $position->PositionName : 'N/A' }}</p>
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <strong><i class="fas fa-envelope mr-1"></i> Email</strong>
                                    <p class="text-muted">{{ $user->employee->email ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong><i class="fas fa-phone mr-1"></i> Contact</strong>
                                    <p class="text-muted">{{ $user->employee->contact_number ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <strong><i class="fas fa-calendar mr-1"></i> Date Hired</strong>
                                    <p class="text-muted">{{ $user->employee->formatted_start_date }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong><i class="fas fa-toggle-on mr-1"></i> Status</strong>
                                    <p class="text-muted">{{ $user->employee->status ?? 'N/A' }}</p>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>No Employee Linked:</strong> This user account is not linked to any employee record.
                            </div>
                        @endif
                    </div>

                    <!-- Account Details Tab -->
                    <div class="tab-pane" id="account">
                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-user mr-1"></i> Username</strong>
                                <p class="text-muted">{{ $user->Username }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-shield-alt mr-1"></i> User Type</strong>
                                <p class="text-muted">
                                    <span class="badge badge-info">{{ $user->userType->UserType ?? 'N/A' }}</span>
                                </p>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-user-tag mr-1"></i> Role</strong>
                                <p class="text-muted">
                                    @if($user->role)
                                        <span class="badge badge-success">{{ $user->role->RoleName }}</span>
                                    @else
                                        <span class="text-muted">No role assigned</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-toggle-on mr-1"></i> Account Status</strong>
                                <p class="text-muted">
                                    <span class="badge badge-success">Active</span>
                                </p>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-key mr-1"></i> Password</strong>
                                <p class="text-muted">••••••••</p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-calendar-alt mr-1"></i> Account Duration</strong>
                                <p class="text-muted">
                                    @php
                                        $createdAt = $user->created_at;
                                        $today = now();
                                        $duration = $createdAt->diffInDays($today);
                                        $years = floor($duration / 365);
                                        $months = floor(($duration % 365) / 30);
                                        $days = $duration % 30;
                                    @endphp
                                    {{ $years }} year(s), {{ $months }} month(s), {{ $days }} day(s)
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
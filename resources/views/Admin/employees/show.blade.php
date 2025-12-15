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
                @php
                    $position = $employee->relationLoaded('position') 
                        ? $employee->getRelation('position') 
                        : $employee->position()->first();
                @endphp
                <p class="text-muted text-center">{{ $position ? $position->PositionName : 'N/A' }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Employee ID</b> <a class="float-right">{{ $employee->id }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Status</b> 
                        <span class="float-right">
                            @php
                                $badgeClass = 'secondary'; // Default for Inactive
                                if ($employee->employee_status_id == \App\Models\EmployeeStatus::ACTIVE) {
                                    $badgeClass = 'success';
                                } elseif ($employee->employee_status_id == \App\Models\EmployeeStatus::ARCHIVED) {
                                    $badgeClass = 'danger';
                                }
                            @endphp
                            <span class="badge badge-{{ $badgeClass }}">
                                {{ $employee->status_name }}
                            </span>
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>Start Date</b> <a class="float-right">{{ $employee->formatted_start_date }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Age</b> <a class="float-right">{{ $employee->age ? $employee->age . ' years old' : 'N/A' }}</a>
                    </li>
                </ul>

                <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
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
                                <p class="text-muted">{{ $employee->formatted_birthday }}</p>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-calendar mr-1"></i> Age</strong>
                                <p class="text-muted">{{ $employee->age ? $employee->age . ' years old' : 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-map-marker-alt mr-1"></i> Address</strong>
                                <p class="text-muted">{{ $employee->full_address ?: 'N/A' }}</p>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-clock mr-1"></i> Date Created</strong>
                                <p class="text-muted">{{ $employee->formatted_created_at }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-edit mr-1"></i> Last Updated</strong>
                                <p class="text-muted">{{ $employee->formatted_updated_at }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Employment Details Tab -->
                    <div class="tab-pane" id="employment">
                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-briefcase mr-1"></i> Position</strong>
                                @php
                                    $position = $employee->relationLoaded('position') 
                                        ? $employee->getRelation('position') 
                                        : $employee->position()->first();
                                @endphp
                                <p class="text-muted">{{ $position ? $position->PositionName : 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-toggle-on mr-1"></i> Status</strong>
                                <p class="text-muted">
                                    @php
                                        $badgeClass = 'secondary'; // Default for Inactive
                                        if ($employee->employee_status_id == \App\Models\EmployeeStatus::ACTIVE) {
                                            $badgeClass = 'success';
                                        } elseif ($employee->employee_status_id == \App\Models\EmployeeStatus::ARCHIVED) {
                                            $badgeClass = 'danger';
                                        }
                                    @endphp
                                    <span class="badge badge-{{ $badgeClass }}">
                                        {{ $employee->status_name }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-calendar-check mr-1"></i> Start Date</strong>
                                <p class="text-muted">{{ $employee->formatted_start_date }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-calendar-alt mr-1"></i> Employment Duration</strong>
                                <p class="text-muted">
                                    @php
                                        if ($employee->start_date) {
                                            $startDate = $employee->start_date;
                                            $today = now();
                                            $duration = $startDate->diffInDays($today);
                                            $years = floor($duration / 365);
                                            $months = floor(($duration % 365) / 30);
                                            $days = $duration % 30;
                                            echo $years . ' year(s), ' . $months . ' month(s), ' . $days . ' day(s)';
                                        } else {
                                            echo 'N/A';
                                        }
                                    @endphp
                                </p>
                            </div>
                        </div>

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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
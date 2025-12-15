@extends('layouts.app')

@section('title', 'My Projects')
@section('page-title', 'My Projects')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h3 class="card-title mb-0">
                    <i class="fas fa-project-diagram mr-2"></i>
                    My Assigned Projects
                </h3>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(isset($message))
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        {{ $message }}
                    </div>
                @endif

                @if($projects->count() > 0)
                    <div class="row">
                        @foreach($projects as $project)
                            @php
                                $totalMilestones = $project->milestones->count();
                                $completedMilestones = $project->milestones->where('status', 'Completed')->count();
                                $inProgressMilestones = $project->milestones->where('status', 'In Progress')->count();
                                $pendingApproval = $project->milestones->where('SubmissionStatus', 'Pending Approval')->count();
                                $progressPercentage = $totalMilestones > 0 ? round(($completedMilestones / $totalMilestones) * 100) : 0;
                            @endphp
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card shadow-sm h-100 border-0" style="transition: transform 0.2s; border-top: 4px solid {{ 
                                    $project->status->StatusName == 'Completed' ? '#28a745' : 
                                    ($project->status->StatusName == 'On Going' ? '#007bff' : 
                                    ($project->status->StatusName == 'Under Warranty' ? '#ffc107' : 
                                    ($project->status->StatusName == 'Pending' ? '#17a2b8' : 
                                    ($project->status->StatusName == 'Pre-Construction' ? '#ffc107' :
                                    ($project->status->StatusName == 'On Hold' ? '#6c757d' : '#dc3545')))))
                                }} !important;">
                                    <div class="card-header bg-white pb-2">
                                        <h5 class="card-title mb-1">
                                            <i class="fas fa-project-diagram mr-2 text-primary"></i>
                                            {{ Str::limit($project->ProjectName, 30) }}
                                        </h5>
                                        <span class="badge badge-{{ 
                                            $project->status->StatusName == 'Completed' ? 'success' : 
                                            ($project->status->StatusName == 'On Going' ? 'primary' : 
                                            ($project->status->StatusName == 'Under Warranty' ? 'warning' : 
                                            ($project->status->StatusName == 'Pending' ? 'info' : 
                                            ($project->status->StatusName == 'Pre-Construction' ? 'warning' :
                                            ($project->status->StatusName == 'On Hold' ? 'secondary' : 'danger')))))
                                        }} badge-pill">
                                            {{ $project->status->StatusName }}
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        @if($project->ProjectDescription)
                                            <p class="text-muted small mb-3">{{ Str::limit($project->ProjectDescription, 80) }}</p>
                                        @endif
                                        
                                        <div class="mb-3">
                                            <small class="text-muted d-block mb-1">
                                                <i class="fas fa-user-tie mr-1"></i>
                                                <strong>Client:</strong> {{ $project->client->ClientName ?? 'N/A' }}
                                            </small>
                                            <small class="text-muted d-block mb-1">
                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                <strong>Start:</strong> {{ $project->formatted_start_date ?? 'N/A' }}
                                            </small>
                                            <small class="text-muted d-block">
                                                <i class="fas fa-calendar-check mr-1"></i>
                                                <strong>End:</strong> {{ $project->formatted_end_date ?? 'N/A' }}
                                            </small>
                                        </div>

                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small class="text-muted">
                                                    <i class="fas fa-flag-checkered mr-1"></i>
                                                    <strong>Milestones Progress</strong>
                                                </small>
                                                <small class="font-weight-bold">{{ $progressPercentage }}%</small>
                                            </div>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progressPercentage }}%" aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    <span class="text-success"><i class="fas fa-check-circle mr-1"></i>{{ $completedMilestones }} Completed</span> | 
                                                    <span class="text-primary"><i class="fas fa-spinner mr-1"></i>{{ $inProgressMilestones }} In Progress</span>
                                                    @if($pendingApproval > 0)
                                                        | <span class="text-warning"><i class="fas fa-clock mr-1"></i>{{ $pendingApproval }} Pending</span>
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-white">
                                        <a href="{{ route('foreman.projects.show', $project) }}" class="btn btn-primary btn-block">
                                            <i class="fas fa-eye mr-1"></i> View Project
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info text-center py-5">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <h5>No Projects Assigned</h5>
                        <p class="text-muted mb-0">You are not currently assigned to any projects. Please contact your administrator.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


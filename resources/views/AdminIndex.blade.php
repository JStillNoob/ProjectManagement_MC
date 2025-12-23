@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')

    <!-- Summary Cards Row -->
    <div class="row">
        <!-- Card 1: Pending Inventory Requests -->
        <div class="col-lg-3 col-6">
            <a href="{{ route('inventory.requests.index') }}" class="text-decoration-none">
                <div class="card" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start">
                            <div class="mr-3" style="font-size: 2.5rem; color: #6c757d;">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-2" style="font-size: 0.875rem; font-weight: 500; color: #6c757d;">Pending Inventory Requests</p>
                                <h3 class="mb-0 text-right" style="font-size: 2rem; font-weight: bold; color: #333;">{{ $pendingRequests }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center py-2" style="background-color: #f39c12; color: white; border: none;">
                        View Requests <i class="fas fa-arrow-circle-right ml-1"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card 2: In-Progress Projects -->
        <div class="col-lg-3 col-6">
            <a href="{{ route('projects.index') }}" class="text-decoration-none">
                <div class="card" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start">
                            <div class="mr-3" style="font-size: 2.5rem; color: #6c757d;">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-2" style="font-size: 0.875rem; font-weight: 500; color: #6c757d;">In-Progress Projects</p>
                                <h3 class="mb-0 text-right" style="font-size: 2rem; font-weight: bold; color: #333;">{{ $inProgressProjects }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center py-2" style="background-color: #17a2b8; color: white; border: none;">
                        View Projects <i class="fas fa-arrow-circle-right ml-1"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card 3: Overdue Milestones -->
        <div class="col-lg-3 col-6">
            <a href="{{ route('projects.index') }}" class="text-decoration-none">
                <div class="card" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start">
                            <div class="mr-3" style="font-size: 2.5rem; color: #6c757d;">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-2" style="font-size: 0.875rem; font-weight: 500; color: #6c757d;">Overdue Milestones</p>
                                <h3 class="mb-0 text-right" style="font-size: 2rem; font-weight: bold; color: #333;">{{ $overdueMilestones }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center py-2" style="background-color: #dc3545; color: white; border: none;">
                        View Projects <i class="fas fa-arrow-circle-right ml-1"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card 4: Projects Pending NTP -->
        <div class="col-lg-3 col-6">
            <a href="{{ route('projects.index') }}" class="text-decoration-none">
                <div class="card" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start">
                            <div class="mr-3" style="font-size: 2.5rem; color: #6c757d;">
                                <i class="fas fa-file-signature"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-2" style="font-size: 0.875rem; font-weight: 500; color: #6c757d;">Projects Pending NTP</p>
                                <h3 class="mb-0 text-right" style="font-size: 2rem; font-weight: bold; color: #333;">{{ $pendingNTPProjects }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center py-2" style="background-color: #87A96B; color: white; border: none;">
                        View Pending <i class="fas fa-arrow-circle-right ml-1"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Projects Overview with Progress Bars -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-line mr-2" style="color: #87A96B;"></i>Active Project
                        Progress</h3>
                </div>
                <div class="card-body">
                    @forelse($activeProjects as $project)
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="font-weight-bold">
                                    <a href="{{ route('projects.show', $project->ProjectID) }}"
                                        style="color: #333; text-decoration: none;">
                                        {{ $project->ProjectName }}
                                    </a>
                                </span>
                                @php
                                    $statusColor = match($project->status->StatusName) {
                                        'On Going' => '#17a2b8',
                                        'Delayed' => '#dc3545',
                                        'Pre-Construction' => '#ffc107',
                                        default => '#6c757d'
                                    };
                                @endphp
                                <span class="badge"
                                    style="background-color: {{ $statusColor }}; color: white;">
                                    @if($project->status->StatusName == 'Delayed')
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                    @endif
                                    {{ $project->status->StatusName }}
                                </span>
                            </div>
                            <div class="progress" style="height: 22px; border-radius: 4px;">
                                <div class="progress-bar" role="progressbar"
                                    style="width: {{ $project->progress_percentage }}%; background-color: #87A96B;"
                                    aria-valuenow="{{ $project->progress_percentage }}" aria-valuemin="0" aria-valuemax="100">
                                    <span style="font-weight: 600;">{{ $project->progress_percentage }}%</span>
                                </div>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-check-circle mr-1"></i>
                                {{ $project->milestone_counts['completed'] }}/{{ $project->milestone_counts['total'] }}
                                milestones completed
                                @if($project->client)
                                    <span class="ml-2">| <i
                                            class="fas fa-building mr-1"></i>{{ $project->client->ClientName ?? 'N/A' }}</span>
                                @endif
                            </small>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-folder-open fa-3x mb-3" style="color: #87A96B;"></i>
                            <p class="mb-0">No active projects at the moment.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Equipment Assignments Card -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-tools mr-2" style="color: #87A96B;"></i>Equipment Assignments
                    </h3>
                </div>
                <div class="card-body p-0">
                    @if($equipmentAssignments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead style="background-color: #f8f9fa;">
                                    <tr>
                                        <th>Equipment</th>
                                        <th>Qty</th>
                                        <th>Assigned To</th>
                                        <th>Date Assigned</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($equipmentAssignments as $assignment)
                                        <tr>
                                            <td>
                                                <i class="fas fa-wrench mr-2" style="color: #87A96B;"></i>
                                                <strong>{{ $assignment->item->resourceCatalog->ItemName ?? 'N/A' }}</strong>
                                            </td>
                                            <td>{{ number_format($assignment->QuantityAssigned, 0) }}</td>
                                            <td>
                                                @if($assignment->milestone && $assignment->milestone->project)
                                                    <a href="{{ route('projects.show', $assignment->milestone->project->ProjectID) }}"
                                                        style="color: #333;">
                                                        {{ $assignment->milestone->project->ProjectName }}
                                                    </a>
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="fas fa-flag mr-1"></i>{{ $assignment->milestone->milestone_name }}
                                                    </small>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $assignment->DateAssigned ? $assignment->DateAssigned->format('M d, Y') : 'N/A' }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge" style="background-color: #17a2b8; color: white;">
                                                    {{ $assignment->Status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-tools fa-3x mb-3" style="color: #87A96B;"></i>
                            <p class="mb-0">No equipment currently assigned.</p>
                        </div>
                    @endif
                </div>
                @if($equipmentAssignments->count() > 0)
                    <div class="card-footer text-center" style="background-color: #f8f9fa;">
                        <a href="{{ route('equipment.returns.index') }}" style="color: #87A96B;">
                            View All Equipment <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-bolt mr-2" style="color: #87A96B;"></i>Quick Actions</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('employees.index') }}" class="btn btn-block mb-3"
                        style="background-color: #87A96B; border-color: #87A96B; color: white;">
                        <i class="fas fa-users mr-2"></i>Manage Employees
                    </a>
                    <a href="{{ route('inventory.index') }}" class="btn btn-block mb-3"
                        style="background-color: #87A96B; border-color: #87A96B; color: white;">
                        <i class="fas fa-boxes mr-2"></i>Manage Inventory
                    </a>
                    <a href="{{ route('projects.index') }}" class="btn btn-block"
                        style="background-color: #87A96B; border-color: #87A96B; color: white;">
                        <i class="fas fa-project-diagram mr-2"></i>Manage Projects
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
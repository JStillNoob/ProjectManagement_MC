@extends('layouts.app')

@section('title', 'Project Details - ' . $project->ProjectName)
@section('page-title', 'Project Details')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @php
                        $milestoneCounts = $project->milestone_counts;
                    @endphp

                    <!-- Top Row: 3 Cards -->
                    <div class="row mb-4">
                        <!-- Card 1: Project & Client Details (Combined) -->
                        <div class="col-lg-4 mb-4 mb-lg-0">
                            <div class="card h-100 shadow-sm" style="border-radius: 8px;">
                                <div class="card-header">
                                    <h5 class="mb-0 font-weight-bold">Project & Client Details</h5>
                                </div>
                                <div class="card-body"
                                    style="background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);">
                                    <!-- Project Details Section -->
                                    <div class="mb-4 pb-4" style="border-bottom: 2px solid #e9ecef;">
                                        <h6 class="text-primary mb-3 font-weight-bold">
                                            <i class="fas fa-project-diagram mr-2"></i>Project
                                        </h6>

                                        <!-- Project Name -->
                                        <div class="mb-3">
                                            <strong
                                                style="color: #2d3748; font-size: 1rem; display: block;">{{ $project->ProjectName }}</strong>
                                        </div>

                                        <!-- Status and Duration -->
                                        <div class="d-flex align-items-center mb-3 pb-3"
                                            style="border-bottom: 1px solid #e9ecef;">
                                            <div class="mr-4">
                                                @php
                                                    $statusClass = $project->status->StatusName == 'Completed' ? 'success' :
                                                        ($project->status->StatusName == 'On Going' ? 'primary' :
                                                            ($project->status->StatusName == 'Under Warranty' ? 'warning' :
                                                                ($project->status->StatusName == 'Pending' ? 'info' :
                                                                    ($project->status->StatusName == 'Pre-Construction' ? 'warning' :
                                                                        ($project->status->StatusName == 'Delayed' ? 'danger' :
                                                                            ($project->status->StatusName == 'On Hold' ? 'secondary' : 'info'))))));
                                                @endphp
                                                <span class="badge badge-{{ $statusClass }}"
                                                    style="font-size: 0.75rem;">
                                                    @if($project->status->StatusName == 'Delayed')
                                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                                    @endif
                                                    {{ $project->status->StatusName }}
                                                </span>
                                            </div>
                                            <div>
                                                <i class="fas fa-calendar-alt text-warning mr-1"></i>
                                                <strong
                                                    style="color: #2d3748; font-size: 0.85rem;">{{ $project->EstimatedAccomplishDays ?? 'N/A' }}
                                                    days</strong>
                                            </div>
                                        </div>

                                        <!-- Start and End Date -->
                                        <div class="d-flex align-items-center mb-3 pb-3"
                                            style="border-bottom: 1px solid #e9ecef;">
                                            <div class="mr-4">
                                                <i class="fas fa-play-circle text-success mr-1"></i>
                                                <strong
                                                    style="color: #2d3748; font-size: 0.85rem;">{{ $project->formatted_start_date ?? 'N/A' }}</strong>
                                            </div>
                                            <div>
                                                <i class="fas fa-flag-checkered text-danger mr-1"></i>
                                                <strong
                                                    style="color: #2d3748; font-size: 0.85rem;">{{ $project->formatted_end_date ?? 'N/A' }}</strong>
                                            </div>
                                        </div>

                                        <!-- Location -->
                                        @if($project->full_address)
                                            <div class="mb-3 pb-3" style="border-bottom: 1px solid #e9ecef;">
                                                <div class="d-flex align-items-start">
                                                    <i class="fas fa-map-marker-alt text-danger mr-2 mt-1"></i>
                                                    <strong
                                                        style="color: #2d3748; font-size: 0.8rem;">{{ $project->full_address }}</strong>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Description -->
                                        @if($project->ProjectDescription)
                                            <div class="mt-3">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-file-alt text-secondary mr-2"></i>
                                                    <small class="text-muted font-weight-bold"
                                                        style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Description</small>
                                                </div>
                                                <p class="text-muted mb-0" style="font-size: 0.8rem; line-height: 1.4;">
                                                    {{ Str::limit($project->ProjectDescription, 100) }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Client Details Section -->
                                    <div>
                                        <h6 class="text-primary mb-3 font-weight-bold">
                                            <i class="fas fa-user-tie mr-2"></i>Client
                                        </h6>
                                        @if($project->client)
                                            <div class="client-header mb-3 pb-3" style="border-bottom: 1px solid #e9ecef;">
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-2"
                                                        style="width: 40px; height: 40px; font-size: 0.9rem; font-weight: bold;">
                                                        {{ strtoupper(substr($project->client->ClientName, 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <strong
                                                            style="color: #2d3748; font-size: 0.9rem;">{{ Str::limit($project->client->ClientName, 20) }}</strong>
                                                    </div>
                                                </div>
                                            </div>

                                            @php
                                                $contactPerson = trim(($project->client->FirstName ?? '') . ' ' . ($project->client->LastName ?? ''));
                                            @endphp

                                            @if($contactPerson)
                                                <div class="info-row mb-3 pb-3" style="border-bottom: 1px solid #e9ecef;">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <i class="fas fa-user text-primary mr-2"></i>
                                                        <small class="text-muted font-weight-bold"
                                                            style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Contact</small>
                                                    </div>
                                                    <div class="ml-4">
                                                        <strong
                                                            style="color: #2d3748; font-size: 0.85rem;">{{ $contactPerson }}</strong>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($project->client->ContactNumber)
                                                <div class="info-row mb-3 pb-3" style="border-bottom: 1px solid #e9ecef;">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <i class="fas fa-phone text-success mr-2"></i>
                                                        <small class="text-muted font-weight-bold"
                                                            style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Phone</small>
                                                    </div>
                                                    <div class="ml-4">
                                                        <strong
                                                            style="color: #2d3748; font-size: 0.85rem;">{{ $project->client->ContactNumber }}</strong>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($project->client->Email)
                                                <div class="info-row mb-3 pb-3" style="border-bottom: 1px solid #e9ecef;">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <i class="fas fa-envelope text-info mr-2"></i>
                                                        <small class="text-muted font-weight-bold"
                                                            style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Email</small>
                                                    </div>
                                                    <div class="ml-4">
                                                        <strong
                                                            style="color: #2d3748; font-size: 0.75rem; word-break: break-word;">{{ Str::limit($project->client->Email, 25) }}</strong>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($project->client->full_address)
                                                <div class="info-row mb-3">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <i class="fas fa-map-marker-alt text-danger mr-2"></i>
                                                        <small class="text-muted font-weight-bold"
                                                            style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Address</small>
                                                    </div>
                                                    <div class="ml-4">
                                                        <strong
                                                            style="color: #2d3748; font-size: 0.8rem;">{{ Str::limit($project->client->full_address, 35) }}</strong>
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            <div class="text-center py-3">
                                                <i class="fas fa-user-slash text-muted mb-2"
                                                    style="font-size: 2rem; opacity: 0.3;"></i>
                                                <p class="text-muted mb-0" style="font-size: 0.8rem;">No client assigned</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2: Assigned Employees -->
                        <div class="col-lg-4 mb-4 mb-lg-0">
                            <div class="card h-100 shadow-sm" style="border-radius: 8px;">
                                <div class="card-header"
                                    style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 font-weight-bold" style="font-size: 0.9rem;">Assigned Employees</h6>
                                        <div>
                                            <a href="{{ route('projects.employees.qr-pdf', $project) }}"
                                                class="btn btn-sm btn-info mr-1" target="_blank"
                                                style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                                                <i class="fas fa-qrcode"></i> QR
                                            </a>
                                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal"
                                                data-target="#assignEmployeeModal"
                                                style="background-color: #7fb069 !important; border-color: #7fb069 !important; padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                                                <i class="fas fa-user-plus"></i> Add
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body" style="padding: 0.75rem;">
                                    @if($project->projectEmployees && $project->projectEmployees->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm mb-0">
                                                <tbody>
                                                    @foreach($project->projectEmployees as $assignment)
                                                        @php
                                                            $emp = $assignment->employee;
                                                            $position = $emp->relationLoaded('position') ? $emp->getRelation('position') : null;
                                                            if (!$position) {
                                                                $position = $emp->position()->first();
                                                            }
                                                            $positionName = ($position && is_object($position)) ? $position->PositionName : 'N/A';
                                                        @endphp
                                                        <tr>
                                                            <td class="border-0 pb-2">
                                                                <div class="d-flex align-items-center justify-content-between">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mr-2"
                                                                            style="width: 32px; height: 32px; font-size: 11px; flex-shrink: 0;">
                                                                            {{ strtoupper(substr($emp->full_name, 0, 2)) }}
                                                                        </div>
                                                                        <div>
                                                                            <div
                                                                                style="font-size: 0.8rem; font-weight: 600; color: #2d3748;">
                                                                                {{ $emp->full_name }}
                                                                            </div>
                                                                            <small class="text-muted"
                                                                                style="font-size: 0.7rem;">{{ $positionName }}</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex align-items-center">
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-outline-info mr-1"
                                                                            style="padding: 0.15rem 0.35rem; font-size: 0.7rem;"
                                                                            data-toggle="modal"
                                                                            data-target="#showQrModal{{ $assignment->ProjectEmployeeID }}">
                                                                            <i class="fas fa-qrcode"></i>
                                                                        </button>
                                                                        <form
                                                                            action="{{ route('projects.assignments.remove', [$project, $assignment]) }}"
                                                                            method="POST" class="d-inline"
                                                                            onsubmit="return confirm('Remove this employee?')">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit"
                                                                                class="btn btn-sm btn-outline-danger"
                                                                                style="padding: 0.15rem 0.35rem; font-size: 0.7rem;">
                                                                                <i class="fas fa-times"></i>
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-users text-muted mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                            <p class="text-muted mb-2" style="font-size: 0.8rem;">No employees assigned</p>
                                            @if(isset($availableEmployees) && $availableEmployees->count() > 0)
                                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal"
                                                    data-target="#assignEmployeeModal"
                                                    style="background-color: #7fb069 !important; border-color: #7fb069 !important; font-size: 0.75rem;">
                                                    <i class="fas fa-user-plus"></i> Assign Employee
                                                </button>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Card 3: Project Status with Progress Bar -->
                        <div class="col-lg-4">
                            <div class="card h-100 shadow-sm" style="border-radius: 8px;">
                                <div class="card-header">
                                    <h6 class="mb-0 font-weight-bold">Project Status</h6>
                                </div>
                                <div class="card-body">
                                    @if($milestoneCounts['total'] > 0)
                                        <!-- Progress Bar Section -->
                                        <div class="mb-3 pb-3" style="border-bottom: 2px solid #e9ecef;">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="font-weight-bold" style="font-size: 0.85rem;">Overall
                                                    Progress</span>
                                                <span class="badge"
                                                    style="background: #7fb069; color: white; font-size: 0.8rem;">{{ $project->progress_percentage }}%</span>
                                            </div>
                                            <div class="progress" style="height: 18px; border-radius: 10px;">
                                                <div class="progress-bar" role="progressbar"
                                                    style="width: {{ $project->progress_percentage }}%; background: linear-gradient(135deg, #7fb069 0%, #6fa05a 100%);"
                                                    aria-valuenow="{{ $project->progress_percentage }}" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                    <span
                                                        style="font-size: 0.75rem;">{{ $project->progress_percentage }}%</span>
                                                </div>
                                            </div>
                                            <div class="mt-2 d-flex justify-content-between" style="font-size: 0.7rem;">
                                                <small class="text-muted">
                                                    <i class="fas fa-check-circle text-success"></i>
                                                    {{ $milestoneCounts['completed'] }} Done
                                                </small>
                                                <small class="text-muted">
                                                    <i class="fas fa-spinner text-warning"></i>
                                                    {{ $milestoneCounts['in_progress'] }} Active
                                                </small>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock text-secondary"></i>
                                                    {{ $milestoneCounts['pending'] }} Pending
                                                </small>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Pie Chart Section -->
                                    <div class="text-center">
                                        <canvas id="projectStatusChart" width="180" height="180"></canvas>
                                        <div class="mt-3">
                                            <div class="row">
                                                <div class="col-6 mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge"
                                                            style="background-color: #dc3545; width: 10px; height: 10px; display: inline-block; margin-right: 6px; border-radius: 50%;"></span>
                                                        <small style="font-size: 0.75rem;">{{ $milestoneCounts['backlog'] }}
                                                            Backlog</small>
                                                    </div>
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge"
                                                            style="background-color: #ff9f40; width: 10px; height: 10px; display: inline-block; margin-right: 6px; border-radius: 50%;"></span>
                                                        <small style="font-size: 0.75rem;">{{ $milestoneCounts['pending'] }}
                                                            Pending</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge"
                                                            style="background-color: #FFCE56; width: 10px; height: 10px; display: inline-block; margin-right: 6px; border-radius: 50%;"></span>
                                                        <small
                                                            style="font-size: 0.75rem;">{{ $milestoneCounts['in_progress'] }}
                                                            Active</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge"
                                                            style="background-color: #28a745; width: 10px; height: 10px; display: inline-block; margin-right: 6px; border-radius: 50%;"></span>
                                                        <small
                                                            style="font-size: 0.75rem;">{{ $milestoneCounts['completed'] }}
                                                            Done</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Project Milestones -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white border-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0 font-weight-bold text-dark">
                                            <i class="fas fa-tasks mr-2"></i>Project Milestones
                                        </h5>
                                        <button type="button" class="btn btn-primary" data-toggle="modal"
                                            data-target="#addMilestoneModal"
                                            style="background-color: #7fb069 !important; border-color: #7fb069 !important; color: white !important;">
                                            <i class="fas fa-plus mr-1"></i> Add New Milestone
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if($project->milestones && $project->milestones->count())
                                        <div class="row">
                                            @foreach($project->milestones as $milestone)
                                                @php
                                                    $items = $milestone->requiredItems;
                                                    $displayItems = $items->take(4);
                                                    $remaining = $items->count() - $displayItems->count();
                                                    $status = strtolower($milestone->status ?? 'Pending');
                                                    $statusClass = $status === 'completed' ? 'success' : ($status === 'in progress' ? 'warning' : 'secondary');
                                                    $isCompleted = $status === 'completed';
                                                @endphp
                                                <div class="col-md-6 col-lg-4 mb-3">
                                                    <div class="card h-100 border-0 shadow-sm edit-milestone-card milestone-req-card {{ $isCompleted ? 'milestone-completed' : '' }}"
                                                        style="border-radius: 14px; {{ $isCompleted ? 'cursor: not-allowed; opacity: 0.7;' : 'cursor: pointer;' }}"
                                                        data-milestone-id="{{ $milestone->milestone_id }}"
                                                        data-milestone-name="{{ $milestone->milestone_name }}"
                                                        data-description="{{ $milestone->description }}"
                                                        data-estimated-days="{{ $milestone->EstimatedDays }}"
                                                        data-actual-date="{{ optional($milestone->target_date)->toDateString() ?? '' }}"
                                                        data-status="{{ $milestone->status }}"
                                                        data-submission-status="{{ $milestone->SubmissionStatus ?? 'Not Submitted' }}"
                                                        data-is-completed="{{ $isCompleted ? 'true' : 'false' }}">
                                                        <div class="card-body pb-3">
                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                @php $targetDate = $milestone->formatted_target_date ?? null; @endphp
                                                                <div>
                                                                    <h6 class="mb-0 font-weight-bold text-dark">
                                                                        {{ $milestone->milestone_name ?? 'Milestone' }}
                                                                    </h6>
                                                                    @if($targetDate && $targetDate !== 'N/A')
                                                                        <small class="text-muted">Target: {{ $targetDate }}</small>
                                                                    @elseif($milestone->status === 'Pending')
                                                                        <small class="text-muted text-info">Target date will be set when started</small>
                                                                    @endif
                                                                    @if($milestone->is_overdue)
                                                                        <br><span class="badge badge-danger mt-1">
                                                                            <i class="fas fa-exclamation-circle"></i> Overdue ({{ $milestone->days_overdue }} days)
                                                                        </span>
                                                                    @endif
                                                                    @if($milestone->is_early)
                                                                        <br><span class="badge badge-success mt-1">
                                                                            <i class="fas fa-check-circle"></i> Completed Early ({{ $milestone->days_early }} days ahead)
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <span
                                                                    class="badge milestone-req-status badge-{{ $statusClass }} text-capitalize">{{ $milestone->status ?? 'Pending' }}</span>
                                                            </div>
                                                            @if($displayItems->count())
                                                                <div class="list-group list-group-flush">
                                                                    @foreach($displayItems as $req)
                                                                        <div
                                                                            class="list-group-item px-0 py-1 d-flex justify-content-between align-items-center">
                                                                            <div>
                                                                                <div class="font-weight-semibold text-dark">
                                                                                    {{ $req->item->resourceCatalog->ItemName ?? '' }}
                                                                                </div>
                                                                                <small class="text-muted">Qty:
                                                                                    {{ number_format($req->estimated_quantity, 2) }}
                                                                                    {{ $req->unit ?? ($req->item->resourceCatalog->Unit ?? '') }}</small>
                                                                            </div>
                                                                            <span
                                                                                class="badge badge-light border text-muted">{{ $req->item->resourceCatalog->Type ?? '' }}</span>
                                                                        </div>
                                                                    @endforeach
                                                                    @if($remaining > 0)
                                                                        <div class="list-group-item px-0 py-1 text-muted small">
                                                                            +{{ $remaining }} more item(s)</div>
                                                                    @endif
                                                                </div>
                                                            @else
                                                                <div class="text-muted small">No required items defined for this
                                                                    milestone.</div>
                                                            @endif
                                                        </div>
                                                        <div class="card-footer bg-white border-0 pt-0">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <span class="badge badge-light text-muted">Required Items</span>
                                                                    <span class="text-muted small">{{ $items->count() }}
                                                                        total</span>
                                                                </div>
                                                                @if($isCompleted)
                                                                    <span class="text-muted small"><i class="fas fa-lock mr-1"></i>Completed - Cannot edit</span>
                                                                @else
                                                                    <span class="text-muted small"><i class="fas fa-edit mr-1"></i>Click
                                                                        card to edit</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-5">
                                            <i class="fas fa-tasks text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                                            <h6 class="text-muted mb-3">No milestones defined yet</h6>
                                            <p class="text-muted mb-4">Create your first milestone to start tracking project
                                                progress</p>
                                            <button type="button" class="btn btn-primary btn-lg" data-toggle="modal"
                                                data-target="#addMilestoneModal"
                                                style="background-color: #7fb069 !important; border-color: #7fb069 !important; color: white !important;">
                                                <i class="fas fa-plus mr-2"></i> Add First Milestone
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Project Attachments -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card shadow-sm" style="border-radius: 8px;">
                                <div class="card-header"
                                    style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0 font-weight-bold">
                                            <i class="fas fa-images mr-2 text-primary"></i>Project Attachments
                                        </h5>
                                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#uploadAttachmentModal"
                                            style="background-color: #87A96B !important; border-color: #87A96B !important;">
                                            <i class="fas fa-upload mr-1"></i> Upload Image
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @php
                                        $hasAttachments = $project->BlueprintPath || $project->FloorPlanPath || $project->NTPAttachment || ($project->additional_images && count($project->additional_images) > 0);
                                    @endphp

                                    @if($hasAttachments)
                                        <div class="row">
                                            @if($project->BlueprintPath)
                                                <div class="col-md-4 mb-3">
                                                    <div class="card border-0 shadow-sm h-100">
                                                        <div class="position-relative">
                                                            <a href="{{ asset('storage/' . $project->BlueprintPath) }}"
                                                                data-lightbox="project-attachments" data-title="Project Blueprint">
                                                                <img src="{{ asset('storage/' . $project->BlueprintPath) }}"
                                                                    class="card-img-top" alt="Blueprint"
                                                                    style="height: 200px; object-fit: cover; cursor: pointer;">
                                                            </a>
                                                            <div class="position-absolute top-0 left-0 m-2">
                                                                <span class="badge badge-primary">
                                                                    <i class="fas fa-drafting-compass mr-1"></i>Blueprint
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <a href="{{ asset('storage/' . $project->BlueprintPath) }}"
                                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-external-link-alt mr-1"></i>Open
                                                            </a>
                                                            <a href="{{ asset('storage/' . $project->BlueprintPath) }}" download
                                                                class="btn btn-sm btn-outline-secondary ml-1">
                                                                <i class="fas fa-download mr-1"></i>Download
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($project->FloorPlanPath)
                                                <div class="col-md-4 mb-3">
                                                    <div class="card border-0 shadow-sm h-100">
                                                        <div class="position-relative">
                                                            <a href="{{ asset('storage/' . $project->FloorPlanPath) }}"
                                                                data-lightbox="project-attachments" data-title="Project Floor Plan">
                                                                <img src="{{ asset('storage/' . $project->FloorPlanPath) }}"
                                                                    class="card-img-top" alt="Floor Plan"
                                                                    style="height: 200px; object-fit: cover; cursor: pointer;">
                                                            </a>
                                                            <div class="position-absolute top-0 left-0 m-2">
                                                                <span class="badge badge-info">
                                                                    <i class="fas fa-layer-group mr-1"></i>Floor Plan
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <a href="{{ asset('storage/' . $project->FloorPlanPath) }}"
                                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-external-link-alt mr-1"></i>Open
                                                            </a>
                                                            <a href="{{ asset('storage/' . $project->FloorPlanPath) }}" download
                                                                class="btn btn-sm btn-outline-secondary ml-1">
                                                                <i class="fas fa-download mr-1"></i>Download
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($project->NTPAttachment)
                                                <div class="col-md-4 mb-3">
                                                    <div class="card border-0 shadow-sm h-100">
                                                        <div class="position-relative">
                                                            @php
                                                                $ext = strtolower(pathinfo($project->NTPAttachment, PATHINFO_EXTENSION));
                                                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
                                                            @endphp

                                                            @if($isImage)
                                                                <a href="{{ asset('storage/' . $project->NTPAttachment) }}"
                                                                    data-lightbox="project-attachments"
                                                                    data-title="Notice to Proceed (NTP)">
                                                                    <img src="{{ asset('storage/' . $project->NTPAttachment) }}"
                                                                        class="card-img-top" alt="NTP Document"
                                                                        style="height: 200px; object-fit: cover; cursor: pointer;">
                                                                </a>
                                                            @else
                                                                <div class="card-img-top d-flex align-items-center justify-content-center bg-light"
                                                                    style="height: 200px;">
                                                                    <div class="text-center">
                                                                        <i class="fas fa-file-pdf text-danger mb-2"
                                                                            style="font-size: 3rem;"></i>
                                                                        <p class="text-muted mb-0">{{ strtoupper($ext) }} File</p>
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            <div class="position-absolute top-0 left-0 m-2">
                                                                <span class="badge badge-success">
                                                                    <i class="fas fa-file-contract mr-1"></i>NTP
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            @if($project->formatted_ntp_start_date)
                                                                <small class="text-muted d-block mb-2">
                                                                    <i
                                                                        class="fas fa-calendar-alt mr-1"></i>{{ $project->formatted_ntp_start_date }}
                                                                </small>
                                                            @endif
                                                            <a href="{{ asset('storage/' . $project->NTPAttachment) }}"
                                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-external-link-alt mr-1"></i>Open
                                                            </a>
                                                            <a href="{{ asset('storage/' . $project->NTPAttachment) }}" download
                                                                class="btn btn-sm btn-outline-secondary ml-1">
                                                                <i class="fas fa-download mr-1"></i>Download
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($project->additional_images && count($project->additional_images) > 0)
                                                @foreach($project->additional_images as $index => $imagePath)
                                                    <div class="col-md-4 mb-3">
                                                        <div class="card border-0 shadow-sm h-100">
                                                            <div class="position-relative">
                                                                <a href="{{ asset('storage/' . $imagePath) }}"
                                                                    data-lightbox="project-attachments" data-title="Additional Image {{ $index + 1 }}">
                                                                    <img src="{{ asset('storage/' . $imagePath) }}"
                                                                        class="card-img-top" alt="Additional Image {{ $index + 1 }}"
                                                                        style="height: 200px; object-fit: cover; cursor: pointer;">
                                                                </a>
                                                                <div class="position-absolute top-0 left-0 m-2">
                                                                    <span class="badge badge-secondary">
                                                                        <i class="fas fa-image mr-1"></i>Image {{ $index + 1 }}
                                                                    </span>
                                                                </div>
                                                                <div class="position-absolute top-0 right-0 m-2">
                                                                    <form action="{{ route('projects.deleteAdditionalImage', [$project, $index]) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-danger btn-sm" 
                                                                            onclick="return confirm('Are you sure you want to delete this image?')">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                <a href="{{ asset('storage/' . $imagePath) }}"
                                                                    target="_blank" class="btn btn-sm btn-outline-primary">
                                                                    <i class="fas fa-external-link-alt mr-1"></i>Open
                                                                </a>
                                                                <a href="{{ asset('storage/' . $imagePath) }}" download
                                                                    class="btn btn-sm btn-outline-secondary ml-1">
                                                                    <i class="fas fa-download mr-1"></i>Download
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-center py-5">
                                            <i class="fas fa-images text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                                            <h6 class="text-muted mb-2">No Attachments Available</h6>
                                            <p class="text-muted">No blueprints, floor plans, or NTP documents have been
                                                uploaded yet.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Project Actions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('ProdHead.projects') }}" class="btn btn-secondary btn-sm"
                                    style="background-color: #6c757d !important; border: 2px solid #6c757d !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                                    <i class="fas fa-arrow-left mr-1"></i> Back to Projects
                                </a>

                                @if($project->status->StatusName === 'Pending')
                                    {{-- Pending projects: show Approve NTP instead of End / Put on Hold --}}
                                    @php
                                        $totalMilestoneDays = $project->milestones->sum('EstimatedDays');
                                        $totalMilestoneWeight = $project->milestones->sum('WeightedPercentage');
                                        $hasMilestones = $project->milestones->count() > 0;
                                        $daysMatch = $totalMilestoneDays == $project->EstimatedAccomplishDays;
                                        $weightComplete = $totalMilestoneWeight >= 100;
                                        $canApproveNTP = $hasMilestones && $daysMatch && $weightComplete;

                                        $validationErrors = [];
                                        if (!$hasMilestones) {
                                            $validationErrors[] = 'No milestones defined';
                                        }
                                        if (!$daysMatch) {
                                            $validationErrors[] = "Milestone days ({$totalMilestoneDays}) must equal project days ({$project->EstimatedAccomplishDays})";
                                        }
                                        if (!$weightComplete) {
                                            $validationErrors[] = "Milestone weight ({$totalMilestoneWeight}%) must reach 100%";
                                        }
                                    @endphp

                                    <button type="button" class="btn btn-success btn-sm ml-2" data-toggle="modal"
                                        data-target="#proceedNTPModal"
                                        style="background-color: #7fb069 !important; border: 2px solid #7fb069 !important; color: white !important;"
                                        @if(!$canApproveNTP) disabled title="{{ implode(', ', $validationErrors) }}" @endif>
                                        <i class="fas fa-check-circle mr-1"></i> Approve NTP
                                    </button>

                                    @if(!$canApproveNTP)
                                        <div class="d-inline-block ml-2">
                                            <small class="text-danger">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                @if(!$hasMilestones)
                                                    Please add milestones first
                                                @elseif(!$daysMatch || !$weightComplete)
                                                    Complete milestone setup:
                                                    @if(!$daysMatch)
                                                        Days: {{ $totalMilestoneDays }}/{{ $project->EstimatedAccomplishDays }}
                                                    @endif
                                                    @if(!$weightComplete)
                                                        {{ !$daysMatch ? ' | ' : '' }}Weight:
                                                        {{ number_format($totalMilestoneWeight, 2) }}%/100%
                                                    @endif
                                                @endif
                                            </small>
                                        </div>
                                    @endif
                                @elseif($project->status->StatusName === 'Pre-Construction' || $project->status->StatusName === 'On Going')
                                    {{-- Pre‑Construction & On Going: only Back to Projects, no manual End / On Hold --}}
                                @else
                                    {{-- Other statuses: keep existing End / On Hold / Reactivate actions --}}
                                    @if($project->status->StatusName != 'Completed')
                                        <form action="{{ route('projects.end', $project) }}" method="POST" class="d-inline ml-2">
                                            @csrf
                                            @method('POST')
                                            <button type="submit" class="btn btn-success btn-sm"
                                                style="background-color: #28a745 !important; border: 2px solid #28a745 !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;"
                                                onclick="return confirm('Are you sure you want to end this project? This will mark it as completed.')">
                                                <i class="fas fa-check-circle mr-1"></i> End Project
                                            </button>
                                        </form>
                                    @endif

                                    @if($project->status->StatusName != 'On Hold')
                                        <form action="{{ route('projects.onHold', $project) }}" method="POST" class="d-inline ml-2">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-warning btn-sm"
                                                style="background-color: #ffc107 !important; border: 2px solid #ffc107 !important; color: #212529 !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;"
                                                onclick="return confirm('Are you sure you want to put this project on hold?')">
                                                <i class="fas fa-pause mr-1"></i> Put on Hold
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('projects.reactivate', $project) }}" method="POST"
                                            class="d-inline ml-2">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm"
                                                style="background-color: #28a745 !important; border: 2px solid #28a745 !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;"
                                                onclick="return confirm('Are you sure you want to reactivate this project?')">
                                                <i class="fas fa-play mr-1"></i> Reactivate
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Proceed with NTP Modal (for Pending projects) -->
    @if($project->status->StatusName === 'Pending')
        <div class="modal fade" id="proceedNTPModal" tabindex="-1" role="dialog" aria-labelledby="proceedNTPModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header text-white" style="background: #7fb069;">
                        <h5 class="modal-title" id="proceedNTPModalLabel">
                            <i class="fas fa-check-circle mr-2"></i>Approve NTP
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="proceedNTPForm" method="POST" enctype="multipart/form-data"
                        action="{{ route('projects.proceed-ntp', $project) }}">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Project Name</label>
                                <input type="text" class="form-control" value="{{ $project->ProjectName }}" readonly>
                            </div>
                            <div class="form-group">
                                <label for="NTPStartDate">NTP Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('NTPStartDate') is-invalid @enderror"
                                    id="NTPStartDate" name="NTPStartDate" required min="{{ now()->addDay()->toDateString() }}">
                                <small class="form-text text-muted">The date when the Notice to Proceed is issued</small>
                                @error('NTPStartDate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="NTPAttachment">NTP Attachment <span class="text-danger">*</span></label>
                                <input type="file" class="form-control-file @error('NTPAttachment') is-invalid @enderror"
                                    id="NTPAttachment" name="NTPAttachment" accept="image/*,application/pdf" required>
                                <small class="form-text text-muted">Upload NTP document/image (Max: 10MB, Formats: JPG, PNG,
                                    PDF)</small>
                                <div id="filePreview" class="mt-2"></div>
                                @error('NTPAttachment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Estimated End Date</label>
                                <input type="text" class="form-control" id="estimatedEndDate" readonly>
                                <small class="form-text text-muted">Calculated: NTP Start Date + Estimated Accomplish
                                    Days</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                            <button type="submit" class="btn text-white" style="background: #7fb069; border-color: #7fb069;">
                                <i class="fas fa-check-circle"></i> Approve NTP
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Assign Employee Modal -->
    <div class="modal fade" id="assignEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="assignEmployeeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: #7fb069;">
                    <h5 class="modal-title" id="assignEmployeeModalLabel">
                        <i class="fas fa-user-plus mr-2"></i>Assign Employee to Project
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('projects.assign-employee', $project) }}" method="POST" id="assignEmployeeForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="modalEmployeeID">Select Employee to Assign <span
                                    class="text-danger">*</span></label>
                            <select class="form-control" id="modalEmployeeID" name="EmployeeID" required>
                                <option value="">Choose an employee...</option>
                                @if(isset($availableEmployees) && $availableEmployees->count() > 0)
                                    @foreach($availableEmployees as $employee)
                                        <option value="{{ $employee->id }}">
                                            {{ $employee->full_name }}
                                            @php
                                                $position = $employee->relationLoaded('position') ? $employee->getRelation('position') : null;
                                                if (!$position) {
                                                    $position = $employee->position()->first();
                                                }
                                            @endphp
                                            @if($position && is_object($position))
                                                - {{ $position->PositionName }}
                                            @endif
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn text-white" style="background: #7fb069; border-color: #7fb069;">
                            <i class="fas fa-user-plus"></i> Assign Employee
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- QR Code Modals for each employee -->
    @if($project->projectEmployees && $project->projectEmployees->count() > 0)
        @foreach($project->projectEmployees as $assignment)
            <div class="modal fade" id="showQrModal{{ $assignment->ProjectEmployeeID }}" tabindex="-1" role="dialog"
                aria-labelledby="showQrModalLabel{{ $assignment->ProjectEmployeeID }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header text-white" style="background: #87A96B;">
                            <h5 class="modal-title" id="showQrModalLabel{{ $assignment->ProjectEmployeeID }}">
                                <i class="fas fa-qrcode mr-2"></i>Employee QR Code
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <h5 class="font-weight-bold">{{ $assignment->employee->full_name ?? 'N/A' }}</h5>
                                <p class="text-muted mb-0">{{ $assignment->employee->position->PositionName ?? 'No Position' }}</p>
                            </div>
                            @if($assignment->qr_code)
                                <div class="qr-code-display p-3 bg-light rounded mb-3">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode($assignment->qr_code) }}"
                                        alt="QR Code" class="img-fluid" style="max-width: 250px;">
                                </div>
                                <div class="bg-light p-2 rounded">
                                    <small class="text-muted">QR Data:</small>
                                    <code class="d-block text-break" style="font-size: 0.8rem;">{{ $assignment->qr_code }}</code>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    No QR code assigned to this employee.
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            @if($assignment->qr_code)
                                <button type="button" class="btn btn-success"
                                    onclick="downloadQrCode('https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode($assignment->qr_code) }}', 'QR_{{ Str::slug($assignment->employee->full_name ?? 'employee') }}.png')">
                                    <i class="fas fa-download"></i> Download
                                </button>
                            @endif
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times"></i> Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <!-- Add Milestone Modal -->
    <div class="modal fade" id="addMilestoneModal" tabindex="-1" role="dialog" aria-labelledby="addMilestoneModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: #7fb069;">
                    <h5 class="modal-title" id="addMilestoneModalLabel">
                        <i class="fas fa-plus mr-2"></i>Add New Milestone
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('projects.milestones.store', $project) }}" method="POST" id="addMilestoneForm">
                    @csrf
                    <div class="modal-body">
                        <div id="addMilestoneErrorAlert" class="alert alert-danger" style="display: none;">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span id="addMilestoneErrorText"></span>
                        </div>

                        <!-- Milestone Information -->
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-info-circle mr-2"></i>Milestone Information
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="milestone_name">Milestone Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="milestone_name" name="milestone_name"
                                        placeholder="e.g., Excavation & Foundation" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="2"
                                        placeholder="Short description of this milestone"></textarea>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">

                        <!-- Timeline & Weight -->
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-calendar-alt mr-2"></i>Timeline & Weight
                        </h6>

                        <div class="form-group mb-3">
                            <label for="EstimatedDays">Estimated Days of Accomplishment <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="EstimatedDays" name="EstimatedDays" min="1"
                                step="1" placeholder="e.g., 10" required>
                            <div class="invalid-feedback"></div>
                            <small class="form-text text-muted">
                                Total milestone days:
                                <span
                                    id="modalTotalMilestoneDays">{{ $project->milestones->sum('EstimatedDays') ?? 0 }}</span>
                                / {{ $project->EstimatedAccomplishDays }}
                                @if($project->StartDate)
                                    <br><span id="modalCalculatedTargetDate" class="text-info"></span>
                                @else
                                    <br><span class="text-info">Target dates will be calculated after NTP approval.</span>
                                @endif
                            </small>
                        </div>

                        <div class="form-group mb-2">
                            <label for="WeightedPercentage">Weight (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="WeightedPercentage" name="WeightedPercentage"
                                min="0" max="100" step="0.01" placeholder="e.g., 25" required>
                            <div class="invalid-feedback"></div>
                            <small class="form-text text-muted">
                                Current total weight:
                                {{ number_format($project->milestones->sum('WeightedPercentage') ?? 0, 2) }}% (overall
                                should not exceed 100%).
                            </small>
                        </div>

                        <hr class="my-3">

                        <!-- Required Items -->
                        <h6 class="text-primary mb-3"><i class="fas fa-boxes mr-2"></i>Required Items</h6>
                        <small class="form-text text-muted mb-3">
                            Add at least one material/equipment for this milestone. These will be the only items available
                            when foreman requests materials.
                        </small>

                        <div class="row mb-2">
                            <div class="col-md-6">
                                <select class="form-control form-control-sm" id="selectItemForMilestone">
                                    <option value="">Select Item...</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-sm">
                                    <input type="number" class="form-control form-control-sm" id="itemEstimatedQty"
                                        placeholder="Estimated Qty" min="0.01" step="0.01">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="itemEstimatedUnit">Unit</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-sm btn-success w-100" id="btnAddItemToMilestone">
                                    <i class="fas fa-plus"></i> Add
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Item</th>
                                        <th>Type</th>
                                        <th width="150">Est. Quantity</th>
                                        <th width="80">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="milestoneItemsList">
                                    <tr class="text-center text-muted">
                                        <td colspan="4">No items added yet</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <input type="hidden" name="required_items" id="requiredItemsData">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn text-white" style="background: #7fb069; border-color: #7fb069;">
                            <i class="fas fa-save"></i> Create Milestone
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Milestone Modal -->
    <div class="modal fade" id="editMilestoneModal" tabindex="-1" role="dialog" aria-labelledby="editMilestoneModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: #87A96B;">
                    <h5 class="modal-title" id="editMilestoneModalLabel">
                        <i class="fas fa-edit mr-2"></i><span id="modalTitleText">Edit Milestone</span>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editMilestoneForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <!-- Milestone Information (Read-only) -->
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-info-circle mr-2"></i>Milestone Information
                        </h6>

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="text-muted" style="font-size: 0.75rem;">Milestone Name</label>
                                    <input type="text" class="form-control-plaintext" id="edit_milestone_name_display"
                                        readonly style="padding: 0.375rem 0; font-weight: 600;">
                                    <input type="hidden" id="edit_milestone_name" name="milestone_name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="text-muted" style="font-size: 0.75rem;">Status</label>
                                    <input type="text" class="form-control-plaintext" id="edit_status_display" readonly
                                        style="padding: 0.375rem 0;">
                                    <input type="hidden" id="edit_status" name="status">
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="text-muted" style="font-size: 0.75rem;">Description</label>
                            <textarea class="form-control-plaintext" id="edit_description_display" readonly rows="2"
                                style="padding: 0.375rem 0;"></textarea>
                            <input type="hidden" id="edit_description" name="description">
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-muted" style="font-size: 0.75rem;">Estimated Days</label>
                                    <input type="text" class="form-control-plaintext" id="edit_EstimatedDays_display"
                                        readonly style="padding: 0.375rem 0;">
                                    <input type="hidden" id="edit_EstimatedDays" name="EstimatedDays">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-muted" style="font-size: 0.75rem;">Target Date</label>
                                    <input type="text" class="form-control-plaintext" id="edit_actual_date_display" readonly
                                        style="padding: 0.375rem 0;">
                                    <input type="hidden" id="edit_actual_date" name="actual_date">
                                </div>
                            </div>
                        </div>

                        <hr class="my-4" id="itemsSectionDivider" style="display: none;">

                        <!-- Required Items Section (hidden when pending approval) -->
                        <div id="requiredItemsSection">
                            <h6 class="text-primary mb-3"><i class="fas fa-boxes mr-2"></i>Required Items</h6>
                            <small class="form-text text-muted mb-3">
                                Update materials/equipment for this milestone. These will be the only items available when
                                foreman requests materials.
                            </small>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <select class="form-control form-control-sm" id="editSelectItemForMilestone">
                                        <option value="">Select Item...</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control form-control-sm" id="editItemEstimatedQty"
                                            placeholder="Estimated Qty" min="0.01" step="0.01">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="editItemEstimatedUnit">Unit</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-sm btn-success w-100" id="editAddItemToMilestone">
                                        <i class="fas fa-plus"></i> Add
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Item</th>
                                            <th>Type</th>
                                            <th width="150">Est. Quantity</th>
                                            <th width="80">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="editMilestoneItemsList">
                                        <tr class="text-center text-muted">
                                            <td colspan="4">No items added yet</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <input type="hidden" name="required_items" id="editRequiredItemsData">
                        </div>
                    </div>
                    <div class="modal-footer bg-white">
                        <!-- Edit Mode Buttons -->
                        <div id="editModeButtons">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                            <button type="submit" form="editMilestoneForm" class="btn text-white" style="background: #87A96B; border-color: #87A96B;">
                                <i class="fas fa-save"></i> Update Milestone
                            </button>
                        </div>
                        
                        <!-- Approval Mode Buttons -->
                        <div id="approvalModeButtons" style="display: none;">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times"></i> Close
                            </button>
                            <button type="submit" form="approveMilestoneForm" class="btn text-white swal-confirm-form" 
                                style="background: #28a745; border-color: #28a745;"
                                data-title="Approve Milestone Completion"
                                data-text="Are you sure you want to approve this milestone completion?"
                                data-icon="question"
                                data-confirm-text="Yes, Approve">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button type="submit" form="rejectMilestoneForm" class="btn btn-danger swal-confirm-form"
                                data-title="Reject Milestone Submission"
                                data-text="Are you sure you want to reject this milestone submission? The foreman will need to resubmit."
                                data-icon="warning"
                                data-confirm-text="Yes, Reject">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </div>
                    </div>
                </form>
                
                <!-- Approval forms (outside edit form to prevent validation conflicts) -->
                <form id="approveMilestoneForm" method="POST" class="d-none swal-confirm-form"
                    data-title="Approve Milestone Completion"
                    data-text="Are you sure you want to approve this milestone completion?"
                    data-icon="question"
                    data-confirm-text="Yes, Approve">
                    @csrf
                </form>
                <form id="rejectMilestoneForm" method="POST" class="d-none swal-confirm-form"
                    data-title="Reject Milestone Submission"
                    data-text="Are you sure you want to reject this milestone submission? The foreman will need to resubmit."
                    data-icon="warning"
                    data-confirm-text="Yes, Reject">
                    @csrf
                </form>
            </div>
        </div>
    </div>

    <!-- Assign Employee Modal -->
    <div class="modal fade" id="assignEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="assignEmployeeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: #7fb069;">
                    <h5 class="modal-title" id="assignEmployeeModalLabel">
                        <i class="fas fa-user-plus mr-2"></i>Assign Employee to Project
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('projects.assign-employee', $project) }}" method="POST" id="assignEmployeeForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="modalEmployeeID">Select Employee to Assign <span
                                    class="text-danger">*</span></label>
                            <select class="form-control" id="modalEmployeeID" name="EmployeeID" required>
                                <option value="">Choose an employee...</option>
                                @if(isset($availableEmployees) && $availableEmployees->count() > 0)
                                    @foreach($availableEmployees as $employee)
                                        <option value="{{ $employee->id }}">
                                            {{ $employee->full_name }}
                                            @php
                                                $position = $employee->relationLoaded('position') ? $employee->getRelation('position') : null;
                                                if (!$position) {
                                                    $position = $employee->position()->first();
                                                }
                                            @endphp
                                            @if($position && is_object($position))
                                                - {{ $position->PositionName }}
                                            @endif
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn text-white" style="background: #7fb069; border-color: #7fb069;">
                            <i class="fas fa-user-plus"></i> Assign Employee
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Material Modal -->
    <div class="modal fade" id="addMaterialModal" tabindex="-1" role="dialog" aria-labelledby="addMaterialModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: #7fb069;">
                    <h5 class="modal-title" id="addMaterialModalLabel">
                        <i class="fas fa-cubes mr-2"></i>Add Material to Milestone
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addMaterialForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="material_milestone_id" name="milestone_id">
                        <div class="form-group">
                            <label for="material_ItemID">Material <span class="text-danger">*</span></label>
                            <select class="form-control" id="material_ItemID" name="ItemID" required>
                                <option value="">Select Material</option>
                                @foreach($materials as $material)
                                    <option value="{{ $material->ItemID }}" data-available="{{ $material->AvailableQuantity }}"
                                        data-unit="{{ $material->Unit }}">
                                        {{ $material->ItemName }} (Available:
                                        {{ number_format($material->AvailableQuantity, 2) }} {{ $material->Unit }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted" id="material-availability"></small>
                        </div>
                        <div class="form-group">
                            <label for="QuantityUsed">Quantity Used <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="QuantityUsed" name="QuantityUsed"
                                min="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="DateUsed">Date Used</label>
                            <input type="date" class="form-control" id="DateUsed" name="DateUsed"
                                value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label for="Remarks">Remarks</label>
                            <textarea class="form-control" id="Remarks" name="Remarks" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn text-white" style="background: #7fb069; border-color: #7fb069;">
                            <i class="fas fa-save"></i> Add Material
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Equipment Modal -->
    <div class="modal fade" id="addEquipmentModal" tabindex="-1" role="dialog" aria-labelledby="addEquipmentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: #7fb069;">
                    <h5 class="modal-title" id="addEquipmentModalLabel">
                        <i class="fas fa-tools mr-2"></i>Add Equipment to Milestone
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addEquipmentForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="equipment_milestone_id" name="milestone_id">
                        <div class="form-group">
                            <label for="equipment_ItemID">Equipment <span class="text-danger">*</span></label>
                            <select class="form-control" id="equipment_ItemID" name="ItemID" required>
                                <option value="">Select Equipment</option>
                                @foreach($equipment as $item)
                                    <option value="{{ $item->ItemID }}" data-available="{{ $item->AvailableQuantity }}"
                                        data-unit="{{ $item->Unit }}">
                                        {{ $item->ItemName }} (Available: {{ number_format($item->AvailableQuantity, 2) }}
                                        {{ $item->Unit }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted" id="equipment-availability"></small>
                        </div>
                        <div class="form-group">
                            <label for="QuantityAssigned">Quantity Assigned <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="QuantityAssigned"
                                name="QuantityAssigned" min="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="DateAssigned">Date Assigned</label>
                            <input type="date" class="form-control" id="DateAssigned" name="DateAssigned"
                                value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label for="equipment_Remarks">Remarks</label>
                            <textarea class="form-control" id="equipment_Remarks" name="Remarks" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn text-white" style="background: #7fb069; border-color: #7fb069;">
                            <i class="fas fa-save"></i> Add Equipment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Return Equipment Modal -->
    <div class="modal fade" id="returnEquipmentModal" tabindex="-1" role="dialog"
        aria-labelledby="returnEquipmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: #7fb069;">
                    <h5 class="modal-title" id="returnEquipmentModalLabel">
                        <i class="fas fa-undo mr-2"></i>Return Equipment
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="returnEquipmentForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <input type="hidden" id="return_equipment_id" name="equipment_id">
                        <div class="form-group">
                            <label>Equipment</label>
                            <input type="text" class="form-control" id="return_item_name" readonly>
                        </div>
                        <div class="form-group">
                            <label for="return_Status">Return Status <span class="text-danger">*</span></label>
                            <select class="form-control" id="return_Status" name="Status" required>
                                <option value="Returned">Returned (Good Condition)</option>
                                <option value="Damaged">Damaged</option>
                                <option value="Missing">Missing</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ReturnRemarks">Return Remarks</label>
                            <textarea class="form-control" id="ReturnRemarks" name="ReturnRemarks" rows="3"
                                placeholder="Enter details about the condition or any issues..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn text-white" style="background: #7fb069; border-color: #7fb069;">
                            <i class="fas fa-save"></i> Return Equipment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
        <style>
            /* Ensure cards and buttons are visible */
            .card {
                opacity: 1 !important;
                visibility: visible !important;
                display: block !important;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
                transition: none !important;
            }

            /* Remove hover effects on cards */
            .card:hover {
                transform: none !important;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
            }

            .btn {
                opacity: 1 !important;
                visibility: visible !important;
                display: inline-block !important;
            }


            /* Extra small button size */
            .btn-xs {
                padding: 0.125rem 0.25rem;
                font-size: 0.75rem;
                line-height: 1.5;
                border-radius: 0.2rem;
            }

            .badge-sm {
                font-size: 0.7rem;
                padding: 0.2em 0.4em;
            }

            /* Form input outline and focus states to match green theme */
            #addMilestoneModal .form-control:focus,
            #editMilestoneModal .form-control:focus {
                border-color: #7fb069;
                outline: 1px solid #7fb069;
                box-shadow: 0 0 0 0.2rem rgba(127, 176, 105, 0.25);
            }

            /* Action buttons outline colors */
            .btn-outline-info {
                border-color: #17a2b8;
                color: #17a2b8;
            }

            .btn-outline-info:hover {
                background-color: #17a2b8;
                border-color: #17a2b8;
                color: #fff;
            }

            .btn-outline-danger {
                border-color: #dc3545;
                color: #dc3545;
            }

            .btn-outline-danger:hover {
                background-color: #dc3545;
                border-color: #dc3545;
                color: #fff;
            }

            /* Milestone required items cards */
            .milestone-completed {
                pointer-events: none;
            }
            
            .milestone-req-card {
                transition: all 0.15s ease-in-out !important;
                border: 1px solid #f2f2f2 !important;
            }

            .milestone-req-card:hover {
                transform: translateY(-2px) !important;
                box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08) !important;
                border-color: #e6e6e6 !important;
            }

            .milestone-req-status.badge-success {
                background-color: #28a745 !important;
                color: white !important;
            }

            .milestone-req-status.badge-warning {
                background-color: #f8d57e;
                color: #6c4a00;
            }

            .milestone-req-status.badge-secondary {
                background-color: #adb5bd;
            }

            /* Gantt Chart Styles */
            #gantt-chart-container {
                padding: 20px;
            }

            .gantt-container {
                overflow-x: auto;
            }

            .gantt .bar-wrapper .bar {
                fill: #7fb069;
            }

            .gantt .bar-wrapper .bar.gantt-green {
                fill: #28a745;
            }

            .gantt .bar-wrapper .bar.gantt-yellow {
                fill: #ffc107;
            }

            /* Select2 dropdown scrollable fix */
            .select2-container--bootstrap4 .select2-results__options {
                max-height: 300px !important;
                overflow-y: auto !important;
            }

            .select2-container--bootstrap4 .select2-dropdown {
                z-index: 9999 !important;
            }

            .select2-container--bootstrap4 .select2-results {
                max-height: 300px !important;
            }

            /* Make modal scrollable when there are many required items */
            #addMilestoneModal .modal-body {
                max-height: calc(100vh - 210px);
                overflow-y: auto;
            }

            #editMilestoneModal .modal-body {
                max-height: calc(100vh - 210px);
                overflow-y: auto;
            }

            /* Ensure table is scrollable within modal */
            #addMilestoneModal .table-responsive,
            #editMilestoneModal .table-responsive {
                max-height: 300px;
                overflow-y: auto;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
        <script>
            // Download QR Code as PNG
            function downloadQrCode(url, filename) {
                fetch(url)
                    .then(response => response.blob())
                    .then(blob => {
                        const link = document.createElement('a');
                        link.href = URL.createObjectURL(blob);
                        link.download = filename;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        URL.revokeObjectURL(link.href);
                    })
                    .catch(error => {
                        console.error('Download failed:', error);
                        alert('Failed to download QR code. Please try again.');
                    });
            }

            // Initialize Project Status Chart (Donut Chart)
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('projectStatusChart');
                if (ctx) {
                    const backlog = {{ $milestoneCounts['backlog'] }};
                    const pending = {{ $milestoneCounts['pending'] }};
                    const inProgress = {{ $milestoneCounts['in_progress'] }};
                    const completed = {{ $milestoneCounts['completed'] }};

                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Backlog', 'Pending', 'In Progress', 'Completed'],
                            datasets: [{
                                data: [backlog, pending, inProgress, completed],
                                backgroundColor: ['#dc3545', '#ff9f40', '#FFCE56', '#28a745'],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            cutout: '60%',
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function (context) {
                                            return context.label + ': ' + context.parsed;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

            });

            // ----------------- NTP APPROVAL (PENDING PROJECTS) -----------------
            @if($project->status->StatusName === 'Pending')
                (function () {
                    const estimatedDays = {{ (int) ($project->EstimatedAccomplishDays ?? 0) }};

                    // Reset modal fields on open
                    $('#proceedNTPModal').on('show.bs.modal', function () {
                        const tomorrow = new Date();
                        tomorrow.setDate(tomorrow.getDate() + 1);
                        const minDate = tomorrow.toISOString().split('T')[0];
                        const $ntp = $('#NTPStartDate');
                        $ntp.attr('min', minDate);
                        $ntp.val('');
                        $('#NTPAttachment').val('');
                        $('#filePreview').html('');
                        $('#estimatedEndDate').val('');
                    });

                    // Calculate estimated end date when NTP start date changes
                    $('#NTPStartDate').on('change input', function () {
                        const tomorrow = new Date();
                        tomorrow.setDate(tomorrow.getDate() + 1);
                        tomorrow.setHours(0, 0, 0, 0);
                        const minDate = $('#NTPStartDate').attr('min');
                        let ntpStartDate = $(this).val();

                        // Validate and clamp to min date if user picks today or a past date
                        if (ntpStartDate) {
                            const selectedDate = new Date(ntpStartDate);
                            selectedDate.setHours(0, 0, 0, 0);

                            if (selectedDate < tomorrow) {
                                alert('You cannot select today or a date in the past. The NTP Start Date must be tomorrow or later.');
                                ntpStartDate = minDate;
                                $(this).val(minDate);
                            }
                        }

                        if (ntpStartDate && estimatedDays && estimatedDays > 0) {
                            const [year, month, day] = ntpStartDate.split('-').map(Number);
                            const startDate = new Date(year, month - 1, day);
                            const endDate = new Date(startDate);
                            endDate.setDate(endDate.getDate() + parseInt(estimatedDays));

                            const formattedDate = endDate.toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric'
                            });
                            $('#estimatedEndDate').val(formattedDate);
                        } else {
                            $('#estimatedEndDate').val('');
                        }
                    });

                    // Validate form before submission
                    $('#proceedNTPForm').on('submit', function (e) {
                        const ntpStartDate = $('#NTPStartDate').val();
                        if (!ntpStartDate) {
                            e.preventDefault();
                            alert('Please select an NTP Start Date.');
                            return false;
                        }

                        const tomorrow = new Date();
                        tomorrow.setDate(tomorrow.getDate() + 1);
                        tomorrow.setHours(0, 0, 0, 0);
                        const selectedDate = new Date(ntpStartDate);
                        selectedDate.setHours(0, 0, 0, 0);

                        if (selectedDate < tomorrow) {
                            e.preventDefault();
                            alert('Invalid date! The NTP Start Date cannot be today or in the past. Please select tomorrow or a future date.');
                            $('#NTPStartDate').focus();
                            return false;
                        }
                    });

                    // Show file preview
                    $('#NTPAttachment').on('change', function () {
                        const file = this.files[0];
                        if (file) {
                            const fileName = file.name;
                            const fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
                            $('#filePreview').html(
                                '<div class="alert alert-info">' +
                                '<i class="fas fa-file mr-2"></i>' +
                                '<strong>' + fileName + '</strong> (' + fileSize + ' MB)' +
                                '</div>'
                            );
                        } else {
                            $('#filePreview').html('');
                        }
                    });

                    // Reset modal on close
                    $('#proceedNTPModal').on('hidden.bs.modal', function () {
                        $('#proceedNTPForm')[0].reset();
                        $('#filePreview').html('');
                        $('#estimatedEndDate').val('');
                    });
                })();
            @endif

            // Handle edit milestone card click
            $(document).on('click', '.edit-milestone-card', function () {
                // Check if milestone is completed - disable editing
                const isCompleted = $(this).data('is-completed') === true || $(this).data('is-completed') === 'true';
                if (isCompleted) {
                    toastr.info('This milestone is completed and cannot be edited.');
                    return false;
                }
                
                const milestoneId = $(this).data('milestone-id');
                const milestoneName = $(this).data('milestone-name');
                const description = $(this).data('description') || '';
                const estimatedDays = $(this).data('estimated-days') || '';
                const actualDate = $(this).data('actual-date') || '';
                const status = $(this).data('status');
                const submissionStatus = $(this).data('submission-status') || 'Not Submitted';
                const isPendingApproval = submissionStatus === 'Pending Approval';

                // Set form action
                $('#editMilestoneForm').attr('action', '{{ route("projects.milestones.update", [$project, ":milestone"]) }}'.replace(':milestone', milestoneId));
                
                // Set approve/reject form actions
                $('#approveMilestoneForm').attr('action', '{{ route("projects.milestones.approve", [$project, ":milestone"]) }}'.replace(':milestone', milestoneId));
                $('#rejectMilestoneForm').attr('action', '{{ route("projects.milestones.reject", [$project, ":milestone"]) }}'.replace(':milestone', milestoneId));

                // Populate hidden form fields
                $('#edit_milestone_name').val(milestoneName);
                $('#edit_description').val(description);
                $('#edit_EstimatedDays').val(estimatedDays);
                $('#edit_actual_date').val(actualDate);
                $('#edit_status').val(status);
                $('#edit_submission_status').val(submissionStatus);

                // Populate display fields (read-only)
                $('#edit_milestone_name_display').val(milestoneName);
                $('#edit_description_display').val(description || 'No description');
                $('#edit_EstimatedDays_display').val(estimatedDays ? estimatedDays + ' days' : 'N/A');
                $('#edit_actual_date_display').val(actualDate || 'N/A');
                $('#edit_status_display').val(status);

                // Show/hide sections based on approval status
                if (isPendingApproval) {
                    // Show approval mode
                    $('#modalTitleText').html('<i class="fas fa-check-circle mr-2"></i>Review Milestone Submission');
                    $('#approvalSection').show();
                    $('#requiredItemsSection').hide();
                    $('#itemsSectionDivider').hide();
                    $('#editModeButtons').hide();
                    $('#approvalModeButtons').show();
                    
                    // Load proof images
                    loadProofImages(milestoneId);
                } else {
                    // Show edit mode
                    $('#modalTitleText').html('<i class="fas fa-edit mr-2"></i>Edit Milestone');
                    $('#approvalSection').hide();
                    $('#requiredItemsSection').show();
                    $('#itemsSectionDivider').show();
                    $('#editModeButtons').show();
                    $('#approvalModeButtons').hide();
                    
                    // Load required items for this milestone
                    editMilestoneRequiredItems = [];
                    $('#editRequiredItemsData').val('');
                    $('#editMilestoneItemsList').html('<tr class="text-center text-muted"><td colspan="4">Loading...</td></tr>');
                    fetch(`/api/milestones/${milestoneId}/required-items`)
                        .then(res => res.json())
                        .then(data => {
                            editMilestoneRequiredItems = (data || []).map(item => ({
                                item_id: item.ItemID,
                                item_name: item.ItemName,
                                item_type: item.ItemType,
                                estimated_quantity: item.estimated_quantity,
                                unit: item.Unit
                            }));
                            renderEditMilestoneItems();
                        })
                        .catch(() => {
                            $('#editMilestoneItemsList').html('<tr class="text-center text-muted"><td colspan="4">Unable to load items.</td></tr>');
                        });
                    
                    // Calculate and show target date preview
                    @if($project->StartDate)
                        updateEditTargetDatePreview();
                    @endif
                }

                // Show modal
                $('#editMilestoneModal').modal('show');
            });

            // Function to load proof images
            function loadProofImages(milestoneId) {
                const container = $('#proofImagesContainer');
                container.html('<div class="col-12 text-center"><i class="fas fa-spinner fa-spin"></i> Loading proof images...</div>');
                
                fetch(`/api/milestones/${milestoneId}/proof-images`)
                    .then(res => res.json())
                    .then(data => {
                        container.empty();
                        if (data && data.length > 0) {
                            data.forEach(image => {
                                const imageUrl = image.image_path.startsWith('http') ? image.image_path : `/storage/${image.image_path}`;
                                container.append(`
                                    <div class="col-6 col-md-4 col-lg-3 mb-3">
                                        <a href="${imageUrl}" data-lightbox="milestone-proofs-${milestoneId}" data-title="Proof Image - ${image.created_at || ''}">
                                            <div class="card h-100 border-0 shadow-sm" style="overflow: hidden; border-radius: 8px;">
                                                <img src="${imageUrl}" class="card-img-top" alt="Proof Image" style="height: 120px; object-fit: cover; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                            </div>
                                        </a>
                                    </div>
                                `);
                            });
                        } else {
                            container.html('<div class="col-12 text-muted text-center">No proof images available.</div>');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading proof images:', error);
                        container.html('<div class="col-12 text-danger text-center">Error loading proof images.</div>');
                    });
            }

            // Update target date preview in edit modal
            function updateEditTargetDatePreview() {
                const estimatedDays = parseInt($('#edit_EstimatedDays').val()) || 0;
                const currentMilestoneId = $('#editMilestoneForm').attr('action').match(/\/(\d+)$/);
                if (!currentMilestoneId) return;

                // Get all milestones except the one being edited
                const allMilestones = @json($project->milestones->map(function ($m) {
                    return ['id' => $m->milestone_id, 'days' => $m->EstimatedDays ?? 0];
                })->values());
                const otherMilestonesDays = allMilestones
                    .filter(m => m.id != currentMilestoneId[1])
                    .reduce((sum, m) => sum + m.days, 0);

                const newTotal = otherMilestonesDays + estimatedDays;
                const projectEstimatedDays = {{ $project->EstimatedAccomplishDays }};

                // Update total display
                $('#editTotalMilestoneDays').text(newTotal);

                if (newTotal > projectEstimatedDays) {
                    $('#editTotalMilestoneDays').parent().addClass('text-danger');
                    $('#editCalculatedTargetDate').html('<span class="text-danger">Warning: Total exceeds project days!</span>');
                } else {
                    $('#editTotalMilestoneDays').parent().removeClass('text-danger');
                    @if($project->StartDate)
                        // Calculate target date (simplified - would need full recalculation)
                        $('#editCalculatedTargetDate').html('<span class="text-info">Target date will be recalculated based on milestone order</span>');
                    @endif
                                            }
            }

            $('#edit_EstimatedDays').on('input', function () {
                updateEditTargetDatePreview();
            });

            // Require at least one required item on update
            $('#editMilestoneForm').on('submit', function (e) {
                const val = $('#editRequiredItemsData').val();
                if (!val) {
                    e.preventDefault();
                    alert('Please add at least one required item for this milestone.');
                }
            });

            // Clear add milestone form when modal is closed
            $('#addMilestoneModal').on('hidden.bs.modal', function () {
                $(this).find('form')[0].reset();
                $(this).find('.is-invalid').removeClass('is-invalid');
                $(this).find('.invalid-feedback').text('');
                $(this).find('#addMilestoneErrorAlert').hide();
                currentTotalDays = {{ $project->milestones->sum('EstimatedDays') ?? 0 }};
                $('#modalTotalMilestoneDays').text(currentTotalDays);
                $('#modalCalculatedTargetDate').html('');
                // Reset required items
                milestoneRequiredItems = [];
                renderMilestoneItems();
            });

            // Clear edit milestone form when modal is closed
            $('#editMilestoneModal').on('hidden.bs.modal', function () {
                $(this).find('form')[0].reset();
                $(this).find('.is-invalid').removeClass('is-invalid');
                $(this).find('.invalid-feedback').remove();
            });

            // Handle add material button click
            $('#addMaterialModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                const milestoneId = button.data('milestone-id');
                $('#material_milestone_id').val(milestoneId);

                // Set form action
                const milestone = milestoneId;
                $('#addMaterialForm').attr('action', '{{ route("milestones.materials.store", [$project, ":milestone"]) }}'.replace(':milestone', milestone));
            });

            // Handle add equipment button click
            $('#addEquipmentModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                const milestoneId = button.data('milestone-id');
                $('#equipment_milestone_id').val(milestoneId);

                // Set form action
                const milestone = milestoneId;
                $('#addEquipmentForm').attr('action', '{{ route("milestones.equipment.store", [$project, ":milestone"]) }}'.replace(':milestone', milestone));
            });

            // Update material availability info
            $('#material_ItemID').on('change', function () {
                const selected = $(this).find('option:selected');
                const available = selected.data('available');
                const unit = selected.data('unit');
                if (available !== undefined) {
                    $('#material-availability').text('Available: ' + parseFloat(available).toFixed(2) + ' ' + unit);
                } else {
                    $('#material-availability').text('');
                }
            });

            // Update equipment availability info
            $('#equipment_ItemID').on('change', function () {
                const selected = $(this).find('option:selected');
                const available = selected.data('available');
                const unit = selected.data('unit');
                if (available !== undefined) {
                    $('#equipment-availability').text('Available: ' + parseFloat(available).toFixed(2) + ' ' + unit);
                } else {
                    $('#equipment-availability').text('');
                }
            });

            // Handle return equipment button click
            $(document).on('click', '.return-equipment-btn', function (e) {
                e.stopPropagation(); // Prevent row expansion
                const equipmentId = $(this).data('equipment-id');
                const itemName = $(this).data('item-name');

                // Find the milestone from the row
                const row = $(this).closest('tr').closest('tbody').closest('table').closest('.accordion-body');
                const milestoneId = row.attr('id').replace('milestone-', '');

                $('#return_item_name').val(itemName);
                $('#return_equipment_id').val(equipmentId);
                $('#returnEquipmentForm').attr('action', '{{ route("milestones.equipment.return", [$project, ":milestone", ":equipment"]) }}'
                    .replace(':milestone', milestoneId)
                    .replace(':equipment', equipmentId));
                $('#returnEquipmentModal').modal('show');
            });

            // Clear modals when closed
            $('#addMaterialModal, #addEquipmentModal, #returnEquipmentModal').on('hidden.bs.modal', function () {
                $(this).find('form')[0].reset();
                $(this).find('.is-invalid').removeClass('is-invalid');
                $(this).find('.invalid-feedback').remove();
                $('#material-availability, #equipment-availability').text('');
            });

            // Prevent row expansion when clicking buttons
            $('.btn-group, .return-equipment-btn, .edit-milestone-btn').on('click', function (e) {
                e.stopPropagation();
            });

            // Calculate target date preview in milestone modal
            const projectEstimatedDays = {{ $project->EstimatedAccomplishDays }};
            let currentTotalDays = {{ $project->milestones->sum('EstimatedDays') ?? 0 }};
            let currentTotalWeight = {{ $project->milestones->sum('WeightedPercentage') ?? 0 }};
            const maxWeight = 100;

            function updateCreateMilestoneButtonState() {
                const daysReached = currentTotalDays >= projectEstimatedDays;
                const weightReached = currentTotalWeight >= maxWeight;
                const disable = daysReached && weightReached;
                const submitBtn = $('#addMilestoneForm button[type="submit"]');

                submitBtn.prop('disabled', disable);

                if (disable) {
                    $('#addMilestoneErrorAlert').show();
                    $('#addMilestoneErrorText').text('Total milestone days and total weight are already at 100%. You cannot add more milestones.');
                } else {
                    // Only hide if no other errors are present
                    if (!$('#addMilestoneErrorText').text()) {
                        $('#addMilestoneErrorAlert').hide();
                    }
                }
            }

            // Required Items Management
            let milestoneRequiredItems = [];
            let editMilestoneRequiredItems = [];
            let resourceCatalogItems = [];
            const $selectMilestoneItem = $('#selectItemForMilestone');
            const $qtyInput = $('#itemEstimatedQty');
            const $unitLabel = $('#itemEstimatedUnit');

            // Load all inventory items from resource catalog
            fetch('{{ route("api.resource-catalog.items") }}')
                .then(response => response.json())
                .then(data => {
                    const select = $selectMilestoneItem;
                    const editSelect = $('#editSelectItemForMilestone');
                    resourceCatalogItems = data;
                    data.forEach(item => {
                        select.append(`<option value="${item.ResourceCatalogID}" data-name="${item.ItemName}" data-type="${item.Type}" data-unit="${item.Unit}">${item.ItemName} (${item.Type})</option>`);
                        editSelect.append(`<option value="${item.ResourceCatalogID}" data-name="${item.ItemName}" data-type="${item.Type}" data-unit="${item.Unit}">${item.ItemName} (${item.Type})</option>`);
                    });

                    // Initialize Select2 after options are loaded
                    select.select2({
                        theme: 'bootstrap4',
                        placeholder: 'Search for an item...',
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#addMilestoneModal')
                    });

                    editSelect.select2({
                        theme: 'bootstrap4',
                        placeholder: 'Search for an item...',
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#editMilestoneModal')
                    });
                })
                .catch(error => {
                    console.error('Error loading resource catalog items:', error);
                });

            // Update quantity placeholder with unit
            $selectMilestoneItem.on('change', function () {
                const unit = $(this).find(':selected').data('unit') || '';
                $unitLabel.text(unit || 'Unit');
            });

            // Update quantity placeholder with unit (edit)
            $('#editSelectItemForMilestone').on('change', function () {
                const unit = $(this).find(':selected').data('unit') || '';
                $('#editItemEstimatedUnit').text(unit || 'Unit');
            });

            // Add item to milestone
            $('#btnAddItemToMilestone').click(function () {
                const select = $selectMilestoneItem;
                // Get value from Select2 properly
                const itemId = select.select2('val') || select.val();
                const selectedOption = select.find(':selected');
                const qty = parseFloat($qtyInput.val());

                if (!itemId || itemId === '' || itemId === null) {
                    alert('Please select an item');
                    return;
                }

                if (!qty || qty <= 0 || isNaN(qty)) {
                    alert('Please enter a valid quantity');
                    return;
                }

                // Get item data from the selected option
                const itemName = selectedOption.data('name') || selectedOption.text().split(' (')[0];
                const itemType = selectedOption.data('type') || '';
                const itemUnit = selectedOption.data('unit') || 'unit';

                if (!itemName) {
                    alert('Unable to get item information. Please try selecting the item again.');
                    return;
                }

                // Check if already added
                if (milestoneRequiredItems.find(i => i.item_id == itemId)) {
                    alert('Item already added');
                    return;
                }

                const itemData = {
                    item_id: itemId,
                    item_name: itemName,
                    item_type: itemType,
                    estimated_quantity: qty,
                    unit: itemUnit
                };

                milestoneRequiredItems.push(itemData);
                renderMilestoneItems();

                // Properly reset Select2
                select.val(null).trigger('change');
                $qtyInput.val('');
                $unitLabel.text('Unit');
            });

            function renderMilestoneItems() {
                const tbody = $('#milestoneItemsList');
                tbody.empty();

                if (milestoneRequiredItems.length === 0) {
                    tbody.append('<tr class="text-center text-muted"><td colspan="4">No items added yet</td></tr>');
                    $('#requiredItemsData').val('');
                    return;
                }

                milestoneRequiredItems.forEach((item, index) => {
                    tbody.append(`
                                                    <tr>
                                                        <td>${item.item_name}</td>
                                                        <td><span class="badge badge-info">${item.item_type}</span></td>
                                                        <td>${item.estimated_quantity} ${item.unit}</td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-danger" onclick="removeMilestoneItem(${index})">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                `);
                });

                // Update hidden input
                $('#requiredItemsData').val(JSON.stringify(milestoneRequiredItems));
            }

            window.removeMilestoneItem = function (index) {
                milestoneRequiredItems.splice(index, 1);
                renderMilestoneItems();
            };

            // Edit required items helpers
            function renderEditMilestoneItems() {
                const tbody = $('#editMilestoneItemsList');
                tbody.empty();

                if (editMilestoneRequiredItems.length === 0) {
                    tbody.append('<tr class="text-center text-muted"><td colspan="4">No items added yet</td></tr>');
                    $('#editRequiredItemsData').val('');
                    return;
                }

                editMilestoneRequiredItems.forEach((item, index) => {
                    tbody.append(`
                                                    <tr>
                                                        <td>${item.item_name}</td>
                                                        <td><span class="badge badge-info">${item.item_type}</span></td>
                                                        <td>${item.estimated_quantity} ${item.unit}</td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-danger" onclick="removeEditMilestoneItem(${index})">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                `);
                });

                $('#editRequiredItemsData').val(JSON.stringify(editMilestoneRequiredItems));
            }

            window.removeEditMilestoneItem = function (index) {
                editMilestoneRequiredItems.splice(index, 1);
                renderEditMilestoneItems();
            };

            $('#editAddItemToMilestone').click(function () {
                const select = $('#editSelectItemForMilestone');
                // Get value from Select2 properly
                const itemId = select.select2('val') || select.val();
                const selectedOption = select.find(':selected');
                const qty = parseFloat($('#editItemEstimatedQty').val());

                if (!itemId || itemId === '' || itemId === null) {
                    alert('Please select an item');
                    return;
                }

                if (!qty || qty <= 0 || isNaN(qty)) {
                    alert('Please enter a valid quantity');
                    return;
                }

                // Get item data from the selected option
                const itemName = selectedOption.data('name') || selectedOption.text().split(' (')[0];
                const itemType = selectedOption.data('type') || '';
                const itemUnit = selectedOption.data('unit') || 'unit';

                if (!itemName) {
                    alert('Unable to get item information. Please try selecting the item again.');
                    return;
                }

                if (editMilestoneRequiredItems.find(i => i.item_id == itemId)) {
                    alert('Item already added');
                    return;
                }

                const itemData = {
                    item_id: itemId,
                    item_name: itemName,
                    item_type: itemType,
                    estimated_quantity: qty,
                    unit: itemUnit
                };

                editMilestoneRequiredItems.push(itemData);
                renderEditMilestoneItems();

                // Properly reset Select2
                select.val(null).trigger('change');
                $('#editItemEstimatedQty').val('');
                $('#editItemEstimatedUnit').text('Unit');
            });

            // Require at least one required item before submitting
            $('#addMilestoneForm').on('submit', function (e) {
                if (!milestoneRequiredItems.length) {
                    e.preventDefault();
                    $('#addMilestoneErrorAlert').show();
                    $('#addMilestoneErrorText').text('Please add at least one required item for this milestone.');
                    return false;
                }
            });

            $('#addMilestoneModal').on('show.bs.modal', function () {
                currentTotalDays = {{ $project->milestones->sum('EstimatedDays') ?? 0 }};
                currentTotalWeight = {{ $project->milestones->sum('WeightedPercentage') ?? 0 }};
                $('#modalTotalMilestoneDays').text(currentTotalDays);
                $('#modalCalculatedTargetDate').html('');
                $('#EstimatedDays').val('');
                $('#WeightedPercentage').val('');
                $('#addMilestoneErrorText').text('');
                // Reset required items
                milestoneRequiredItems = [];
                renderMilestoneItems();
                updateCreateMilestoneButtonState();
            });

            $('#EstimatedDays').on('input', function () {
                const estimatedDays = parseInt($(this).val()) || 0;
                const newTotal = currentTotalDays + estimatedDays;

                // Update total milestone days display
                $('#modalTotalMilestoneDays').text(newTotal);

                // Check if exceeds project days
                if (newTotal > projectEstimatedDays) {
                    $('#modalTotalMilestoneDays').parent().addClass('text-danger');
                    $('#modalCalculatedTargetDate').html('<span class="text-danger">Warning: Total exceeds project days! Please reduce the estimated days.</span>');
                    $(this).addClass('is-invalid');
                    $(this).next('.invalid-feedback').text(`Total milestone days (${newTotal}) cannot exceed project estimated accomplish days (${projectEstimatedDays}).`);
                } else {
                    $('#modalTotalMilestoneDays').parent().removeClass('text-danger');
                    $(this).removeClass('is-invalid');
                    $(this).next('.invalid-feedback').text('');

                    @if($project->StartDate)
                        // Calculate target date (only if project has StartDate)
                        const projectStartDate = new Date('{{ $project->StartDate->format('Y-m-d') }}');
                        const cumulativeDays = currentTotalDays + estimatedDays;
                        const targetDate = new Date(projectStartDate);
                        targetDate.setDate(targetDate.getDate() + cumulativeDays);

                        const formattedDate = targetDate.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        });
                        $('#modalCalculatedTargetDate').html('Estimated Target Date: <strong>' + formattedDate + '</strong>');
                    @endif
                                            }
            });

            // Handle form submission with validation
            $('#addMilestoneForm').on('submit', function (e) {
                e.preventDefault();

                // Hide previous errors
                $('#addMilestoneErrorAlert').hide();
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                // Get form values
                const estimatedDays = parseInt($('#EstimatedDays').val()) || 0;
                const newTotal = currentTotalDays + estimatedDays;

                // Client-side validation: Check if total exceeds project days
                if (newTotal > projectEstimatedDays) {
                    $('#addMilestoneErrorAlert').show();
                    $('#addMilestoneErrorText').text(`Total milestone days (${newTotal}) cannot exceed project estimated accomplish days (${projectEstimatedDays}). Please adjust milestone durations.`);
                    $('#EstimatedDays').addClass('is-invalid');
                    $('#EstimatedDays').next('.invalid-feedback').text(`Total milestone days (${newTotal}) cannot exceed project estimated accomplish days (${projectEstimatedDays}).`);
                    return false;
                }

                // Client-side validation: prevent submit if existing totals are already maxed
                if (currentTotalDays >= projectEstimatedDays && currentTotalWeight >= maxWeight) {
                    updateCreateMilestoneButtonState();
                    return false;
                }

                // Submit via AJAX
                const formData = $(this).serialize();
                const formAction = $(this).attr('action');

                $.ajax({
                    url: formAction,
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            // Close modal and reload page to show new milestone
                            $('#addMilestoneModal').modal('hide');
                            location.reload();
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors || {};
                            const message = xhr.responseJSON.message || 'Validation failed. Please check the form.';

                            // Show general error message
                            $('#addMilestoneErrorAlert').show();
                            $('#addMilestoneErrorText').text(message);

                            // Show field-specific errors
                            $.each(errors, function (field, messages) {
                                const input = $('#' + field);
                                input.addClass('is-invalid');
                                input.next('.invalid-feedback').text(messages[0]);
                            });
                        } else {
                            // Other errors
                            $('#addMilestoneErrorAlert').show();
                            $('#addMilestoneErrorText').text(xhr.responseJSON.message || 'An error occurred. Please try again.');
                        }
                    }
                });
            });
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
        
        <!-- Upload Attachment Modal Script -->
        <script>
            $(document).ready(function() {
                const currentAdditionalCount = {{ $currentAdditionalCount ?? 0 }};
                
                // Handle attachment type change
                $('#attachment_type').on('change', function() {
                    const type = $(this).val();
                    const $singleGroup = $('#singleFileGroup');
                    const $multipleGroup = $('#multipleFilesGroup');
                    const $singleInput = $('#attachment');
                    const $multipleInput = $('#attachments');
                    
                    if (type === 'additional') {
                        // Show multiple file input
                        $singleGroup.hide();
                        $multipleGroup.show();
                        $singleInput.removeAttr('required');
                        $multipleInput.attr('required', 'required');
                    } else if (type) {
                        // Show single file input for blueprint/floorplan
                        $singleGroup.show();
                        $multipleGroup.hide();
                        $multipleInput.removeAttr('required');
                        $singleInput.attr('required', 'required');
                    } else {
                        // No type selected - hide both
                        $singleGroup.hide();
                        $multipleGroup.hide();
                    }
                    
                    // Reset previews
                    $('#imagePreview').hide();
                    $('#previewContainer').empty();
                });
                
                // Single file input label update and preview
                $('#attachment').on('change', function(e) {
                    const fileName = e.target.files[0] ? e.target.files[0].name : 'Choose file';
                    $(this).next('.custom-file-label').text(fileName);
                    
                    // Show preview
                    if (e.target.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            $('#previewContainer').html('<img src="' + event.target.result + '" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">');
                            $('#imagePreview').show();
                        };
                        reader.readAsDataURL(e.target.files[0]);
                    } else {
                        $('#imagePreview').hide();
                    }
                });
                
                // Multiple files input label update and preview
                $('#attachments').on('change', function(e) {
                    const files = e.target.files;
                    const maxFiles = 8 - currentAdditionalCount;
                    
                    if (files.length > maxFiles) {
                        alert('You can only upload up to ' + maxFiles + ' more images!');
                        $(this).val('');
                        $(this).next('.custom-file-label').text('Choose files');
                        $('#imagePreview').hide();
                        return;
                    }
                    
                    if (files.length > 0) {
                        $(this).next('.custom-file-label').text(files.length + ' file(s) selected');
                        
                        // Show previews
                        $('#previewContainer').empty();
                        Array.from(files).forEach(function(file) {
                            const reader = new FileReader();
                            reader.onload = function(event) {
                                $('#previewContainer').append(
                                    '<div class="mr-2 mb-2"><img src="' + event.target.result + '" alt="Preview" class="rounded" style="height: 100px; object-fit: cover;"></div>'
                                );
                            };
                            reader.readAsDataURL(file);
                        });
                        $('#imagePreview').show();
                    } else {
                        $(this).next('.custom-file-label').text('Choose files');
                        $('#imagePreview').hide();
                    }
                });
                
                // Reset modal when closed
                $('#uploadAttachmentModal').on('hidden.bs.modal', function () {
                    $('#uploadAttachmentForm')[0].reset();
                    $('#attachment_type').val('');
                    $('#singleFileGroup').hide();
                    $('#multipleFilesGroup').hide();
                    $('#imagePreview').hide();
                    $('#previewContainer').empty();
                });
                
                // Reset on modal open
                $('#uploadAttachmentModal').on('shown.bs.modal', function () {
                    $('#attachment_type').val('');
                    $('#singleFileGroup').hide();
                    $('#multipleFilesGroup').hide();
                    $('#imagePreview').hide();
                    $('#previewContainer').empty();
                });
            });
        </script>
    @endpush

<!-- Upload Attachment Modal -->
<div class="modal fade" id="uploadAttachmentModal" tabindex="-1" role="dialog" aria-labelledby="uploadAttachmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #87A96B;">
                <h5 class="modal-title text-white" id="uploadAttachmentModalLabel">
                    <i class="fas fa-upload mr-2"></i>Upload Project Attachment
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('projects.uploadAttachment', $project) }}" method="POST" enctype="multipart/form-data" id="uploadAttachmentForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="attachment_type">Attachment Type <span class="text-danger">*</span></label>
                        <select class="form-control" id="attachment_type" name="attachment_type" required>
                            <option value="">Select Type</option>
                            @if(!$project->BlueprintPath)
                                <option value="blueprint">Blueprint</option>
                            @endif
                            @if(!$project->FloorPlanPath)
                                <option value="floorplan">Floor Plan</option>
                            @endif
                            <option value="additional">Additional Images (Max 8 total)</option>
                        </select>
                        @php
                            $currentAdditionalCount = $project->additional_images ? count($project->additional_images) : 0;
                        @endphp
                        @if($currentAdditionalCount > 0)
                            <small class="form-text text-info">
                                Current additional images: {{ $currentAdditionalCount }}/8
                            </small>
                        @endif
                    </div>
                    <div class="form-group" id="singleFileGroup">
                        <label for="attachment">Image File <span class="text-danger">*</span></label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="attachment" name="attachment" accept="image/*">
                            <label class="custom-file-label" for="attachment">Choose file</label>
                        </div>
                        <small class="form-text text-muted">Max: 5MB (JPEG, PNG, JPG, GIF)</small>
                    </div>
                    <div class="form-group" id="multipleFilesGroup" style="display:none;">
                        <label for="attachments">Image Files <span class="text-danger">*</span> (Select multiple)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="attachments" name="attachments[]" accept="image/*" multiple>
                            <label class="custom-file-label" for="attachments">Choose files</label>
                        </div>
                        <small class="form-text text-muted">Max: 5MB per image (JPEG, PNG, JPG, GIF). You can select up to {{ 8 - $currentAdditionalCount }} more images.</small>
                    </div>
                    <div id="imagePreview" class="mt-3" style="display: none;">
                        <label>Preview:</label>
                        <div id="previewContainer" class="d-flex flex-wrap"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success" style="background-color: #87A96B !important; border-color: #87A96B !important;">
                        <i class="fas fa-upload"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

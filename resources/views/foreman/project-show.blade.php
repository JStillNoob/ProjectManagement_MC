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
                        <!-- Card 1: Project Details -->
                        <div class="col-md-4 mb-4 mb-md-0">
                            <div class="card h-100 shadow-sm" style="border-radius: 8px;">
                                <div class="card-header">
                                    <h5 class="mb-0 font-weight-bold">Project Details</h5>
                                </div>
                                <div class="card-body"
                                    style="background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);">
                                    <div class="info-row mb-3 pb-3" style="border-bottom: 1px solid #e9ecef;">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="fas fa-project-diagram text-primary mr-2"></i>
                                            <small class="text-muted font-weight-bold"
                                                style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Project
                                                Name</small>
                                        </div>
                                        <div class="ml-4">
                                            <strong
                                                style="color: #2d3748; font-size: 0.95rem;">{{ Str::limit($project->ProjectName, 30) }}</strong>
                                        </div>
                                    </div>

                                    <!-- Added Client Name here -->
                                    @if($project->client)
                                        <div class="info-row mb-3 pb-3" style="border-bottom: 1px solid #e9ecef;">
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="fas fa-user-tie text-secondary mr-2"></i>
                                                <small class="text-muted font-weight-bold"
                                                    style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Client</small>
                                            </div>
                                            <div class="ml-4">
                                                <strong
                                                    style="color: #2d3748; font-size: 0.9rem;">{{ $project->client->ClientName }}</strong>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="info-row mb-3 pb-3" style="border-bottom: 1px solid #e9ecef;">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="fas fa-tag text-info mr-2"></i>
                                            <small class="text-muted font-weight-bold"
                                                style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Status</small>
                                        </div>
                                        <div class="ml-4">
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
                                                style="font-size: 0.8rem; padding: 0.4em 0.8em; {{ $project->status->StatusName == 'Delayed' ? 'background-color: #dc3545 !important; color: white !important;' : 'background-color: transparent !important; border: none !important; color: #2d3748 !important;' }} font-weight: 600;">
                                                @if($project->status->StatusName == 'Delayed')
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                                @endif
                                                {{ $project->status->StatusName }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="info-row mb-3 pb-3" style="border-bottom: 1px solid #e9ecef;">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="fas fa-calendar-alt text-warning mr-2"></i>
                                            <small class="text-muted font-weight-bold"
                                                style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Duration</small>
                                        </div>
                                        <div class="ml-4">
                                            <strong
                                                style="color: #2d3748; font-size: 0.9rem;">{{ $project->EstimatedAccomplishDays ?? 'N/A' }}
                                                days</strong>
                                        </div>
                                    </div>

                                    @if($project->formatted_start_date)
                                        <div class="info-row mb-3 pb-3" style="border-bottom: 1px solid #e9ecef;">
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="fas fa-play-circle text-success mr-2"></i>
                                                <small class="text-muted font-weight-bold"
                                                    style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Start
                                                    Date</small>
                                            </div>
                                            <div class="ml-4">
                                                <strong
                                                    style="color: #2d3748; font-size: 0.9rem;">{{ $project->formatted_start_date }}</strong>
                                            </div>
                                        </div>
                                    @endif

                                    @if($project->formatted_end_date)
                                        <div class="info-row mb-3 pb-3" style="border-bottom: 1px solid #e9ecef;">
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="fas fa-flag-checkered text-danger mr-2"></i>
                                                <small class="text-muted font-weight-bold"
                                                    style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">End
                                                    Date</small>
                                            </div>
                                            <div class="ml-4">
                                                <strong
                                                    style="color: #2d3748; font-size: 0.9rem;">{{ $project->formatted_end_date }}</strong>
                                            </div>
                                        </div>
                                    @endif

                                    @if($project->full_address)
                                        <div class="info-row mb-3">
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="fas fa-map-marker-alt text-danger mr-2"></i>
                                                <small class="text-muted font-weight-bold"
                                                    style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Location</small>
                                            </div>
                                            <div class="ml-4">
                                                <strong
                                                    style="color: #2d3748; font-size: 0.85rem;">{{ $project->full_address }}</strong>
                                            </div>
                                        </div>
                                    @endif

                                    @if($project->ProjectDescription)
                                        <div class="mt-3 pt-3" style="border-top: 2px solid #e9ecef;">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-file-alt text-secondary mr-2"></i>
                                                <small class="text-muted font-weight-bold"
                                                    style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Description</small>
                                            </div>
                                            <p class="text-muted mb-0" style="font-size: 0.85rem; line-height: 1.5;">
                                                {{ Str::limit($project->ProjectDescription, 100) }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Card 2: Assigned Employees (Moved from bottom) -->
                        <div class="col-md-4 mb-4 mb-md-0">
                            <div class="card h-100" style="display: flex; flex-direction: column;">
                                <div class="card-header" style="flex-shrink: 0;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0 font-weight-bold">Assigned Employees</h5>
                                        <a href="{{ route('projects.employees.qr-pdf', $project) }}"
                                            class="btn btn-sm btn-info" target="_blank"
                                            style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                                            <i class="fas fa-qrcode mr-1"></i> Print QR
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body" style="padding: 0.75rem; flex: 1; display: flex; flex-direction: column; min-height: 0; overflow: hidden;">
                                    @if($project->projectEmployees && $project->projectEmployees->count() > 0)
                                        <div class="table-responsive" style="flex: 1; overflow-y: auto; overflow-x: hidden; min-height: 0;">
                                            <table class="table table-sm mb-0">
                                                <thead style="background-color: #f0f8f0; position: sticky; top: 0; z-index: 10;">
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Designation</th>
                                                        <th class="text-right">QR</th>
                                                    </tr>
                                                </thead>
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
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mr-2"
                                                                        style="width: 32px; height: 32px; font-size: 12px;">
                                                                        {{ strtoupper(substr($emp->full_name, 0, 2)) }}
                                                                    </div>
                                                                    <span><small>{{ $emp->full_name }}</small></span>
                                                                </div>
                                                            </td>
                                                            <td><small>{{ $positionName }}</small></td>
                                                            <td class="text-right">
                                                                <button type="button" class="btn btn-sm btn-outline-info"
                                                                    style="padding: 0.15rem 0.35rem; font-size: 0.7rem;"
                                                                    data-toggle="modal"
                                                                    data-target="#showQrModal{{ $assignment->id }}">
                                                                    <i class="fas fa-qrcode"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info mb-3">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            <small>No employees assigned to this project yet.</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Card 3: Project Status -->
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="mb-0 font-weight-bold">Project Status</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="projectStatusChart" width="200" height="200"></canvas>
                                    <div class="mt-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge"
                                                style="background-color: #dc3545; width: 12px; height: 12px; display: inline-block; margin-right: 8px; border-radius: 50%;"></span>
                                            <small>{{ $milestoneCounts['backlog'] }} Backlog</small>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge"
                                                style="background-color: #ff9f40; width: 12px; height: 12px; display: inline-block; margin-right: 8px; border-radius: 50%;"></span>
                                            <small>{{ $milestoneCounts['pending'] }} Pending</small>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge"
                                                style="background-color: #FFCE56; width: 12px; height: 12px; display: inline-block; margin-right: 8px; border-radius: 50%;"></span>
                                            <small>{{ $milestoneCounts['in_progress'] }} In progress</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="badge"
                                                style="background-color: #28a745; width: 12px; height: 12px; display: inline-block; margin-right: 8px; border-radius: 50%;"></span>
                                            <small>Completed {{ $milestoneCounts['completed'] }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Row: 1 Card (Full Width) -->
                    <div class="row mb-4">
                        <!-- Card 4: Project Milestones -->
                        <div class="col-12 mb-4 mb-md-0">
                            <div class="card h-100">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0 font-weight-bold">Project Milestones</h5>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if($milestoneCounts['total'] > 0)
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="font-weight-bold">Overall Progress</span>
                                                <span class="badge"
                                                    style="background: #7fb069; color: white;">{{ $project->progress_percentage }}%</span>
                                            </div>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" role="progressbar"
                                                    style="width: {{ $project->progress_percentage }}%; background: linear-gradient(135deg, #7fb069 0%, #6fa05a 100%);"
                                                    aria-valuenow="{{ $project->progress_percentage }}" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                    {{ $project->progress_percentage }}%
                                                </div>
                                            </div>
                                            <div class="mt-2 d-flex justify-content-between">
                                                <small class="text-muted">
                                                    <i class="fas fa-check-circle text-success"></i>
                                                    {{ $milestoneCounts['completed'] }} Completed
                                                </small>
                                                <small class="text-muted">
                                                    <i class="fas fa-spinner text-warning"></i>
                                                    {{ $milestoneCounts['in_progress'] }} In Progress
                                                </small>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock text-secondary"></i>
                                                    {{ $milestoneCounts['pending'] }} Pending
                                                </small>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table id="foremanMilestonesTable" class="table table-bordered table-striped mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Milestone</th>
                                                        <th class="text-center">Status</th>
                                                        <th class="text-center">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($project->milestones->sortBy([['order', 'asc'], ['milestone_id', 'asc']]) as $index => $milestone)
                                                        <tr>
                                                            <td>
                                                                <strong>{{ Str::limit($milestone->milestone_name, 40) }}</strong>
                                                                @if($milestone->description)
                                                                    <br><small
                                                                        class="text-muted">{{ Str::limit($milestone->description, 50) }}</small>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                @if($milestone->SubmissionStatus == 'Pending Approval')
                                                                    <span class="badge badge-warning badge-sm">Pending Approval</span>
                                                                @else
                                                                    @php
                                                                        $statusClass = $milestone->status == 'Completed' ? 'success' :
                                                                            ($milestone->status == 'In Progress' ? 'warning' : 'secondary');
                                                                    @endphp
                                                                    <span
                                                                        class="badge badge-{{ $statusClass }} badge-sm">{{ $milestone->status }}</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                <div style="display: inline-flex; align-items: center; justify-content: center; gap: 0.75rem;">
                                                                    <button type="button" class="btn btn-link text-info p-0"
                                                                        style="cursor: pointer;"
                                                                        data-toggle="modal"
                                                                        data-target="#milestoneDetailsModal{{ $milestone->milestone_id }}"
                                                                        title="View">
                                                                        <i class="fas fa-eye"></i>
                                                                    </button>
                                                                    @if($milestone->canUserSubmit(Auth::user()))
                                                                        <button type="button" class="btn btn-link text-success p-0"
                                                                            style="cursor: pointer;"
                                                                            data-toggle="modal"
                                                                            data-target="#submitMilestoneModal{{ $milestone->milestone_id }}"
                                                                            title="Submit">
                                                                            <i class="fas fa-paper-plane"></i>
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            <small>No milestones have been added to this project yet.</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Milestone Detail Modals -->
    @foreach($project->milestones->sortBy([['order', 'asc'], ['milestone_id', 'asc']]) as $milestone)
        <div class="modal fade" id="milestoneDetailsModal{{ $milestone->milestone_id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white">
                            <i class="fas fa-flag-checkered mr-2"></i>
                            {{ $milestone->milestone_name }}
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Description:</strong>
                                <p>{{ $milestone->description ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Target Date:</strong>
                                <p>
                                    @if($milestone->formatted_target_date && $milestone->formatted_target_date !== 'N/A')
                                        {{ $milestone->formatted_target_date }}
                                    @elseif($milestone->status === 'Pending')
                                        <span class="text-info">Will be set when milestone starts</span>
                                    @else
                                        N/A
                                    @endif
                                    @if($milestone->is_overdue)
                                        <span class="badge badge-danger ml-2">
                                            <i class="fas fa-exclamation-circle"></i> Overdue ({{ $milestone->days_overdue }} days)
                                        </span>
                                    @endif
                                    @if($milestone->is_early)
                                        <span class="badge badge-success ml-2">
                                            <i class="fas fa-check-circle"></i> Completed Early ({{ $milestone->days_early }} days ahead)
                                        </span>
                                    @endif
                                </p>
                                <strong>Status:</strong>
                                <p>
                                    @if($milestone->SubmissionStatus == 'Pending Approval')
                                        <span class="badge badge-warning">Pending Approval</span>
                                    @else
                                                        <span class="badge badge-{{ 
                                                                                                                                $milestone->status == 'Completed' ? 'success' :
                                        ($milestone->status == 'In Progress' ? 'primary' : 'secondary')
                                                                                                                            }}">
                                                            {{ $milestone->status }}
                                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Proof Images -->
                        @include('projects._proof_images', ['milestone' => $milestone])

                        <!-- Submission Log -->
                        @include('projects._milestone_log', ['milestone' => $milestone])

                        <!-- Actions for Foreman -->
                        @if($milestone->canUserSubmit(Auth::user()))
                            <div class="mt-3">
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#submitMilestoneModal{{ $milestone->milestone_id }}">
                                    <i class="fas fa-paper-plane mr-1"></i> Submit Completion
                                </button>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submission Modal -->
        @if($milestone->canUserSubmit(Auth::user()))
            @include('projects._milestone_submit_modal', ['milestone' => $milestone, 'project' => $project])
        @endif
    @endforeach

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

            /* DataTables Styling */
            #foremanMilestonesTable {
                border: none !important;
            }

            #foremanMilestonesTable thead th {
                border: none !important;
                border-bottom: 1px solid #dee2e6 !important;
                background-color: #f8f9fa;
                color: #495057;
            }

            #foremanMilestonesTable tbody td {
                border: none !important;
                border-bottom: 1px solid #dee2e6 !important;
                vertical-align: middle;
            }

            #foremanMilestonesTable tbody tr:last-child td {
                border-bottom: none !important;
            }

            /* DataTable wrapper styling */
            #foremanMilestonesTable_wrapper {
                margin: 0 !important;
            }

            #foremanMilestonesTable_wrapper .dataTables_length,
            #foremanMilestonesTable_wrapper .dataTables_filter {
                margin: 0.5rem 0 !important;
            }

            #foremanMilestonesTable_wrapper .dataTables_info,
            #foremanMilestonesTable_wrapper .dataTables_paginate {
                margin: 0.5rem 0 !important;
                padding: 0 !important;
            }

            #foremanMilestonesTable_wrapper .dataTables_paginate .paginate_button {
                background: none !important;
                border: none !important;
                padding: 0.25rem 0.5rem !important;
                text-decoration: underline !important;
                color: #007bff !important;
            }

            #foremanMilestonesTable_wrapper .dataTables_paginate .paginate_button.current {
                background: none !important;
                border: none !important;
                color: #007bff !important;
                text-decoration: underline !important;
                font-weight: bold !important;
            }

            #foremanMilestonesTable_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
                background: none !important;
                border: none !important;
                text-decoration: underline !important;
            }

            /* Milestone required items cards */
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
                background-color: #87A96B;
            }

            .milestone-req-status.badge-warning {
                background-color: #f8d57e;
                color: #6c4a00;
            }

            .milestone-req-status.badge-secondary {
                background-color: #adb5bd;
            }

            .milestone-items-list span {
                display: block;
            }
        </style>
    @endpush

    <!-- QR Code Modals for each employee -->
    @if($project->projectEmployees && $project->projectEmployees->count() > 0)
        @foreach($project->projectEmployees as $assignment)
            <div class="modal fade" id="showQrModal{{ $assignment->id }}" tabindex="-1" role="dialog"
                aria-labelledby="showQrModalLabel{{ $assignment->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header text-white" style="background: #87A96B;">
                            <h5 class="modal-title" id="showQrModalLabel{{ $assignment->id }}">
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

                // Initialize DataTables
                if ($.fn.DataTable) {
                    $('#foremanMilestonesTable').DataTable({
                        "responsive": true,
                        "lengthChange": true,
                        "autoWidth": false,
                        "searching": true,
                        "paging": true,
                        "info": true,
                        "order": [], // Disable default sorting to preserve server-side order (by 'order' field)
                        "columnDefs": [
                            { "orderable": false, "targets": [2] }, // Actions column not orderable
                            { "className": "text-center", "targets": [1, 2] } // Center Status and Actions
                        ],
                        "language": {
                            "emptyTable": "No milestones available"
                        }
                    });
                }
            });
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    @endpush
@endsection
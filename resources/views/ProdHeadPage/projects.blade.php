@extends('layouts.app')

@section('title', 'Projects Management')
@section('page-title', 'Projects Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header p-0 pt-1">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-project-diagram mr-2"></i>
                            Project Status
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm"
                                style="background-color: #7fb069 !important; border: 2px solid #7fb069 !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                                <i class="fas fa-plus mr-1"></i>
                                New Project
                            </a>
                        </div>
                    </div>
                    <ul class="nav custom-pill-tabs" id="projectTabs" role="tablist">
                        @foreach($statuses as $index => $status)
                            @php
                                $projectCount = count($projectsByStatus[$status->StatusName] ?? []);
                                $tabId = strtolower(str_replace(' ', '', $status->StatusName));
                                $badgeClass = $status->StatusName == 'Completed' ? 'success' :
                                    ($status->StatusName == 'On Going' ? 'primary' :
                                        ($status->StatusName == 'Under Warranty' ? 'warning' :
                                            ($status->StatusName == 'Pending' ? 'info' :
                                                ($status->StatusName == 'Pre-Construction' ? 'warning' :
                                                    ($status->StatusName == 'Delayed' ? 'danger' :
                                                        ($status->StatusName == 'On Hold' ? 'secondary' : 'info'))))));
                            @endphp
                            <li class="nav-item">
                                <a class="nav-link custom-pill-tab {{ $index === 0 ? 'active' : '' }}" id="{{ $tabId }}-tab" data-toggle="tab"
                                    href="#{{ $tabId }}" role="tab" aria-controls="{{ $tabId }}"
                                    aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                    {{ $status->StatusName }}
                                    <span class="badge badge-{{ $badgeClass }} ml-2">{{ $projectCount }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-body">
                    <!-- Tab Content -->
                    <div class="tab-content" id="projectTabsContent">
                        @foreach($statuses as $index => $status)
                            @php
                                $tabId = strtolower(str_replace(' ', '', $status->StatusName));
                                $projects = $projectsByStatus[$status->StatusName] ?? [];
                            @endphp
                            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="{{ $tabId }}" role="tabpanel"
                                aria-labelledby="{{ $tabId }}-tab">

                                @if(count($projects) > 0)
                                    <div class="row">
                                        @foreach($projects as $project)
                                            @php
                                                $statusClass = $project->status->StatusName == 'Completed' ? 'success' :
                                                    ($project->status->StatusName == 'On Going' ? 'primary' :
                                                        ($project->status->StatusName == 'Under Warranty' ? 'warning' :
                                                            ($project->status->StatusName == 'Pending' ? 'info' :
                                                                ($project->status->StatusName == 'Pre-Construction' ? 'warning' :
                                                                    ($project->status->StatusName == 'Delayed' ? 'danger' :
                                                                        ($project->status->StatusName == 'On Hold' ? 'secondary' : 'info'))))));
                                                $clientName = $project->client ? $project->client->ClientName : ($project->Client ?? 'No Client');
                                            @endphp
                                            <div class="col-md-6 col-lg-4 mb-4">
                                                <a href="{{ route('projects.show', $project) }}" class="card-link-wrapper" style="text-decoration: none; color: inherit;">
                                                    <div class="card project-card-modern h-100 shadow-sm clickable-card">
                                                        <div class="card-body p-3">
                                                            <!-- Title -->
                                                            <h6 class="card-title mb-2" style="font-weight: 600; color: #333; line-height: 1.3; font-size: 1.4rem;">
                                                                {{ Str::limit($project->ProjectName, 50) }}
                                                            </h6>
                                                            
                                                            <!-- Location -->
                                                            @php
                                                                $addressParts = [];
                                                                if($project->StreetAddress) {
                                                                    $addressParts[] = $project->StreetAddress;
                                                                }
                                                                if($project->Barangay) {
                                                                    $addressParts[] = $project->Barangay;
                                                                }
                                                                if($project->City) {
                                                                    $addressParts[] = $project->City;
                                                                }
                                                                if($project->StateProvince) {
                                                                    $addressParts[] = $project->StateProvince;
                                                                }
                                                                if($project->ZipCode) {
                                                                    $addressParts[] = $project->ZipCode;
                                                                }
                                                                $fullAddress = !empty($addressParts) ? implode(', ', $addressParts) : 'Location not specified';
                                                            @endphp
                                                            <p class="text-muted small mb-2" style="font-size: 0.85rem; line-height: 1.4; min-height: 38px;">
                                                                <i class="fas fa-map-marker-alt mr-1" style="color: #7fb069;"></i>
                                                                <span style="color: #555;">{{ $fullAddress }}</span>
                                                            </p>
                                                            
                                                            <!-- Status Badge -->
                                                            <div class="mb-3">
                                                                <span style="font-size: 1rem; color: #333; font-weight: 600;">
                                                                    {{ $project->status->StatusName }}
                                                                </span>
                                                            </div>
                                                            
                                                            <!-- Footer with Client Name and Icons -->
                                                            <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                                                                <div class="client-name" style="flex: 1;">
                                                                    <small style="font-size: 1rem; color: #333; font-weight: 600;">
                                                                        {{ Str::limit($clientName, 25) }}
                                                                    </small>
                                                                </div>
                                                                <div class="action-icons d-flex align-items-center">
                                                                    @if($project->status->StatusName == 'Pending' && Auth::user()->UserTypeID == 2)
                                                                        <button type="button" 
                                                                                class="btn btn-sm text-primary p-1 mr-1" 
                                                                                data-toggle="modal" 
                                                                                data-target="#proceedNTPModal"
                                                                                data-project-id="{{ $project->ProjectID }}"
                                                                                data-project-name="{{ $project->ProjectName }}"
                                                                                data-estimated-days="{{ $project->EstimatedAccomplishDays }}"
                                                                                title="Proceed with NTP"
                                                                                style="border: none; background: transparent;"
                                                                                onclick="event.preventDefault(); event.stopPropagation();">
                                                                            <i class="fas fa-check-circle" style="font-size: 1rem;"></i>
                                                                        </button>
                                                                    @endif
                                                                    <i class="fas fa-info-circle text-primary" style="font-size: 1rem;"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No {{ $status->StatusName }} Projects</h5>
                                        <p class="text-muted">No projects found in this category.</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Proceed with NTP Modal -->
    <div class="modal fade" id="proceedNTPModal" tabindex="-1" role="dialog" aria-labelledby="proceedNTPModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: #7fb069;">
                    <h5 class="modal-title" id="proceedNTPModalLabel">
                        <i class="fas fa-check-circle mr-2"></i>Proceed with NTP
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="proceedNTPForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="ntp_project_id" name="project_id">
                        <div class="form-group">
                            <label>Project Name</label>
                            <input type="text" class="form-control" id="ntp_project_name" readonly>
                        </div>
                        <div class="form-group">
                            <label for="NTPStartDate">NTP Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('NTPStartDate') is-invalid @enderror" 
                                   id="NTPStartDate" name="NTPStartDate" required>
                            <small class="form-text text-muted">The date when the Notice to Proceed is issued</small>
                            @error('NTPStartDate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="NTPAttachment">NTP Attachment <span class="text-danger">*</span></label>
                            <input type="file" class="form-control-file @error('NTPAttachment') is-invalid @enderror" 
                                   id="NTPAttachment" name="NTPAttachment" 
                                   accept="image/*,application/pdf" required>
                            <small class="form-text text-muted">Upload NTP document/image (Max: 10MB, Formats: JPG, PNG, PDF)</small>
                            <div id="filePreview" class="mt-2"></div>
                            @error('NTPAttachment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Estimated End Date</label>
                            <input type="text" class="form-control" id="estimatedEndDate" readonly>
                            <small class="form-text text-muted">Calculated: NTP Start Date + Estimated Accomplish Days</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn text-white" style="background: #7fb069; border-color: #7fb069;">
                            <i class="fas fa-check-circle"></i> Proceed with NTP
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
        .custom-pill-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        border-bottom: none !important;
        padding: 10px 0;
        margin-left: 20px; /* Add left margin */
    }

    /* Remove border from card-header containing tabs */

    .card-header.p-0.pt-1 {
        border-bottom: none !important;
    }

    .custom-pill-tabs .nav-item {
        margin: 0;
    }
    /* Custom Pill-Shaped Tabs Design */
    .custom-pill-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        border-bottom: none;
        padding: 10px 0;
    }

    .custom-pill-tabs .nav-item {
        margin: 0;
    }

    .custom-pill-tab {
        display: inline-block;
        padding: 10px 20px;
        border-radius: 25px;
        border: 1px solid rgba(0, 123, 255, 0.3);
        background-color: white;
        color: #6c757d;
        text-decoration: none;
        font-weight: normal;
        transition: all 0.3s ease;
        cursor: pointer;
        margin-right: 0;
    }

    .custom-pill-tab:hover {
        border-color: rgba(0, 123, 255, 0.5);
        color: #495057;
        text-decoration: none;
    }

    .custom-pill-tab.active {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
        font-weight: bold;
    }

    .custom-pill-tab.active:hover {
        background-color: #0056b3;
        border-color: #0056b3;
        color: white;
    }

    /* Badge styling within tabs */
    .custom-pill-tab .badge {
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 12px;
    }

    .custom-pill-tab.active .badge {
        background-color: rgba(255, 255, 255, 0.3) !important;
        color: white !important;
    }

    /* Modern Project Card Styling */
    .card-link-wrapper {
        display: block;
        height: 100%;
    }

    .card-link-wrapper:hover {
        text-decoration: none;
        color: inherit;
    }

    .project-card-modern {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        transition: all 0.3s ease;
        background: white;
        cursor: pointer;
    }

    .project-card-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12) !important;
        border-color: #d0d0d0;
    }

    .project-card-modern .card-body {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .project-card-modern .card-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: #2d5a3d;
        margin-bottom: 8px;
    }

    .project-card-modern .action-icons .btn:hover {
        background-color: #f5f5f5 !important;
        border-radius: 4px;
    }

    .project-card-modern .action-icons .btn:focus {
        box-shadow: none;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .custom-pill-tabs {
            gap: 8px;
        }

        .custom-pill-tab {
            padding: 8px 16px;
            font-size: 0.9rem;
        }
    }
</style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function () {
            // Initialize Bootstrap tabs
            $('#projectTabs a').on('click', function (e) {
                e.preventDefault();
                $(this).tab('show');
            });

            // Store estimated days when modal opens
            let currentEstimatedDays = 0;
            
            // Handle Proceed with NTP modal
            $('#proceedNTPModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                // Use attr() for better compatibility with hyphens in data attributes
                const projectId = button.attr('data-project-id') || button.data('projectId');
                const projectName = button.attr('data-project-name') || button.data('projectName') || '';
                currentEstimatedDays = parseInt(button.attr('data-estimated-days') || button.data('estimatedDays') || 0);

                // Set form action
                if (projectId) {
                    $('#proceedNTPForm').attr('action', '{{ url("projects") }}/' + projectId + '/proceed-ntp');
                }
                
                // Populate modal fields
                $('#ntp_project_id').val(projectId || '');
                $('#ntp_project_name').val(projectName || '');
                
                // Clear previous values
                $('#NTPStartDate').val('');
                $('#NTPAttachment').val('');
                $('#filePreview').html('');
                $('#estimatedEndDate').val('');
            });

            // Calculate estimated end date when NTP start date changes
            $('#NTPStartDate').on('change input', function() {
                const ntpStartDate = $(this).val();
                
                if (ntpStartDate && currentEstimatedDays && currentEstimatedDays > 0) {
                    // Parse the date string (format: YYYY-MM-DD)
                    const [year, month, day] = ntpStartDate.split('-').map(Number);
                    const startDate = new Date(year, month - 1, day);
                    const endDate = new Date(startDate);
                    endDate.setDate(endDate.getDate() + parseInt(currentEstimatedDays));
                    
                    // Format date for display
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

            // Show file preview
            $('#NTPAttachment').on('change', function() {
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

            // Reset modal when closed
            $('#proceedNTPModal').on('hidden.bs.modal', function () {
                $('#proceedNTPForm')[0].reset();
                $('#filePreview').html('');
                $('#estimatedEndDate').val('');
            });
        });
    </script>
@endpush




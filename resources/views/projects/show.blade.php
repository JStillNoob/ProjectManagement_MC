@extends('layouts.app')

@section('title', 'Project Details')
@section('page-title', 'Project Details')

@section('content')
    <!-- Top Action Bar -->
    @if($project->status && (trim($project->status->StatusName) == 'Pending' || $project->StatusID == 1))
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-end">
                <button type="button" class="btn text-white" data-toggle="modal" data-target="#approveNTPModal"
                        style="background: #87A96B; border-color: #87A96B;">
                    <i class="fas fa-check-circle mr-1"></i> Approve NTP
                </button>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-project-diagram mr-2"></i>
                        {{ $project->ProjectName }}
                    </h3>
                    <div class="card-tools">
                        @if(Auth::user()->UserTypeID == 3)
                            <a href="{{ route('foreman.projects') }}" class="btn btn-secondary btn-sm"
                               style="background-color: #6c757d !important; border: 2px solid #6c757d !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                                <i class="fas fa-arrow-left mr-1"></i>
                                Back to My Projects
                            </a>
                        @else
                            <a href="{{ route('ProdHead.projects') }}" class="btn btn-secondary btn-sm"
                               style="background-color: #6c757d !important; border: 2px solid #6c757d !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                                <i class="fas fa-arrow-left mr-1"></i>
                                Back to Projects
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Project Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Project ID:</strong></td>
                                    <td>{{ $project->ProjectID }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Project Name:</strong></td>
                                    <td>{{ $project->ProjectName }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge badge-{{ 
                                            $project->status->StatusName == 'Completed' ? 'success' : 
                                            ($project->status->StatusName == 'On Going' ? 'primary' : 
                                            ($project->status->StatusName == 'Under Warranty' ? 'warning' : 
                                            ($project->status->StatusName == 'Pending' ? 'info' : 
                                            ($project->status->StatusName == 'Pre-Construction' ? 'warning' :
                                            ($project->status->StatusName == 'On Hold' ? 'secondary' : 'danger'))))
                                        }}">
                                            {{ $project->status->StatusName }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Client:</strong></td>
                                    <td>{{ $project->Client ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Start Date:</strong></td>
                                    <td>{{ $project->formatted_start_date }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Target End Date:</strong></td>
                                    <td>{{ $project->formatted_end_date }}</td>
                                </tr>
                                @if($project->WarrantyDays && $project->WarrantyDays > 0)
                                <tr>
                                    <td><strong>Warranty:</strong></td>
                                    <td>
                                        {{ $project->WarrantyDays }} day(s)
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Project Description</h5>
                            <div class="card">
                                <div class="card-body">
                                    @if($project->ProjectDescription)
                                        {{ $project->ProjectDescription }}
                                    @else
                                        <em class="text-muted">No description provided.</em>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($project->StreetAddress || $project->City || $project->StateProvince)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Project Location</h5>
                            <div class="card">
                                <div class="card-body">
                                    <address>
                                        @if($project->StreetAddress)
                                            {{ $project->StreetAddress }}<br>
                                        @endif
                                        @if($project->Barangay)
                                            {{ $project->Barangay }}<br>
                                        @endif
                                        @if($project->City)
                                            {{ $project->City }},
                                        @endif
                                        @if($project->StateProvince)
                                            {{ $project->StateProvince }}
                                        @endif
                                        @if($project->ZipCode)
                                            {{ $project->ZipCode }}
                                        @endif
                                    </address>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Project Timeline</h5>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-info">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Start Date</span>
                                                    <span class="info-box-number">{{ $project->formatted_start_date }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-warning">
                                                    <i class="fas fa-calendar-check"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">End Date</span>
                                                    <span class="info-box-number">{{ $project->formatted_end_date }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-success">
                                                    <i class="fas fa-clock"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Duration</span>
                                                    <span class="info-box-number">
                                                        @if($project->StartDate && $project->EndDate)
                                                            {{ $project->StartDate->diffInDays($project->EndDate) }} days
                                                        @else
                                                            N/A
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Milestones Section -->
    @php
        $user = Auth::user();
        $isForeman = false;
        $isEngineerOrAdmin = false;
        $isForemanUser = ($user->UserTypeID == 3);
        
        // Check if user is Admin (UserTypeID == 2)
        if (\App\Models\ProjectMilestone::isAdmin($user)) {
            $isEngineerOrAdmin = true;
        } elseif ($user->EmployeeID) {
            $employee = \App\Models\Employee::with('position')->find($user->EmployeeID);
            if ($employee) {
                $isForeman = \App\Models\ProjectMilestone::isForeman($employee);
                $isEngineerOrAdmin = \App\Models\ProjectMilestone::isEngineer($employee);
            }
        }
    @endphp

    @if($project->milestones && $project->milestones->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-flag-checkered mr-2"></i>
                        Project Milestones
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Milestone Name</th>
                                    <th>Description</th>
                                    <th>Target Date</th>
                                    <th>Status</th>
                                    <th>Submission Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($project->milestones as $milestone)
                                    <tr>
                                        <td><strong>{{ $milestone->milestone_name }}</strong></td>
                                        <td>{{ Str::limit($milestone->description ?? 'N/A', 50) }}</td>
                                        <td>{{ $milestone->formatted_target_date ?? 'N/A' }}</td>
                                        <td>
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
                                        </td>
                                        <td>
                                            @if($milestone->SubmissionStatus)
                                                <span class="badge badge-{{ 
                                                    $milestone->SubmissionStatus == 'Approved' ? 'success' : 
                                                    ($milestone->SubmissionStatus == 'Pending Approval' ? 'warning' : 'secondary')
                                                }}">
                                                    {{ $milestone->SubmissionStatus }}
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">Not Submitted</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#milestoneDetailsModal{{ $milestone->milestone_id }}">
                                                <i class="fas fa-eye"></i> View Details
                                            </button>
                                            @if($isForeman && $milestone->canUserSubmit($user))
                                                <button type="button" class="btn btn-sm btn-primary ml-1" data-toggle="modal" data-target="#submitMilestoneModal{{ $milestone->milestone_id }}">
                                                    <i class="fas fa-paper-plane"></i> Submit
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Milestone Detail Modals -->
                    @foreach($project->milestones as $milestone)
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
                                                <p>{{ $milestone->formatted_target_date ?? 'N/A' }}</p>
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
                                        @if($isForeman && $milestone->canUserSubmit($user))
                                            <div class="mt-3">
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#submitMilestoneModal{{ $milestone->milestone_id }}">
                                                    <i class="fas fa-paper-plane mr-1"></i> Submit Completion
                                                </button>
                                            </div>
                                        @endif

                                        <!-- Actions for Engineer/Admin -->
                                        @if($isForemanUser == false && $isEngineerOrAdmin && $milestone->canUserApprove($user))
                                            <div class="mt-3">
                                                <form action="{{ route('projects.milestones.approve', [$project, $milestone]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this milestone completion?')">
                                                        <i class="fas fa-check mr-1"></i> Approve
                                                    </button>
                                                </form>
                                                <form action="{{ route('projects.milestones.reject', [$project, $milestone]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to reject this milestone submission?')">
                                                        <i class="fas fa-times mr-1"></i> Reject
                                                    </button>
                                                </form>
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
                        @if($isForeman && $milestone->canUserSubmit($user))
                            @include('projects._milestone_submit_modal', ['milestone' => $milestone, 'project' => $project])
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Bottom Action Bar -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-start align-items-center">
                        <a href="{{ route('ProdHead.projects') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Back to Projects
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Approve NTP Modal -->
@if($project->status && $project->status->StatusName == 'Pending')
<div class="modal fade" id="approveNTPModal" tabindex="-1" role="dialog" aria-labelledby="approveNTPModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background: #87A96B;">
                <h5 class="modal-title" id="approveNTPModalLabel">
                    <i class="fas fa-check-circle mr-2"></i>Approve NTP
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="approveNTPForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="ntp_project_id" name="project_id" value="{{ $project->ProjectID }}">
                    <div class="form-group">
                        <label>Project Name</label>
                        <input type="text" class="form-control" value="{{ $project->ProjectName }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="NTPStartDate">NTP Start Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('NTPStartDate') is-invalid @enderror" 
                               id="NTPStartDate" name="NTPStartDate" required min="{{ now()->toDateString() }}">
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
                    <button type="submit" class="btn text-white" style="background: #87A96B; border-color: #87A96B;">
                        <i class="fas fa-check-circle"></i> Approve NTP
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Milestone Required Items -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <div class="d-flex align-items-center">
                        <div class="bg-success rounded-circle d-flex align-items-center justify-content-center mr-2" style="width: 36px; height: 36px; background-color: #87A96B !important;">
                            <i class="fas fa-boxes text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 font-weight-bold text-dark">Milestone Required Items</h5>
                            <small class="text-muted">Grouped by milestone</small>
                        </div>
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
                                @endphp
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100 border-0 shadow-sm edit-milestone-card milestone-req-card"
                                         style="border-radius: 14px; cursor: pointer;"
                                         data-milestone-id="{{ $milestone->milestone_id }}"
                                         data-milestone-name="{{ $milestone->milestone_name }}"
                                         data-description="{{ $milestone->description }}"
                                         data-estimated-days="{{ $milestone->EstimatedDays }}"
                                         data-actual-date="{{ optional($milestone->target_date)->toDateString() ?? '' }}"
                                         data-status="{{ $milestone->status }}">
                                        <div class="card-body pb-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                @php $targetDate = $milestone->formatted_target_date ?? null; @endphp
                                                <div>
                                                    <h6 class="mb-0 font-weight-bold text-dark">{{ $milestone->milestone_name ?? 'Milestone' }}</h6>
                                                    @if($targetDate && $targetDate !== 'N/A')
                                                        <small class="text-muted">{{ $targetDate }}</small>
                                                    @endif
                                                </div>
                                                <span class="badge milestone-req-status badge-{{ $statusClass }} text-capitalize">{{ $milestone->status ?? 'Pending' }}</span>
                                            </div>
                                            @if($displayItems->count())
                                                <div class="list-group list-group-flush">
                                                    @foreach($displayItems as $req)
                                                        <div class="list-group-item px-0 py-1 d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <div class="font-weight-semibold text-dark">{{ $req->item->resourceCatalog->ItemName ?? '' }}</div>
                                                                <small class="text-muted">Qty: {{ number_format($req->estimated_quantity, 2) }} {{ $req->unit ?? ($req->item->resourceCatalog->Unit ?? '') }}</small>
                                                            </div>
                                                            <span class="badge badge-light border text-muted">{{ $req->item->resourceCatalog->Type ?? '' }}</span>
                                                        </div>
                                                    @endforeach
                                                    @if($remaining > 0)
                                                        <div class="list-group-item px-0 py-1 text-muted small">+{{ $remaining }} more item(s)</div>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="text-muted small">No required items defined for this milestone.</div>
                                            @endif
                                        </div>
                                        <div class="card-footer bg-white border-0 pt-0">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <span class="badge badge-light text-muted">Required Items</span>
                                                    <span class="text-muted small">{{ $items->count() }} total</span>
                                                </div>
                                                <span class="text-muted small"><i class="fas fa-edit mr-1"></i>Click card to edit</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No milestones defined for this project yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
<style>
    .milestone-req-card {
        transition: all 0.15s ease-in-out;
        border: 1px solid #f2f2f2;
    }
    .milestone-req-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.08);
        border-color: #e6e6e6;
    }
    .milestone-req-status.badge-success { background-color: #87A96B; }
    .milestone-req-status.badge-warning { background-color: #f8d57e; color: #6c4a00; }
    .milestone-req-status.badge-secondary { background-color: #adb5bd; }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
<script>
    $(document).ready(function() {
        @if($project->status && $project->status->StatusName == 'Pending')
        // Set form action when modal opens
        $('#approveNTPModal').on('show.bs.modal', function () {
            $('#approveNTPForm').attr('action', '{{ route("projects.proceed-ntp", $project) }}');
            const today = new Date();
            const minDate = today.toISOString().split('T')[0];
            const $ntp = $('#NTPStartDate');
            $ntp.attr('min', minDate);
            $ntp.val(minDate);
            $('#NTPAttachment').val('');
            $('#filePreview').html('');
            $('#estimatedEndDate').val('');
        });

        // Calculate estimated end date when NTP start date changes
        $('#NTPStartDate').on('change input', function() {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const minDate = $('#NTPStartDate').attr('min');
            let ntpStartDate = $(this).val();

            // Validate and clamp to min date if user picks a past date
            if (ntpStartDate) {
                const selectedDate = new Date(ntpStartDate);
                selectedDate.setHours(0, 0, 0, 0);
                
                if (selectedDate < today) {
                    alert('You cannot select a date in the past. The NTP Start Date must be today or later.');
                    ntpStartDate = minDate;
                    $(this).val(minDate);
                }
            }

            const estimatedDays = {{ $project->EstimatedAccomplishDays ?? 0 }};
            
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
        $('#approveNTPForm').on('submit', function(e) {
            const ntpStartDate = $('#NTPStartDate').val();
            if (!ntpStartDate) {
                e.preventDefault();
                alert('Please select an NTP Start Date.');
                return false;
            }
            
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const selectedDate = new Date(ntpStartDate);
            selectedDate.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                e.preventDefault();
                alert('Invalid date! The NTP Start Date cannot be in the past. Please select today or a future date.');
                $('#NTPStartDate').focus();
                return false;
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
        $('#approveNTPModal').on('hidden.bs.modal', function () {
            $('#approveNTPForm')[0].reset();
            $('#filePreview').html('');
            $('#estimatedEndDate').val('');
        });
        @endif
    });
</script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ----- Edit Milestone Required Items (card button) -----
    if (!document.getElementById('editMilestoneModal')) {
        const modalHtml = `
        <div class="modal fade" id="editMilestoneModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header text-white" style="background:#87A96B;">
                        <h5 class="modal-title"><i class="fas fa-edit mr-2"></i>Edit Milestone</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="editMilestoneForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <!-- Keep milestone fields hidden to satisfy validation; editing limited to required items -->
                            <input type="hidden" id="edit_milestone_name" name="milestone_name">
                            <input type="hidden" id="edit_description" name="description">
                            <input type="hidden" id="edit_EstimatedDays" name="EstimatedDays">
                            <input type="hidden" id="edit_actual_date" name="actual_date">
                            <input type="hidden" id="edit_status" name="status">

                            <h6 class="text-primary mb-3"><i class="fas fa-boxes mr-2"></i>Required Items</h6>
                            <div class="form-row mb-2">
                                <div class="col-md-6">
                                    <select class="form-control form-control-sm" id="editSelectItemForMilestone">
                                        <option value="">Select Item...</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control form-control-sm" id="editItemEstimatedQty" placeholder="Estimated Qty" min="0.01" step="0.01">
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
                                <table class="table table-sm table-bordered mb-0">
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
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cancel</button>
                            <button type="submit" class="btn text-white" style="background:#87A96B; border-color:#87A96B;">
                                <i class="fas fa-save"></i> Update Milestone
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    let editMilestoneRequiredItems = [];
    let resourceCatalogItems = [];

    // Prefetch resource catalog items
    fetch('{{ route("api.resource-catalog.items") }}')
        .then(res => res.json())
        .then(data => {
            resourceCatalogItems = data || [];
            const editSelect = document.getElementById('editSelectItemForMilestone');
            resourceCatalogItems.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.ItemID;
                opt.dataset.name = item.ItemName;
                opt.dataset.type = item.ItemType;
                opt.dataset.unit = item.Unit;
                opt.textContent = `${item.ItemName} (${item.ItemType})`;
                editSelect.appendChild(opt);
            });
        });

    $(document).on('change', '#editSelectItemForMilestone', function() {
        const unit = $(this).find(':selected').data('unit') || 'Unit';
        $('#editItemEstimatedUnit').text(unit);
    });

    function renderEditMilestoneItems() {
        const tbody = $('#editMilestoneItemsList');
        tbody.empty();
        if (!editMilestoneRequiredItems.length) {
            tbody.append('<tr class="text-center text-muted"><td colspan="4">No items added yet</td></tr>');
            $('#editRequiredItemsData').val('');
            return;
        }
        editMilestoneRequiredItems.forEach((item, index) => {
            tbody.append(`
                <tr>
                    <td>${item.item_name}</td>
                    <td><span class="badge badge-info">${item.item_type}</span></td>
                    <td>${item.estimated_quantity} ${item.unit || ''}</td>
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

    window.removeEditMilestoneItem = function(index) {
        editMilestoneRequiredItems.splice(index, 1);
        renderEditMilestoneItems();
    };

    $(document).on('click', '#editAddItemToMilestone', function() {
        const select = $('#editSelectItemForMilestone');
        const selected = select.find(':selected');
        const itemId = select.val();
        const qty = parseFloat($('#editItemEstimatedQty').val());
        if (!itemId) { alert('Please select an item'); return; }
        if (!qty || qty <= 0) { alert('Please enter a valid quantity'); return; }
        if (editMilestoneRequiredItems.find(i => i.item_id == itemId)) { alert('Item already added'); return; }
        editMilestoneRequiredItems.push({
            item_id: itemId,
            item_name: selected.data('name'),
            item_type: selected.data('type'),
            estimated_quantity: qty,
            unit: selected.data('unit')
        });
        renderEditMilestoneItems();
        select.val('');
        $('#editItemEstimatedQty').val('');
        $('#editItemEstimatedUnit').text('Unit');
    });

        // Wire up card click (edit items only)
    $(document).on('click', '.edit-milestone-card', function() {
        const milestoneId = $(this).data('milestone-id');
        const milestoneName = $(this).data('milestone-name');
        const description = $(this).data('description') || '';
        const estimatedDays = $(this).data('estimated-days') || '';
        const actualDate = $(this).data('actual-date') || '';
        const status = $(this).data('status') || 'Pending';

        $('#editMilestoneForm').attr('action', '{{ route("projects.milestones.update", [$project, ":milestone"]) }}'.replace(':milestone', milestoneId));
        $('#edit_milestone_name').val(milestoneName);
        $('#edit_description').val(description);
        $('#edit_EstimatedDays').val(estimatedDays);
        $('#edit_actual_date').val(actualDate);
        $('#edit_status').val(status);

        editMilestoneRequiredItems = [];
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

        $('#editMilestoneModal').modal('show');
    });

    $(document).on('submit', '#editMilestoneForm', function(e) {
        if (!$('#editRequiredItemsData').val()) {
            e.preventDefault();
            alert('Please add at least one required item for this milestone.');
        }
    });
});
</script>
@endpush

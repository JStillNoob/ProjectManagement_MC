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
                            <a href="{{ route('foreman.projects') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i>
                                Back to My Projects
                            </a>
                        @else
                            <a href="{{ route('ProdHead.projects') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i>
                                Back to Projects
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs" id="projectTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" role="tab">
                                <i class="fas fa-info-circle mr-1"></i> Project Details
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="milestones-tab" data-toggle="tab" href="#milestones" role="tab">
                                <i class="fas fa-flag-checkered mr-1"></i> Milestones
                                @if($project->milestones && $project->milestones->count() > 0)
                                    <span class="badge badge-primary ml-1">{{ $project->milestones->count() }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="attachments-tab" data-toggle="tab" href="#attachments" role="tab">
                                <i class="fas fa-paperclip mr-1"></i> Attachments
                            </a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content mt-4" id="projectTabsContent">
                        <!-- Tab 1: Project Details -->
                        <div class="tab-pane fade show active" id="details" role="tabpanel">
                            @php
                                $milestoneCounts = $project->milestone_counts;
                            @endphp
                            <div class="row">
                                <!-- Card 1: Project & Client Details -->
                                <div class="col-lg-4 mb-4">
                                    <div class="card h-100 shadow-sm" style="border-radius: 8px;">
                                        <div class="card-header" style="background-color: #f8f9fa;">
                                            <h5 class="mb-0 font-weight-bold">Project & Client Details</h5>
                                        </div>
                                        <div class="card-body">
                                            <!-- Project Details Section -->
                                            <div class="mb-4 pb-4" style="border-bottom: 2px solid #e9ecef;">
                                                <h6 class="text-primary mb-3 font-weight-bold">
                                                    <i class="fas fa-project-diagram mr-2"></i>Project
                                                </h6>
                                                <div class="mb-3">
                                                    <strong style="color: #2d3748; font-size: 1rem;">{{ $project->ProjectName }}</strong>
                                                </div>
                                                <div class="d-flex align-items-center mb-3 pb-3" style="border-bottom: 1px solid #e9ecef;">
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
                                                        <span class="badge badge-{{ $statusClass }}">
                                                            @if($project->status->StatusName == 'Delayed')
                                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                            @endif
                                                            {{ $project->status->StatusName }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <i class="fas fa-calendar-alt text-warning mr-1"></i>
                                                        <strong style="color: #2d3748; font-size: 0.85rem;">{{ $project->EstimatedAccomplishDays ?? 'N/A' }} days</strong>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center mb-3 pb-3" style="border-bottom: 1px solid #e9ecef;">
                                                    <div class="mr-4">
                                                        <i class="fas fa-play-circle text-success mr-1"></i>
                                                        <strong style="color: #2d3748; font-size: 0.85rem;">{{ $project->formatted_start_date ?? 'N/A' }}</strong>
                                                    </div>
                                                    <div>
                                                        <i class="fas fa-flag-checkered text-danger mr-1"></i>
                                                        <strong style="color: #2d3748; font-size: 0.85rem;">{{ $project->formatted_end_date ?? 'N/A' }}</strong>
                                                    </div>
                                                </div>
                                                @if($project->full_address)
                                                    <div class="mb-3 pb-3" style="border-bottom: 1px solid #e9ecef;">
                                                        <div class="d-flex align-items-start">
                                                            <i class="fas fa-map-marker-alt text-danger mr-2 mt-1"></i>
                                                            <strong style="color: #2d3748; font-size: 0.8rem;">{{ $project->full_address }}</strong>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if($project->ProjectDescription)
                                                    <div class="mt-3">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <i class="fas fa-file-alt text-secondary mr-2"></i>
                                                            <small class="text-muted font-weight-bold text-uppercase">Description</small>
                                                        </div>
                                                        <p class="text-muted mb-0" style="font-size: 0.85rem;">{{ Str::limit($project->ProjectDescription, 150) }}</p>
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
                                                                <strong style="color: #2d3748; font-size: 0.9rem;">{{ $project->client->ClientName }}</strong>
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
                                                                <small class="text-muted font-weight-bold text-uppercase">Contact</small>
                                                            </div>
                                                            <div class="ml-4">
                                                                <strong style="color: #2d3748; font-size: 0.85rem;">{{ $contactPerson }}</strong>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if($project->client->ContactNumber)
                                                        <div class="info-row mb-3 pb-3" style="border-bottom: 1px solid #e9ecef;">
                                                            <div class="d-flex align-items-center mb-1">
                                                                <i class="fas fa-phone text-success mr-2"></i>
                                                                <small class="text-muted font-weight-bold text-uppercase">Phone</small>
                                                            </div>
                                                            <div class="ml-4">
                                                                <strong style="color: #2d3748; font-size: 0.85rem;">{{ $project->client->ContactNumber }}</strong>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if($project->client->Email)
                                                        <div class="info-row mb-3">
                                                            <div class="d-flex align-items-center mb-1">
                                                                <i class="fas fa-envelope text-info mr-2"></i>
                                                                <small class="text-muted font-weight-bold text-uppercase">Email</small>
                                                            </div>
                                                            <div class="ml-4">
                                                                <strong style="color: #2d3748; font-size: 0.85rem;">{{ $project->client->Email }}</strong>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="text-center py-3">
                                                        <i class="fas fa-user-slash text-muted mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                                        <p class="text-muted mb-0">No client assigned</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 2: Assigned Employees -->
                                <div class="col-lg-4 mb-4">
                                    <div class="card h-100 shadow-sm" style="border-radius: 8px;">
                                        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #f8f9fa;">
                                            <h5 class="mb-0 font-weight-bold">Assigned Employees</h5>
                                            @if(Auth::user()->UserTypeID != 3)
                                            <a href="{{ route('projects.manage-employees', $project) }}" class="btn btn-sm text-white" style="background-color: #87A96B;">
                                                <i class="fas fa-user-plus mr-1"></i> Add
                                            </a>
                                            @endif
                                        </div>
                                        <div class="card-body" style="max-height: 450px; overflow-y: auto;">
                                            @if($project->employees && $project->employees->count() > 0)
                                                @foreach($project->employees as $employee)
                                                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3" style="border-bottom: 1px solid #e9ecef;">
                                                        <div class="d-flex align-items-center">
                                                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mr-2"
                                                                style="width: 36px; height: 36px; font-size: 0.8rem; font-weight: bold;">
                                                                {{ strtoupper(substr($employee->full_name ?? 'NA', 0, 2)) }}
                                                            </div>
                                                            <div>
                                                                <div class="font-weight-bold" style="font-size: 0.9rem;">{{ $employee->full_name }}</div>
                                                                <small class="text-muted">{{ $employee->position->PositionName ?? 'N/A' }}</small>
                                                            </div>
                                                        </div>
                                                        @if(Auth::user()->UserTypeID != 3)
                                                        <div>
                                                            <a href="{{ route('projects.employee.qr', ['project' => $project, 'employee' => $employee]) }}" 
                                                               class="btn btn-sm btn-outline-primary" title="View QR Code">
                                                                <i class="fas fa-qrcode"></i>
                                                            </a>
                                                            <form action="{{ route('projects.remove-employee', ['project' => $project, 'employee' => $employee]) }}" 
                                                                  method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                        onclick="return confirm('Remove this employee from the project?')" title="Remove">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="text-center py-4">
                                                    <i class="fas fa-users text-muted mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                                    <p class="text-muted mb-0">No employees assigned</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 3: Project Status -->
                                <div class="col-lg-4 mb-4">
                                    <div class="card h-100 shadow-sm" style="border-radius: 8px;">
                                        <div class="card-header" style="background-color: #f8f9fa;">
                                            <h5 class="mb-0 font-weight-bold">Project Status</h5>
                                        </div>
                                        <div class="card-body">
                                            <!-- Overall Progress -->
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="font-weight-bold">Overall Progress</span>
                                                <span class="badge" style="background-color: #87A96B; color: white;">{{ $project->progress_percentage }}%</span>
                                            </div>
                                            <div class="progress mb-3" style="height: 10px;">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: {{ $project->progress_percentage }}%; background-color: #87A96B;">
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between small text-muted mb-4">
                                                <span><i class="fas fa-check-circle text-success mr-1"></i>{{ $milestoneCounts['completed'] ?? 0 }} Done</span>
                                                <span><i class="fas fa-spinner text-warning mr-1"></i>{{ $milestoneCounts['in_progress'] ?? 0 }} Active</span>
                                                <span><i class="fas fa-clock text-secondary mr-1"></i>{{ $milestoneCounts['pending'] ?? 0 }} Pending</span>
                                            </div>

                                            <!-- Donut Chart -->
                                            <div class="text-center">
                                                <canvas id="milestoneChart" style="max-height: 200px;"></canvas>
                                            </div>
                                            <div class="mt-3 d-flex justify-content-around small">
                                                <span><i class="fas fa-circle text-danger mr-1"></i>{{ $milestoneCounts['backlog'] ?? 0 }} Backlog</span>
                                                <span><i class="fas fa-circle text-warning mr-1"></i>{{ $milestoneCounts['pending'] ?? 0 }} Pending</span>
                                            </div>
                                            <div class="mt-2 d-flex justify-content-around small">
                                                <span><i class="fas fa-circle" style="color: #f8d57e;"></i> {{ $milestoneCounts['in_progress'] ?? 0 }} Active</span>
                                                <span><i class="fas fa-circle text-success mr-1"></i>{{ $milestoneCounts['completed'] ?? 0 }} Done</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 2: Milestones -->
                        <div class="tab-pane fade" id="milestones" role="tabpanel">
                            @php
                                $user = Auth::user();
                                $isForeman = false;
                                $isEngineerOrAdmin = false;
                                $isForemanUser = ($user->UserTypeID == 3);
                                
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

                            <!-- Add Milestone Button -->
                            @if(Auth::user()->UserTypeID != 3)
                            <div class="d-flex justify-content-end mb-3">
                                <a href="{{ route('projects.create-milestones', $project) }}" class="btn text-white" style="background-color: #87A96B;">
                                    <i class="fas fa-plus mr-1"></i> Add New Milestone
                                </a>
                            </div>
                            @endif

                            @if($project->milestones && $project->milestones->count() > 0)
                                <!-- Milestone Cards -->
                                <div class="row">
                                    @foreach($project->milestones->sortBy([['order', 'asc'], ['milestone_id', 'asc']]) as $milestone)
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
                                                                <small class="text-muted">Target: {{ $targetDate }}</small>
                                                            @else
                                                                <small class="text-info">Target date will be set when started</small>
                                                            @endif
                                                        </div>
                                                        <span class="badge milestone-req-status badge-{{ $statusClass }} text-capitalize">{{ $milestone->status ?? 'Pending' }}</span>
                                                    </div>

                                                    <!-- Show early/overdue badges -->
                                                    @if($milestone->is_overdue)
                                                        <span class="badge badge-danger mb-2">
                                                            <i class="fas fa-exclamation-circle"></i> Overdue ({{ $milestone->days_overdue }} days)
                                                        </span>
                                                    @elseif($milestone->is_early)
                                                        <span class="badge badge-success mb-2">
                                                            <i class="fas fa-check-circle"></i> Completed Early ({{ $milestone->days_early }} days ahead)
                                                        </span>
                                                    @endif

                                                    @if($displayItems->count())
                                                        <div class="list-group list-group-flush">
                                                            @foreach($displayItems as $req)
                                                                <div class="list-group-item px-0 py-1 d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <div class="font-weight-semibold text-dark">{{ $req->item->resourceCatalog->ItemName ?? '' }}</div>
                                                                        <small class="text-muted">Qty: 
                                                                            @if($req->item->resourceCatalog && $req->item->resourceCatalog->requiresIntegerQuantity())
                                                                                {{ number_format((int) $req->estimated_quantity, 0) }}
                                                                            @else
                                                                                {{ number_format($req->estimated_quantity, 2) }}
                                                                            @endif
                                                                            {{ $req->unit ?? ($req->item->resourceCatalog->Unit ?? '') }}
                                                                        </small>
                                                                    </div>
                                                                    <span class="badge badge-light border text-muted">{{ $req->item->resourceCatalog->Type ?? '' }}</span>
                                                                </div>
                                                            @endforeach
                                                            @if($remaining > 0)
                                                                <div class="list-group-item px-0 py-1 text-muted small">+{{ $remaining }} more item(s)</div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="text-muted small py-2">No required items defined for this milestone.</div>
                                                    @endif
                                                </div>
                                                <div class="card-footer bg-white border-0 pt-0">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <span class="badge badge-light text-muted">Required Items</span>
                                                            <span class="text-muted small">{{ $items->count() }} total</span>
                                                        </div>
                                                        @if($milestone->status === 'Completed')
                                                            <span class="text-muted small"><i class="fas fa-lock mr-1"></i>Completed - Cannot edit</span>
                                                        @else
                                                            <span class="text-muted small"><i class="fas fa-edit mr-1"></i>Click card to edit</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-flag-checkered text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="text-muted mb-3">No milestones defined for this project yet.</p>
                                    @if(Auth::user()->UserTypeID != 3)
                                    <a href="{{ route('projects.create-milestones', $project) }}" class="btn text-white" style="background-color: #87A96B;">
                                        <i class="fas fa-plus mr-1"></i> Add First Milestone
                                    </a>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Tab 3: Attachments -->
                        <div class="tab-pane fade" id="attachments" role="tabpanel">
                            @if(Auth::user()->UserTypeID != 3)
                            <div class="d-flex justify-content-end mb-3">
                                <button type="button" class="btn text-white" data-toggle="modal" data-target="#uploadImageModal"
                                        style="background-color: #87A96B;">
                                    <i class="fas fa-upload mr-1"></i> Upload Image
                                </button>
                            </div>
                            @endif

                            <div class="row">
                                <!-- Blueprint -->
                                @if($project->BlueprintPath)
                                <div class="col-md-4 mb-4">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-body text-center">
                                            @php
                                                $blueprintExt = pathinfo($project->BlueprintPath, PATHINFO_EXTENSION);
                                                $isBlueprintPdf = strtolower($blueprintExt) === 'pdf';
                                            @endphp
                                            @if($isBlueprintPdf)
                                                <div class="mb-3" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                                                    <img src="{{ asset('images/pdf-icon.png') }}" alt="PDF" style="max-height: 100px;">
                                                </div>
                                            @else
                                                <a href="{{ Storage::url($project->BlueprintPath) }}" data-lightbox="attachments" data-title="Blueprint">
                                                    <img src="{{ Storage::url($project->BlueprintPath) }}" alt="Blueprint" 
                                                         class="img-fluid mb-3" style="max-height: 200px; cursor: pointer;">
                                                </a>
                                            @endif
                                            <span class="badge badge-primary mb-2"><i class="fas fa-drafting-compass mr-1"></i> Blueprint</span>
                                            <div class="mt-2">
                                                <a href="{{ Storage::url($project->BlueprintPath) }}" class="btn btn-sm btn-outline-secondary" download>
                                                    <i class="fas fa-download mr-1"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Floor Plan -->
                                @if($project->FloorPlanPath)
                                <div class="col-md-4 mb-4">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-body text-center">
                                            @php
                                                $floorPlanExt = pathinfo($project->FloorPlanPath, PATHINFO_EXTENSION);
                                                $isFloorPlanPdf = strtolower($floorPlanExt) === 'pdf';
                                            @endphp
                                            @if($isFloorPlanPdf)
                                                <div class="mb-3" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                                                    <img src="{{ asset('images/pdf-icon.png') }}" alt="PDF" style="max-height: 100px;">
                                                </div>
                                            @else
                                                <a href="{{ Storage::url($project->FloorPlanPath) }}" data-lightbox="attachments" data-title="Floor Plan">
                                                    <img src="{{ Storage::url($project->FloorPlanPath) }}" alt="Floor Plan" 
                                                         class="img-fluid mb-3" style="max-height: 200px; cursor: pointer;">
                                                </a>
                                            @endif
                                            <span class="badge badge-success mb-2"><i class="fas fa-map mr-1"></i> Floor Plan</span>
                                            <div class="mt-2">
                                                <a href="{{ Storage::url($project->FloorPlanPath) }}" class="btn btn-sm btn-outline-secondary" download>
                                                    <i class="fas fa-download mr-1"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- NTP Attachment -->
                                @if($project->NTPAttachment)
                                <div class="col-md-4 mb-4">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-body text-center">
                                            @php
                                                $ntpExt = pathinfo($project->NTPAttachment, PATHINFO_EXTENSION);
                                                $isNtpPdf = strtolower($ntpExt) === 'pdf';
                                            @endphp
                                            @if($isNtpPdf)
                                                <div class="mb-3" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                                                    <img src="{{ asset('images/pdf-icon.png') }}" alt="PDF" style="max-height: 100px;">
                                                </div>
                                                <p class="mb-2">PDF File</p>
                                            @else
                                                <a href="{{ Storage::url($project->NTPAttachment) }}" data-lightbox="attachments" data-title="NTP Document">
                                                    <img src="{{ Storage::url($project->NTPAttachment) }}" alt="NTP" 
                                                         class="img-fluid mb-3" style="max-height: 200px; cursor: pointer;">
                                                </a>
                                            @endif
                                            <span class="badge badge-info mb-2"><i class="fas fa-file-contract mr-1"></i> NTP</span>
                                            @if($project->NTPStartDate)
                                                <p class="small text-muted mb-2"><i class="fas fa-calendar mr-1"></i>{{ $project->formatted_ntp_start_date }}</p>
                                            @endif
                                            <div class="mt-2">
                                                <a href="{{ Storage::url($project->NTPAttachment) }}" class="btn btn-sm btn-outline-secondary" download>
                                                    <i class="fas fa-download mr-1"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Additional Images -->
                                @if($project->additional_images && count($project->additional_images) > 0)
                                    @foreach($project->additional_images as $image)
                                    <div class="col-md-4 mb-4">
                                        <div class="card shadow-sm h-100">
                                            <div class="card-body text-center">
                                                @php
                                                    $imgExt = pathinfo($image['path'] ?? '', PATHINFO_EXTENSION);
                                                    $isImgPdf = strtolower($imgExt) === 'pdf';
                                                @endphp
                                                @if($isImgPdf)
                                                    <div class="mb-3" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                                                        <img src="{{ asset('images/pdf-icon.png') }}" alt="PDF" style="max-height: 100px;">
                                                    </div>
                                                    <p class="mb-2">PDF File</p>
                                                @else
                                                    <a href="{{ Storage::url($image['path']) }}" data-lightbox="attachments" data-title="{{ $image['label'] ?? 'Additional Image' }}">
                                                        <img src="{{ Storage::url($image['path']) }}" alt="{{ $image['label'] ?? 'Image' }}" 
                                                             class="img-fluid mb-3" style="max-height: 200px; cursor: pointer;">
                                                    </a>
                                                @endif
                                                @if(isset($image['label']))
                                                    <span class="badge badge-secondary mb-2">{{ $image['label'] }}</span>
                                                @endif
                                                @if(isset($image['uploaded_at']))
                                                    <p class="small text-muted mb-2"><i class="fas fa-calendar mr-1"></i>{{ \Carbon\Carbon::parse($image['uploaded_at'])->format('M d, Y') }}</p>
                                                @endif
                                                <div class="mt-2">
                                                    <a href="{{ Storage::url($image['path']) }}" class="btn btn-sm btn-outline-secondary" download>
                                                        <i class="fas fa-download mr-1"></i> Download
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif

                                <!-- No Attachments Message -->
                                @if(!$project->BlueprintPath && !$project->FloorPlanPath && !$project->NTPAttachment && (!$project->additional_images || count($project->additional_images) == 0))
                                <div class="col-12">
                                    <div class="text-center py-5">
                                        <i class="fas fa-paperclip text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                                        <p class="text-muted">No attachments uploaded for this project yet.</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Action Bar -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-start">
                @if(Auth::user()->UserTypeID == 3)
                    <a href="{{ route('foreman.projects') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Back to My Projects
                    </a>
                @else
                    <a href="{{ route('ProdHead.projects') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Projects
                    </a>
                @endif
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
@endif

<!-- Upload Image Modal -->
@if(Auth::user()->UserTypeID != 3)
<div class="modal fade" id="uploadImageModal" tabindex="-1" role="dialog" aria-labelledby="uploadImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background: #87A96B;">
                <h5 class="modal-title" id="uploadImageModalLabel">
                    <i class="fas fa-upload mr-2"></i>Upload Image
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('projects.upload-image', $project) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="image">Select Image <span class="text-danger">*</span></label>
                        <input type="file" class="form-control-file" id="image" name="image" accept="image/*,application/pdf" required>
                        <small class="form-text text-muted">Supported formats: JPG, PNG, PDF (Max: 10MB)</small>
                    </div>
                    <div class="form-group">
                        <label for="label">Label (Optional)</label>
                        <input type="text" class="form-control" id="label" name="label" placeholder="e.g., Site Photo, Progress Photo">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white" style="background: #87A96B;">
                        <i class="fas fa-upload mr-1"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
<style>
    .nav-tabs .nav-link {
        color: #6c757d;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 0.75rem 1.25rem;
    }
    .nav-tabs .nav-link:hover {
        border-color: transparent;
        color: #87A96B;
    }
    .nav-tabs .nav-link.active {
        color: #87A96B;
        background-color: transparent;
        border-color: transparent transparent #87A96B;
        border-bottom: 2px solid #87A96B;
    }
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
<script>
    $(document).ready(function() {
        // Milestone Chart
        const ctx = document.getElementById('milestoneChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Backlog', 'Pending', 'Active', 'Done'],
                    datasets: [{
                        data: [
                            {{ $project->milestone_counts['backlog'] ?? 0 }},
                            {{ $project->milestone_counts['pending'] ?? 0 }},
                            {{ $project->milestone_counts['in_progress'] ?? 0 }},
                            {{ $project->milestone_counts['completed'] ?? 0 }}
                        ],
                        backgroundColor: ['#dc3545', '#fd7e14', '#f8d57e', '#87A96B'],
                        borderWidth: 0
                    }]
                },
                options: {
                    cutout: '60%',
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }

        @if($project->status && $project->status->StatusName == 'Pending')
        // NTP Form handling
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

        $('#NTPStartDate').on('change input', function() {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const minDate = $('#NTPStartDate').attr('min');
            let ntpStartDate = $(this).val();

            if (ntpStartDate) {
                const selectedDate = new Date(ntpStartDate);
                selectedDate.setHours(0, 0, 0, 0);
                
                if (selectedDate < today) {
                    alert('You cannot select a date in the past.');
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

        $('#NTPAttachment').on('change', function() {
            const file = this.files[0];
            if (file) {
                const fileName = file.name;
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
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
        @endif
    });
</script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Edit Milestone Modal
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
                                        <input type="number" class="form-control form-control-sm" id="editItemEstimatedQty" placeholder="Qty" min="0.01" step="0.01">
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

    $(document).on('click', '.edit-milestone-card', function() {
        const milestoneId = $(this).data('milestone-id');
        const milestoneName = $(this).data('milestone-name');
        const description = $(this).data('description') || '';
        const estimatedDays = $(this).data('estimated-days') || '';
        const actualDate = $(this).data('actual-date') || '';
        const status = $(this).data('status') || 'Pending';

        // Don't allow editing completed milestones
        if (status === 'Completed') {
            return;
        }

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

@extends('layouts.app')

@section('title', 'Equipment Return Details')
@section('page-title', 'Equipment Return Details')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <!-- Card Header -->
                    <div class="card-header" style="background-color: #87A96B;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0" style="color: white; font-size: 1.25rem;">
                                <i class="fas fa-info-circle me-2"></i>
                                Equipment Return Details
                            </h3>
                            <a href="{{ route('equipment.returns.index') }}" class="btn btn-secondary" aria-label="Back to Equipment Returns">
                                <i class="fas fa-arrow-left me-1" aria-hidden="true"></i> Back
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Equipment Information Section -->
                        <div class="row mb-4">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                                    <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.85rem;">
                                        <i class="fas fa-tools me-2"></i>Equipment Details
                                    </h6>
                                    <table class="table table-sm table-borderless mb-0" style="font-size: 0.95rem;">
                                        <tr>
                                            <td class="text-muted ps-0" style="width: 50%;">Equipment:</td>
                                            <td class="fw-bold">{{ $assignment->inventoryItem->resourceCatalog->ItemName ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0">Quantity Issued:</td>
                                            <td><strong>{{ number_format((int) $assignment->QuantityAssigned, 0) }}</strong> {{ $assignment->inventoryItem->resourceCatalog->Unit ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0">Type:</td>
                                            <td>
                                                @if($assignment->inventoryItem->resourceCatalog->Type == 'Equipment')
                                                    <span class="badge bg-info">{{ $assignment->inventoryItem->resourceCatalog->Type }}</span>
                                                @else
                                                    <span class="badge bg-success">{{ $assignment->inventoryItem->resourceCatalog->Type }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                                    <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.85rem;">
                                        <i class="fas fa-project-diagram me-2"></i>Project Information
                                    </h6>
                                    <table class="table table-sm table-borderless mb-0" style="font-size: 0.95rem;">
                                        <tr>
                                            <td class="text-muted ps-0" style="width: 40%;">Project:</td>
                                            <td class="fw-bold">{{ $assignment->milestone->project->ProjectName ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0">Milestone:</td>
                                            <td>{{ $assignment->milestone->milestone_name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0">Location:</td>
                                            <td>
                                                @php
                                                    $project = $assignment->milestone->project;
                                                    $location = $project->City ?? $project->full_address ?? 'Not specified';
                                                @endphp
                                                {{ $location }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                                    <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.85rem;">
                                        <i class="fas fa-clock me-2"></i>Usage Summary
                                    </h6>
                                    <table class="table table-sm table-borderless mb-0" style="font-size: 0.95rem;">
                                        @php
                                            $endDate = $assignment->DateReturned ?? now();
                                            $days = $assignment->DateAssigned ? (int) $assignment->DateAssigned->diffInDays($endDate) : 0;
                                        @endphp
                                        <tr>
                                            <td class="text-muted ps-0" style="width: 40%;">Days in Use:</td>
                                            <td><span class="badge" style="background-color: #17a2b8; color: white;">{{ $days }} days</span></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0">Status:</td>
                                            <td>
                                                @if($assignment->Status == 'Returned')
                                                    <span class="badge bg-success">Returned</span>
                                                @elseif($assignment->Status == 'Damaged')
                                                    <span class="badge bg-warning">Returned (Damaged)</span>
                                                @elseif($assignment->Status == 'Missing')
                                                    <span class="badge bg-danger">Returned (Missing)</span>
                                                @else
                                                    <span class="badge bg-info">{{ $assignment->Status }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Return Details Section -->
                        <div class="border rounded p-3 mb-4" style="background-color: #f8f9fa;">
                            <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.85rem;">
                                <i class="fas fa-clipboard-check me-2"></i>Return Details
                            </h6>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label text-muted">Date Assigned</label>
                                    <div class="fw-bold">{{ $assignment->DateAssigned ? $assignment->DateAssigned->format('M d, Y') : 'N/A' }}</div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label text-muted">Date Returned</label>
                                    <div class="fw-bold">{{ $assignment->DateReturned ? $assignment->DateReturned->format('M d, Y') : 'N/A' }}</div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label text-muted">Return Condition</label>
                                    <div class="fw-bold">
                                        @if($assignment->Status == 'Returned')
                                            <span class="text-success">Good Condition</span>
                                        @elseif($assignment->Status == 'Damaged')
                                            <span class="text-warning">Damaged</span>
                                        @elseif($assignment->Status == 'Missing')
                                            <span class="text-danger">Missing/Lost</span>
                                        @else
                                            {{ $assignment->Status }}
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label text-muted">Returned By</label>
                                    <div class="fw-bold">
                                        @if($assignment->returnedByUser && $assignment->returnedByUser->employee)
                                            {{ $assignment->returnedByUser->employee->full_name }}
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </div>

                                @if($assignment->ReturnRemarks)
                                    <div class="col-12">
                                        <label class="form-label text-muted">Return Notes</label>
                                        <div class="p-2 border rounded" style="background-color: white;">
                                            {{ $assignment->ReturnRemarks }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Incident Report (shown when Damaged or Missing) -->
                        @if($incident)
                            <div class="border rounded p-4 mb-3" style="background-color: #fff3cd; border-color: #ffc107 !important; border-width: 2px !important;">
                                <h5 class="text-uppercase fw-bold mb-4" style="color: #856404; font-size: 1.1rem;">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Full Incident Report
                                </h5>
                                
                                <!-- Basic Incident Information -->
                                <div class="row mb-3">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label text-muted fw-bold">Incident Type</label>
                                        <div class="fw-bold" style="font-size: 1.05rem;">
                                            @if($incident->IncidentType == 'Damage')
                                                <span class="badge bg-warning text-dark" style="font-size: 0.9rem;">
                                                    <i class="fas fa-tools me-1"></i>{{ $incident->IncidentType }}
                                                </span>
                                            @elseif($incident->IncidentType == 'Loss')
                                                <span class="badge bg-danger" style="font-size: 0.9rem;">
                                                    <i class="fas fa-times-circle me-1"></i>{{ $incident->IncidentType }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary" style="font-size: 0.9rem;">{{ $incident->IncidentType }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label text-muted fw-bold">Incident Date</label>
                                        <div class="fw-bold" style="font-size: 1.05rem;">
                                            @if($incident->IncidentDate)
                                                {{ \Carbon\Carbon::parse($incident->IncidentDate)->format('M d, Y') }}
                                            @else
                                                <span class="text-muted">Not specified</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label text-muted fw-bold">Status</label>
                                        <div class="fw-bold" style="font-size: 1.05rem;">
                                            @if($incident->Status)
                                                @if($incident->Status == 'Resolved')
                                                    <span class="badge bg-success" style="font-size: 0.9rem;">
                                                        <i class="fas fa-check-circle me-1"></i>{{ $incident->Status }}
                                                    </span>
                                                @elseif($incident->Status == 'Pending')
                                                    <span class="badge bg-warning text-dark" style="font-size: 0.9rem;">
                                                        <i class="fas fa-clock me-1"></i>{{ $incident->Status }}
                                                    </span>
                                                @elseif($incident->Status == 'Under Investigation')
                                                    <span class="badge bg-info" style="font-size: 0.9rem;">
                                                        <i class="fas fa-search me-1"></i>{{ $incident->Status }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary" style="font-size: 0.9rem;">{{ $incident->Status }}</span>
                                                @endif
                                            @else
                                                <span class="text-muted">Not specified</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label text-muted fw-bold">Responsible Employee</label>
                                        <div class="fw-bold" style="font-size: 1.05rem;">
                                            @if($incident->responsibleEmployee)
                                                <i class="fas fa-user me-1 text-muted"></i>{{ $incident->responsibleEmployee->full_name }}
                                            @else
                                                <span class="text-muted">Not Assigned</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Equipment and Project Details -->
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted fw-bold">Equipment Involved</label>
                                        <div class="fw-bold" style="font-size: 1.05rem;">
                                            <i class="fas fa-wrench me-1 text-muted"></i>{{ $assignment->inventoryItem->resourceCatalog->ItemName ?? 'N/A' }}
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted fw-bold">Project</label>
                                        <div class="fw-bold" style="font-size: 1.05rem;">
                                            <i class="fas fa-project-diagram me-1 text-muted"></i>{{ $assignment->milestone->project->ProjectName ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Description -->
                                @if($incident->Description)
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <label class="form-label text-muted fw-bold">Incident Description</label>
                                            <div class="p-3 border rounded" style="background-color: white;">
                                                {{ $incident->Description }}
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Action Taken -->
                                @if($incident->ActionTaken)
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <label class="form-label text-muted fw-bold">Action Taken</label>
                                            <div class="p-3 border rounded" style="background-color: white;">
                                                {{ $incident->ActionTaken }}
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Photo Evidence -->
                                @if($incident->PhotoPath)
                                    <div class="row">
                                        <div class="col-12">
                                            <label class="form-label text-muted fw-bold">Photo Evidence</label>
                                            <div class="mt-2">
                                                <img src="{{ Storage::url($incident->PhotoPath) }}" 
                                                     alt="Incident Photo" 
                                                     class="img-thumbnail" 
                                                     style="max-width: 100%; max-height: 400px; cursor: pointer; border: 2px solid #ffc107;"
                                                     onclick="window.open('{{ Storage::url($incident->PhotoPath) }}', '_blank')">
                                                <div class="mt-2">
                                                    <a href="{{ Storage::url($incident->PhotoPath) }}" target="_blank" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-external-link-alt me-1"></i> Open in New Tab
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection


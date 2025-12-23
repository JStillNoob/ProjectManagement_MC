@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h2>Incident Details</h2>
                <p class="text-muted">Incident #{{ $incident->IncidentID }}</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('equipment.incidents.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Incident Information -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0">Incident Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th style="width: 40%;">Incident Type:</th>
                                <td>
                                    @if($incident->IncidentType == 'Damage')
                                        <span class="badge bg-warning">{{ $incident->IncidentType }}</span>
                                    @elseif($incident->IncidentType == 'Loss' || $incident->IncidentType == 'Theft')
                                        <span class="badge bg-danger">{{ $incident->IncidentType }}</span>
                                    @else
                                        <span class="badge bg-info">{{ $incident->IncidentType }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Incident Date:</th>
                                <td>{{ $incident->IncidentDate ? $incident->IncidentDate->format('F d, Y') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    @if($incident->Status == 'Reported')
                                        <span class="badge bg-secondary">{{ $incident->Status }}</span>
                                    @elseif($incident->Status == 'Under Investigation')
                                        <span class="badge bg-info">{{ $incident->Status }}</span>
                                    @elseif($incident->Status == 'Resolved')
                                        <span class="badge bg-success">{{ $incident->Status }}</span>
                                    @else
                                        <span class="badge bg-dark">{{ $incident->Status }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Estimated Cost:</th>
                                <td>
                                    @if($incident->EstimatedCost)
                                        <strong>₱{{ number_format($incident->EstimatedCost, 2) }}</strong>
                                    @else
                                        <span class="text-muted">Not specified</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Equipment Details</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th style="width: 40%;">Equipment:</th>
                                <td><strong>{{ $incident->inventoryItem->ItemName ?? 'N/A' }}</strong></td>
                            </tr>
                            <tr>
                                <th>Item Code:</th>
                                <td>{{ $incident->inventoryItem->ItemCode ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Equipment Type:</th>
                                <td>{{ $incident->inventoryItem->inventoryItemType->TypeName ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Project & Personnel Information -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Project Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th style="width: 40%;">Project:</th>
                                <td>
                                    @if($incident->project)
                                        <a href="{{ route('projects.show', $incident->project) }}">
                                            {{ $incident->project->ProjectName }}
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            @if($incident->project)
                                <tr>
                                    <th>Location:</th>
                                    <td>{{ $incident->project->Location ?? 'N/A' }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Personnel</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th style="width: 40%;">Responsible Employee:</th>
                                <td>{{ $incident->responsibleEmployee->FullName ?? 'Not assigned' }}</td>
                            </tr>
                            @if($incident->responsibleEmployee)
                                <tr>
                                    <th>Position:</th>
                                    <td>{{ $incident->responsibleEmployee->position->PositionName ?? 'N/A' }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                @if($incident->PhotoPath)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Photo Evidence</h5>
                        </div>
                        <div class="card-body text-center">
                            <img src="{{ asset('storage/' . $incident->PhotoPath) }}" alt="Incident Photo" class="img-fluid"
                                style="max-height: 400px; border: 1px solid #ddd; padding: 10px;">
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Description -->
        @if($incident->Description)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Incident Description</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $incident->Description }}</p>
                </div>
            </div>
        @endif

        <!-- Resolution Notes -->
        @if($incident->ResolutionNotes)
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Resolution Notes</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $incident->ResolutionNotes }}</p>
                </div>
            </div>
        @endif

        <!-- Update Status Form -->
        @if($incident->Status != 'Closed')
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Update Incident Status</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('equipment.incidents.update', $incident->IncidentID) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="Status" class="form-label">Status</label>
                                    <select name="Status" id="Status" class="form-select" required>
                                        <option value="Reported" {{ $incident->Status == 'Reported' ? 'selected' : '' }}>Reported
                                        </option>
                                        <option value="Under Investigation" {{ $incident->Status == 'Under Investigation' ? 'selected' : '' }}>Under Investigation</option>
                                        <option value="Resolved" {{ $incident->Status == 'Resolved' ? 'selected' : '' }}>Resolved
                                        </option>
                                        <option value="Closed" {{ $incident->Status == 'Closed' ? 'selected' : '' }}>Closed
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="ResolutionNotes" class="form-label">Resolution Notes</label>
                                    <textarea name="ResolutionNotes" id="ResolutionNotes" class="form-control"
                                        rows="3">{{ $incident->ResolutionNotes }}</textarea>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
@endsection
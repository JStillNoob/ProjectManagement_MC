@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h2>Equipment Incidents</h2>
                <p class="text-muted">Track damage, loss, and theft incidents</p>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body">
                        <h6 class="card-title text-warning">Damage Incidents</h6>
                        <h3>{{ $incidentStats['damage'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-danger">
                    <div class="card-body">
                        <h6 class="card-title text-danger">Loss/Theft</h6>
                        <h3>{{ ($incidentStats['loss'] ?? 0) + ($incidentStats['theft'] ?? 0) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-info">
                    <div class="card-body">
                        <h6 class="card-title text-info">Under Investigation</h6>
                        <h3>{{ $incidentStats['under_investigation'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body">
                        <h6 class="card-title text-success">Resolved</h6>
                        <h3>{{ $incidentStats['resolved'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('equipment.incidents.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Incident Type</label>
                            <select name="type" class="form-select">
                                <option value="">All Types</option>
                                <option value="Damage" {{ request('type') == 'Damage' ? 'selected' : '' }}>Damage</option>
                                <option value="Loss" {{ request('type') == 'Loss' ? 'selected' : '' }}>Loss</option>
                                <option value="Theft" {{ request('type') == 'Theft' ? 'selected' : '' }}>Theft</option>
                                <option value="Malfunction" {{ request('type') == 'Malfunction' ? 'selected' : '' }}>
                                    Malfunction</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="Reported" {{ request('status') == 'Reported' ? 'selected' : '' }}>Reported
                                </option>
                                <option value="Under Investigation" {{ request('status') == 'Under Investigation' ? 'selected' : '' }}>Under Investigation</option>
                                <option value="Resolved" {{ request('status') == 'Resolved' ? 'selected' : '' }}>Resolved
                                </option>
                                <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Project</label>
                            <select name="project_id" class="form-select">
                                <option value="">All Projects</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->ProjectID }}" {{ request('project_id') == $project->ProjectID ? 'selected' : '' }}>
                                        {{ $project->ProjectName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-secondary w-100">
                                <i class="bi bi-funnel"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Incidents Table -->
        <div class="card">
            <div class="card-body">
                @if($incidents->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Equipment</th>
                                    <th>Type</th>
                                    <th>Project</th>
                                    <th>Responsible</th>
                                    <th>Est. Cost</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($incidents as $incident)
                                    <tr>
                                        <td>{{ $incident->IncidentDate ? $incident->IncidentDate->format('M d, Y') : 'N/A' }}</td>
                                        <td>
                                            <strong>{{ $incident->inventoryItem->ItemName ?? 'N/A' }}</strong>
                                        </td>
                                        <td>
                                            @if($incident->IncidentType == 'Damage')
                                                <span class="badge bg-warning">{{ $incident->IncidentType }}</span>
                                            @elseif($incident->IncidentType == 'Loss' || $incident->IncidentType == 'Theft')
                                                <span class="badge bg-danger">{{ $incident->IncidentType }}</span>
                                            @else
                                                <span class="badge bg-info">{{ $incident->IncidentType }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $incident->project->ProjectName ?? 'N/A' }}</td>
                                        <td>{{ $incident->responsibleEmployee->FullName ?? 'N/A' }}</td>
                                        <td>
                                            @if($incident->EstimatedCost)
                                                ₱{{ number_format($incident->EstimatedCost, 2) }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
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
                                        <td>
                                            <a href="{{ route('equipment.incidents.show', $incident->IncidentID) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $incidents->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-shield-check" style="font-size: 3rem; color: #28a745;"></i>
                        <p class="text-muted mt-3">No incidents reported</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Client Details: {{ $client->ClientName }}</h3>
                    <div>
                        <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Client Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Client ID:</strong></td>
                                    <td>{{ $client->ClientID }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Client Name:</strong></td>
                                    <td>{{ $client->ClientName }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Contact Person:</strong></td>
                                    <td>{{ $client->ContactPerson ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Contact Number:</strong></td>
                                    <td>{{ $client->ContactNumber ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $client->Email ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Project Statistics</h5>
                            <div class="card">
                                <div class="card-body text-center">
                                    <h3 class="text-primary">{{ $client->projects->count() }}</h3>
                                    <p class="mb-0">Total Projects</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Associated Projects</h5>
                            @if($client->projects->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Project ID</th>
                                                <th>Project Name</th>
                                                <th>Status</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($client->projects as $project)
                                                <tr>
                                                    <td>{{ $project->ProjectID }}</td>
                                                    <td>{{ $project->ProjectName }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $project->status->StatusName == 'Completed' ? 'success' : ($project->status->StatusName == 'On Going' ? 'primary' : 'warning') }}">
                                                            {{ $project->status->StatusName }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $project->StartDate->format('M d, Y') }}</td>
                                                    <td>{{ $project->EndDate->format('M d, Y') }}</td>
                                                    <td>
                                                        <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-info">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No projects associated with this client.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
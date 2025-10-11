@extends('layouts.app')

@section('title', 'Project Details - ' . $project->ProjectName)
@section('page-title', 'Project Details')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="position: relative;">
                    <h3 class="card-title mb-0" style="display: inline-block;">
                        <i class="fas fa-project-diagram mr-2"></i>
                        Project: {{ $project->ProjectName }}
                    </h3>
                    <div style="position: absolute; top: 50%; right: 20px; transform: translateY(-50%);">
                        <a href="{{ route('projects.manage-employees', $project) }}" class="btn btn-primary me-2" 
                           style="background-color: #52b788 !important; border: 2px solid #52b788 !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                            <i class="fas fa-users"></i> Manage Employees
                        </a>
                        <a href="{{ route('projects.edit', $project) }}" class="btn btn-warning me-2"
                           style="background-color: #ffc107 !important; border: 2px solid #ffc107 !important; color: #212529 !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                            <i class="fas fa-edit"></i> Edit Project
                        </a>
                        <a href="{{ route('ProdHead.projects') }}" class="btn btn-secondary"
                           style="background-color: #6c757d !important; border: 2px solid #6c757d !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                            <i class="fas fa-arrow-left"></i> Back to Projects
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Project Information -->
                        <div class="col-md-8">
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
                                    <td><strong>Description:</strong></td>
                                    <td>{{ $project->ProjectDescription ?? 'No description provided' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @php
                                            $statusClass = $project->status->StatusName == 'Completed' ? 'success' :
                                                ($project->status->StatusName == 'On Going' ? 'primary' :
                                                    ($project->status->StatusName == 'Under Warranty' ? 'warning' :
                                                        ($project->status->StatusName == 'Upcoming' ? 'info' :
                                                            ($project->status->StatusName == 'On Hold' ? 'secondary' : 'info'))));
                                        @endphp
                                        <span class="badge badge-{{ $statusClass }}">{{ $project->status->StatusName }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Start Date:</strong></td>
                                    <td>{{ $project->StartDate->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>End Date:</strong></td>
                                    <td>{{ $project->EndDate->format('M d, Y') }}</td>
                                </tr>
                                @if($project->WarrantyEndDate)
                                    <tr>
                                        <td><strong>Warranty End Date:</strong></td>
                                        <td>{{ $project->WarrantyEndDate->format('M d, Y') }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>

                        <!-- Client Information -->
                        <div class="col-md-4">
                            <h5>Client Information</h5>
                            @if($project->client)
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $project->client->ClientName }}</h6>
                                        @if($project->client->ContactPerson)
                                            <p class="card-text"><strong>Contact:</strong> {{ $project->client->ContactPerson }}</p>
                                        @endif
                                        @if($project->client->ContactNumber)
                                            <p class="card-text"><strong>Phone:</strong> {{ $project->client->ContactNumber }}</p>
                                        @endif
                                        @if($project->client->Email)
                                            <p class="card-text"><strong>Email:</strong> {{ $project->client->Email }}</p>
                                        @endif
                                        @if($project->client->full_address)
                                            <p class="card-text"><strong>Address:</strong> {{ $project->client->full_address }}</p>
                                        @endif
                                        <a href="{{ route('clients.show', $project->client) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View Client Details
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    No client assigned to this project.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Project Address -->
                    @if($project->full_address)
                        <div class="row mt-3">
                            <div class="col-12">
                                <h5>Project Location</h5>
                                <p class="text-muted">{{ $project->full_address }}</p>
                            </div>
                        </div>
                    @endif



                    <!-- Project Actions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Project Actions</h5>
                            <div class="btn-group" role="group">
                                @if($project->status->StatusName != 'On Hold')
                                    <form action="{{ route('projects.onHold', $project) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-warning" 
                                                onclick="return confirm('Are you sure you want to put this project on hold?')">
                                            <i class="fas fa-pause"></i> Put on Hold
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('projects.reactivate', $project) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success" 
                                                onclick="return confirm('Are you sure you want to reactivate this project?')">
                                            <i class="fas fa-play"></i> Reactivate
                                        </button>
                                    </form>
                                @endif
                                
                                <form action="{{ route('projects.destroy', $project) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this project? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Delete Project
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



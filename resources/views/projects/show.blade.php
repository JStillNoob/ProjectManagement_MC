@extends('layouts.app')

@section('title', 'Project Details')
@section('page-title', 'Project Details')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-project-diagram mr-2"></i>
                        {{ $project->ProjectName }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('projects.edit', $project) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit mr-1"></i>
                            Edit Project
                        </a>
                        <a href="{{ route('ProdHead.projects') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Back to Projects
                        </a>
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
                                            ($project->status->StatusName == 'Upcoming' ? 'info' : 
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
                                                    <span class="info-box-number">{{ $project->StartDate->format('M d, Y') }}</span>
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
                                                    <span class="info-box-number">{{ $project->EndDate->format('M d, Y') }}</span>
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
                                                    <span class="info-box-number">{{ $project->StartDate->diffInDays($project->EndDate) }} days</span>
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
@endsection

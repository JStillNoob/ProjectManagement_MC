@extends('layouts.app')

@section('title', 'Projects Management')
@section('page-title', 'Projects Management')

@section('content')

    <!-- Projects Tabs -->
    <div class="row">
        <div class="col-12">
            <div class="card  mt-3">
                <div class="card-header p-0 pt-1">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-project-diagram mr-2"></i>
                            Project Status
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                data-target="#createProjectModal"
                                style="background-color: #52b788 !important; border: 2px solid #52b788 !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                                <i class="fas fa-plus mr-1"></i>
                                New Project
                            </button>
                        </div>
                    </div>
                    <ul class="nav nav-tabs" id="projectTabs" role="tablist">
                        @foreach($statuses as $index => $status)
                            @php
                                $projectCount = count($projectsByStatus[$status->StatusName] ?? []);
                                $tabId = strtolower(str_replace(' ', '', $status->StatusName));
                                $badgeClass = $status->StatusName == 'Completed' ? 'success' :
                                    ($status->StatusName == 'On Going' ? 'primary' :
                                        ($status->StatusName == 'Under Warranty' ? 'warning' :
                                            ($status->StatusName == 'Upcoming' ? 'info' :
                                                ($status->StatusName == 'On Hold' ? 'secondary' : 'info'))));
                            @endphp
                            <li class="nav-item">
                                <a class="nav-link {{ $index === 0 ? 'active' : '' }}" id="{{ $tabId }}-tab" data-toggle="tab"
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
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

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
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Project Name</th>
                                                    <th>Client</th>
                                                    <th>Start Date</th>
                                                    <th>End Date</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($projects as $project)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $project->ProjectName }}</strong>
                                                            @if($project->ProjectDescription)
                                                                <br><small
                                                                    class="text-muted">{{ Str::limit($project->ProjectDescription, 50) }}</small>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($project->client)
                                                                {{ $project->client->ClientName }}
                                                            @elseif($project->Client)
                                                                {{ $project->Client }}
                                                            @else
                                                                No Client
                                                            @endif
                                                        </td>
                                                        <td>{{ $project->StartDate->format('M d, Y') }}</td>
                                                        <td>{{ $project->EndDate->format('M d, Y') }}</td>
                                                        <td>
                                                            @php
                                                                $statusClass = $project->status->StatusName == 'Completed' ? 'success' :
                                                                    ($project->status->StatusName == 'On Going' ? 'primary' :
                                                                        ($project->status->StatusName == 'Under Warranty' ? 'warning' :
                                                                            ($project->status->StatusName == 'Upcoming' ? 'info' :
                                                                                ($project->status->StatusName == 'On Hold' ? 'secondary' : 'info'))));
                                                            @endphp
                                                            <span
                                                                class="badge badge-{{ $statusClass }}">{{ $project->status->StatusName }}</span>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                <a href="{{ route('projects.show', $project) }}"
                                                                    class="btn btn-info btn-sm">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <a href="{{ route('projects.edit', $project) }}"
                                                                    class="btn btn-warning btn-sm">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                @if($project->status->StatusName != 'On Hold')
                                                                    <form action="{{ route('projects.onHold', $project) }}" method="POST"
                                                                        style="display: inline-block;"
                                                                        onsubmit="return confirm('Are you sure you want to put this project on hold?')">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="submit" class="btn btn-secondary btn-sm"
                                                                            title="Put on Hold">
                                                                            <i class="fas fa-pause"></i>
                                                                        </button>
                                                                    </form>
                                                                @else
                                                                    <form action="{{ route('projects.reactivate', $project) }}"
                                                                        method="POST" style="display: inline-block;"
                                                                        onsubmit="return confirm('Are you sure you want to reactivate this project?')">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="submit" class="btn btn-success btn-sm"
                                                                            title="Reactivate Project">
                                                                            <i class="fas fa-play"></i>
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
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

    <!-- Create Project Modal -->
    <div class="modal fade" id="createProjectModal" tabindex="-1" role="dialog" aria-labelledby="createProjectModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createProjectModalLabel">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Create New Project
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('projects.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ProjectName">Project Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ProjectName') is-invalid @enderror"
                                        id="ProjectName" name="ProjectName" value="{{ old('ProjectName') }}" required>
                                    @error('ProjectName')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ClientID">Client</label>
                                    <select class="form-control @error('ClientID') is-invalid @enderror" id="ClientID" name="ClientID">
                                        <option value="">Select a client...</option>
                                        @foreach(\App\Models\Client::all() as $client)
                                            <option value="{{ $client->ClientID }}" {{ old('ClientID') == $client->ClientID ? 'selected' : '' }}>
                                                {{ $client->ClientName }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('ClientID')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="StartDate">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('StartDate') is-invalid @enderror"
                                        id="StartDate" name="StartDate" value="{{ old('StartDate') }}" required>
                                    @error('StartDate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="EndDate">End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('EndDate') is-invalid @enderror"
                                        id="EndDate" name="EndDate" value="{{ old('EndDate') }}" required>
                                    @error('EndDate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="WarrantyEndDate">Warranty End Date</label>
                                    <input type="date" class="form-control @error('WarrantyEndDate') is-invalid @enderror"
                                        id="WarrantyEndDate" name="WarrantyEndDate" value="{{ old('WarrantyEndDate') }}">
                                    @error('WarrantyEndDate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ProjectDescription">Description</label>
                            <textarea class="form-control @error('ProjectDescription') is-invalid @enderror"
                                id="ProjectDescription" name="ProjectDescription" rows="3"
                                placeholder="Enter project description...">{{ old('ProjectDescription') }}</textarea>
                            @error('ProjectDescription')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="StreetAddress">Street Address</label>
                                    <input type="text" class="form-control @error('StreetAddress') is-invalid @enderror"
                                        id="StreetAddress" name="StreetAddress" value="{{ old('StreetAddress') }}">
                                    @error('StreetAddress')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Barangay">Barangay</label>
                                    <input type="text" class="form-control @error('Barangay') is-invalid @enderror"
                                        id="Barangay" name="Barangay" value="{{ old('Barangay') }}">
                                    @error('Barangay')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="City">City</label>
                                    <input type="text" class="form-control @error('City') is-invalid @enderror" id="City"
                                        name="City" value="{{ old('City') }}">
                                    @error('City')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="StateProvince">State/Province</label>
                                    <input type="text" class="form-control @error('StateProvince') is-invalid @enderror"
                                        id="StateProvince" name="StateProvince" value="{{ old('StateProvince') }}">
                                    @error('StateProvince')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ZipCode">Zip Code</label>
                                    <input type="text" class="form-control @error('ZipCode') is-invalid @enderror"
                                        id="ZipCode" name="ZipCode" value="{{ old('ZipCode') }}">
                                    @error('ZipCode')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>
                            Create Project
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Initialize Bootstrap tabs
            $('#projectTabs a').on('click', function (e) {
                e.preventDefault();
                $(this).tab('show');
            });

            // Form validation
            $('#createProjectModal form').on('submit', function (e) {
                const startDate = new Date($('#StartDate').val());
                const endDate = new Date($('#EndDate').val());

                if (endDate <= startDate) {
                    e.preventDefault();
                    alert('End date must be after start date.');
                    return false;
                }
            });
        });
    </script>
@endpush




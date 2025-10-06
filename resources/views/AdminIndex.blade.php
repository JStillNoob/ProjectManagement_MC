@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')

    <div class="row">
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ \App\Models\Project::count() }}</h3>
                    <p>Total Projects</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <a href="{{ route('ProdHead.projects') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ \App\Models\Client::count() }}</h3>
                    <p>Total Clients</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person"></i>
                </div>
                <a href="{{ route('clients.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ \App\Models\Employee::active()->count() }}</h3>
                    <p>Active Employees</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="{{ route('admin.employees.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ \App\Models\User::active()->count() }}</h3>
                    <p>System Users</p>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
                <a href="{{ route('users.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Projects Overview</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Project Name</th>
                                    <th>Client</th>
                                    <th>Status</th>
                                    <th>Due Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(\App\Models\Project::with(['client', 'status'])->latest()->take(5)->get() as $project)
                                    <tr>
                                        <td>{{ $project->ProjectName }}</td>
                                        <td>{{ $project->client ? $project->client->ClientName : 'No Client' }}</td>
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
                                        <td>{{ $project->EndDate->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No projects found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('projects.create') }}" class="btn btn-primary btn-block">
                            <i class="fas fa-plus"></i> New Project
                        </a>
                        <a href="{{ route('clients.create') }}" class="btn btn-success btn-block">
                            <i class="fas fa-user-plus"></i> Add New Client
                        </a>
                        <a href="{{ route('clients.index') }}" class="btn btn-info btn-block">
                            <i class="fas fa-users"></i> Manage Clients
                        </a>
                        <a href="{{ route('admin.employees.index') }}" class="btn btn-warning btn-block">
                            <i class="fas fa-user-cog"></i> Manage Employees
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-users-cog"></i> Manage Users
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Clients</h3>
                </div>
                <div class="card-body">
                    @forelse(\App\Models\Client::latest()->take(3)->get() as $client)
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0">{{ $client->ClientName }}</h6>
                                <small class="text-muted">{{ $client->created_at->diffForHumans() }}</small>
                            </div>
                            <div>
                                <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">No clients found.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
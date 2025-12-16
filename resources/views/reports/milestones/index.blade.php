@extends('layouts.app')

@section('title', 'Milestone Reports')
@section('page-title', 'Milestone Reports')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-tasks mr-2"></i>
                            Completed Milestones Report
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <!-- Filter Section -->
                        <div class="row mx-3 my-3">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label for="project_id" class="small text-muted mb-1">Filter by Project</label>
                                    <form method="GET" action="{{ route('reports.milestones.index') }}" class="d-flex align-items-end">
                                        <select name="project_id" id="project_id" class="form-control form-control-sm mr-2">
                                            <option value="">All Projects</option>
                                            @foreach($projects as $project)
                                                <option value="{{ $project->ProjectID }}" 
                                                    {{ request('project_id') == $project->ProjectID ? 'selected' : '' }}>
                                                    {{ $project->ProjectName }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm text-white mr-2"
                                            style="background-color: #87A96B !important; border: 2px solid #87A96B !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                                            <i class="fas fa-filter mr-1"></i> Filter
                                        </button>
                                        <a href="{{ route('reports.milestones.index') }}" class="btn btn-outline-secondary btn-sm"
                                            style="background-color: #6c757d !important; border: 2px solid #6c757d !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                                            <i class="fas fa-times mr-1"></i> Clear
                                        </a>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Milestones Table -->
                        @if($milestones->count() > 0)
                            <div class="table-responsive">
                                <table id="milestonesTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Milestone Name</th>
                                            <th>Project</th>
                                            <th>Completion Date</th>
                                            <th>Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($milestones as $milestone)
                                            <tr>
                                                <td>
                                                    <strong>{{ $milestone->milestone_name }}</strong>
                                                    @if($milestone->description)
                                                        <br><small class="text-muted">{{ Str::limit($milestone->description, 50) }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $milestone->project->ProjectName ?? 'N/A' }}</td>
                                                <td>
                                                    @if($milestone->actual_date)
                                                        {{ $milestone->actual_date->format('M d, Y') }}
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge badge-success">Completed</span>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('reports.milestones.show', $milestone) }}" 
                                                       class="btn btn-sm text-white"
                                                       style="background-color: #007bff !important; border: 2px solid #007bff !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                                                        <i class="fas fa-eye mr-1"></i> View Report
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="mx-3 my-3">
                                {{ $milestones->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No completed milestones found.</p>
                                @if(request('project_id'))
                                    <a href="{{ route('reports.milestones.index') }}" class="btn btn-secondary btn-sm"
                                        style="background-color: #6c757d !important; border: 2px solid #6c757d !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                                        <i class="fas fa-times mr-1"></i> Clear Filter
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @push('styles')
        <style>
            /* DataTables Borderline Styling - Only horizontal borders between rows */
            #milestonesTable {
                border: none !important;
            }

            #milestonesTable thead th {
                border: none !important;
                border-bottom: 1px solid #dee2e6 !important;
            }

            #milestonesTable tbody td {
                border: none !important;
                border-bottom: 1px solid #dee2e6 !important;
            }

            #milestonesTable tbody tr:last-child td {
                border-bottom: none !important;
            }

            /* Center Actions column */
            #milestonesTable thead th:last-child,
            #milestonesTable tbody td:last-child {
                text-align: center !important;
            }

            /* Filter section styling */
            #project_id {
                border: 1px solid #ced4da;
                border-radius: 4px;
            }

            #project_id:focus {
                border-color: #87A96B;
                box-shadow: 0 0 0 0.2rem rgba(135, 169, 107, 0.25);
            }
        </style>
        @endpush

        @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize DataTables if table exists
                @if($milestones->count() > 0)
                var table = $('#milestonesTable').DataTable({
                    "responsive": true,
                    "lengthChange": true,
                    "autoWidth": false,
                    "pageLength": 10,
                    "order": [[2, 'desc']], // Sort by completion date descending
                    "columnDefs": [
                        { "orderable": false, "targets": [4] }, // Disable sorting on Actions
                        { "className": "text-center", "targets": [4] } // Center Actions column
                    ],
                    "language": {
                        "search": "Search:",
                        "lengthMenu": "_MENU_",
                        "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                        "infoEmpty": "Showing 0 to 0 of 0 entries",
                        "infoFiltered": "(filtered from _MAX_ total entries)",
                        "paginate": {
                            "first": "First",
                            "last": "Last",
                            "next": "Next",
                            "previous": "Previous"
                        }
                    }
                });
                @endif
            });
        </script>
        @endpush
    </div>
@endsection


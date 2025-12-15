@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h2>Material Consumption Report</h2>
                <p class="text-muted">Material usage analysis by project and milestone</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('reports.inventory.consumption', array_merge(request()->all(), ['export' => 'pdf'])) }}"
                    class="btn btn-danger" target="_blank">
                    <i class="bi bi-file-pdf"></i> Export PDF
                </a>
                <a href="{{ route('reports.inventory.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('reports.inventory.consumption') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Date From</label>
                            <input type="date" name="date_from" class="form-control"
                                value="{{ request('date_from', now()->subDays(30)->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date To</label>
                            <input type="date" name="date_to" class="form-control"
                                value="{{ request('date_to', now()->format('Y-m-d')) }}">
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
                                <i class="bi bi-funnel"></i> Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6>Total Materials Consumed</h6>
                        <h3>{{ number_format($totalMaterialsConsumed, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6>Total Value</h6>
                        <h3>₱{{ number_format($totalValue, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6>Projects Served</h6>
                        <h3>{{ $consumption->pluck('project_id')->unique()->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Consumption Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Project</th>
                                <th>Milestone</th>
                                <th>Material</th>
                                <th class="text-end">Quantity</th>
                                <th>Unit</th>
                                <th class="text-end">Est. Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($consumption as $item)
                                <tr>
                                    <td>{{ $item->issuance_date ? \Carbon\Carbon::parse($item->issuance_date)->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td>{{ $item->project_name ?? 'N/A' }}</td>
                                    <td>{{ $item->milestone_name ?? 'N/A' }}</td>
                                    <td><strong>{{ $item->item_name }}</strong></td>
                                    <td class="text-end">{{ number_format($item->total_quantity, 2) }}</td>
                                    <td>{{ $item->unit }}</td>
                                    <td class="text-end">
                                        @if($item->estimated_value)
                                            ₱{{ number_format($item->estimated_value, 2) }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @if($consumption->count() == 0)
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No consumption data found for the selected period
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                        @if($consumption->count() > 0)
                            <tfoot>
                                <tr class="table-secondary">
                                    <th colspan="4" class="text-end">Total:</th>
                                    <th class="text-end">{{ number_format($totalMaterialsConsumed, 2) }}</th>
                                    <th></th>
                                    <th class="text-end">₱{{ number_format($totalValue, 2) }}</th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
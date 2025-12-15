@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h2>Equipment Utilization Report</h2>
                <p class="text-muted">Equipment usage and assignment statistics</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('reports.inventory.equipment-utilization', array_merge(request()->all(), ['export' => 'pdf'])) }}"
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
                <form method="GET" action="{{ route('reports.inventory.equipment-utilization') }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Date From</label>
                            <input type="date" name="date_from" class="form-control"
                                value="{{ request('date_from', now()->subDays(30)->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date To</label>
                            <input type="date" name="date_to" class="form-control"
                                value="{{ request('date_to', now()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-secondary w-100">
                                <i class="bi bi-funnel"></i> Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Utilization Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Equipment</th>
                                <th class="text-center">Total Assignments</th>
                                <th class="text-center">Currently In Use</th>
                                <th class="text-center">Total Days Used</th>
                                <th class="text-center">Avg Days/Assignment</th>
                                <th class="text-center">Projects Served</th>
                                <th class="text-end">Utilization Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($utilization as $item)
                                @php
                                    $utilizationRate = $item->available_quantity > 0
                                        ? ($item->committed_quantity / ($item->committed_quantity + $item->available_quantity)) * 100
                                        : 0;
                                @endphp
                                <tr>
                                    <td><strong>{{ $item->item_name }}</strong></td>
                                    <td class="text-center">{{ $item->total_assignments }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $item->currently_in_use > 0 ? 'warning' : 'success' }}">
                                            {{ $item->currently_in_use }}
                                        </span>
                                    </td>
                                    <td class="text-center">{{ $item->total_days_used }}</td>
                                    <td class="text-center">{{ number_format($item->avg_days_per_assignment, 1) }}</td>
                                    <td class="text-center">{{ $item->projects_count }}</td>
                                    <td class="text-end">
                                        <div class="d-flex align-items-center justify-content-end">
                                            <div class="progress" style="width: 100px; height: 20px;">
                                                <div class="progress-bar bg-{{ $utilizationRate > 80 ? 'danger' : ($utilizationRate > 50 ? 'warning' : 'success') }}"
                                                    role="progressbar" style="width: {{ $utilizationRate }}%"
                                                    aria-valuenow="{{ $utilizationRate }}" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                </div>
                                            </div>
                                            <span class="ms-2">{{ number_format($utilizationRate, 1) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @if($utilization->count() == 0)
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No equipment utilization data found
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
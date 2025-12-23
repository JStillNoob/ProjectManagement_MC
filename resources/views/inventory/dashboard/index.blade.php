@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h2>Inventory Dashboard</h2>
                <p class="text-muted">Real-time inventory overview and analytics</p>
            </div>
            <div class="col-auto">
                <div class="btn-group">
                    <a href="{{ route('inventory.dashboard') }}" class="btn btn-outline-primary active">Overview</a>
                    <a href="{{ route('inventory.dashboard.materials') }}" class="btn btn-outline-primary">Materials</a>
                    <a href="{{ route('inventory.dashboard.equipment') }}" class="btn btn-outline-primary">Equipment</a>
                    <a href="{{ route('inventory.dashboard.alerts') }}" class="btn btn-outline-primary">Alerts</a>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Total Items</h6>
                                <h2 class="mb-0">{{ $totalItems }}</h2>
                            </div>
                            <i class="bi bi-box-seam" style="font-size: 2.5rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Available</h6>
                                <h2 class="mb-0">{{ $availableItems }}</h2>
                            </div>
                            <i class="bi bi-check-circle" style="font-size: 2.5rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">In Use</h6>
                                <h2 class="mb-0">{{ $committedItems }}</h2>
                            </div>
                            <i class="bi bi-arrow-repeat" style="font-size: 2.5rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Low Stock</h6>
                                <h2 class="mb-0">{{ $lowStockCount }}</h2>
                            </div>
                            <i class="bi bi-exclamation-triangle" style="font-size: 2.5rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Material Consumption Chart -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Material Consumption (Last 30 Days)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="consumptionChart" height="80"></canvas>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($recentIssuances as $issuance)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                <i class="bi bi-box-arrow-right text-primary"></i>
                                                {{ $issuance->IssuanceNumber }}
                                            </h6>
                                            <p class="mb-1 small text-muted">
                                                {{ $issuance->project->ProjectName ?? 'N/A' }} -
                                                {{ $issuance->items->count() }} items issued
                                            </p>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted">{{ $issuance->IssuanceDate->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            @if($recentIssuances->count() == 0)
                                <p class="text-muted text-center py-3">No recent activity</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-4">
                <!-- Critical Alerts -->
                <div class="card mb-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Critical Alerts</h5>
                    </div>
                    <div class="card-body">
                        @if($criticalAlerts->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($criticalAlerts as $alert)
                                    @php
                                        $requiresInteger = $alert->Type === 'Equipment' || \App\Models\ResourceCatalog::unitRequiresInteger($alert->Unit);
                                    @endphp
                                    <div class="list-group-item px-0 py-2">
                                        <strong>{{ $alert->ItemName }}</strong>
                                        <br>
                                        <small class="text-danger">
                                            Available: 
                                            @if($requiresInteger)
                                                {{ number_format((int) $alert->AvailableQuantity, 0) }}
                                            @else
                                                {{ number_format($alert->AvailableQuantity, 2) }}
                                            @endif
                                            {{ $alert->Unit }}
                                            <br>
                                            Reorder Level: 
                                            @if($requiresInteger)
                                                {{ number_format((int) $alert->ReorderLevel, 0) }}
                                            @else
                                                {{ number_format($alert->ReorderLevel, 2) }}
                                            @endif
                                            {{ $alert->Unit }}
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                            <a href="{{ route('inventory.dashboard.alerts') }}"
                                class="btn btn-sm btn-outline-danger w-100 mt-2">
                                View All Alerts
                            </a>
                        @else
                            <p class="text-center text-muted mb-0">
                                <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                                <br>No critical alerts
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Equipment Status -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Equipment Status</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="equipmentStatusChart"></canvas>
                        <div class="mt-3">
                            <table class="table table-sm">
                                <tr>
                                    <td><span class="badge bg-success">Available</span></td>
                                    <td class="text-end">{{ $equipmentStats['available'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-warning">In Use</span></td>
                                    <td class="text-end">{{ $equipmentStats['in_use'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-danger">Damaged</span></td>
                                    <td class="text-end">{{ $equipmentStats['damaged'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-dark">Missing</span></td>
                                    <td class="text-end">{{ $equipmentStats['missing'] ?? 0 }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Incoming Purchase Orders -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Incoming POs</h5>
                    </div>
                    <div class="card-body">
                        @if($incomingPOs->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($incomingPOs as $po)
                                    <div class="list-group-item px-0 py-2">
                                        <div class="d-flex justify-content-between">
                                            <strong>PO #{{ $po->POID }}</strong>
                                            <span class="badge bg-primary">{{ $po->Status }}</span>
                                        </div>
                                        <small class="text-muted">
                                            Expected:
                                            {{ $po->ExpectedDeliveryDate ? $po->ExpectedDeliveryDate->format('M d, Y') : 'TBD' }}
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-center text-muted mb-0">No pending POs</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Material Consumption Chart
        const consumptionData = @json($consumptionData);
        const ctx1 = document.getElementById('consumptionChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: consumptionData.labels,
                datasets: [{
                    label: 'Quantity Issued',
                    data: consumptionData.data,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Equipment Status Pie Chart
        const equipmentStats = @json($equipmentStats);
        const ctx2 = document.getElementById('equipmentStatusChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Available', 'In Use', 'Damaged', 'Missing'],
                datasets: [{
                    data: [
                        equipmentStats.available || 0,
                        equipmentStats.in_use || 0,
                        equipmentStats.damaged || 0,
                        equipmentStats.missing || 0
                    ],
                    backgroundColor: [
                        'rgb(40, 167, 69)',
                        'rgb(255, 193, 7)',
                        'rgb(220, 53, 69)',
                        'rgb(52, 58, 64)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
@endsection
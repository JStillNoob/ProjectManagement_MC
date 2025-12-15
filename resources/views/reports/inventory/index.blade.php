@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h2>Inventory Reports</h2>
                <p class="text-muted">Generate comprehensive inventory reports</p>
            </div>
        </div>

        <div class="row">
            <!-- Stock Level Report -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <i class="bi bi-boxes" style="font-size: 2.5rem; color: #0d6efd;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title mb-0">Stock Level Report</h5>
                            </div>
                        </div>
                        <p class="card-text">View current stock levels, total quantities, and low stock items.</p>
                        <a href="{{ route('reports.inventory.stock-level') }}" class="btn btn-primary w-100">
                            <i class="bi bi-file-earmark-text"></i> Generate Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Consumption Report -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <i class="bi bi-graph-down" style="font-size: 2.5rem; color: #198754;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title mb-0">Consumption Report</h5>
                            </div>
                        </div>
                        <p class="card-text">Analyze material consumption trends by project, milestone, and date range.</p>
                        <a href="{{ route('reports.inventory.consumption') }}" class="btn btn-success w-100">
                            <i class="bi bi-file-earmark-bar-graph"></i> Generate Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Equipment Utilization Report -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <i class="bi bi-tools" style="font-size: 2.5rem; color: #ffc107;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title mb-0">Equipment Utilization</h5>
                            </div>
                        </div>
                        <p class="card-text">Track equipment usage, assignments, and utilization rates.</p>
                        <a href="{{ route('reports.inventory.equipment-utilization') }}" class="btn btn-warning w-100">
                            <i class="bi bi-file-earmark-check"></i> Generate Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Purchase Order Summary -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <i class="bi bi-receipt" style="font-size: 2.5rem; color: #6f42c1;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title mb-0">PO Summary Report</h5>
                            </div>
                        </div>
                        <p class="card-text">Summary of purchase orders by status, supplier, and value.</p>
                        <a href="{{ route('reports.inventory.po-summary') }}" class="btn btn-purple w-100"
                            style="background-color: #6f42c1; border-color: #6f42c1; color: white;">
                            <i class="bi bi-file-earmark-spreadsheet"></i> Generate Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Issuance History Report -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <i class="bi bi-clock-history" style="font-size: 2.5rem; color: #0dcaf0;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title mb-0">Issuance History</h5>
                            </div>
                        </div>
                        <p class="card-text">Complete history of all material and equipment issuances.</p>
                        <a href="{{ route('reports.inventory.issuance-history') }}" class="btn btn-info w-100">
                            <i class="bi bi-file-earmark-arrow-down"></i> Generate Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Damage Report -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <i class="bi bi-exclamation-triangle" style="font-size: 2.5rem; color: #dc3545;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title mb-0">Damage/Loss Report</h5>
                            </div>
                        </div>
                        <p class="card-text">Track equipment incidents, damages, losses, and associated costs.</p>
                        <a href="{{ route('reports.inventory.damage-report') }}" class="btn btn-danger w-100">
                            <i class="bi bi-file-earmark-x"></i> Generate Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
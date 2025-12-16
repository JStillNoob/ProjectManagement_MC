@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h2>Inventory Reports</h2>
                <p class="text-muted">Generate comprehensive inventory reports in PDF format</p>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header" style="background-color: #87A96B;">
                <h5 class="mb-0 text-white">
                    <i class="fas fa-file-pdf mr-2"></i>Available PDF Reports
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 25%;">Report Name</th>
                                <th style="width: 50%;">Description</th>
                                <th style="width: 20%;" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>
                                    <i class="fas fa-boxes mr-2" style="color: #0d6efd;"></i>
                                    <strong>Stock Level Report</strong>
                                </td>
                                <td>View current stock levels, total quantities, and low stock items for all inventory items.</td>
                                <td class="text-center">
                                    <a href="{{ route('reports.inventory.stock-level', ['export' => 'pdf']) }}" 
                                       class="btn btn-sm btn-primary" target="_blank">
                                        <i class="fas fa-file-pdf mr-1"></i> Download PDF
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>
                                    <i class="fas fa-chart-line mr-2" style="color: #198754;"></i>
                                    <strong>Consumption Report</strong>
                                </td>
                                <td>Analyze material consumption trends by project, milestone, and date range.</td>
                                <td class="text-center">
                                    <a href="{{ route('reports.inventory.consumption') }}" 
                                       class="btn btn-sm btn-success">
                                        <i class="fas fa-file-pdf mr-1"></i> Generate PDF
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>
                                    <i class="fas fa-tools mr-2" style="color: #ffc107;"></i>
                                    <strong>Equipment Utilization Report</strong>
                                </td>
                                <td>Track equipment usage, assignments, and utilization rates across projects.</td>
                                <td class="text-center">
                                    <a href="{{ route('reports.inventory.equipment-utilization') }}" 
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-file-pdf mr-1"></i> Generate PDF
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>
                                    <i class="fas fa-receipt mr-2" style="color: #6f42c1;"></i>
                                    <strong>Purchase Order Summary Report</strong>
                                </td>
                                <td>Summary of purchase orders by status, supplier, and value within a date range.</td>
                                <td class="text-center">
                                    <a href="{{ route('reports.inventory.po-summary') }}" 
                                       class="btn btn-sm" style="background-color: #6f42c1; border-color: #6f42c1; color: white;">
                                        <i class="fas fa-file-pdf mr-1"></i> Generate PDF
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>
                                    <i class="fas fa-history mr-2" style="color: #0dcaf0;"></i>
                                    <strong>Issuance History Report</strong>
                                </td>
                                <td>Complete history of all material and equipment issuances with detailed item information.</td>
                                <td class="text-center">
                                    <a href="{{ route('reports.inventory.issuance-history') }}" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-file-pdf mr-1"></i> Generate PDF
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>6</td>
                                <td>
                                    <i class="fas fa-exclamation-triangle mr-2" style="color: #dc3545;"></i>
                                    <strong>Damage/Loss Report</strong>
                                </td>
                                <td>Track equipment incidents, damages, losses, and associated costs with incident details.</td>
                                <td class="text-center">
                                    <a href="{{ route('reports.inventory.damage-report') }}" 
                                       class="btn btn-sm btn-danger">
                                        <i class="fas fa-file-pdf mr-1"></i> Generate PDF
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
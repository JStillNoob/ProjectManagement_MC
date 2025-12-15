@extends('layouts.app')

@section('title', 'Reports')
@section('page-title', 'Enterprise Reports')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header" style="background-color: #ffffff; border-bottom: 2px solid #87A96B;">
                        <h5 class="mb-0" style="color: #87A96B;"><i class="fas fa-chart-bar"></i> Report Generator</h5>
                    </div>
                    <div class="card-body">
                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="row">
                            <!-- Purchase Order Reports -->
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fas fa-file-invoice fa-3x" style="color: #87A96B;"></i>
                                        </div>
                                        <h5 class="card-title">Purchase Order Report</h5>
                                        <p class="card-text text-muted">Generate detailed purchase order reports with
                                            supplier and item information.</p>
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-sm"
                                                style="background-color: #87A96B; color: white;" data-bs-toggle="modal"
                                                data-bs-target="#poReportModal">
                                                <i class="fas fa-file-alt"></i> Generate Report
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Inventory Report -->
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fas fa-boxes fa-3x" style="color: #87A96B;"></i>
                                        </div>
                                        <h5 class="card-title">Inventory Report</h5>
                                        <p class="card-text text-muted">Complete inventory stock levels, item types, and
                                            availability.</p>
                                        <form action="{{ route('reports.inventory') }}" method="GET" class="mt-3">
                                            <div class="btn-group" role="group">
                                                <button type="submit" name="format" value="pdf" class="btn btn-sm"
                                                    style="background-color: #87A96B; color: white;">
                                                    <i class="fas fa-file-pdf"></i> PDF
                                                </button>
                                                <button type="submit" name="format" value="excel"
                                                    class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-file-excel"></i> Excel
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Project Summary -->
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fas fa-project-diagram fa-3x text-muted"></i>
                                        </div>
                                        <h5 class="card-title">Project Summary</h5>
                                        <p class="card-text text-muted">Coming soon - Project progress and milestone
                                            reports.</p>
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-sm btn-secondary" disabled>
                                                <i class="fas fa-lock"></i> Coming Soon
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Employee Attendance -->
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fas fa-user-clock fa-3x text-muted"></i>
                                        </div>
                                        <h5 class="card-title">Employee Attendance</h5>
                                        <p class="card-text text-muted">Coming soon - Employee attendance tracking reports.
                                        </p>
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-sm btn-secondary" disabled>
                                                <i class="fas fa-lock"></i> Coming Soon
                                            </button>
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

    <!-- Purchase Order Report Modal -->
    <div class="modal fade" id="poReportModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #87A96B; color: white;">
                    <h5 class="modal-title"><i class="fas fa-file-invoice"></i> Generate Purchase Order Report</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('reports.purchase-order', ['id' => 1]) }}" method="GET" id="poReportForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="poid" class="form-label">Purchase Order ID</label>
                            <input type="number" class="form-control" id="poid" name="id" required min="1"
                                placeholder="Enter POID">
                        </div>
                        <div class="mb-3">
                            <label for="format" class="form-label">Export Format</label>
                            <select class="form-select" name="format">
                                <option value="pdf" selected>📄 PDF Document</option>
                                <option value="excel">📊 Excel Spreadsheet</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn" style="background-color: #87A96B; color: white;">
                            <i class="fas fa-download"></i> Generate Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('poReportForm').addEventListener('submit', function (e) {
            const poid = document.getElementById('poid').value;
            this.action = this.action.replace('/1', '/' + poid);
        });
    </script>
@endsection
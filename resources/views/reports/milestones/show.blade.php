@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <!-- Card Header -->
                    <div class="card-header" style="background-color: #87A96B;">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="card-title mb-0" style="color: white; font-size: 1.25rem;">
                                    <i class="fas fa-tasks me-2"></i>
                                    Milestone Report: {{ $milestone->milestone_name }}
                                </h3>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('reports.milestones.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Back to List
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Info Section -->
                        <div class="row mb-4">
                            <!-- Project Information -->
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                                    <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.85rem;">
                                        <i class="fas fa-building me-2"></i>Project Information
                                    </h6>
                                    <h5 class="fw-bold mb-2" style="font-size: 1.1rem;">{{ $milestone->project->ProjectName ?? 'N/A' }}</h5>
                                    @if($milestone->project && $milestone->project->client)
                                        <p class="mb-1 text-muted" style="font-size: 0.95rem;">
                                            <i class="fas fa-user-tie me-2"></i>{{ $milestone->project->client->ClientName }}
                                        </p>
                                    @endif
                                    @if($milestone->description)
                                        <p class="mb-0 text-muted" style="font-size: 0.85rem; line-height: 1.4;">
                                            {{ Str::limit($milestone->description, 100) }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Milestone Details -->
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                                    <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.85rem;">
                                        <i class="fas fa-info-circle me-2"></i>Milestone Details
                                    </h6>
                                    <table class="table table-sm table-borderless mb-0" style="font-size: 0.95rem;">
                                        <tr>
                                            <td class="text-muted ps-0">Milestone Name</td>
                                            <td class="fw-semibold">{{ $milestone->milestone_name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0">Status</td>
                                            <td>
                                                <span class="badge" style="background-color: #87A96B;">Completed</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0">Estimated Days</td>
                                            <td class="fw-semibold">{{ $milestone->EstimatedDays ?? 'N/A' }} days</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Completion Information -->
                            <div class="col-md-4">
                                <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                                    <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.85rem;">
                                        <i class="fas fa-calendar-check me-2"></i>Completion Information
                                    </h6>
                                    <table class="table table-sm table-borderless mb-0" style="font-size: 0.95rem;">
                                        <tr>
                                            <td class="text-muted ps-0">Target Date</td>
                                            <td class="fw-semibold">
                                                @if($milestone->target_date)
                                                    {{ $milestone->target_date->format('M d, Y') }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0">Completion Date</td>
                                            <td class="fw-semibold">
                                                @if($milestone->actual_date)
                                                    {{ $milestone->actual_date->format('M d, Y') }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0">Total Items</td>
                                            <td class="fw-semibold">{{ count($comparisonData) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Items Comparison Table -->
                        <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.75rem;">
                            <i class="fas fa-balance-scale me-2"></i>Required Items vs Actual Items Used
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Type</th>
                                        <th class="text-center">Required Qty</th>
                                        <th class="text-center">Actual Qty</th>
                                        <th class="text-center">Variance</th>
                                        <th class="text-center">Percentage</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($comparisonData as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-2"
                                                        style="width: 32px; height: 32px; background-color: {{ $item['item_type'] == 'Equipment' ? 'rgba(23, 162, 184, 0.15)' : 'rgba(135, 169, 107, 0.15)' }};">
                                                        <i class="fas {{ $item['item_type'] == 'Equipment' ? 'fa-tools' : 'fa-cube' }}"
                                                            style="color: {{ $item['item_type'] == 'Equipment' ? '#17a2b8' : '#87A96B' }}; font-size: 0.8rem;"></i>
                                                    </div>
                                                    <span><strong>{{ $item['item_name'] }}</strong></span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge" style="background-color: {{ $item['item_type'] == 'Equipment' ? '#17a2b8' : '#87A96B' }};">
                                                    {{ $item['item_type'] }}
                                                </span>
                                            </td>
                                            <td class="text-center">{{ number_format($item['required_qty'], 2) }} {{ $item['unit'] }}</td>
                                            <td class="text-center">{{ number_format($item['actual_qty'], 2) }} {{ $item['unit'] }}</td>
                                            <td class="text-center">
                                                @if($item['variance'] > 0)
                                                    <span class="text-danger">+{{ number_format($item['variance'], 2) }} {{ $item['unit'] }}</span>
                                                @elseif($item['variance'] < 0)
                                                    <span class="text-warning">{{ number_format($item['variance'], 2) }} {{ $item['unit'] }}</span>
                                                @else
                                                    <span class="text-muted">{{ number_format($item['variance'], 2) }} {{ $item['unit'] }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ number_format($item['percentage'], 2) }}%</td>
                                            <td class="text-center">
                                                @if($item['status_color'] == 'success')
                                                    <span class="badge" style="background-color: #87A96B;">{{ $item['status_text'] }}</span>
                                                @elseif($item['status_color'] == 'warning')
                                                    <span class="badge bg-warning text-dark">{{ $item['status_text'] }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ $item['status_text'] }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="fas fa-box-open fa-2x mb-2"></i>
                                                <p class="mb-0">No required items found for this milestone.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if(count($comparisonData) > 0)
                                    <tfoot>
                                        <tr class="fw-bold">
                                            <td colspan="2">Total</td>
                                            <td class="text-center">{{ number_format(collect($comparisonData)->sum('required_qty'), 2) }}</td>
                                            <td class="text-center">{{ number_format(collect($comparisonData)->sum('actual_qty'), 2) }}</td>
                                            <td class="text-center">{{ number_format(collect($comparisonData)->sum('variance'), 2) }}</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- Card Footer with Buttons -->
                    <div class="card-footer bg-white py-3">
                        <div class="text-right">
                            <a href="{{ route('reports.milestones.pdf', $milestone) }}" 
                               class="btn btn-secondary mr-2"
                               style="background-color: #87A96B !important; border-color: #87A96B !important; color: #fff !important;"
                               target="_blank">
                                <i class="fas fa-file-pdf"></i> Print PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


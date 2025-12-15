@extends('layouts.app')

@section('title', 'Inventory Item Details')
@section('page-title', 'Inventory Item Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-box mr-2"></i>{{ $inventory->ItemName }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('inventory.edit', $inventory) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('inventory.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Item Name</th>
                                    <td><strong>{{ $inventory->ItemName }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td>
                                        @if($inventory->resourceCatalog)
                                            <span class="badge badge-{{ $inventory->resourceCatalog->Type == 'Materials' ? 'info' : 'primary' }}">
                                                {{ $inventory->resourceCatalog->Type ?? 'N/A' }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Unit</th>
                                    <td>{{ $inventory->Unit }}</td>
                                </tr>
                                <tr>
                                    <th>Total Quantity</th>
                                    <td><strong>{{ number_format($inventory->TotalQuantity, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Available Quantity</th>
                                    <td>
                                        <strong class="{{ $inventory->is_low_stock ? 'text-danger' : '' }}">
                                            {{ number_format($inventory->AvailableQuantity, 2) }}
                                        </strong>
                                        @if($inventory->is_low_stock)
                                            <span class="badge badge-warning ml-2">Low Stock</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($inventory->is_material && $inventory->MinimumStockLevel)
                                <tr>
                                    <th>Minimum Stock Level</th>
                                    <td>{{ number_format($inventory->MinimumStockLevel, 2) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge badge-{{ $inventory->Status == 'Active' ? 'success' : 'secondary' }}">
                                            {{ $inventory->Status }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Usage History -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <ul class="nav nav-tabs" id="usageTabs" role="tablist">
                                @if($inventory->is_material)
                                <li class="nav-item">
                                    <a class="nav-link active" id="materials-tab" data-toggle="tab" href="#materials" role="tab">
                                        Material Usage ({{ $inventory->milestoneMaterials->count() }})
                                    </a>
                                </li>
                                @endif
                                @if($inventory->is_equipment)
                                <li class="nav-item">
                                    <a class="nav-link {{ $inventory->is_material ? '' : 'active' }}" id="equipment-tab" data-toggle="tab" href="#equipment" role="tab">
                                        Equipment Assignments ({{ $inventory->milestoneEquipment->count() }})
                                    </a>
                                </li>
                                @endif
                            </ul>
                            <div class="tab-content mt-3" id="usageTabsContent">
                                @if($inventory->is_material)
                                <div class="tab-pane fade show active" id="materials" role="tabpanel">
                                    @if($inventory->milestoneMaterials->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Project</th>
                                                        <th>Milestone</th>
                                                        <th>Quantity Used</th>
                                                        <th>Date Used</th>
                                                        <th>Remarks</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($inventory->milestoneMaterials as $material)
                                                        <tr>
                                                            <td>{{ $material->milestone->project->ProjectName ?? 'N/A' }}</td>
                                                            <td>{{ $material->milestone->milestone_name }}</td>
                                                            <td>{{ number_format($material->QuantityUsed, 2) }} {{ $inventory->Unit }}</td>
                                                            <td>{{ $material->DateUsed->format('M d, Y') }}</td>
                                                            <td>{{ $material->Remarks ?? 'N/A' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted">No material usage recorded.</p>
                                    @endif
                                </div>
                                @endif
                                @if($inventory->is_equipment)
                                <div class="tab-pane fade {{ $inventory->is_material ? '' : 'show active' }}" id="equipment" role="tabpanel">
                                    @if($inventory->milestoneEquipment->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Project</th>
                                                        <th>Milestone</th>
                                                        <th>Quantity Assigned</th>
                                                        <th>Date Assigned</th>
                                                        <th>Date Returned</th>
                                                        <th>Status</th>
                                                        <th>Remarks</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($inventory->milestoneEquipment as $equipment)
                                                        <tr>
                                                            <td>{{ $equipment->milestone->project->ProjectName ?? 'N/A' }}</td>
                                                            <td>{{ $equipment->milestone->milestone_name }}</td>
                                                            <td>{{ number_format($equipment->QuantityAssigned, 2) }} {{ $inventory->Unit }}</td>
                                                            <td>{{ $equipment->DateAssigned->format('M d, Y') }}</td>
                                                            <td>{{ $equipment->DateReturned ? $equipment->DateReturned->format('M d, Y') : 'Not Returned' }}</td>
                                                            <td>
                                                                @php
                                                                    $statusClass = $equipment->Status == 'Returned' ? 'success' : 
                                                                                   ($equipment->Status == 'Damaged' ? 'danger' : 
                                                                                   ($equipment->Status == 'Missing' ? 'warning' : 'info'));
                                                                @endphp
                                                                <span class="badge badge-{{ $statusClass }}">{{ $equipment->Status }}</span>
                                                            </td>
                                                            <td>{{ $equipment->Remarks ?? 'N/A' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted">No equipment assignments recorded.</p>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


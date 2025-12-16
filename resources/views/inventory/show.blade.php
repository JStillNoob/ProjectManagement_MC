@extends('layouts.app')

@section('title', 'Inventory Item Details')
@section('page-title', 'Inventory Item Details')

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Item Summary Card -->
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <div class="profile-user-img img-fluid img-circle bg-{{ $inventory->resourceCatalog && $inventory->resourceCatalog->Type == 'Materials' ? 'info' : 'primary' }} d-flex align-items-center justify-content-center mx-auto" 
                         style="width: 100px; height: 100px; font-size: 40px; color: white;">
                        <i class="fas fa-{{ $inventory->resourceCatalog && $inventory->resourceCatalog->Type == 'Materials' ? 'box' : 'tools' }}"></i>
                    </div>
                </div>

                <h3 class="profile-username text-center mt-3">{{ $inventory->resourceCatalog->ItemName ?? 'N/A' }}</h3>
                <p class="text-muted text-center">
                    @if($inventory->resourceCatalog)
                        <span class="badge badge-{{ $inventory->resourceCatalog->Type == 'Materials' ? 'info' : 'primary' }}">
                            {{ $inventory->resourceCatalog->Type }}
                        </span>
                    @else
                        <span class="badge badge-secondary">N/A</span>
                    @endif
                </p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Total Quantity</b> 
                        <a class="float-right">{{ number_format($inventory->TotalQuantity, 2) }} {{ $inventory->resourceCatalog->Unit ?? 'N/A' }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Available Quantity</b> 
                        <a class="float-right {{ $inventory->is_low_stock ? 'text-danger' : '' }}">
                            {{ number_format($inventory->AvailableQuantity, 2) }} {{ $inventory->resourceCatalog->Unit ?? 'N/A' }}
                            @if($inventory->is_low_stock)
                                <span class="badge badge-warning ml-2">Low Stock</span>
                            @endif
                        </a>
                    </li>
                    @if($inventory->is_material && $inventory->MinimumStockLevel)
                    <li class="list-group-item">
                        <b>Minimum Stock Level</b> 
                        <a class="float-right">{{ number_format($inventory->MinimumStockLevel, 2) }} {{ $inventory->resourceCatalog->Unit ?? 'N/A' }}</a>
                    </li>
                    @endif
                    <li class="list-group-item">
                        <b>Status</b> 
                        <a class="float-right">
                            <span class="badge badge-{{ $inventory->Status == 'Active' ? 'success' : 'secondary' }}">
                                {{ $inventory->Status }}
                            </span>
                        </a>
                    </li>
                </ul>

                <a href="{{ route('inventory.index') }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-arrow-left mr-1"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Item Details -->
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link active" href="#details" data-toggle="tab">
                            <i class="fas fa-info-circle mr-1"></i>Item Details
                        </a>
                    </li>
                    @if($inventory->is_material)
                    <li class="nav-item">
                        <a class="nav-link" href="#materials" data-toggle="tab">
                            <i class="fas fa-list mr-1"></i>Material Usage ({{ $inventory->milestoneMaterials->count() }})
                        </a>
                    </li>
                    @endif
                    @if($inventory->is_equipment)
                    <li class="nav-item">
                        <a class="nav-link {{ $inventory->is_material ? '' : 'active' }}" href="#equipment" data-toggle="tab">
                            <i class="fas fa-tools mr-1"></i>Equipment Assignments ({{ $inventory->milestoneEquipment->count() }})
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Item Details Tab -->
                    <div class="active tab-pane" id="details">
                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-box mr-1"></i> Item Name</strong>
                                <p class="text-muted">{{ $inventory->resourceCatalog->ItemName ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-tag mr-1"></i> Type</strong>
                                <p class="text-muted">
                                    @if($inventory->resourceCatalog)
                                        <span class="badge badge-{{ $inventory->resourceCatalog->Type == 'Materials' ? 'info' : 'primary' }}">
                                            {{ $inventory->resourceCatalog->Type }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">N/A</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-ruler mr-1"></i> Unit</strong>
                                <p class="text-muted">{{ $inventory->resourceCatalog->Unit ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-cubes mr-1"></i> Total Quantity</strong>
                                <p class="text-muted"><strong>{{ number_format($inventory->TotalQuantity, 2) }}</strong></p>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-check-circle mr-1"></i> Available Quantity</strong>
                                <p class="text-muted">
                                    <strong class="{{ $inventory->is_low_stock ? 'text-danger' : '' }}">
                                        {{ number_format($inventory->AvailableQuantity, 2) }}
                                    </strong>
                                    @if($inventory->is_low_stock)
                                        <span class="badge badge-warning ml-2">Low Stock</span>
                                    @endif
                                </p>
                            </div>
                            @if($inventory->is_material && $inventory->MinimumStockLevel)
                            <div class="col-md-6">
                                <strong><i class="fas fa-exclamation-triangle mr-1"></i> Minimum Stock Level</strong>
                                <p class="text-muted">{{ number_format($inventory->MinimumStockLevel, 2) }}</p>
                            </div>
                            @endif
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-info-circle mr-1"></i> Status</strong>
                                <p class="text-muted">
                                    <span class="badge badge-{{ $inventory->Status == 'Active' ? 'success' : 'secondary' }}">
                                        {{ $inventory->Status }}
                                    </span>
                                </p>
                            </div>
                            @if($inventory->resourceCatalog)
                            <div class="col-md-6">
                                <strong><i class="fas fa-link mr-1"></i> Resource Catalog</strong>
                                <p class="text-muted">
                                    <a href="{{ route('resource-catalog.show', $inventory->resourceCatalog->ResourceCatalogID) }}" class="text-primary">
                                        {{ $inventory->resourceCatalog->ItemName }}
                                    </a>
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Material Usage Tab -->
                    @if($inventory->is_material)
                    <div class="tab-pane" id="materials">
                        @if($inventory->milestoneMaterials->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="thead-light">
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
                                                <td><strong>{{ number_format($material->QuantityUsed, 2) }} {{ $inventory->resourceCatalog->Unit ?? 'N/A' }}</strong></td>
                                                <td>{{ $material->DateUsed->format('M d, Y') }}</td>
                                                <td>{{ $material->Remarks ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No material usage recorded.</p>
                            </div>
                        @endif
                    </div>
                    @endif

                    <!-- Equipment Assignments Tab -->
                    @if($inventory->is_equipment)
                    <div class="tab-pane {{ $inventory->is_material ? '' : 'show active' }}" id="equipment">
                        @if($inventory->milestoneEquipment->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="thead-light">
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
                                                <td><strong>{{ number_format($equipment->QuantityAssigned, 2) }} {{ $inventory->resourceCatalog->Unit ?? 'N/A' }}</strong></td>
                                                <td>{{ $equipment->DateAssigned->format('M d, Y') }}</td>
                                                <td>
                                                    @if($equipment->DateReturned)
                                                        {{ $equipment->DateReturned->format('M d, Y') }}
                                                    @else
                                                        <span class="text-muted">Not Returned</span>
                                                    @endif
                                                </td>
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
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No equipment assignments recorded.</p>
                            </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

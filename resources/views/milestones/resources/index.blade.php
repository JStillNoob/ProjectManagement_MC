@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h2>Resource Planning</h2>
                <p class="text-muted">{{ $milestone->project->ProjectName ?? 'N/A' }} - {{ $milestone->milestone_name }}</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('milestones.resources.create', $milestone) }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add Resource
                </a>
                <a href="{{ route('projects.show', $milestone->ProjectID) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Project
                </a>
            </div>
        </div>

        <!-- Milestone Info -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Start Date:</strong><br>
                        {{ $milestone->StartDate ? $milestone->StartDate->format('M d, Y') : 'N/A' }}
                    </div>
                    <div class="col-md-3">
                        <strong>End Date:</strong><br>
                        {{ $milestone->EndDate ? $milestone->EndDate->format('M d, Y') : 'N/A' }}
                    </div>
                    <div class="col-md-3">
                        <strong>Status:</strong><br>
                        <span
                            class="badge bg-{{ $milestone->Status == 'Completed' ? 'success' : ($milestone->Status == 'In Progress' ? 'primary' : 'secondary') }}">
                            {{ $milestone->Status }}
                        </span>
                    </div>
                    <div class="col-md-3">
                        <strong>Progress:</strong><br>
                        {{ $milestone->PercentageComplete ?? 0 }}%
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Warnings -->
        @if($stockWarnings->count() > 0)
            <div class="alert alert-warning" role="alert">
                <h5 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Stock Warnings</h5>
                <p class="mb-2">The following planned items have insufficient stock:</p>
                <ul class="mb-0">
                    @foreach($stockWarnings as $warning)
                        <li>
                            <strong>{{ $warning['item_name'] }}</strong>:
                            Planned {{ number_format($warning['planned_quantity'], 2) }} {{ $warning['unit'] }},
                            Available {{ number_format($warning['available_quantity'], 2) }} {{ $warning['unit'] }}
                            <span class="text-danger">(Short by {{ number_format($warning['shortage'], 2) }}
                                {{ $warning['unit'] }})</span>
                        </li>
                    @endforeach
                </ul>
                <hr>
                <p class="mb-0">
                    <a href="{{ route('milestones.resources.generate-request', ['plan' => $resourcePlans->first()->PlanID ?? 0]) }}"
                        class="btn btn-sm btn-warning">
                        <i class="bi bi-file-earmark-plus"></i> Generate Inventory Request
                    </a>
                </p>
            </div>
        @endif

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6 class="card-title">Total Resources</h6>
                        <h3>{{ $resourcePlans->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6 class="card-title">Materials</h6>
                        <h3>{{ $resourcePlans->where('inventoryItem.ItemType', 'Material')->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h6 class="card-title">Equipment</h6>
                        <h3>{{ $resourcePlans->where('inventoryItem.ItemType', 'Equipment')->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-{{ $stockWarnings->count() > 0 ? 'danger' : 'success' }} text-white">
                    <div class="card-body">
                        <h6 class="card-title">Stock Status</h6>
                        <h3>{{ $stockWarnings->count() > 0 ? 'ALERT' : 'OK' }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resource Plans Table -->
        <div class="card">
            <div class="card-body">
                @if($resourcePlans->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Type</th>
                                    <th class="text-end">Planned Qty</th>
                                    <th class="text-end">Available Stock</th>
                                    <th>Unit</th>
                                    <th>Status</th>
                                    <th class="text-end">Est. Cost</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($resourcePlans as $plan)
                                    @php
                                        $available = $plan->inventoryItem ? $plan->inventoryItem->AvailableQuantity : 0;
                                        $planned = $plan->PlannedQuantity;
                                        $shortage = max(0, $planned - $available);
                                        $hasShortage = $shortage > 0;
                                    @endphp
                                    <tr class="{{ $hasShortage ? 'table-warning' : '' }}">
                                        <td>
                                            <strong>{{ $plan->inventoryItem->ItemName ?? 'N/A' }}</strong>
                                            @if($hasShortage)
                                                <br><small class="text-danger"><i class="bi bi-exclamation-circle"></i> Insufficient
                                                    stock</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($plan->inventoryItem)
                                                <span
                                                    class="badge bg-{{ $plan->inventoryItem->ItemType == 'Material' ? 'info' : 'warning' }}">
                                                    {{ $plan->inventoryItem->ItemType }}
                                                </span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($plan->inventoryItem && $plan->inventoryItem->requiresIntegerQuantity())
                                                {{ number_format((int) $plan->PlannedQuantity, 0) }}
                                            @else
                                                {{ number_format($plan->PlannedQuantity, 2) }}
                                            @endif
                                        </td>
                                        <td class="text-end {{ $hasShortage ? 'text-danger fw-bold' : 'text-success' }}">
                                            @if($plan->inventoryItem && $plan->inventoryItem->requiresIntegerQuantity())
                                                {{ number_format((int) $available, 0) }}
                                            @else
                                                {{ number_format($available, 2) }}
                                            @endif
                                            @if($hasShortage)
                                                <br><small>(Short: 
                                                    @if($plan->inventoryItem && $plan->inventoryItem->requiresIntegerQuantity())
                                                        {{ number_format((int) $shortage, 0) }}
                                                    @else
                                                        {{ number_format($shortage, 2) }}
                                                    @endif
                                                )</small>
                                            @endif
                                        </td>
                                        <td>{{ $plan->inventoryItem->Unit ?? 'N/A' }}</td>
                                        <td>
                                            @if($plan->IsAllocated)
                                                <span class="badge bg-success">Allocated</span>
                                            @else
                                                <span class="badge bg-secondary">Planned</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($plan->EstimatedCost)
                                                ₱{{ number_format($plan->EstimatedCost, 2) }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('milestones.resources.destroy', [$milestone, $plan]) }}"
                                                method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this resource plan?');"
                                                style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6" class="text-end">Total Estimated Cost:</th>
                                    <th class="text-end">₱{{ number_format($resourcePlans->sum('EstimatedCost'), 2) }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-clipboard-data" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="text-muted mt-3">No resources planned yet</p>
                        <a href="{{ route('milestones.resources.create', $milestone) }}" class="btn btn-primary">Add First
                            Resource</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@extends('layouts.app')

@section('title', 'Low Stock Items')
@section('page-title', 'Low Stock Items')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle mr-2 text-warning"></i>Low Stock Items
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('inventory.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Inventory
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($items->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Available Qty</th>
                                        <th>Minimum Level</th>
                                        <th>Unit</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                        <tr class="{{ $item->AvailableQuantity <= 0 ? 'table-danger' : '' }}">
                                            <td><strong>{{ $item->ItemName }}</strong></td>
                                            <td>
                                                <strong class="text-danger">
                                                    @if($item->requiresIntegerQuantity())
                                                        {{ number_format((int) $item->AvailableQuantity, 0) }}
                                                    @else
                                                        {{ number_format($item->AvailableQuantity, 2) }}
                                                    @endif
                                                </strong>
                                            </td>
                                            <td>
                                                @if($item->requiresIntegerQuantity())
                                                    {{ number_format((int) $item->MinimumStockLevel, 0) }}
                                                @else
                                                    {{ number_format($item->MinimumStockLevel, 2) }}
                                                @endif
                                            </td>
                                            <td>{{ $item->Unit }}</td>
                                            <td>
                                                <a href="{{ route('inventory.show', $item) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('inventory.edit', $item) }}" class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $items->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="text-muted">No Low Stock Items</h5>
                            <p class="text-muted">All items are above their minimum stock levels.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


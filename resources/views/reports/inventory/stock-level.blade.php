@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h2>Stock Level Report</h2>
                <p class="text-muted">Current inventory stock levels</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('reports.inventory.stock-level', array_merge(request()->all(), ['export' => 'pdf'])) }}"
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
                <form method="GET" action="{{ route('reports.inventory.stock-level') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Item Type</label>
                            <select name="type" class="form-select">
                                <option value="">All Types</option>
                                <option value="Material" {{ request('type') == 'Material' ? 'selected' : '' }}>Material
                                </option>
                                <option value="Equipment" {{ request('type') == 'Equipment' ? 'selected' : '' }}>Equipment
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->TypeID }}" {{ request('category') == $category->TypeID ? 'selected' : '' }}>
                                        {{ $category->TypeName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Stock Status</label>
                            <select name="stock_status" class="form-select">
                                <option value="">All Items</option>
                                <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Low Stock Only
                                </option>
                                <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Out of Stock
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-secondary w-100">
                                <i class="bi bi-funnel"></i> Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6>Total Items</h6>
                        <h3>{{ $items->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6>In Stock</h6>
                        <h3>{{ $items->where('AvailableQuantity', '>', 0)->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h6>Low Stock</h6>
                        <h3>{{ $items->filter(fn($i) => $i->AvailableQuantity <= $i->ReorderLevel && $i->AvailableQuantity > 0)->count() }}
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h6>Out of Stock</h6>
                        <h3>{{ $items->where('AvailableQuantity', '<=', 0)->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Level Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th class="text-end">Total Qty</th>
                                <th class="text-end">Available</th>
                                <th class="text-end">Committed</th>
                                <th class="text-end">Reorder Level</th>
                                <th>Unit</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                @php
                                    $isLowStock = $item->AvailableQuantity <= $item->ReorderLevel && $item->AvailableQuantity > 0;
                                    $isOutOfStock = $item->AvailableQuantity <= 0;
                                @endphp
                                <tr class="{{ $isOutOfStock ? 'table-danger' : ($isLowStock ? 'table-warning' : '') }}">
                                    <td>{{ $item->ItemCode }}</td>
                                    <td><strong>{{ $item->ItemName }}</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $item->ItemType == 'Material' ? 'info' : 'warning' }}">
                                            {{ $item->ItemType }}
                                        </span>
                                    </td>
                                    <td>{{ $item->inventoryItemType->TypeName ?? 'N/A' }}</td>
                                    <td class="text-end">{{ number_format($item->TotalQuantity, 2) }}</td>
                                    <td class="text-end">
                                        <strong
                                            class="{{ $isOutOfStock ? 'text-danger' : ($isLowStock ? 'text-warning' : 'text-success') }}">
                                            {{ number_format($item->AvailableQuantity, 2) }}
                                        </strong>
                                    </td>
                                    <td class="text-end">{{ number_format($item->CommittedQuantity, 2) }}</td>
                                    <td class="text-end">{{ number_format($item->ReorderLevel, 2) }}</td>
                                    <td>{{ $item->Unit }}</td>
                                    <td>
                                        @if($isOutOfStock)
                                            <span class="badge bg-danger">Out of Stock</span>
                                        @elseif($isLowStock)
                                            <span class="badge bg-warning">Low Stock</span>
                                        @else
                                            <span class="badge bg-success">OK</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
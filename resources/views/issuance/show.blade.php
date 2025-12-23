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
                                    <i class="fas fa-truck-loading me-2"></i>
                                    Issuance {{ $issuance->IssuanceNumber }}
                                </h3>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('issuance.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Info Section - 3 Boxes -->
                        <div class="row mb-4">
                            <!-- Issuance Information (Left) -->
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                                    <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.85rem;">
                                        <i class="fas fa-file-alt me-2"></i>Issuance Information
                                    </h6>
                                    <table class="table table-sm table-borderless mb-0" style="font-size: 0.95rem;">
                                        <tr>
                                            <td class="text-muted ps-0">Issuance No.</td>
                                            <td class="fw-bold">{{ $issuance->IssuanceNumber }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0">Date</td>
                                            <td class="fw-semibold">{{ $issuance->IssuanceDate->format('M d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0">Status</td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'Issued' => 'success',
                                                        'Returned' => 'secondary',
                                                        'Partial' => 'warning',
                                                    ];
                                                    $badgeColor = $statusColors[$issuance->Status] ?? 'info';
                                                @endphp
                                                <span
                                                    class="badge bg-{{ $badgeColor }}">{{ $issuance->Status ?? 'Issued' }}</span>
                                            </td>
                                        </tr>
                                        @if($issuance->inventoryRequest)
                                            <tr>
                                                <td class="text-muted ps-0">Request</td>
                                                <td>
                                                    <a href="{{ route('inventory.requests.show', $issuance->inventoryRequest->RequestID) }}"
                                                        class="fw-semibold">
                                                        REQ-{{ str_pad($issuance->inventoryRequest->RequestID, 4, '0', STR_PAD_LEFT) }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>

                            <!-- Project Information (Center) -->
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                                    <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.85rem;">
                                        <i class="fas fa-project-diagram me-2"></i>Project Information
                                    </h6>
                                    <table class="table table-sm table-borderless mb-0" style="font-size: 0.95rem;">
                                        <tr>
                                            <td class="text-muted ps-0">Project</td>
                                            <td class="fw-bold">{{ $issuance->project->ProjectName ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0">Milestone</td>
                                            <td class="fw-semibold">{{ $issuance->milestone->milestone_name ?? '-' }}</td>
                                        </tr>
                                        @if($issuance->project && $issuance->project->Location)
                                            <tr>
                                                <td class="text-muted ps-0">Location</td>
                                                <td class="fw-semibold">{{ $issuance->project->Location }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>

                            <!-- Personnel (Right) -->
                            <div class="col-md-4">
                                <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                                    <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.85rem;">
                                        <i class="fas fa-users me-2"></i>Personnel
                                    </h6>
                                    <table class="table table-sm table-borderless mb-0" style="font-size: 0.95rem;">
                                        <tr>
                                            <td class="text-muted ps-0">Issued By</td>
                                            <td class="fw-semibold">
                                                @if($issuance->issuer)
                                                    {{ $issuance->issuer->first_name }} {{ $issuance->issuer->last_name }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0">Received By</td>
                                            <td class="fw-bold">
                                                @if($issuance->receiver)
                                                    {{ $issuance->receiver->first_name }} {{ $issuance->receiver->last_name }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                    @if($issuance->SignaturePath)
                                        <div class="mt-2 pt-2 border-top">
                                            <small class="text-muted">Signature:</small>
                                            <img src="{{ asset('storage/' . $issuance->SignaturePath) }}" alt="Signature"
                                                style="max-width: 150px; border: 1px solid #ddd; padding: 3px; display: block; margin-top: 5px;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-center">Type</th>
                                        <th class="text-center">Qty Issued</th>
                                        <th class="text-center">Unit</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($issuance->items as $item)
                                        @php
                                            $itemName = $item->inventoryItem->resourceCatalog->ItemName ?? 'Unknown Item';
                                            $itemType = $item->ItemType ?? ($item->inventoryItem->resourceCatalog->Type ?? 'Materials');
                                            $unit = $item->Unit ?? ($item->inventoryItem->resourceCatalog->Unit ?? 'unit');
                                        @endphp
                                        <tr>
                                            <td>
                                                <span class="fw-semibold">{{ $itemName }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $itemType == 'Equipment' ? 'bg-info' : 'bg-primary' }}">
                                                    {{ $itemType }}
                                                </span>
                                            </td>
                                            <td class="text-center fw-bold">
                                                @if($item->inventoryItem && $item->inventoryItem->requiresIntegerQuantity())
                                                    {{ number_format((int) $item->QuantityIssued, 0) }}
                                                @else
                                                    {{ number_format($item->QuantityIssued, 2) }}
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $unit }}</td>
                                            <td class="text-center">
                                                @if($itemType == 'Equipment')
                                                    @if($item->QuantityReturned >= $item->QuantityIssued)
                                                        <span class="badge bg-success">Returned</span>
                                                    @elseif($item->QuantityReturned > 0)
                                                        <span class="badge bg-warning">Partial Return</span>
                                                    @else
                                                        <span class="badge bg-info">In Use</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary">Consumed</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <td colspan="2" class="fw-bold">Total Items: {{ $issuance->items->count() }}</td>
                                        <td class="text-center fw-bold">
                                            {{ number_format($issuance->items->sum('QuantityIssued'), 2) }}</td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @if($issuance->Remarks)
                            <div class="mt-3 p-3 bg-light rounded">
                                <h6 class="text-uppercase fw-bold mb-2" style="color: #87A96B; font-size: 0.85rem;">
                                    <i class="fas fa-sticky-note me-2"></i>Notes/Remarks
                                </h6>
                                <p class="mb-0">{{ $issuance->Remarks }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Card Footer -->
                    <div class="card-footer bg-white d-flex justify-content-end">
                        @if(Auth::user()->UserTypeID != 3)
                            <a href="{{ route('issuance.pdf', $issuance->IssuanceID) }}" class="btn text-white"
                                style="background-color: #87A96B;" target="_blank">
                                <i class="fas fa-file-pdf me-1"></i> Print PDF
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
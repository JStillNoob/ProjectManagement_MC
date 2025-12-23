@extends('layouts.app')

@section('title', 'Equipment Returns')
@section('page-title', 'Equipment Returns')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-undo-alt mr-2"></i>
                            Equipment Returns
                        </h3>
                    </div>
                    <div class="card-body">
                        <!-- Table -->
                        <div class="table-responsive">
                            <table id="equipmentTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Equipment</th>
                                        <th>Project</th>
                                        <th>Milestone</th>
                                        <th>Quantity</th>
                                        <th>Assigned Date</th>
                                        <th>Days in Use</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($assignments as $assignment)
                                        <tr>
                                            <td><strong>{{ $assignment->inventoryItem->resourceCatalog->ItemName ?? 'N/A' }}</strong>
                                            </td>
                                            <td>{{ $assignment->milestone->project->ProjectName ?? 'N/A' }}</td>
                                            <td>{{ $assignment->milestone->milestone_name ?? 'N/A' }}</td>
                                            <td>{{ number_format((int) $assignment->QuantityAssigned, 0) }}</td>
                                            <td>{{ $assignment->DateAssigned ? $assignment->DateAssigned->format('M d, Y') : 'N/A' }}
                                            </td>
                                            <td>
                                                @php
                                                    $endDate = $assignment->DateReturned ?? now();
                                                    $days = $assignment->DateAssigned ? (int) $assignment->DateAssigned->diffInDays($endDate) : 0;
                                                @endphp
                                                {{ $days }} days
                                            </td>
                                            <td>
                                                @if($assignment->DateReturned)
                                                    @if($assignment->Status == 'Returned')
                                                        <span class="badge bg-success">Returned</span>
                                                    @elseif($assignment->Status == 'Damaged')
                                                        <span class="badge bg-warning">Returned (Damaged)</span>
                                                    @elseif($assignment->Status == 'Missing')
                                                        <span class="badge bg-danger">Returned (Missing)</span>
                                                    @else
                                                        <span class="badge bg-success">Returned</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-info">In Use</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if(!$assignment->DateReturned)
                                                    <a href="{{ route('equipment.returns.create', $assignment->EquipmentAssignmentID) }}"
                                                        class="text-info"
                                                        style="cursor: pointer; font-size: 1.2rem; display: inline-block; text-decoration: none;"
                                                        title="Return Equipment">
                                                        <i class="fas fa-undo-alt"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('equipment.returns.show', $assignment->EquipmentAssignmentID) }}"
                                                        class="text-info"
                                                        style="cursor: pointer; font-size: 1.2rem; display: inline-block; text-decoration: none;"
                                                        title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No equipment assignments found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            #equipmentTable {
                border: none !important;
            }

            #equipmentTable thead th {
                border: none !important;
                border-bottom: 1px solid #dee2e6 !important;
            }

            #equipmentTable tbody td {
                border: none !important;
                border-bottom: 1px solid #dee2e6 !important;
            }

            #equipmentTable tbody tr:last-child td {
                border-bottom: none !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            $(document).ready(function () {
                $('#equipmentTable').DataTable({
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "responsive": true,
                    "order": [[4, 'desc']]
                });
            });
        </script>
    @endpush
@endsection
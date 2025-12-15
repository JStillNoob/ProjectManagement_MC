@extends('layouts.app')

@section('title', 'Receiving Records')
@section('page-title', 'Receiving Records')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-box-open mr-2"></i>
                            Receiving Records
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('receiving.create') }}" class="btn btn-success btn-sm"
                                style="background-color: #87A96B !important; border: 2px solid #87A96B !important; color: white !important;">
                                <i class="fas fa-plus"></i> Receive Items
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <!-- Table -->
                        <div class="table-responsive">
                            <table id="receivingTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Date Received</th>
                                        <th>PO Number</th>
                                        <th>Supplier</th>
                                        <th>Items</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($receivingRecords as $record)
                                        <tr>
                                            <td>{{ $record->ReceivedDate->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('purchase-orders.show', $record->POID) }}">
                                                    #{{ $record->purchaseOrder->POID ?? 'N/A' }}
                                                </a>
                                            </td>
                                            <td>{{ $record->purchaseOrder->supplier->SupplierName ?? 'N/A' }}</td>
                                            <td>{{ $record->items->count() }} items</td>
                                            <td class="text-center">
                                                <a href="{{ route('receiving.show', $record->ReceivingID) }}" class="text-info"
                                                    style="text-decoration: underline; cursor: pointer;">
                                                    <i class="fas fa-eye mr-1"></i> View Details
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No receiving records found.</td>
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
            #receivingTable {
                border: none !important;
            }

            #receivingTable thead th {
                border: none !important;
                border-bottom: 1px solid #dee2e6 !important;
            }

            #receivingTable tbody td {
                border: none !important;
                border-bottom: 1px solid #dee2e6 !important;
            }

            #receivingTable tbody tr:last-child td {
                border-bottom: none !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            $(document).ready(function () {
                $('#receivingTable').DataTable({
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "responsive": true,
                    "order": [[0, 'desc']]
                });
            });
        </script>
    @endpush
@endsection
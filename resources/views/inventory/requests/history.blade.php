@extends('layouts.app')

@section('title', 'Request History')
@section('page-title', 'Request History')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-success rounded-circle d-flex align-items-center justify-content-center mr-2"
                                    style="width: 36px; height: 36px; background-color: #87A96B !important;">
                                    <i class="fas fa-history text-white"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 font-weight-bold text-dark">Request History</h5>
                                    <small class="text-muted">All inventory requests for {{ $foremanProject->ProjectName ?? 'your project' }}</small>
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('inventory.requests.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-arrow-left mr-1"></i> Back to Requests
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Status Filter -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <form method="GET" action="{{ route('inventory.requests.history') }}" class="form-inline">
                                    <div class="form-group">
                                        <label for="status" class="mr-2">Filter by Status:</label>
                                        <select name="status" id="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                            <option value="">All Statuses</option>
                                            <option value="Pending" {{ $status === 'Pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="Approved" {{ $status === 'Approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="Pending - To Order" {{ $status === 'Pending - To Order' ? 'selected' : '' }}>Pending - To Order</option>
                                            <option value="Ordered" {{ $status === 'Ordered' ? 'selected' : '' }}>Ordered</option>
                                            <option value="Rejected" {{ $status === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                            <option value="Fulfilled" {{ $status === 'Fulfilled' ? 'selected' : '' }}>Fulfilled</option>
                                        </select>
                                    </div>
                                </form>
                            </div>
                        </div>

                        @if($requests->count())
                            <div class="table-responsive">
                                <table class="table table-hover" id="requestHistoryTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Request ID</th>
                                            <th>Milestone</th>
                                            <th>Type</th>
                                            <th>Items</th>
                                            <th>Status</th>
                                            <th>Date Requested</th>
                                            <th>Approved By</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($requests as $request)
                                            <tr>
                                                <td>
                                                    <strong>#{{ $request->RequestID }}</strong>
                                                    @if($request->IsAdditionalRequest)
                                                        <span class="badge badge-warning ml-2" title="Additional Request">
                                                            <i class="fas fa-plus-circle mr-1"></i>Additional
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>{{ $request->milestone->milestone_name ?? 'N/A' }}</td>
                                                <td><span class="badge badge-info">{{ $request->RequestType }}</span></td>
                                                <td>
                                                    <span class="badge badge-light">{{ $request->items->count() }} item(s)</span>
                                                </td>
                                                <td>
                                                    @if($request->Status === 'Pending')
                                                        <span class="badge badge-warning">Pending</span>
                                                    @elseif($request->Status === 'Approved')
                                                        <span class="badge badge-success">Approved</span>
                                                    @elseif($request->Status === 'Pending - To Order')
                                                        <span class="badge badge-warning">To Order</span>
                                                    @elseif($request->Status === 'Ordered')
                                                        <span class="badge badge-primary">Ordered</span>
                                                    @elseif($request->Status === 'Rejected')
                                                        <span class="badge badge-danger">Rejected</span>
                                                    @elseif($request->Status === 'Fulfilled')
                                                        <span class="badge badge-success">Fulfilled</span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ $request->Status }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $request->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    @if($request->approver)
                                                        {{ $request->approver->FirstName ?? '' }} {{ $request->approver->LastName ?? '' }}
                                                        @if($request->ApprovedAt)
                                                            <br><small class="text-muted">{{ $request->ApprovedAt->format('M d, Y') }}</small>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('inventory.requests.show', $request) }}"
                                                        class="btn btn-outline-info btn-sm" title="View Details">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    Showing {{ $requests->firstItem() }} to {{ $requests->lastItem() }} of {{ $requests->total() }} entries
                                </div>
                                <div>
                                    {{ $requests->links() }}
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No requests found.</p>
                                @if($status)
                                    <a href="{{ route('inventory.requests.history') }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-times mr-1"></i> Clear Filter
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#requestHistoryTable').DataTable({
                    "paging": false,
                    "searching": true,
                    "ordering": true,
                    "info": false,
                    "autoWidth": false,
                    "responsive": true,
                    "order": [[5, 'desc']] // Sort by date descending
                });
            });
        </script>
    @endpush
@endsection


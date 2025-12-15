@extends('layouts.app')

@section('title', 'Issuance Records')
@section('page-title', 'Issuance Records')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-truck-loading mr-2"></i>
                            Issuance Records
                        </h3>
                        <div class="card-tools">
                            @if(Auth::user()->UserTypeID != 3)
                                <a href="{{ route('issuance.create') }}" class="btn btn-success btn-sm"
                                    style="background-color: #87A96B !important; border: 2px solid #87A96B !important; color: white !important;">
                                    <i class="fas fa-plus"></i> New Issuance
                                </a>
                            @endif
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
                            <table id="issuanceTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Issuance #</th>
                                        <th>Date</th>
                                        <th>Project</th>
                                        <th>Milestone</th>
                                        <th>Status</th>
                                        <th>Items</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($issuances as $issuance)
                                        <tr>
                                            <td><strong>{{ $issuance->IssuanceNumber }}</strong></td>
                                            <td>{{ $issuance->IssuanceDate->format('M d, Y') }}</td>
                                            <td>{{ $issuance->project->ProjectName ?? 'N/A' }}</td>
                                            <td>{{ $issuance->milestone->milestone_name ?? 'N/A' }}</td>
                                            <td>
                                                @if($issuance->Status == 'Issued')
                                                    <span class="badge bg-success">{{ $issuance->Status }}</span>
                                                @elseif($issuance->Status == 'Returned')
                                                    <span class="badge bg-secondary">{{ $issuance->Status }}</span>
                                                @else
                                                    <span class="badge bg-warning">{{ $issuance->Status }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $issuance->items->count() }}</td>
                                            <td class="text-center" style="white-space: nowrap;">
                                                <a href="{{ route('issuance.show', $issuance) }}" class="text-info mr-2"
                                                    style="text-decoration: underline; cursor: pointer;">
                                                    <i class="fas fa-eye mr-1"></i> View
                                                </a>
                                                @if(Auth::user()->UserTypeID != 3)
                                                    <a href="{{ route('issuance.pdf', $issuance) }}" class="text-success mr-2"
                                                        style="text-decoration: underline; cursor: pointer;" target="_blank">
                                                        <i class="fas fa-file-pdf mr-1"></i> PDF
                                                    </a>
                                                    @if($issuance->Status == 'Issued')
                                                        <form action="{{ route('issuance.destroy', $issuance) }}" method="POST"
                                                            style="display: inline-block;"
                                                            onsubmit="return confirm('Are you sure you want to delete this issuance record?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-link text-danger p-0"
                                                                style="text-decoration: underline; border: none; background: none; cursor: pointer;">
                                                                <i class="fas fa-trash mr-1"></i> Delete
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No issuance records found.</td>
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
            #issuanceTable {
                border: none !important;
            }

            #issuanceTable thead th {
                border: none !important;
                border-bottom: 1px solid #dee2e6 !important;
            }

            #issuanceTable tbody td {
                border: none !important;
                border-bottom: 1px solid #dee2e6 !important;
            }

            #issuanceTable tbody tr:last-child td {
                border-bottom: none !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            $(document).ready(function () {
                $('#issuanceTable').DataTable({
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "responsive": true,
                    "order": [[1, 'desc']]
                });
            });
        </script>
    @endpush
@endsection
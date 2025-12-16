@extends('layouts.app')

@section('title', 'Position Management')
@section('page-title', 'Position Management')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-briefcase text-primary mr-2"></i>
                            Position Management
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                data-target="#createPositionModal"
                                style="background-color: #87A96B !important; border: 2px solid #87A96B !important; color: white !important; opacity: 1 !important; visibility: visible !important; display: inline-block !important;">
                                <i class="fas fa-plus"></i> Add New Position
                            </button>
                        </div>
                    </div>
                    <div class="card-body">

                        <table id="positionsTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">Position Name</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($positions as $position)
                                    <tr>
                                        <td class="text-center">
                                            <strong>{{ $position->PositionName }}</strong>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('positions.show', $position) }}" class="text-info">
                                                <i class="fas fa-eye mr-1"></i> View Details
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center">No positions found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->

        <!-- Create Position Modal -->
        <div class="modal fade" id="createPositionModal" tabindex="-1" role="dialog"
            aria-labelledby="createPositionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header text-white" style="background: #7fb069;">
                        <h5 class="modal-title" id="createPositionModalLabel">
                            <i class="fas fa-plus mr-2"></i>Create New Position
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('positions.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <!-- Position Information -->
                            <h5 class="mb-3" style="color: #7fb069;"><i class="fas fa-briefcase mr-2"></i>Position
                                Information</h5>
                            <div class="form-group">
                                <label for="modal_PositionName">Position Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('PositionName') is-invalid @enderror"
                                    id="modal_PositionName" name="PositionName" value="{{ old('PositionName') }}" required>
                                @error('PositionName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                            <button type="submit" class="btn text-white"
                                style="background: #7fb069; border-color: #7fb069;">
                                <i class="fas fa-save"></i> Create Position
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @push('styles')
            <style>
                /* Form input outline and focus states to match green theme */
                #createPositionModal .form-control {
                    border: 1px solid #ced4da;
                    outline: 1px solid #ced4da;
                }

                #createPositionModal .form-control:focus {
                    border-color: #7fb069;
                    outline: 1px solid #7fb069;
                    box-shadow: 0 0 0 0.2rem rgba(127, 176, 105, 0.25);
                }

                /* DataTables Styling */
                #positionsTable {
                    border-collapse: collapse !important;
                    width: 100% !important;
                }

                #positionsTable thead th {
                    background-color: #f8f9fa !important;
                    border-top: 1px solid #dee2e6 !important;
                    border-bottom: 2px solid #dee2e6 !important;
                    border-left: none !important;
                    border-right: none !important;
                    padding: 12px !important;
                    font-weight: 600 !important;
                }

                #positionsTable tbody td {
                    border-top: 1px solid #dee2e6 !important;
                    border-bottom: none !important;
                    border-left: none !important;
                    border-right: none !important;
                    padding: 12px !important;
                    vertical-align: middle !important;
                }

                #positionsTable tbody tr:hover {
                    background-color: #f8f9fa !important;
                }

                /* DataTable wrapper styling */
                #positionsTable_wrapper {
                    padding: 1rem !important;
                }

                #positionsTable_wrapper .dataTables_length {
                    margin-bottom: 1rem !important;
                }

                #positionsTable_wrapper .dataTables_filter {
                    margin-bottom: 1rem !important;
                }

                #positionsTable_wrapper .dataTables_filter input {
                    border: 1px solid #ced4da !important;
                    border-radius: 0.25rem !important;
                    padding: 0.375rem 0.75rem !important;
                    margin-left: 0.5rem !important;
                }

                #positionsTable_wrapper .dataTables_length select {
                    border: 1px solid #ced4da !important;
                    border-radius: 0.25rem !important;
                    padding: 0.375rem 2rem 0.375rem 0.75rem !important;
                    margin: 0 0.5rem !important;
                }

                #positionsTable_wrapper .dataTables_info {
                    padding-top: 1rem !important;
                    color: #6c757d !important;
                }

                #positionsTable_wrapper .dataTables_paginate {
                    padding-top: 1rem !important;
                }

                #positionsTable_wrapper .dataTables_paginate .paginate_button {
                    padding: 0.375rem 0.75rem !important;
                    margin-left: 0.25rem !important;
                    border: 1px solid #dee2e6 !important;
                    border-radius: 0.25rem !important;
                    background-color: #fff !important;
                    color: #007bff !important;
                }

                #positionsTable_wrapper .dataTables_paginate .paginate_button.current {
                    background-color: #007bff !important;
                    color: #fff !important;
                    border-color: #007bff !important;
                }

                #positionsTable_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled):not(.current) {
                    background-color: #e9ecef !important;
                    border-color: #dee2e6 !important;
                    color: #007bff !important;
                }

                #positionsTable_wrapper .dataTables_paginate .paginate_button.disabled {
                    opacity: 0.5 !important;
                    cursor: not-allowed !important;
                }
            </style>
        @endpush

        @push('scripts')
            <script>
                // Clear modal form when modal is closed
                $('#createPositionModal').on('hidden.bs.modal', function () {
                    $(this).find('form')[0].reset();
                    $(this).find('.is-invalid').removeClass('is-invalid');
                    $(this).find('.invalid-feedback').remove();
                });

                // Auto-open modal if there are validation errors
                @if($errors->has('PositionName'))
                    $(document).ready(function () {
                        $('#createPositionModal').modal('show');
                    });
                @endif

                // Initialize DataTables for positions table
                $(document).ready(function () {
                    $('#positionsTable').DataTable({
                        "responsive": true,
                        "lengthChange": true,
                        "autoWidth": false,
                        "pageLength": 10,
                        "order": [[0, 'asc']],
                        "columnDefs": [
                            { "orderable": false, "targets": [1] },
                            { "className": "text-center", "targets": [0, 1] }
                        ],
                        "language": {
                            "search": "Search:",
                            "lengthMenu": "Show _MENU_ entries",
                            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                            "infoEmpty": "Showing 0 to 0 of 0 entries",
                            "infoFiltered": "(filtered from _MAX_ total entries)",
                            "paginate": {
                                "first": "First",
                                "last": "Last",
                                "next": "Next",
                                "previous": "Previous"
                            }
                        }
                    });
                });
            </script>
        @endpush
    </div>
    </div>
@endsection
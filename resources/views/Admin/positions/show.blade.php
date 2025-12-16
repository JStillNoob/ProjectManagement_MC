@extends('layouts.app')

@section('title', 'Position Details')
@section('page-title', 'Position Details')

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Position Card -->
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <div class="profile-user-img img-fluid img-circle bg-primary d-flex align-items-center justify-content-center mx-auto" 
                         style="width: 100px; height: 100px; font-size: 40px; color: white;">
                        <i class="fas fa-briefcase"></i>
                    </div>
                </div>

                <h3 class="profile-username text-center mt-3" id="position-name-display">{{ $position->PositionName }}</h3>
                <p class="text-muted text-center">Position ID: {{ $position->PositionID }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Total Employees</b> <a class="float-right">{{ $position->employees->count() }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Active Employees</b> 
                        <a class="float-right">{{ $position->employees->where('employee_status_id', \App\Models\EmployeeStatus::ACTIVE)->count() }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Created</b> <a class="float-right">{{ $position->formatted_created_at }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Last Updated</b> <a class="float-right" id="last-updated-display">{{ $position->formatted_updated_at }}</a>
                    </li>
                </ul>

                <button type="button" class="btn btn-warning btn-block mb-2" data-toggle="modal" data-target="#editPositionModal">
                    <i class="fas fa-edit"></i> Edit Position
                </button>
                <a href="{{ route('positions.index') }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Position Details -->
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link active" href="#details" data-toggle="tab">Position Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#employees" data-toggle="tab">Employees ({{ $position->employees->count() }})</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Position Details Tab -->
                    <div class="active tab-pane" id="details">
                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-id-badge mr-1"></i> Position ID</strong>
                                <p class="text-muted">{{ $position->PositionID }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-briefcase mr-1"></i> Position Name</strong>
                                <p class="text-muted" id="position-name-details">{{ $position->PositionName }}</p>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-users mr-1"></i> Total Employees</strong>
                                <p class="text-muted">{{ $position->employees->count() }} employee(s)</p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-user-check mr-1"></i> Active Employees</strong>
                                <p class="text-muted">{{ $position->employees->where('employee_status_id', \App\Models\EmployeeStatus::ACTIVE)->count() }} employee(s)</p>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-clock mr-1"></i> Date Created</strong>
                                <p class="text-muted">{{ $position->formatted_created_at }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-edit mr-1"></i> Last Updated</strong>
                                <p class="text-muted" id="last-updated-details">{{ $position->formatted_updated_at }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Employees Tab -->
                    <div class="tab-pane" id="employees">
                        @if($position->employees->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Employee ID</th>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($position->employees as $employee)
                                            <tr>
                                                <td>{{ $employee->EmployeeID }}</td>
                                                <td>
                                                    <strong>{{ $employee->first_name }} {{ $employee->last_name }}</strong>
                                                </td>
                                                <td>
                                                    @php
                                                        $badgeClass = 'secondary';
                                                        if ($employee->employee_status_id == \App\Models\EmployeeStatus::ACTIVE) {
                                                            $badgeClass = 'success';
                                                        } elseif ($employee->employee_status_id == \App\Models\EmployeeStatus::ARCHIVED) {
                                                            $badgeClass = 'danger';
                                                        }
                                                    @endphp
                                                    <span class="badge badge-{{ $badgeClass }}">
                                                        {{ $employee->status_name ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('employees.show', $employee) }}" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                No employees are currently assigned to this position.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Position Modal -->
<div class="modal fade" id="editPositionModal" tabindex="-1" role="dialog" aria-labelledby="editPositionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="editPositionModalLabel">
                    <i class="fas fa-edit mr-2"></i>Edit Position
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editPositionForm" action="{{ route('positions.update', $position) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Position ID:</strong> {{ $position->PositionID }}
                    </div>
                    
                    <div class="form-group">
                        <label for="PositionName">Position Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="PositionName" name="PositionName" 
                               value="{{ $position->PositionName }}" required>
                        <div class="invalid-feedback" id="positionNameError"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-warning" id="savePositionBtn">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#editPositionForm').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var submitBtn = $('#savePositionBtn');
        var originalBtnText = submitBtn.html();
        
        // Clear previous errors
        $('#PositionName').removeClass('is-invalid');
        $('#positionNameError').text('');
        
        // Disable button and show loading
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                // Update the position name displays on the page
                var newName = $('#PositionName').val();
                $('#position-name-display').text(newName);
                $('#position-name-details').text(newName);
                
                // Update last updated time
                var now = new Date();
                var options = { year: 'numeric', month: 'short', day: '2-digit', hour: 'numeric', minute: '2-digit', hour12: true };
                var formattedDate = now.toLocaleDateString('en-US', options).replace(',', '');
                $('#last-updated-display').text(formattedDate);
                $('#last-updated-details').text(formattedDate);
                
                // Close modal
                $('#editPositionModal').modal('hide');
                
                // Show success message
                toastr.success('Position updated successfully!');
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    if (errors.PositionName) {
                        $('#PositionName').addClass('is-invalid');
                        $('#positionNameError').text(errors.PositionName[0]);
                    }
                } else {
                    toastr.error('An error occurred. Please try again.');
                }
            },
            complete: function() {
                // Re-enable button
                submitBtn.html(originalBtnText).prop('disabled', false);
            }
        });
    });
    
    // Reset form when modal is closed
    $('#editPositionModal').on('hidden.bs.modal', function() {
        $('#PositionName').removeClass('is-invalid');
        $('#positionNameError').text('');
    });
});
</script>
@endsection

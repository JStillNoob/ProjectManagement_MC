@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('equipment.returns.store', $assignment->EquipmentAssignmentID) }}" method="POST"
            enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <!-- Card Header -->
                        <div class="card-header" style="background-color: #87A96B;">
                            <h3 class="card-title mb-0" style="color: white; font-size: 1.25rem;">
                                <i class="fas fa-undo-alt me-2"></i>
                                Process Equipment Return
                            </h3>
                        </div>

                        <div class="card-body">
                            <!-- Equipment Information Section -->
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                                        <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.85rem;">
                                            <i class="fas fa-tools me-2"></i>Equipment Details
                                        </h6>
                                        <table class="table table-sm table-borderless mb-0" style="font-size: 0.95rem;">
                                            <tr>
                                                <td class="text-muted ps-0" style="width: 40%;">Equipment</td>
                                                <td class="fw-bold">{{ $assignment->inventoryItem->resourceCatalog->ItemName ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted ps-0">Qty Issued</td>
                                                <td><strong>{{ number_format($assignment->QuantityAssigned, 2) }}</strong> {{ $assignment->inventoryItem->resourceCatalog->Unit ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted ps-0">Date Assigned</td>
                                                <td>{{ $assignment->DateAssigned ? $assignment->DateAssigned->format('M d, Y') : 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                                        <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.85rem;">
                                            <i class="fas fa-project-diagram me-2"></i>Project Information
                                        </h6>
                                        <table class="table table-sm table-borderless mb-0" style="font-size: 0.95rem;">
                                            <tr>
                                                <td class="text-muted ps-0" style="width: 40%;">Project</td>
                                                <td class="fw-bold">{{ $assignment->milestone->project->ProjectName ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted ps-0">Milestone</td>
                                                <td>{{ $assignment->milestone->milestone_name ?? 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="border rounded p-3 h-100" style="background-color: #f8f9fa;">
                                        <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.85rem;">
                                            <i class="fas fa-clock me-2"></i>Usage Summary
                                        </h6>
                                        <table class="table table-sm table-borderless mb-0" style="font-size: 0.95rem;">
                                            @php
                                                $days = $assignment->DateAssigned ? (int) $assignment->DateAssigned->diffInDays(now()) : 0;
                                            @endphp
                                            <tr>
                                                <td class="text-muted ps-0" style="width: 40%;">Days in Use</td>
                                                <td><span class="badge" style="background-color: #17a2b8; color: white;">{{ $days }} days</span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted ps-0">Status</td>
                                                <td><span class="badge" style="background-color: #87A96B; color: white;">{{ $assignment->Status }}</span></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Return Details Section -->
                            <div class="border rounded p-3 mb-4" style="background-color: #f8f9fa;">
                                <h6 class="text-uppercase fw-bold mb-3" style="color: #87A96B; font-size: 0.85rem;">
                                    <i class="fas fa-clipboard-check me-2"></i>Return Details
                                </h6>
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="DateReturned" class="form-label">Return Date <span class="text-danger">*</span></label>
                                        <input type="date" name="DateReturned" id="DateReturned" class="form-control"
                                            value="{{ old('DateReturned', date('Y-m-d')) }}" required max="{{ date('Y-m-d') }}">
                                        @error('DateReturned')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="QuantityReturned" class="form-label">Quantity Returned <span class="text-danger">*</span></label>
                                        <input type="number" name="QuantityReturned" id="QuantityReturned" class="form-control"
                                            step="0.01" min="0.01" max="{{ $assignment->QuantityAssigned }}"
                                            value="{{ old('QuantityReturned', $assignment->QuantityAssigned) }}" required>
                                        <small class="form-text text-muted">Max: {{ number_format($assignment->QuantityAssigned, 2) }}</small>
                                        @error('QuantityReturned')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="ReturnCondition" class="form-label">Return Condition <span class="text-danger">*</span></label>
                                        <select name="ReturnCondition" id="ReturnCondition" class="form-select" required onchange="toggleIncidentFields()">
                                            <option value="">Select Condition</option>
                                            <option value="Good" {{ old('ReturnCondition') == 'Good' ? 'selected' : '' }}>Good Condition</option>
                                            <option value="Damaged" {{ old('ReturnCondition') == 'Damaged' ? 'selected' : '' }}>Damaged</option>
                                            <option value="Missing" {{ old('ReturnCondition') == 'Missing' ? 'selected' : '' }}>Missing/Lost</option>
                                        </select>
                                        @error('ReturnCondition')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="ReturnedBy" class="form-label">Returned By <span class="text-danger">*</span></label>
                                        <select name="ReturnedBy" id="ReturnedBy" class="form-select" required>
                                            <option value="">Select Employee</option>
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee->id }}" {{ old('ReturnedBy') == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('ReturnedBy')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label for="ReturnNotes" class="form-label">Return Notes</label>
                                        <textarea name="ReturnNotes" id="ReturnNotes" class="form-control" rows="2" placeholder="Optional notes about the return...">{{ old('ReturnNotes') }}</textarea>
                                        @error('ReturnNotes')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Incident Details (shown when Damaged or Missing) -->
                            <div class="border rounded p-3 mb-3" id="incidentSection" style="display: none; background-color: #fff3cd;">
                                <h6 class="text-uppercase fw-bold mb-3" style="color: #856404; font-size: 0.85rem;">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Incident Report
                                </h6>
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="IncidentType" class="form-label">Incident Type</label>
                                        <select name="IncidentType" id="IncidentType" class="form-select">
                                            <option value="">Select Type</option>
                                            <option value="Damage" {{ old('IncidentType') == 'Damage' ? 'selected' : '' }}>Damage</option>
                                            <option value="Loss" {{ old('IncidentType') == 'Loss' ? 'selected' : '' }}>Loss</option>
                                            <option value="Theft" {{ old('IncidentType') == 'Theft' ? 'selected' : '' }}>Theft</option>
                                            <option value="Malfunction" {{ old('IncidentType') == 'Malfunction' ? 'selected' : '' }}>Malfunction</option>
                                        </select>
                                        @error('IncidentType')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="IncidentDate" class="form-label">Incident Date</label>
                                        <input type="date" name="IncidentDate" id="IncidentDate" class="form-control"
                                            value="{{ old('IncidentDate', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}">
                                        @error('IncidentDate')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="ResponsibleEmployeeID" class="form-label">Responsible Employee</label>
                                        <select name="ResponsibleEmployeeID" id="ResponsibleEmployeeID" class="form-select">
                                            <option value="">Select Employee (if applicable)</option>
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee->id }}" {{ old('ResponsibleEmployeeID') == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('ResponsibleEmployeeID')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="EstimatedCost" class="form-label">Est. Repair/Replacement Cost</label>
                                        <input type="number" name="EstimatedCost" id="EstimatedCost" class="form-control"
                                            step="0.01" min="0" value="{{ old('EstimatedCost') }}" placeholder="0.00">
                                        @error('EstimatedCost')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-8 mb-3">
                                        <label for="IncidentDescription" class="form-label">Incident Description</label>
                                        <textarea name="IncidentDescription" id="IncidentDescription" class="form-control" rows="2"
                                            placeholder="Describe what happened...">{{ old('IncidentDescription') }}</textarea>
                                        @error('IncidentDescription')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="PhotoPath" class="form-label">Photo Evidence</label>
                                        <input type="file" name="PhotoPath" id="PhotoPath" class="form-control" accept="image/*">
                                        <small class="form-text text-muted">Upload photo of damage</small>
                                        @error('PhotoPath')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="card-footer bg-white d-flex justify-content-end">
                            <a href="{{ route('equipment.returns.index') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn text-white" style="background-color: #87A96B;">
                                <i class="fas fa-check me-1"></i> Process Return
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function toggleIncidentFields() {
            const condition = document.getElementById('ReturnCondition').value;
            const incidentSection = document.getElementById('incidentSection');

            if (condition === 'Damaged' || condition === 'Missing') {
                incidentSection.style.display = 'block';

                // Auto-populate incident type based on condition
                const incidentType = document.getElementById('IncidentType');
                if (condition === 'Damaged') {
                    incidentType.value = 'Damage';
                } else if (condition === 'Missing') {
                    incidentType.value = 'Loss';
                }
            } else {
                incidentSection.style.display = 'none';
            }
        }

        // Check on page load if condition is already selected
        window.addEventListener('DOMContentLoaded', function () {
            toggleIncidentFields();
        });
    </script>
@endsection
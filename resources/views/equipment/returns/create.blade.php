@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                                        <input type="hidden" name="DateReturned" value="{{ date('Y-m-d') }}">
                                        <input type="text" id="DateReturned" class="form-control" value="{{ date('M d, Y') }}" readonly style="background-color: #f8f9fa;" aria-label="Return Date">
                                        @error('DateReturned')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="QuantityReturned" class="form-label">Quantity Returned <span class="text-danger">*</span></label>
                                        <input type="number" name="QuantityReturned" id="QuantityReturned" class="form-control"
                                            step="1" min="1" max="{{ (int) $assignment->QuantityAssigned }}"
                                            value="{{ old('QuantityReturned', (int) $assignment->QuantityAssigned) }}" required>
                                        <small class="form-text text-muted">Max: {{ (int) $assignment->QuantityAssigned }}</small>
                                        @error('QuantityReturned')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="Status" class="form-label">Return Condition <span class="text-danger">*</span></label>
                                        <select name="Status" id="Status" class="form-control" required onchange="toggleIncidentFields()" aria-label="Return Condition">
                                            <option value="">Select Condition</option>
                                            <option value="Returned" {{ old('Status') == 'Returned' ? 'selected' : '' }}>Good Condition</option>
                                            <option value="Damaged" {{ old('Status') == 'Damaged' ? 'selected' : '' }}>Damaged</option>
                                            <option value="Missing" {{ old('Status') == 'Missing' ? 'selected' : '' }}>Missing/Lost</option>
                                        </select>
                                        @error('Status')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="ReturnedBy" class="form-label">Returned By <span class="text-danger">*</span></label>
                                        @php
                                            $currentUser = Auth::user()->load('employee');
                                            $returnedByName = 'User';
                                            if ($currentUser->employee) {
                                                $returnedByName = $currentUser->employee->full_name ?? 'User';
                                            } else {
                                                $emailParts = explode('@', $currentUser->Email ?? '');
                                                $returnedByName = ucfirst($emailParts[0] ?? 'User');
                                            }
                                        @endphp
                                        <input type="hidden" name="ReturnedBy" value="{{ $currentUser->EmployeeID }}">
                                        <input type="text" id="ReturnedBy" class="form-control" value="{{ $returnedByName }}" readonly style="background-color: #f8f9fa;" aria-label="Returned By">
                                        @error('ReturnedBy')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label for="ReturnRemarks" class="form-label">Return Notes</label>
                                        <textarea name="ReturnRemarks" id="ReturnRemarks" class="form-control" rows="2" placeholder="Optional notes about the return...">{{ old('ReturnRemarks') }}</textarea>
                                        @error('ReturnRemarks')
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
                                        <label for="incident_type" class="form-label">Incident Type</label>
                                        <select name="incident_type" id="IncidentType" class="form-select" aria-label="Incident Type">
                                            <option value="">Select Type</option>
                                            <option value="Damage" {{ old('incident_type') == 'Damage' ? 'selected' : '' }}>Damage</option>
                                            <option value="Loss" {{ old('incident_type') == 'Loss' ? 'selected' : '' }}>Loss</option>
                                            <option value="Theft" {{ old('incident_type') == 'Theft' ? 'selected' : '' }}>Theft</option>
                                            <option value="Malfunction" {{ old('incident_type') == 'Malfunction' ? 'selected' : '' }}>Malfunction</option>
                                        </select>
                                        @error('incident_type')
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
                                        <select name="ResponsibleEmployeeID" id="ResponsibleEmployeeID" class="form-select" aria-label="Responsible Employee">
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
                                        <label for="estimated_cost" class="form-label">Est. Repair/Replacement Cost</label>
                                        <input type="number" name="estimated_cost" id="EstimatedCost" class="form-control"
                                            step="0.01" min="0" value="{{ old('estimated_cost') }}" placeholder="0.00">
                                        @error('estimated_cost')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-8 mb-3">
                                        <label for="incident_description" class="form-label">Incident Description</label>
                                        <textarea name="incident_description" id="IncidentDescription" class="form-control" rows="2"
                                            placeholder="Describe what happened...">{{ old('incident_description') }}</textarea>
                                        @error('incident_description')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="damage_photo" class="form-label">Photo Evidence</label>
                                        <input type="file" name="damage_photo" id="PhotoPath" class="form-control" accept="image/*">
                                        <small class="form-text text-muted">Upload photo of damage</small>
                                        @error('damage_photo')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="card-footer bg-white d-flex justify-content-end">
                            <a href="{{ route('equipment.returns.index') }}" class="btn btn-secondary me-3" aria-label="Cancel equipment return">
                                <i class="fas fa-times me-1" aria-hidden="true"></i> Cancel
                            </a>
                            <button type="submit" class="btn text-white" style="background-color: #87A96B;" aria-label="Process equipment return">
                                <i class="fas fa-check me-1" aria-hidden="true"></i> Process Return
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function toggleIncidentFields() {
            const condition = document.getElementById('Status').value;
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
            
            // Prevent decimal input for quantity returned
            const quantityInput = document.getElementById('QuantityReturned');
            if (quantityInput) {
                quantityInput.addEventListener('input', function(e) {
                    // Remove any decimal points and non-numeric characters except digits
                    let value = this.value.replace(/[^0-9]/g, '');
                    if (value !== this.value) {
                        this.value = value;
                    }
                });
                
                quantityInput.addEventListener('keydown', function(e) {
                    // Prevent decimal point, minus sign, and 'e' (scientific notation)
                    if (e.key === '.' || e.key === ',' || e.key === '-' || e.key === 'e' || e.key === 'E') {
                        e.preventDefault();
                    }
                });
                
                quantityInput.addEventListener('paste', function(e) {
                    e.preventDefault();
                    let paste = (e.clipboardData || window.clipboardData).getData('text');
                    // Only allow whole numbers
                    paste = paste.replace(/[^0-9]/g, '');
                    this.value = paste;
                });
            }
        });
    </script>
@endsection
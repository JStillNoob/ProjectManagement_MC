@extends('layouts.app')

@section('title', 'Create New Project')
@section('page-title', 'Create New Project')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">
                        <form id="createProjectForm" action="{{ route('projects.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <!-- Project Information Section -->
                            <h6 class="text-primary mb-3"><i class="fas fa-info-circle mr-2"></i>Project Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ProjectName">Project Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('ProjectName') is-invalid @enderror"
                                            id="ProjectName" name="ProjectName" value="{{ old('ProjectName') }}"
                                            placeholder="Enter project name" required>
                                        @error('ProjectName')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ClientID">Client</label>
                                        <select class="form-control @error('ClientID') is-invalid @enderror" id="ClientID"
                                            name="ClientID">
                                            <option value="">Select a client...</option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->ClientID }}" {{ old('ClientID') == $client->ClientID ? 'selected' : '' }}>
                                                    {{ $client->ClientName }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('ClientID')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="ProjectDescription">Project Description</label>
                                <textarea class="form-control @error('ProjectDescription') is-invalid @enderror"
                                    id="ProjectDescription" name="ProjectDescription" rows="3"
                                    placeholder="Enter project description...">{{ old('ProjectDescription') }}</textarea>
                                @error('ProjectDescription')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-4">

                            <!-- Timeline Section -->
                            <h6 class="text-primary mb-3"><i class="fas fa-calendar-alt mr-2"></i>Project Timeline</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="EstimatedAccomplishDays">Estimated Accomplish Days <span
                                                class="text-danger">*</span></label>
                                        <input type="number"
                                            class="form-control @error('EstimatedAccomplishDays') is-invalid @enderror"
                                            id="EstimatedAccomplishDays" name="EstimatedAccomplishDays"
                                            value="{{ old('EstimatedAccomplishDays') }}" min="1" step="1"
                                            placeholder="e.g., 30" required>
                                        <small class="form-text text-muted">Number of days from NTP start date to complete
                                            the project</small>
                                        @error('EstimatedAccomplishDays')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="WarrantyDays">Warranty Days <span class="text-danger">*</span></label>
                                        <input type="number"
                                            class="form-control @error('WarrantyDays') is-invalid @enderror"
                                            id="WarrantyDays" name="WarrantyDays" value="{{ old('WarrantyDays', 0) }}"
                                            min="0" step="1" placeholder="e.g., 365" required>
                                        <small class="form-text text-muted">Number of days warranty will last after project
                                            ends</small>
                                        @error('WarrantyDays')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Project Documents Section -->
                            <h6 class="text-primary mb-3"><i class="fas fa-file-alt mr-2"></i>Project Documents</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Blueprint">Blueprint</label>
                                        <div class="custom-file">
                                            <input type="file"
                                                class="custom-file-input @error('Blueprint') is-invalid @enderror"
                                                id="Blueprint" name="Blueprint" accept=".pdf,.jpg,.jpeg,.png,.dwg">
                                            <label class="custom-file-label" for="Blueprint">Choose file...</label>
                                        </div>
                                        <small class="form-text text-muted">Accepted formats: PDF, JPG, PNG, DWG (Max:
                                            10MB)</small>
                                        @error('Blueprint')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="FloorPlan">Floor Plan</label>
                                        <div class="custom-file">
                                            <input type="file"
                                                class="custom-file-input @error('FloorPlan') is-invalid @enderror"
                                                id="FloorPlan" name="FloorPlan" accept=".pdf,.jpg,.jpeg,.png,.dwg">
                                            <label class="custom-file-label" for="FloorPlan">Choose file...</label>
                                        </div>
                                        <small class="form-text text-muted">Accepted formats: PDF, JPG, PNG, DWG (Max:
                                            10MB)</small>
                                        @error('FloorPlan')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Location Section -->
                            <h6 class="text-primary mb-3"><i class="fas fa-map-marker-alt mr-2"></i>Project Location</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="StreetAddress">Street Address</label>
                                        <input type="text" class="form-control @error('StreetAddress') is-invalid @enderror"
                                            id="StreetAddress" name="StreetAddress" value="{{ old('StreetAddress') }}"
                                            placeholder="Enter street address">
                                        @error('StreetAddress')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Barangay">Barangay</label>
                                        <input type="text" class="form-control @error('Barangay') is-invalid @enderror"
                                            id="Barangay" name="Barangay" value="{{ old('Barangay') }}"
                                            placeholder="Enter barangay">
                                        @error('Barangay')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="City">City</label>
                                        <input type="text" class="form-control @error('City') is-invalid @enderror"
                                            id="City" name="City" value="{{ old('City') }}" placeholder="Enter city">
                                        @error('City')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="StateProvince">State/Province</label>
                                        <input type="text" class="form-control @error('StateProvince') is-invalid @enderror"
                                            id="StateProvince" name="StateProvince" value="{{ old('StateProvince') }}"
                                            placeholder="Enter state/province">
                                        @error('StateProvince')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="ZipCode">Zip Code</label>
                                        <input type="text" class="form-control @error('ZipCode') is-invalid @enderror"
                                            id="ZipCode" name="ZipCode" value="{{ old('ZipCode') }}"
                                            placeholder="Enter zip code">
                                        @error('ZipCode')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('ProdHead.projects') }}" class="btn btn-secondary mr-2">
                                    <i class="fas fa-times mr-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn text-white"
                                    style="background: #87A96B; border-color: #87A96B;">
                                    <i class="fas fa-arrow-right mr-1"></i> Next: Add Milestones
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card-header {
            border-radius: 0 !important;
        }

        .card-body h6 {
            color: #87A96B !important;
            font-weight: 600;
        }

        .card-body hr {
            border-color: #e9ecef;
        }

        /* Form input styling */
        .form-control {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 0.5rem 0.75rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-control:focus {
            border-color: #87A96B;
            box-shadow: 0 0 0 0.2rem rgba(135, 169, 107, 0.25);
        }

        .form-control::placeholder {
            color: #adb5bd;
            font-size: 0.9rem;
        }

        label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.3rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function () {
            // Custom file input label update
            $('.custom-file-input').on('change', function () {
                const fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').html(fileName || 'Choose file...');
            });

            // Form validation
            $('form').on('submit', function (e) {
                const estimatedDays = parseInt($('#EstimatedAccomplishDays').val());

                if (!estimatedDays || estimatedDays < 1) {
                    e.preventDefault();
                    alert('Estimated Accomplish Days must be at least 1 day.');
                    $('#EstimatedAccomplishDays').focus();
                    return false;
                }
            });
        });
    </script>
@endpush
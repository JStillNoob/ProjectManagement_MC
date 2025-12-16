@extends('layouts.app')

@section('title', 'Add New Employee')
@section('page-title', 'Add New Employee')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-plus mr-2"></i>
                        Add New Employee
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Personal Information Section -->
                        <h5 class="mb-3 text-primary"><i class="fas fa-user mr-2"></i>Personal Information</h5>
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="first_name">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                        id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="middle_name">Middle Name</label>
                                    <input type="text" class="form-control @error('middle_name') is-invalid @enderror"
                                        id="middle_name" name="middle_name" value="{{ old('middle_name') }}">
                                    @error('middle_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                        id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="birthday">Birthday <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('birthday') is-invalid @enderror"
                                        id="birthday" name="birthday" value="{{ old('birthday') }}" 
                                        max="{{ now()->subYears(18)->format('Y-m-d') }}" required>
                                    @error('birthday')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="contact_number">Contact Number</label>
                                    <input type="text" class="form-control @error('contact_number') is-invalid @enderror"
                                        id="contact_number" name="contact_number" value="{{ old('contact_number') }}" placeholder="e.g., 09123456789">
                                    @error('contact_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="image">Employee Photo</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('image') is-invalid @enderror"
                                            id="image" name="image" accept="image/*">
                                        <label class="custom-file-label" for="image">Choose file</label>
                                    </div>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Max: 2MB (JPEG, PNG, JPG, GIF)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Address Information Section -->
                        <h5 class="mb-3 mt-4 text-primary"><i class="fas fa-map-marker-alt mr-2"></i>Address Information</h5>
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="house_number">House/Unit Number</label>
                                    <input type="text" class="form-control @error('house_number') is-invalid @enderror"
                                        id="house_number" name="house_number" value="{{ old('house_number') }}" placeholder="e.g., 123">
                                    @error('house_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="street">Street <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('street') is-invalid @enderror"
                                        id="street" name="street" value="{{ old('street') }}" placeholder="e.g., Main Street" required>
                                    @error('street')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="barangay">Barangay <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('barangay') is-invalid @enderror"
                                        id="barangay" name="barangay" value="{{ old('barangay') }}" placeholder="e.g., Barangay San Jose" required>
                                    @error('barangay')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city">City/Municipality <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror"
                                        id="city" name="city" value="{{ old('city') }}" placeholder="e.g., Manila" required>
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="province">Province <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('province') is-invalid @enderror"
                                        id="province" name="province" value="{{ old('province') }}" placeholder="e.g., Metro Manila" required>
                                    @error('province')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="postal_code">Postal Code</label>
                                    <input type="text" class="form-control @error('postal_code') is-invalid @enderror"
                                        id="postal_code" name="postal_code" value="{{ old('postal_code') }}" placeholder="e.g., 1000">
                                    @error('postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Employment Information Section -->
                        <h5 class="mb-3 mt-4 text-primary"><i class="fas fa-briefcase mr-2"></i>Employment Information</h5>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="PositionID">Position <span class="text-danger">*</span></label>
                                    <select class="form-control select2 @error('PositionID') is-invalid @enderror" 
                                            id="PositionID" name="PositionID" required style="width: 100%;">
                                        <option value="">Select Position</option>
                                        @foreach(\App\Models\Position::all() as $position)
                                            <option value="{{ $position->PositionID }}" 
                                                    {{ old('PositionID') == $position->PositionID ? 'selected' : '' }}>
                                                {{ $position->PositionName }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('PositionID')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                        id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="mt-4">
                        <div class="form-group mb-0 text-right">
                            <a href="{{ route('employees.index') }}" class="btn btn-secondary mr-2">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success"
                                    style="background-color: #87A96B !important; border-color: #87A96B !important;">
                                <i class="fas fa-save"></i> Create Employee
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize Select2 for Position dropdown
                $('#PositionID').select2({
                    theme: 'bootstrap4',
                    placeholder: 'Select Position',
                    allowClear: true
                });
            });

            // File input label update
            document.getElementById('image').addEventListener('change', function (e) {
                var fileName = e.target.files[0] ? e.target.files[0].name : 'Choose file';
                e.target.nextElementSibling.textContent = fileName;
            });
        </script>
    @endpush
@endsection

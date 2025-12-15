@extends('layouts.app')

@section('title', 'Edit Regular Employee')
@section('page-title', 'Edit Regular Employee')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit text-success mr-2"></i>
                        Edit Regular Employee: {{ $regular_employee->full_name }}
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('regular-employees.update', $regular_employee) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Personal Information -->
                        <h5 class="text-primary mb-3"><i class="fas fa-user mr-2"></i>Personal Information</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="first_name">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                        id="first_name" name="first_name"
                                        value="{{ old('first_name', $regular_employee->first_name) }}" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="middle_name">Middle Name</label>
                                    <input type="text" class="form-control @error('middle_name') is-invalid @enderror"
                                        id="middle_name" name="middle_name"
                                        value="{{ old('middle_name', $regular_employee->middle_name) }}">
                                    @error('middle_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                        id="last_name" name="last_name"
                                        value="{{ old('last_name', $regular_employee->last_name) }}" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="birthday">Birthday <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('birthday') is-invalid @enderror"
                                        id="birthday" name="birthday"
                                        value="{{ old('birthday', $regular_employee->birthday ? $regular_employee->birthday->format('Y-m-d') : '') }}"
                                        max="{{ now()->subYears(18)->format('Y-m-d') }}" required>
                                    @error('birthday')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <h5 class="text-primary mb-3 mt-4"><i class="fas fa-map-marker-alt mr-2"></i>Address Information
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="house_number">House Number</label>
                                    <input type="text" class="form-control @error('house_number') is-invalid @enderror"
                                        id="house_number" name="house_number"
                                        value="{{ old('house_number', $regular_employee->house_number) }}">
                                    @error('house_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="street">Street</label>
                                    <input type="text" class="form-control @error('street') is-invalid @enderror"
                                        id="street" name="street" value="{{ old('street', $regular_employee->street) }}">
                                    @error('street')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="barangay">Barangay <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('barangay') is-invalid @enderror"
                                        id="barangay" name="barangay"
                                        value="{{ old('barangay', $regular_employee->barangay) }}" required>
                                    @error('barangay')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="city">City <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" id="city"
                                        name="city" value="{{ old('city', $regular_employee->city) }}" required>
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="province">Province <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('province') is-invalid @enderror"
                                        id="province" name="province"
                                        value="{{ old('province', $regular_employee->province) }}" required>
                                    @error('province')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="postal_code">Postal Code</label>
                                    <input type="text" class="form-control @error('postal_code') is-invalid @enderror"
                                        id="postal_code" name="postal_code"
                                        value="{{ old('postal_code', $regular_employee->postal_code) }}">
                                    @error('postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Employment Information -->
                        <h5 class="text-primary mb-3 mt-4"><i class="fas fa-briefcase mr-2"></i>Employment Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="PositionID">Position <span class="text-danger">*</span></label>
                                    <select class="form-control @error('PositionID') is-invalid @enderror" id="PositionID"
                                        name="PositionID" required>
                                        <option value="">Select Position...</option>
                                        @foreach(\App\Models\Position::active()->get() as $position)
                                            <option value="{{ $position->PositionID }}" {{ old('PositionID', $regular_employee->PositionID) == $position->PositionID ? 'selected' : '' }}>
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
                                        id="start_date" name="start_date"
                                        value="{{ old('start_date', $regular_employee->start_date ? $regular_employee->start_date->format('Y-m-d') : '') }}"
                                        required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Salary Information -->
                        <h5 class="text-primary mb-3 mt-4"><i class="fas fa-money-bill-wave mr-2"></i>Salary Information
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="base_salary">Base Salary <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('base_salary') is-invalid @enderror"
                                        id="base_salary" name="base_salary"
                                        value="{{ old('base_salary', $regular_employee->base_salary) }}" step="0.01" min="0"
                                        required readonly>
                                    @error('base_salary')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Salary is automatically set based on the selected
                                        position.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <h5 class="text-info mb-3 mt-4"><i class="fas fa-phone mr-2"></i>Contact Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_number">Contact Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('contact_number') is-invalid @enderror"
                                        id="contact_number" name="contact_number"
                                        value="{{ old('contact_number', $regular_employee->contact_number) }}" required>
                                    @error('contact_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Photo -->
                        <div class="form-group">
                            <label for="image">Employee Photo</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('image') is-invalid @enderror" id="image"
                                    name="image" accept="image/*">
                                <label class="custom-file-label" for="image">Choose file</label>
                            </div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Max file size: 2MB. Allowed formats: JPEG, PNG, JPG,
                                GIF</small>

                            @if($regular_employee->image_name)
                                <div class="mt-2">
                                    <small class="text-muted">Current photo:</small>
                                    <img src="{{ asset('storage/' . $regular_employee->image_name) }}" alt="Current photo"
                                        class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                                </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Update Regular Employee
                            </button>
                            <a href="{{ route('regular-employees.show', $regular_employee) }}" class="btn btn-secondary">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            <a href="{{ route('regular-employees.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle text-success mr-2"></i>
                        Regular Employee Details
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <h6><i class="fas fa-user-tie mr-2"></i>Regular Employment</h6>
                        <ul class="list-unstyled mb-0">
                            <li><i class="fas fa-calendar text-success mr-1"></i> Fixed Schedule</li>
                            <li><i class="fas fa-handshake text-success mr-1"></i> Full-time Work</li>
                            <li><i class="fas fa-gift text-success mr-1"></i> Benefits Eligible</li>
                            <li><i class="fas fa-money-bill-wave text-success mr-1"></i> Monthly Salary</li>
                        </ul>
                    </div>

                    <div class="alert alert-info">
                        <h6><i class="fas fa-check-circle mr-2"></i>Advantages</h6>
                        <ul class="list-unstyled mb-0">
                            <li><i class="fas fa-shield-alt text-info mr-1"></i> Job Security</li>
                            <li><i class="fas fa-gift text-info mr-1"></i> Company Benefits</li>
                            <li><i class="fas fa-chart-line text-info mr-1"></i> Career Growth</li>
                            <li><i class="fas fa-users text-info mr-1"></i> Team Integration</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // File input label update
            document.getElementById('image').addEventListener('change', function (e) {
                var fileName = e.target.files[0] ? e.target.files[0].name : 'Choose file';
                e.target.nextElementSibling.textContent = fileName;
            });


            // Auto-populate salary based on position
            document.getElementById('PositionID').addEventListener('change', function () {
                const positionId = this.value;
                const salaryInput = document.getElementById('base_salary');

                if (positionId) {
                    // Fetch position salary via AJAX
                    fetch(`/api/position-salary/${positionId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.salary) {
                                salaryInput.value = data.salary;
                            } else {
                                salaryInput.value = '';
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching position salary:', error);
                            salaryInput.value = '';
                        });
                } else {
                    salaryInput.value = '';
                }
            });
        </script>
    @endpush
@endsection
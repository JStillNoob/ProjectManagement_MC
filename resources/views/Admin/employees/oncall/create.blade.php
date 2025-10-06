@extends('layouts.app')

@section('title', 'Add On-Call Employee')
@section('page-title', 'Add On-Call Employee')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-phone text-info mr-2"></i>
                        On-Call Employee Information
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('oncall-employees.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Personal Information -->
                        <h5 class="text-primary mb-3"><i class="fas fa-user mr-2"></i>Personal Information</h5>
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="birthday">Birthday <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('birthday') is-invalid @enderror"
                                        id="birthday" name="birthday" value="{{ old('birthday') }}" required>
                                    @error('birthday')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="age">Age <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('age') is-invalid @enderror" id="age"
                                        name="age" value="{{ old('age') }}" min="18" max="100" required>
                                    @error('age')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address">Address <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address"
                                name="address" rows="3" required>{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Employment Information -->
                        <h5 class="text-primary mb-3 mt-4"><i class="fas fa-briefcase mr-2"></i>Employment Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="position">Position <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('position') is-invalid @enderror"
                                        id="position" name="position" value="{{ old('position') }}" required>
                                    @error('position')
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

                        <!-- Availability -->
                        <h5 class="text-warning mb-3 mt-4"><i class="fas fa-clock mr-2"></i>Availability</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="availability">Availability <span class="text-danger">*</span></label>
                                    <select class="form-control @error('availability') is-invalid @enderror" id="availability" name="availability" required>
                                        <option value="">Select Availability</option>
                                        <option value="Weekdays Only" {{ old('availability') == 'Weekdays Only' ? 'selected' : '' }}>Weekdays Only</option>
                                        <option value="Weekends Only" {{ old('availability') == 'Weekends Only' ? 'selected' : '' }}>Weekends Only</option>
                                        <option value="Evenings" {{ old('availability') == 'Evenings' ? 'selected' : '' }}>Evenings</option>
                                        <option value="Flexible" {{ old('availability') == 'Flexible' ? 'selected' : '' }}>Flexible</option>
                                        <option value="24/7" {{ old('availability') == '24/7' ? 'selected' : '' }}>24/7</option>
                                    </select>
                                    @error('availability')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                        id="contact_number" name="contact_number" value="{{ old('contact_number') }}" required>
                                    @error('contact_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <h5 class="text-danger mb-3 mt-4"><i class="fas fa-exclamation-triangle mr-2"></i>Emergency Contact</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emergency_contact">Emergency Contact Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('emergency_contact') is-invalid @enderror"
                                        id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact') }}" required>
                                    @error('emergency_contact')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emergency_phone">Emergency Contact Phone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('emergency_phone') is-invalid @enderror"
                                        id="emergency_phone" name="emergency_phone" value="{{ old('emergency_phone') }}" required>
                                    @error('emergency_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Photo -->
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
                            <small class="form-text text-muted">Max file size: 2MB. Allowed formats: JPEG, PNG, JPG, GIF</small>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-save"></i> Create On-Call Employee
                            </button>
                            <a href="{{ route('oncall-employees.index') }}" class="btn btn-secondary">
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
                        <i class="fas fa-info-circle text-info mr-2"></i>
                        On-Call Employee Details
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-phone mr-2"></i>On-Call Employment</h6>
                        <ul class="list-unstyled mb-0">
                            <li><i class="fas fa-clock text-warning mr-1"></i> Flexible Schedule</li>
                            <li><i class="fas fa-phone text-primary mr-1"></i> Called as Needed</li>
                            <li><i class="fas fa-calendar text-info mr-1"></i> No Fixed Hours</li>
                            <li><i class="fas fa-handshake text-success mr-1"></i> Project-Based Work</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-success">
                        <h6><i class="fas fa-check-circle mr-2"></i>Advantages</h6>
                        <ul class="list-unstyled mb-0">
                            <li><i class="fas fa-calendar-alt text-success mr-1"></i> Flexible Schedule</li>
                            <li><i class="fas fa-handshake text-success mr-1"></i> Project-Based Work</li>
                            <li><i class="fas fa-clock text-success mr-1"></i> Work When Available</li>
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

            // Age calculation based on birthday
            document.getElementById('birthday').addEventListener('change', function () {
                const birthday = new Date(this.value);
                const today = new Date();
                const age = today.getFullYear() - birthday.getFullYear();
                const monthDiff = today.getMonth() - birthday.getMonth();

                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthday.getDate())) {
                    age--;
                }

                if (age >= 18 && age <= 100) {
                    document.getElementById('age').value = age;
                }
            });
        </script>
    @endpush
@endsection
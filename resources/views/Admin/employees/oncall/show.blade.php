@extends('layouts.app')

@section('title', 'On-Call Employee Profile')
@section('page-title', 'On-Call Employee Profile')

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Employee Card -->
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle" 
                         src="{{ $oncall_employee->image_path }}" 
                         alt="{{ $oncall_employee->full_name }}">
                </div>

                <h3 class="profile-username text-center">{{ $oncall_employee->full_name }}</h3>
                @php
                    $position = $oncall_employee->relationLoaded('position') 
                        ? $oncall_employee->getRelation('position') 
                        : $oncall_employee->position()->first();
                @endphp
                <p class="text-muted text-center">{{ $position ? $position->PositionName : 'N/A' }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Employee ID</b> <a class="float-right">{{ $oncall_employee->id }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Status</b> 
                        <span class="float-right">
                            <span class="badge badge-{{ $oncall_employee->status == 'Active' ? 'success' : 'danger' }}">
                                {{ $oncall_employee->status }}
                            </span>
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>Start Date</b> <a class="float-right">{{ $oncall_employee->formatted_start_date }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Age</b> <a class="float-right">{{ $oncall_employee->age ? $oncall_employee->age . ' years old' : 'N/A' }}</a>
                    </li>
                </ul>

                <div class="row">
                    <div class="col-6">
                        <a href="{{ route('oncall-employees.edit', $oncall_employee) }}" class="btn btn-warning btn-block">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('oncall-employees.index') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Employee Details -->
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link active" href="#personal" data-toggle="tab">Personal Information</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#employment" data-toggle="tab">Employment Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#qrcode" data-toggle="tab">QR Code</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Personal Information Tab -->
                    <div class="active tab-pane" id="personal">
                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-user mr-1"></i> First Name</strong>
                                <p class="text-muted">{{ $oncall_employee->first_name }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-user mr-1"></i> Last Name</strong>
                                <p class="text-muted">{{ $oncall_employee->last_name }}</p>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-user mr-1"></i> Middle Name</strong>
                                <p class="text-muted">{{ $oncall_employee->middle_name ?: 'Not provided' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-birthday-cake mr-1"></i> Birthday</strong>
                                <p class="text-muted">{{ $oncall_employee->formatted_birthday }}</p>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-calendar mr-1"></i> Age</strong>
                                <p class="text-muted">{{ $oncall_employee->age ? $oncall_employee->age . ' years old' : 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-map-marker-alt mr-1"></i> Address</strong>
                                <p class="text-muted">{{ $oncall_employee->address }}</p>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-phone mr-1"></i> Contact Number</strong>
                                <p class="text-muted">{{ $oncall_employee->contact_number ?: 'Not provided' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-clock mr-1"></i> Date Created</strong>
                                <p class="text-muted">{{ $oncall_employee->formatted_created_at }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Employment Details Tab -->
                    <div class="tab-pane" id="employment">
                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-briefcase mr-1"></i> Position</strong>
                                @php
                                    $position = $oncall_employee->relationLoaded('position') 
                                        ? $oncall_employee->getRelation('position') 
                                        : $oncall_employee->position()->first();
                                @endphp
                                <p class="text-muted">{{ $position ? $position->PositionName : 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-dollar-sign mr-1"></i> Base Salary</strong>
                                <p class="text-muted">{{ $oncall_employee->formatted_salary }}</p>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-calendar-check mr-1"></i> Start Date</strong>
                                <p class="text-muted">{{ $oncall_employee->formatted_start_date }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-toggle-on mr-1"></i> Status</strong>
                                <p class="text-muted">
                                    <span class="badge badge-{{ $oncall_employee->status == 'Active' ? 'success' : 'danger' }}">
                                        {{ $oncall_employee->status }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-calendar-alt mr-1"></i> Employment Duration</strong>
                                <p class="text-muted">
                                    @php
                                        if ($oncall_employee->start_date) {
                                            $startDate = $oncall_employee->start_date;
                                            $today = now();
                                            $duration = $startDate->diffInDays($today);
                                            $years = floor($duration / 365);
                                            $months = floor(($duration % 365) / 30);
                                            $days = $duration % 30;
                                            echo $years . ' year(s), ' . $months . ' month(s), ' . $days . ' day(s)';
                                        } else {
                                            echo 'N/A';
                                        }
                                    @endphp
                                </p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-dollar-sign mr-1"></i> Base Salary</strong>
                                <p class="text-muted">{{ $oncall_employee->formatted_salary }}</p>
                            </div>
                        </div>
                        <hr>

                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle"></i> On-Call Employee Information</h5>
                            <p class="mb-0">This employee is classified as on-call, meaning they are called to work as needed rather than having a fixed schedule. They may be eligible for different benefits and compensation structures compared to regular full-time employees.</p>
                        </div>

                        @if($oncall_employee->image_name)
                        <div class="row">
                            <div class="col-12">
                                <strong><i class="fas fa-image mr-1"></i> Employee Photo</strong>
                                <div class="mt-2">
                                    <img src="{{ $oncall_employee->image_path }}" alt="{{ $oncall_employee->full_name }}" 
                                         class="img-thumbnail" style="max-width: 200px;">
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- QR Code Tab -->
                    <div class="tab-pane" id="qrcode">
                        <div class="row">
                            <div class="col-12 text-center">
                                <h4><i class="fas fa-qrcode mr-2"></i>Employee QR Code</h4>
                                <hr>
                                
                                @if($oncall_employee->qr_code)
                                    <div class="mb-4">
                                        <!-- QR Code Display -->
                                        <div id="qr-code-display" style="width: 300px; height: 300px; margin: 0 auto; border: 2px solid #dee2e6; border-radius: 8px; background: #fff; display: flex; align-items: center; justify-content: center;">
                                            <img id="qr-code-image" src="{{ $oncall_employee->generateQrCodeImageUrl(300) }}" alt="Employee QR Code" class="img-fluid" style="max-width: 100%; height: auto; display: none;">
                                            <div id="qr-code-placeholder" class="text-center">
                                                <i class="fas fa-qrcode fa-4x text-muted mb-3"></i>
                                                <div class="text-muted">QR Code: {{ $oncall_employee->qr_code }}</div>
                                                <div class="mt-2">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="showQrCodeImage()">
                                                        <i class="fas fa-eye"></i> Show QR Code
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <h5><i class="fas fa-info-circle"></i> QR Code Information</h5>
                                        <p class="mb-2">This QR code contains the following employee information:</p>
                                        <ul class="list-unstyled text-left">
                                            <li><strong>Name:</strong> {{ $oncall_employee->full_name }}</li>
                                            <li><strong>Position:</strong> 
                                                @php
                                                    $position = $oncall_employee->relationLoaded('position') 
                                                        ? $oncall_employee->getRelation('position') 
                                                        : $oncall_employee->position()->first();
                                                @endphp
                                                {{ $position ? $position->PositionName : 'N/A' }}
                                            </li>
                                            <li><strong>Status:</strong> {{ $oncall_employee->status }}</li>
                                            <li><strong>Start Date:</strong> {{ $oncall_employee->formatted_start_date }}</li>
                                            <li><strong>QR Code:</strong> {{ $oncall_employee->qr_code }}</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <button class="btn btn-primary" onclick="copyQrCode('{{ $oncall_employee->qr_code }}')">
                                            <i class="fas fa-copy"></i> Copy QR Code
                                        </button>
                                        <button class="btn btn-success" onclick="generateQrImage('{{ $oncall_employee->qr_code }}')">
                                            <i class="fas fa-download"></i> Generate QR Image
                                        </button>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6>QR Code Data (for manual QR generation)</h6>
                                            </div>
                                            <div class="card-body">
                                                <code style="word-break: break-all; font-size: 12px;">{{ $oncall_employee->qr_code }}</code>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <h5><i class="fas fa-exclamation-triangle"></i> No QR Code Available</h5>
                                        <p>This employee doesn't have a QR code generated yet. Please contact the administrator.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showQrCodeImage() {
    const qrImage = document.getElementById('qr-code-image');
    const placeholder = document.getElementById('qr-code-placeholder');
    
    if (qrImage && placeholder) {
        qrImage.style.display = 'block';
        placeholder.style.display = 'none';
    }
}

function copyQrCode(qrCode) {
    navigator.clipboard.writeText(qrCode).then(function() {
        alert('QR Code copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = qrCode;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('QR Code copied to clipboard!');
    });
}

function generateQrImage(qrCode) {
    // Generate QR code image using online API
    const qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' + encodeURIComponent(qrCode);
    
    // Create a temporary link to download the image
    const link = document.createElement('a');
    link.href = qrImageUrl;
    link.download = 'employee_qr_code_' + qrCode.substring(0, 8) + '.png';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Also update the display to show the generated QR code
    const qrDisplay = document.getElementById('qr-code-display');
    if (qrDisplay) {
        qrDisplay.innerHTML = '<img src="' + qrImageUrl + '" alt="Employee QR Code" class="img-fluid" style="max-width: 100%; height: auto;">';
    }
}

// Auto-show QR code when tab is clicked
$(document).ready(function() {
    $('a[href="#qrcode"]').on('click', function() {
        setTimeout(function() {
            showQrCodeImage();
        }, 100);
    });
});
</script>
@endpush
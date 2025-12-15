@extends('layouts.app')

@section('title', 'QR Code Generator')
@section('page-title', 'QR Code Generator')

@push('styles')
<style>
    .qr-generator-container {
        max-width: 600px;
        margin: 0 auto;
    }
    .qr-display {
        text-align: center;
        padding: 20px;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        background: #f8f9fa;
        margin: 20px 0;
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .qr-image {
        max-width: 100%;
        height: auto;
        border: 2px solid #dee2e6;
        border-radius: 8px;
    }
    .generated-code-display {
        background: #e9ecef;
        padding: 10px;
        border-radius: 4px;
        font-family: monospace;
        word-break: break-all;
        margin: 10px 0;
    }
    .form-control:disabled {
        background-color: #f8f9fa;
        opacity: 1;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-qrcode mr-1"></i> QR Code Generator
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('attendance.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i> Back to Attendance
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="qr-generator-container">
                        <!-- QR Generator Form -->
                        <div id="qr-generator" class="qr-generator">
                            <div class="form-group">
                                <label for="studentName">Student/Employee Name:</label>
                                <input type="text" id="studentName" class="form-control" placeholder="Enter name">
                            </div>
                            
                            <div class="form-group">
                                <label for="studentCourse">Course/Position:</label>
                                <input type="text" id="studentCourse" class="form-control" placeholder="Enter course or position">
                            </div>
                            
                            <div class="form-group">
                                <label for="generatedCode">Generated Code:</label>
                                <input type="text" id="generatedCode" class="form-control" readonly>
                            </div>
                            
                            <div class="text-center">
                                <button type="button" class="btn btn-success btn-lg" onclick="generateQrCode()">
                                    <i class="fas fa-qrcode mr-2"></i> Generate QR Code
                                </button>
                            </div>
                        </div>
                        
                        <!-- QR Display Area -->
                        <div id="qr-con" class="qr-con" style="display: none;">
                            <div class="qr-display">
                                <div id="qr-placeholder">
                                    <i class="fas fa-qrcode fa-4x text-muted mb-3"></i>
                                    <div class="text-muted">QR Code will appear here</div>
                                </div>
                                <img id="qrImg" class="qr-image" style="display: none;" alt="Generated QR Code">
                            </div>
                            
                            <div class="generated-code-display">
                                <strong>Generated Code:</strong> <span id="displayCode"></span>
                            </div>
                            
                            <div class="text-center mt-3">
                                <button type="button" class="btn btn-primary" onclick="copyGeneratedCode()">
                                    <i class="fas fa-copy mr-1"></i> Copy Code
                                </button>
                                <button type="button" class="btn btn-success" onclick="downloadQrCode()">
                                    <i class="fas fa-download mr-1"></i> Download QR Code
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="resetGenerator()">
                                    <i class="fas fa-redo mr-1"></i> Generate New
                                </button>
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
// Generate random alphanumeric code
function generateRandomCode(length) {
    const characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let result = '';
    for (let i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    return result;
}

// Generate UUID-like code (like your other project)
function generateUuidCode() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        const r = Math.random() * 16 | 0;
        const v = c == 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
}

// Main QR code generation function
function generateQrCode() {
    const qrImg = document.getElementById('qrImg');
    const qrPlaceholder = document.getElementById('qr-placeholder');
    const displayCode = document.getElementById('displayCode');
    
    // Generate random code (10 characters like your example)
    let text = generateRandomCode(10);
    
    // Alternative: Generate UUID-like code
    // let text = generateUuidCode();
    
    $("#generatedCode").val(text);
    displayCode.textContent = text;

    if (text === "") {
        alert("Please enter text to generate a QR code.");
        return;
    } else {
        const apiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(text)}`;

        // Show loading state
        qrPlaceholder.innerHTML = '<i class="fas fa-spinner fa-spin fa-4x text-primary mb-3"></i><div class="text-muted">Generating QR Code...</div>';
        
        // Load the QR code image
        qrImg.onload = function() {
            qrPlaceholder.style.display = 'none';
            qrImg.style.display = 'block';
        };
        
        qrImg.onerror = function() {
            qrPlaceholder.innerHTML = '<i class="fas fa-exclamation-triangle fa-4x text-danger mb-3"></i><div class="text-danger">Failed to generate QR Code</div>';
        };
        
        qrImg.src = apiUrl;
        
        // Disable form inputs and show QR display
        document.getElementById('studentName').style.pointerEvents = 'none';
        document.getElementById('studentCourse').style.pointerEvents = 'none';
        document.getElementById('studentName').disabled = true;
        document.getElementById('studentCourse').disabled = true;
        
        document.getElementById('qr-con').style.display = '';
        document.getElementById('qr-generator').style.display = 'none';
    }
}

// Copy generated code to clipboard
function copyGeneratedCode() {
    const code = document.getElementById('generatedCode').value;
    navigator.clipboard.writeText(code).then(function() {
        alert('Code copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = code;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('Code copied to clipboard!');
    });
}

// Download QR code image
function downloadQrCode() {
    const qrImg = document.getElementById('qrImg');
    const code = document.getElementById('generatedCode').value;
    
    if (qrImg.src) {
        const link = document.createElement('a');
        link.href = qrImg.src;
        link.download = `qr_code_${code.substring(0, 8)}.png`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

// Reset generator to create new QR code
function resetGenerator() {
    // Re-enable form inputs
    document.getElementById('studentName').style.pointerEvents = 'auto';
    document.getElementById('studentCourse').style.pointerEvents = 'auto';
    document.getElementById('studentName').disabled = false;
    document.getElementById('studentCourse').disabled = false;
    
    // Clear form
    document.getElementById('studentName').value = '';
    document.getElementById('studentCourse').value = '';
    document.getElementById('generatedCode').value = '';
    
    // Hide QR display and show generator
    document.getElementById('qr-con').style.display = 'none';
    document.getElementById('qr-generator').style.display = '';
    
    // Reset QR image
    const qrImg = document.getElementById('qrImg');
    const qrPlaceholder = document.getElementById('qr-placeholder');
    qrImg.style.display = 'none';
    qrPlaceholder.style.display = 'block';
    qrPlaceholder.innerHTML = '<i class="fas fa-qrcode fa-4x text-muted mb-3"></i><div class="text-muted">QR Code will appear here</div>';
}

// Auto-generate code when page loads (optional)
$(document).ready(function() {
    // You can uncomment this to auto-generate a code on page load
    // const autoCode = generateRandomCode(10);
    // $("#generatedCode").val(autoCode);
});
</script>
@endpush













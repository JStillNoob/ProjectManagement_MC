@extends('layouts.app')

@section('title', 'Employee QR Codes')
@section('page-title', 'Employee QR Codes')

@push('styles')
<style>
    .qr-code-container {
        text-align: center;
        padding: 20px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        margin-bottom: 20px;
        background: #f8f9fa;
    }
    .qr-code-placeholder {
        width: 200px;
        height: 200px;
        background: #fff;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 14px;
        color: #6c757d;
    }
    .employee-info {
        margin-top: 15px;
    }
    .employee-name {
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 5px;
    }
    .employee-position {
        color: #6c757d;
        font-size: 14px;
    }
    .qr-data {
        font-size: 12px;
        color: #6c757d;
        word-break: break-all;
        margin-top: 10px;
        padding: 10px;
        background: #fff;
        border-radius: 4px;
        border: 1px solid #dee2e6;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-qrcode mr-1"></i> Employee QR Codes for Attendance
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('attendance.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i> Back to Attendance
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($employees as $employee)
                        <div class="col-md-4 col-lg-3 mb-4">
                            <div class="qr-code-container">
                                <div class="qr-code-placeholder">
                                    <div>
                                        <i class="fas fa-qrcode fa-3x mb-2"></i>
                                        <div>QR Code</div>
                                        <div>ID: {{ $employee->id }}</div>
                                    </div>
                                </div>
                                
                                <div class="employee-info">
                                    <div class="employee-name">{{ $employee->full_name }}</div>
                                    <div class="employee-position">{{ $employee->position ?? 'No Position' }}</div>
                                </div>
                                
                                <div class="qr-data">
                                    <strong>QR Data:</strong><br>
                                    {{ $employee->qr_code_data }}
                                </div>
                                
                                <div class="mt-3">
                                    <button class="btn btn-sm btn-outline-primary" onclick="copyQrData('{{ $employee->qr_code_data }}')">
                                        <i class="fas fa-copy mr-1"></i> Copy QR Data
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                No employees found. Please add employees first.
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyQrData(qrData) {
        navigator.clipboard.writeText(qrData).then(function() {
            alert('QR Code data copied to clipboard!');
        }, function(err) {
            console.error('Could not copy text: ', err);
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = qrData;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            alert('QR Code data copied to clipboard!');
        });
    }
</script>
@endpush













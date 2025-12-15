<!-- QR Code Modal -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" role="dialog" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="qrCodeModalLabel">
                    <i class="fas fa-qrcode mr-2"></i>Employee QR Code
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <h6 class="text-muted">Take a pic with your QR code</h6>
                </div>
                
                <!-- QR Code Display -->
                <div class="qr-code-container mb-4">
                    <div id="qr-code-display" style="width: 300px; height: 300px; margin: 0 auto; border: 2px solid #dee2e6; border-radius: 8px; background: #fff; display: flex; align-items: center; justify-content: center;">
                        <div class="text-center">
                            <i class="fas fa-qrcode fa-4x text-muted mb-3"></i>
                            <div class="text-muted">QR Code: <span id="qr-code-text">Loading...</span></div>
                        </div>
                    </div>
                </div>
                
                <!-- Employee Info -->
                <div class="employee-info mb-3">
                    <h5 id="employee-name" class="mb-1">Employee Name</h5>
                    <p id="employee-position" class="text-muted mb-0">Position</p>
                </div>
                
                <!-- QR Code Actions -->
                <div class="qr-actions">
                    <button type="button" class="btn btn-outline-primary btn-sm mr-2" onclick="copyQrCode()">
                        <i class="fas fa-copy"></i> Copy QR Code
                    </button>
                    <button type="button" class="btn btn-outline-success btn-sm" onclick="generateQrImage()">
                        <i class="fas fa-download"></i> Generate QR Image
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="addToList()">Add List</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentQrCode = '';

function showQrCodeModal(employee) {
    currentQrCode = employee.qr_code;
    
    // Update modal content
    document.getElementById('employee-name').textContent = employee.full_name;
    document.getElementById('employee-position').textContent = employee.position || 'Not assigned';
    document.getElementById('qr-code-text').textContent = employee.qr_code;
    
    // Generate QR code image
    generateQrImage();
    
    // Show modal
    $('#qrCodeModal').modal('show');
}

function copyQrCode() {
    if (currentQrCode) {
        navigator.clipboard.writeText(currentQrCode).then(function() {
            // Show success message
            showToast('QR Code copied to clipboard!', 'success');
        }, function(err) {
            console.error('Could not copy text: ', err);
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = currentQrCode;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            showToast('QR Code copied to clipboard!', 'success');
        });
    }
}

function generateQrImage() {
    if (currentQrCode) {
        // Generate QR code image using online API
        const qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' + encodeURIComponent(currentQrCode);
        
        // Update the display to show the generated QR code
        const qrDisplay = document.getElementById('qr-code-display');
        if (qrDisplay) {
            qrDisplay.innerHTML = '<img src="' + qrImageUrl + '" alt="Employee QR Code" class="img-fluid" style="max-width: 100%; height: auto;">';
        }
    }
}

function addToList() {
    // This function can be customized based on your needs
    // For now, it just closes the modal
    $('#qrCodeModal').modal('hide');
    showToast('Employee added to list!', 'success');
}

function showToast(message, type = 'info') {
    // Simple toast notification
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 3000);
}
</script>
@endpush













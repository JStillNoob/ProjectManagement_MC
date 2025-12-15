<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camera Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        button { padding: 10px 20px; margin: 5px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        video { width: 100%; max-width: 400px; border: 2px solid #ddd; border-radius: 5px; }
        .debug { background: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Camera Access Test</h1>
        
        <div id="status-container">
            <div class="status info">Checking browser compatibility...</div>
        </div>
        
        <div class="debug" id="debug-info">
            Loading debug information...
        </div>
        
        <div style="margin: 20px 0;">
            <button class="btn-primary" onclick="testCamera()">Test Camera Access</button>
            <button class="btn-success" onclick="startCamera()">Start Camera</button>
            <button class="btn-danger" onclick="stopCamera()">Stop Camera</button>
        </div>
        
        <div id="video-container" style="display: none;">
            <h3>Camera Feed:</h3>
            <video id="video" autoplay muted></video>
        </div>
        
        <div id="results"></div>
    </div>

    <script>
        let currentStream = null;
        
        function updateStatus(message, type = 'info') {
            const container = document.getElementById('status-container');
            container.innerHTML = `<div class="status ${type}">${message}</div>`;
        }
        
        function updateDebug() {
            const isSecure = location.protocol === 'https:' || location.hostname === 'localhost' || location.hostname === '127.0.0.1';
            const hasMediaDevices = !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia);
            const userAgent = navigator.userAgent;
            
            const debugInfo = `
URL: ${location.href}
Protocol: ${location.protocol}
Hostname: ${location.hostname}
Is Secure Context: ${isSecure}
Has MediaDevices: ${hasMediaDevices}
User Agent: ${userAgent}
            `.trim();
            
            document.getElementById('debug-info').textContent = debugInfo;
        }
        
        function testCamera() {
            updateStatus('Testing camera access...', 'info');
            
            // Check security context
            const isSecure = location.protocol === 'https:' || location.hostname === 'localhost' || location.hostname === '127.0.0.1';
            
            if (!isSecure) {
                updateStatus('❌ Camera access requires HTTPS or localhost. Current URL: ' + location.href, 'error');
                return;
            }
            
            // Check browser support
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                updateStatus('❌ Camera not supported in this browser', 'error');
                return;
            }
            
            // Check available devices
            navigator.mediaDevices.enumerateDevices()
                .then(devices => {
                    const videoDevices = devices.filter(device => device.kind === 'videoinput');
                    
                    if (videoDevices.length === 0) {
                        updateStatus('❌ No camera devices found', 'error');
                        return;
                    }
                    
                    updateStatus(`✅ Found ${videoDevices.length} camera device(s)`, 'success');
                    
                    // Test camera access
                    return navigator.mediaDevices.getUserMedia({ video: true });
                })
                .then(stream => {
                    updateStatus('✅ Camera access successful!', 'success');
                    
                    // Stop the test stream
                    stream.getTracks().forEach(track => track.stop());
                })
                .catch(err => {
                    console.error('Camera test failed:', err);
                    
                    let errorMessage = '❌ Camera access failed: ';
                    switch(err.name) {
                        case 'NotAllowedError':
                            errorMessage += 'Permission denied. Please allow camera access.';
                            break;
                        case 'NotFoundError':
                            errorMessage += 'No camera found.';
                            break;
                        case 'NotReadableError':
                            errorMessage += 'Camera is busy.';
                            break;
                        default:
                            errorMessage += err.message;
                    }
                    
                    updateStatus(errorMessage, 'error');
                });
        }
        
        function startCamera() {
            updateStatus('Starting camera...', 'info');
            
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(stream => {
                    currentStream = stream;
                    const video = document.getElementById('video');
                    video.srcObject = stream;
                    document.getElementById('video-container').style.display = 'block';
                    updateStatus('✅ Camera started successfully!', 'success');
                })
                .catch(err => {
                    console.error('Failed to start camera:', err);
                    updateStatus('❌ Failed to start camera: ' + err.message, 'error');
                });
        }
        
        function stopCamera() {
            if (currentStream) {
                currentStream.getTracks().forEach(track => track.stop());
                currentStream = null;
                document.getElementById('video-container').style.display = 'none';
                updateStatus('Camera stopped', 'info');
            }
        }
        
        // Initialize
        updateDebug();
        setInterval(updateDebug, 5000);
        
        // Check initial status
        const isSecure = location.protocol === 'https:' || location.hostname === 'localhost' || location.hostname === '127.0.0.1';
        const hasMediaDevices = !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia);
        
        if (!isSecure) {
            updateStatus('⚠️ Camera access requires HTTPS or localhost', 'warning');
        } else if (!hasMediaDevices) {
            updateStatus('❌ Camera not supported in this browser', 'error');
        } else {
            updateStatus('✅ Browser supports camera access', 'success');
        }
    </script>
</body>
</html>













<div class="modal fade" id="submitMilestoneModal{{ $milestone->milestone_id }}" tabindex="-1" role="dialog" aria-labelledby="submitMilestoneModalLabel{{ $milestone->milestone_id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background: #87A96B;">
                <h5 class="modal-title" id="submitMilestoneModalLabel{{ $milestone->milestone_id }}">
                    <i class="fas fa-paper-plane mr-2"></i>Submit Milestone Completion
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('projects.milestones.submit', ['project' => $project->ProjectID, 'milestone' => $milestone->milestone_id]) }}" method="POST" enctype="multipart/form-data" class="swal-confirm-form" 
                data-title="Submit Milestone Completion" 
                data-text="Are you sure you want to submit this milestone for approval? Make sure you have uploaded all required proof images." 
                data-icon="question" 
                data-confirm-text="Yes, Submit">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info border-left-info">
                        <i class="fas fa-info-circle mr-2"></i> You are submitting completion for <strong>{{ $milestone->milestone_name }}</strong>. Please upload proof images.
                    </div>
                    
                    <div class="form-group">
                        <label for="proof_images_{{ $milestone->milestone_id }}" class="font-weight-bold">Proof Images (Required)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="proof_images_{{ $milestone->milestone_id }}" name="proof_images[]" multiple accept="image/*" required onchange="previewImages(this, 'preview_container_{{ $milestone->milestone_id }}')">
                            <label class="custom-file-label" for="proof_images_{{ $milestone->milestone_id }}">Choose images...</label>
                        </div>
                        <small class="form-text text-muted">You can select multiple images. Accepted formats: JPG, PNG, JPEG.</small>
                    </div>

                    <div id="preview_container_{{ $milestone->milestone_id }}" class="row mt-3">
                        <!-- Image previews will appear here -->
                    </div>
                </div>
                <div class="modal-footer bg-white">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn text-white" style="background: #87A96B; border-color: #87A96B;">
                        <i class="fas fa-check mr-1"></i> Submit for Approval
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Simple image preview script
    function previewImages(input, containerId) {
        const container = document.getElementById(containerId);
        container.innerHTML = ''; // Clear previous previews
        
        if (input.files) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-4 mb-2';
                    col.innerHTML = `
                        <div class="card h-100 border-0 shadow-sm">
                            <img src="${e.target.result}" class="card-img-top rounded" style="height: 80px; object-fit: cover;">
                        </div>
                    `;
                    container.appendChild(col);
                }
                reader.readAsDataURL(file);
            });
            
            // Update label text
            const label = input.nextElementSibling;
            label.innerText = input.files.length + ' file(s) selected';
        }
    }
</script>
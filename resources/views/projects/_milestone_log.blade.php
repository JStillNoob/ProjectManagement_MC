@if($milestone->SubmittedBy || $milestone->ApprovedBy)
    <div class="mt-3 bg-light p-3 rounded">
        <h6 class="font-weight-bold border-bottom pb-2 mb-2">Activity Log</h6>
        
        @if($milestone->SubmittedBy)
            <div class="d-flex align-items-start mb-2">
                <div class="mr-2 mt-1">
                    <i class="fas fa-paper-plane text-primary"></i>
                </div>
                <div>
                    <small class="d-block"><strong>Submitted by:</strong> 
                        {{ $milestone->submitter ? $milestone->submitter->full_name : 'Unknown' }}
                        ({{ $milestone->submitter && $milestone->submitter->position ? $milestone->submitter->position->PositionName : 'N/A' }})
                    </small>
                    <small class="text-muted">{{ $milestone->SubmittedAt ? \Carbon\Carbon::parse($milestone->SubmittedAt)->format('M d, Y h:i A') : 'N/A' }}</small>
                </div>
            </div>
        @endif

        @if($milestone->ApprovedBy)
            <div class="d-flex align-items-start mt-2 pt-2 border-top">
                <div class="mr-2 mt-1">
                    <i class="fas fa-check-circle text-success"></i>
                </div>
                <div>
                    <small class="d-block"><strong>Approved by:</strong> 
                        {{ $milestone->approver ? $milestone->approver->full_name : 'Unknown' }}
                        ({{ $milestone->approver && $milestone->approver->position ? $milestone->approver->position->PositionName : 'N/A' }})
                    </small>
                    <small class="text-muted">{{ $milestone->ApprovedAt ? \Carbon\Carbon::parse($milestone->ApprovedAt)->format('M d, Y h:i A') : 'N/A' }}</small>
                </div>
            </div>
        @endif
    </div>
@endif
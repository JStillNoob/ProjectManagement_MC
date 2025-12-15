@if($milestone->proofImages && $milestone->proofImages->count() > 0)
    <div class="mt-3">
        <h6 class="font-weight-bold border-bottom pb-2 mb-3">Proof Images</h6>
        <div class="row">
            @foreach($milestone->proofImages as $image)
                <div class="col-6 col-md-4 col-lg-3 mb-3">
                    <a href="{{ Storage::url($image->image_path) }}" data-lightbox="milestone-{{ $milestone->milestone_id }}" data-title="Proof Image - {{ $image->created_at->format('M d, Y h:i A') }}">
                        <div class="card h-100 border-0 shadow-sm" style="overflow: hidden; border-radius: 8px;">
                            <img src="{{ Storage::url($image->image_path) }}" class="card-img-top" alt="Proof Image" style="height: 120px; object-fit: cover; transition: transform 0.3s;">
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endif
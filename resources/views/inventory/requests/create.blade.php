@extends('layouts.app')

@section('title', 'Create Inventory Request')
@section('page-title', 'Create Inventory Request')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-info-circle mr-2 text-success"></i>Bulk Requests Moved
                    </h3>
                </div>
                <div class="card-body text-center py-5">
                    <p class="lead">
                        The standalone create page has been replaced by the new <strong>Bulk Material Requisition</strong> workflow.
                    </p>
                    <p class="text-muted mb-4">
                        You can now submit multiple items in a single request from the Inventory Requests dashboard.
                    </p>
                    <a href="{{ route('inventory.requests.index') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Inventory Requests
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


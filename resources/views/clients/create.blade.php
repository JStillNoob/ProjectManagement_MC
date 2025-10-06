@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create New Client</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('clients.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ClientName" class="form-label">Client Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ClientName') is-invalid @enderror" 
                                           id="ClientName" name="ClientName" value="{{ old('ClientName') }}" required>
                                    @error('ClientName')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ContactPerson" class="form-label">Contact Person</label>
                                    <input type="text" class="form-control @error('ContactPerson') is-invalid @enderror" 
                                           id="ContactPerson" name="ContactPerson" value="{{ old('ContactPerson') }}">
                                    @error('ContactPerson')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ContactNumber" class="form-label">Contact Number</label>
                                    <input type="text" class="form-control @error('ContactNumber') is-invalid @enderror" 
                                           id="ContactNumber" name="ContactNumber" value="{{ old('ContactNumber') }}">
                                    @error('ContactNumber')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="Email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('Email') is-invalid @enderror" 
                                           id="Email" name="Email" value="{{ old('Email') }}">
                                    @error('Email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('clients.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Client</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Edit Contact Person')
@section('page-title', 'Edit Contact Person')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-edit mr-2"></i>Edit Contact Person for: {{ $client->ClientName }}
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('clients.update', $client) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Hidden field to preserve ClientName -->
                        <input type="hidden" name="ClientName" value="{{ $client->ClientName }}">

                        <h6 class="text-muted mb-3"><i class="fas fa-user mr-2"></i>Contact Person Details</h6>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="FirstName">First Name</label>
                                    <input type="text" class="form-control @error('FirstName') is-invalid @enderror" 
                                           id="FirstName" name="FirstName" value="{{ old('FirstName', $client->FirstName) }}"
                                           placeholder="Enter first name">
                                    @error('FirstName')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="LastName">Last Name</label>
                                    <input type="text" class="form-control @error('LastName') is-invalid @enderror" 
                                           id="LastName" name="LastName" value="{{ old('LastName', $client->LastName) }}"
                                           placeholder="Enter last name">
                                    @error('LastName')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h6 class="text-muted mb-3"><i class="fas fa-address-book mr-2"></i>Contact Information</h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ContactNumber">Contact Number</label>
                                    <input type="text" class="form-control @error('ContactNumber') is-invalid @enderror" 
                                           id="ContactNumber" name="ContactNumber" value="{{ old('ContactNumber', $client->ContactNumber) }}"
                                           placeholder="e.g., 09123456789">
                                    @error('ContactNumber')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="Email">Email Address</label>
                                    <input type="email" class="form-control @error('Email') is-invalid @enderror" 
                                           id="Email" name="Email" value="{{ old('Email', $client->Email) }}"
                                           placeholder="e.g., contact@email.com">
                                    @error('Email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary mr-2">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn text-white" style="background: #87A96B; border-color: #87A96B;">
                                <i class="fas fa-save"></i> Update Contact Person
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .form-control:focus {
        border-color: #87A96B;
        box-shadow: 0 0 0 0.2rem rgba(135, 169, 107, 0.25);
    }
</style>
@endpush
@endsection

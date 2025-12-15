@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col">
                <h2>Add Supplier</h2>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('suppliers.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="SupplierName" class="form-label">Supplier Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="SupplierName" id="SupplierName"
                                    class="form-control @error('SupplierName') is-invalid @enderror"
                                    value="{{ old('SupplierName') }}" required>
                                @error('SupplierName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="ContactFirstName" class="form-label">Contact First Name</label>
                                <input type="text" name="ContactFirstName" id="ContactFirstName"
                                    class="form-control @error('ContactFirstName') is-invalid @enderror"
                                    value="{{ old('ContactFirstName') }}">
                                @error('ContactFirstName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="ContactLastName" class="form-label">Contact Last Name</label>
                                <input type="text" name="ContactLastName" id="ContactLastName"
                                    class="form-control @error('ContactLastName') is-invalid @enderror"
                                    value="{{ old('ContactLastName') }}">
                                @error('ContactLastName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="PhoneNumber" class="form-label">Phone Number</label>
                                <input type="text" name="PhoneNumber" id="PhoneNumber"
                                    class="form-control @error('PhoneNumber') is-invalid @enderror"
                                    value="{{ old('PhoneNumber') }}">
                                @error('PhoneNumber')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="Email" class="form-label">Email</label>
                                <input type="email" name="Email" id="Email"
                                    class="form-control @error('Email') is-invalid @enderror" value="{{ old('Email') }}">
                                @error('Email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="Street" class="form-label">Street Address</label>
                        <input type="text" name="Street" id="Street"
                            class="form-control @error('Street') is-invalid @enderror" value="{{ old('Street') }}">
                        @error('Street')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="City" class="form-label">City</label>
                                <input type="text" name="City" id="City"
                                    class="form-control @error('City') is-invalid @enderror" value="{{ old('City') }}">
                                @error('City')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="Province" class="form-label">Province</label>
                                <input type="text" name="Province" id="Province"
                                    class="form-control @error('Province') is-invalid @enderror"
                                    value="{{ old('Province') }}">
                                @error('Province')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="PostalCode" class="form-label">Postal Code</label>
                                <input type="text" name="PostalCode" id="PostalCode"
                                    class="form-control @error('PostalCode') is-invalid @enderror"
                                    value="{{ old('PostalCode') }}">
                                @error('PostalCode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success"
                            style="background-color: #87A96B !important; border: 2px solid #87A96B !important; color: white !important;">
                            <i class="fas fa-save"></i> Save Supplier
                        </button>
                        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
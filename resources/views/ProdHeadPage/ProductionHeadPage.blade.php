@extends('layouts.app')

@section('title', 'Production Head Dashboard')
@section('page-title', 'Production Head Dashboard')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-shield mr-2"></i>
                        Welcome, {{ Auth::user()->FirstName }} {{ Auth::user()->LastName }}
                    </h3>
                </div>
                <div class="card-body">
                    <p class="mb-0">Welcome to the Production Head Dashboard. You have full access to manage projects and
                        system settings.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-6">
            <a href="{{ route('prodhead.attendance') }}" class="small-box bg-info">
                <div class="inner">
                    <h3><i class="fas fa-chart-line"></i></h3>
                    <p>Attendance Overview</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="small-box-footer">
                    View Reports <i class="fas fa-arrow-circle-right"></i>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3><i class="fas fa-tools"></i></h3>
                    <p>Under Construction</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="small-box-footer">
                    Coming Soon <i class="fas fa-arrow-circle-right"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3><i class="fas fa-tools"></i></h3>
                    <p>Under Construction</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="small-box-footer">
                    Coming Soon <i class="fas fa-arrow-circle-right"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3><i class="fas fa-tools"></i></h3>
                    <p>Under Construction</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="small-box-footer">
                    Coming Soon <i class="fas fa-arrow-circle-right"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tools mr-2"></i>
                        Quick Actions - Under Construction
                    </h3>
                </div>
                <div class="card-body text-center">
                    <div class="alert alert-info">
                        <i class="fas fa-tools fa-2x mb-2"></i>
                        <h5>Under Construction</h5>
                        <p class="mb-0">This section is currently being developed.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tools mr-2"></i>
                        Recent Activity - Under Construction
                    </h3>
                </div>
                <div class="card-body text-center">
                    <div class="alert alert-info">
                        <i class="fas fa-tools fa-2x mb-2"></i>
                        <h5>Under Construction</h5>
                        <p class="mb-0">This section is currently being developed.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
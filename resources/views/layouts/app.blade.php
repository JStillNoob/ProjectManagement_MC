<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Project Management') | AdminLTE</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">

    <style>
        /* Pastel Green Solid Theme */
        :root {
            --primary-green: rgb(255, 255, 255);
            --secondary-green: rgb(240, 243, 235);
            --accent-green: #88d8a3;
            --dark-green: #7fb069;
            --light-green: #f0f8f0;
            --reseda-green: rgb(248, 252, 247);
        }

        /* Sidebar Styling */
        .main-sidebar {
            background: var(--reseda-green) !important;
        }

        .main-sidebar .brand-link {
            background: white !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .main-sidebar .brand-link .brand-text {
            color: black !important;
            font-weight: 600;
        }

        .main-sidebar .brand-link:hover .brand-text {
            color: grey !important;
        }

        /* Sidebar links default (text + p tag) */
        .main-sidebar .nav-sidebar .nav-link,
        .main-sidebar .nav-sidebar .nav-link p {
            color: #2e2e2e !important;
            /* pastel black */
            transition: color 0.3s ease;

        }

        /* Sidebar link hover (text + p tag + icon) */
        .main-sidebar .nav-sidebar>.nav-item>.nav-link:hover,
        .main-sidebar .nav-sidebar>.nav-item>.nav-link:hover p {
            background: #52b788 !important;
            color: #ffffff !important;
            /* white text */
            transform: translateX(5px);

        }

        /* Active link (text + p tag + icon) */
        .main-sidebar .nav-sidebar>.nav-item>.nav-link.active,
        .main-sidebar .nav-sidebar>.nav-item>.nav-link.active p {
            background: #52b788 !important;
            color: #ffffff !important;
            /* white text */
            font-weight: 600;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Sidebar icons */
        .main-sidebar .nav-sidebar .nav-icon {
            color: rgb(51, 51, 51) !important;
            opacity: 0.7;
            transition: color 0.3s ease;
        }

        .main-sidebar .nav-sidebar>.nav-item>.nav-link:hover .nav-icon,
        .main-sidebar .nav-sidebar>.nav-item>.nav-link.active .nav-icon {
            color: white !important;
            opacity: 1;

        }

        /* User Panel */
        .main-sidebar .user-panel {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .main-sidebar .user-panel .info a {
            color: rgb(11, 11, 11) !important;
            font-weight: 600;
        }

        /* Sidebar Footer */
        .sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--reseda-green);
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            padding: 1rem;
        }

        /* Add padding to sidebar content to prevent overlap */
        .main-sidebar .sidebar {
            padding-bottom: 200px;
        }

        .sidebar-footer .user-panel {
            border-bottom: none;
            margin-bottom: 0.5rem;
        }

        .sidebar-footer .logout-section {
            margin-top: 0.5rem;
        }

        .sidebar-footer .logout-link {
            color: #2e2e2e !important;
            transition: color 0.3s ease;
            border-radius: 5px;
            padding: 0.75rem 1.25rem;
            display: flex;
            align-items: center;
        }

        .sidebar-footer .logout-link:hover {
            color: #dc3545 !important;
        }


        .sidebar-footer .logout-link .nav-icon {
            color: #dc3545 !important;
            transition: color 0.3s ease;
            margin-right: 0.75rem;
            font-size: 1.25rem;
            line-height: 1;
        }

        .sidebar-footer .logout-link p {
            margin: 0;
            font-size: 1.1rem;
            line-height: 1.2;
            font-weight: 500;
        }

        /* Header Styling */
        .main-header {
            background: var(--primary-green) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .main-header .navbar-nav .nav-link {
            color: #2d5a3d !important;
        }

        .main-header .navbar-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.2) !important;
            border-radius: 5px;
        }

        /* Content Header */
        .content-header {
            background: var(--light-green) !important;
            border-bottom: 1px solid var(--accent-green);
        }

        .content-header h1 {
            color: #2d5a3d !important;
            font-weight: 600;
        }

        /* Button Styling */
        .btn-primary,
        a.btn-primary,
        button.btn-primary,
        input.btn-primary {
            background: var(--primary-green) !important;
            border: 2px solid var(--primary-green) !important;
            color: white !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-decoration: none !important;
        }

        .btn-primary:hover,
        a.btn-primary:hover,
        button.btn-primary:hover,
        input.btn-primary:hover {
            background: var(--dark-green) !important;
            border-color: var(--dark-green) !important;
            color: white !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            text-decoration: none !important;
        }

        .btn-success {
            background: #28a745 !important;
            border: 2px solid #28a745 !important;
            color: white !important;
        }

        .btn-success:hover {
            background: #218838 !important;
            border-color: #218838 !important;
            color: white !important;
        }

        .btn-info {
            background: #17a2b8 !important;
            border: 2px solid #17a2b8 !important;
            color: white !important;
        }

        .btn-info:hover {
            background: #138496 !important;
            border-color: #138496 !important;
            color: white !important;
        }

        .btn-warning {
            background: #ffc107 !important;
            border: 2px solid #ffc107 !important;
            color: #212529 !important;
        }

        .btn-warning:hover {
            background: #e0a800 !important;
            border-color: #e0a800 !important;
            color: #212529 !important;
        }

        .btn-danger {
            background: #dc3545 !important;
            border: 2px solid #dc3545 !important;
            color: white !important;
        }

        .btn-danger:hover {
            background: #c82333 !important;
            border-color: #c82333 !important;
            color: white !important;
        }

        .btn-secondary {
            background: #6c757d !important;
            border: 2px solid #6c757d !important;
            color: white !important;
        }

        .btn-secondary:hover {
            background: #5a6268 !important;
            border-color: #5a6268 !important;
            color: white !important;
        }

        .btn-light {
            background: #f8f9fa !important;
            border: 2px solid #dee2e6 !important;
            color: #495057 !important;
        }

        .btn-light:hover {
            background: #e2e6ea !important;
            border-color: #dae0e5 !important;
            color: #495057 !important;
        }

        .btn-dark {
            background: #343a40 !important;
            border: 2px solid #343a40 !important;
            color: white !important;
        }

        .btn-dark:hover {
            background: #23272b !important;
            border-color: #23272b !important;
            color: white !important;
        }

        /* Outline Button Variants */
        .btn-outline-primary {
            color: var(--primary-green) !important;
            border: 2px solid var(--primary-green) !important;
            background: transparent !important;
        }

        .btn-outline-primary:hover {
            background: var(--primary-green) !important;
            color: white !important;
        }

        .btn-outline-success {
            color: #28a745 !important;
            border: 2px solid #28a745 !important;
            background: transparent !important;
        }

        .btn-outline-success:hover {
            background: #28a745 !important;
            color: white !important;
        }

        .btn-outline-danger {
            color: #dc3545 !important;
            border: 2px solid #dc3545 !important;
            background: transparent !important;
        }

        .btn-outline-danger:hover {
            background: #dc3545 !important;
            color: white !important;
        }

        .btn-outline-secondary {
            color: #6c757d !important;
            border: 2px solid #6c757d !important;
            background: transparent !important;
        }

        .btn-outline-secondary:hover {
            background: #6c757d !important;
            color: white !important;
        }

        /* Small Button Variants */
        .btn-sm {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.875rem !important;
            border-radius: 0.2rem !important;
        }

        /* Large Button Variants */
        .btn-lg {
            padding: 0.5rem 1rem !important;
            font-size: 1.25rem !important;
            border-radius: 0.3rem !important;
        }

        /* Button Focus States */
        .btn:focus {
            outline: none !important;
            box-shadow: 0 0 0 0.2rem rgba(82, 183, 136, 0.25) !important;
        }

        .btn-primary:focus {
            box-shadow: 0 0 0 0.2rem rgba(82, 183, 136, 0.5) !important;
        }

        .btn-danger:focus {
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.5) !important;
        }

        .btn-success:focus {
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.5) !important;
        }

        /* Ensure all button states are visible */
        .btn:not(:disabled):not(.disabled) {
            cursor: pointer;
        }

        /* Force visibility for all button types */
        .btn {
            display: inline-block;
            font-weight: 400;
            text-align: center;
            vertical-align: middle;
            user-select: none;
            border: 1px solid transparent;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: 0.25rem;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        /* Specific fixes for anchor buttons */
        a.btn {
            text-decoration: none !important;
        }

        a.btn:hover {
            text-decoration: none !important;
        }

        a.btn:focus {
            text-decoration: none !important;
        }

        /* Card header button fixes */
        .card-header .btn {
            margin-left: 0.5rem;
        }

        .card-header .btn:first-child {
            margin-left: 0;
        }

        /* Force visibility for buttons in card headers */
        .card-header a.btn-primary,
        .card-header button.btn-primary,
        .card-header .btn-primary {
            background: var(--primary-green) !important;
            border: 2px solid var(--primary-green) !important;
            color: white !important;
            opacity: 1 !important;
            visibility: visible !important;
            display: inline-block !important;
        }

        .card-header a.btn-primary:hover,
        .card-header button.btn-primary:hover,
        .card-header .btn-primary:hover {
            background: var(--dark-green) !important;
            border-color: var(--dark-green) !important;
            color: white !important;
        }

        /* Ensure all buttons in card headers are visible */
        .card-header .btn {
            opacity: 1 !important;
            visibility: visible !important;
            display: inline-block !important;
        }

        /* Override any potential hiding styles */
        .card-header a,
        .card-header button {
            opacity: 1 !important;
            visibility: visible !important;
        }

        /* Specific fix for the project show page buttons */
        .card-header .d-flex .btn {
            opacity: 1 !important;
            visibility: visible !important;
            display: inline-block !important;
            background-color: var(--primary-green) !important;
            color: white !important;
            border: 2px solid var(--primary-green) !important;
        }

        .card-header .d-flex .btn-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
            border: 2px solid #ffc107 !important;
        }

        .card-header .d-flex .btn-secondary {
            background-color: #6c757d !important;
            color: white !important;
            border: 2px solid #6c757d !important;
        }

        /* Card Styling */
        .card {
            border: 1px solid var(--accent-green) !important;
            box-shadow: 0 2px 8px rgba(168, 230, 207, 0.1) !important;
        }

        .card-header {
            background: var(--light-green) !important;
            border-bottom: 1px solid var(--accent-green) !important;
        }

        .card-header h3 {
            color: #2d5a3d !important;
            font-weight: 600;
        }

        /* Badge Styling */
        .badge-success {
            background: var(--accent-green) !important;
        }

        .badge-info {
            background: #b8e6d1 !important;
            color: #2d5a3d !important;
        }

        .badge-warning {
            background: #f0e68c !important;
        }

        .badge-danger {
            background: #ffb3ba !important;
        }

        /* Form Styling */
        .form-control:focus {
            border-color: var(--accent-green) !important;
            box-shadow: 0 0 0 0.2rem rgba(136, 216, 163, 0.25) !important;
        }

        /* Small Box Styling */
        .small-box {
            background: var(--primary-green) !important;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .small-box .inner h3 {
            color: #2d5a3d !important;
        }

        .small-box .inner p {
            color: #1a3d26 !important;
        }

        .small-box .icon {
            color: rgba(45, 90, 61, 0.3) !important;
        }

        /* Footer */
        .main-footer {
            background: var(--primary-green) !important;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: #2d5a3d !important;
        }

        /* Progress Bars */
        .progress-bar {
            background: var(--accent-green) !important;
        }

        /* Table Styling */
        .table thead th {
            background: var(--light-green) !important;
            color: #2d5a3d !important;
            border-bottom: 2px solid var(--accent-green) !important;
        }

        /* Alert Styling */
        .alert-success {
            background: rgba(168, 230, 207, 0.2) !important;
            border: 1px solid var(--accent-green) !important;
            color: #2d5a3d !important;
        }
    </style>



    @stack('styles')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="https://adminlte.io/themes/v3/dist/img/AdminLTELogo.png"
                alt="AdminLTELogo" height="60" width="60">
        </div>

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    @if(Auth::check())
                        @if(Auth::user()->UserTypeID == 1)
                            <a href="{{ route('showAdmin') }}" class="nav-link">Production Head Dashboard</a>
                        @elseif(Auth::user()->UserTypeID == 2)
                            <a href="{{ route('go_newPage') }}" class="nav-link">Admin Dashboard</a>
                        @else
                            <a href="{{ route('go_newPage') }}" class="nav-link">Home</a>
                        @endif
                    @else
                        <a href="{{ route('go_newPage') }}" class="nav-link">Home</a>
                    @endif
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Notifications Dropdown Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-bell"></i>
                        <span class="badge badge-warning navbar-badge">15</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <span class="dropdown-item dropdown-header">15 Notifications</span>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-envelope mr-2"></i> 4 new messages
                            <span class="float-right text-muted text-sm">3 mins</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-users mr-2"></i> 8 friend requests
                            <span class="float-right text-muted text-sm">12 hours</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-file mr-2"></i> 3 new reports
                            <span class="float-right text-muted text-sm">2 days</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-widget="control-sidebar" data-controlsidebar-slide="true" href="#"
                        role="button">
                        <i class="fas fa-th-large"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ route('go_newPage') }}" class="brand-link d-flex justify-content-center">
                <img src="{{ asset('images/Screenshot_2025-06-23_082305-removebg-preview.png') }}" alt="Logo"
                    class="brand-image  " style="opacity:.8; max-height:100px; width:auto;">
            </a>


            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar Menu -->
                <nav class="mt-4">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">

                        <!-- Dashboard Link based on User Type (Admin and HR only) -->
                        <li class="nav-item">
                            @if(Auth::check())
                                    @if(Auth::user()->UserTypeID == 1)
                                        <li class="nav-item">
                                            <a href="{{ route('showProdHead') }}"
                                                class="nav-link {{ request()->routeIs('showAdmin') ? 'active' : '' }}">
                                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                                <p>Dashboard</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('ProdHead.projects') }}"
                                                class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                                <i class="nav-icon fas fa-user-cog"></i>
                                                <p>Manage Projects</p>
                                            </a>
                                        </li>
                                    @elseif(Auth::user()->UserTypeID == 2)
                                    <li class="nav-item">
                                        <a href="{{ route('go_newPage') }}"
                                            class="nav-link {{ request()->routeIs('go_newPage') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-tachometer-alt"></i>
                                            <p>Admin Dashboard</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('employees.index') }}"
                                            class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-users"></i>
                                            <p>Manage Employees</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('users.index') }}"
                                            class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-user-tie"></i>
                                            <p>Employee Users</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('clients.index') }}"
                                            class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-handshake"></i>
                                            <p>Manage Clients</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('positions.index') }}"
                                            class="nav-link {{ request()->routeIs('positions.*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-briefcase"></i>
                                            <p>Manage Positions</p>
                                        </a>
                                    </li>
                                @elseif(Auth::user()->UserTypeID == 3)
                                    <li class="nav-item">
                                        <a href="{{ route('attendance.index') }}"
                                            class="nav-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-clock"></i>
                                            <p>Employee Attendance</p>
                                        </a>
                                    </li>
                                @else
                                    <a href="{{ route('go_newPage') }}"
                                        class="nav-link {{ request()->routeIs('go_newPage') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-tachometer-alt"></i>
                                        <p>Dashboard</p>
                                    </a>
                                @endif
                            @else
                            <a href="{{ route('go_newPage') }}"
                                class="nav-link {{ request()->routeIs('go_newPage') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        @endif
                        </li>



                    </ul>
                </nav>
                <!-- /.sidebar-menu -->

                <!-- Sidebar user panel and logout at bottom -->
                <div class="sidebar-footer">
                    <!-- User Panel -->
                    <div class="user-panel mt-2 pb-2 mb-2 d-flex"
                        style="background: rgba(113, 199, 124, 0.7); border-radius: 8px; padding: 10px;">
                        <div class="image">
                            <img src="https://adminlte.io/themes/v3/dist/img/user2-160x160.jpg"
                                class="img-circle elevation-2" alt="User Image">
                        </div>
                        <div class="info">
                            <a href="#" class="d-block">{{ Auth::user()->employee->full_name ?? 'User' }}</a>
                        </div>
                    </div>

                    <!-- Logout Button -->
                    <div class="logout-section">
                        <a href="#" class="nav-link logout-link"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>Logout</p>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('page-title', 'Dashboard')</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                @if(Auth::check())
                                    @if(Auth::user()->UserTypeID == 1)
                                        <li class="breadcrumb-item"><a href="{{ route('showAdmin') }}">Production Head
                                                Dashboard</a></li>
                                    @elseif(Auth::user()->UserTypeID == 2)
                                        <li class="breadcrumb-item"><a href="{{ route('go_newPage') }}">HR Dashboard</a></li>
                                    @else
                                        <li class="breadcrumb-item"><a href="{{ route('go_newPage') }}">Home</a></li>
                                    @endif
                                @else
                                    <li class="breadcrumb-item"><a href="{{ route('go_newPage') }}">Home</a></li>
                                @endif
                                <li class="breadcrumb-item active">@yield('page-title', 'Dashboard')</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    @yield('content')
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <footer class="main-footer">
            <strong>Copyright &copy; 2024 <a href="#">Project Management</a>.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 1.0.0
            </div>
        </footer>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

    @stack('scripts')
</body>

</html>
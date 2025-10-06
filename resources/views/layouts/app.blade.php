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
        .btn-primary {
            background: var(--accent-green) !important;
            border: none !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-primary:hover {
            background: var(--dark-green) !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-success {
            background: var(--primary-green) !important;
            border: none !important;
        }

        .btn-success:hover {
            background: var(--accent-green) !important;
        }

        .btn-info {
            background: #b8e6d1 !important;
            border: none !important;
        }

        .btn-warning {
            background: #f0e68c !important;
            border: none !important;
        }

        .btn-danger {
            background: #ffb3ba !important;
            border: none !important;
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
                            <a href="{{ route('showAdmin') }}" class="nav-link">Admin Dashboard</a>
                        @elseif(Auth::user()->UserTypeID == 2)
                            <a href="{{ route('go_newPage') }}" class="nav-link">HR Dashboard</a>
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
                                        <a href="{{ route('clients.index') }}"
                                            class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-user-friends"></i>
                                            <p>Manage Clients</p>
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



                        <!-- Logout -->
                        <li class="nav-item">
                            <a href="#" class="nav-link"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>Logout</p>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="https://adminlte.io/themes/v3/dist/img/user2-160x160.jpg"
                            class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">{{ Auth::user()->FirstName ?? 'User' }}</a>
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
    <!-- AdminLTE for demo purposes -->
    <script src="https://adminlte.io/themes/v3/dist/js/demo.js"></script>

    @stack('scripts')
</body>

</html>
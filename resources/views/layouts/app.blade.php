<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css"
        rel="stylesheet" />
    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        /* Pastel Green Solid Theme */
        :root {
            --primary-green: rgb(255, 255, 255);
            --secondary-green: rgb(255, 255, 255);
            --accent-green: rgba(99, 97, 97, 0.23);
            --dark-green: rgb(255, 255, 255);
            --light-green: rgb(255, 255, 255);
            --reseda-green: rgb(7, 7, 7);
        }

        /* Sidebar Styling - Green Theme */
        .main-sidebar {
            background: #ffffff !important;
            z-index: 1020 !important;
            transition: all 0.3s ease;
        }

        /* Remove pointer events on collapsed sidebar to prevent hover expansion */
        .sidebar-collapse .main-sidebar:hover {
            width: 4.6rem !important;
        }

        /* Adjust sidebar shadow to not overlap navbar - keep shadow but clip right edge */
        @media (min-width: 992px) {
            .main-sidebar.elevation-4 {
                box-shadow: 4px 0 8px rgba(0, 0, 0, 0.15), 0 4px 8px rgba(0, 0, 0, 0.15), 0 -2px 4px rgba(0, 0, 0, 0.1) !important;
                /* Enhanced shadow on left, top, and bottom - not on right where navbar is */
            }
        }

        .main-sidebar .brand-link {
            background: white !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            padding: 0.8125rem 0.5rem !important;
            transition: all 0.3s ease;
        }

        .main-sidebar .brand-link .brand-image {
            transition: all 0.3s ease;
            max-height: 100px !important;
            width: auto !important;
        }

        /* Brand logo when sidebar is collapsed */
        .sidebar-collapse .main-sidebar .brand-link {
            padding: 0.5rem !important;
            text-align: center;
        }

        .sidebar-collapse .main-sidebar .brand-link .brand-image {
            max-height: 50px !important;
            margin: 0 auto !important;
            float: none !important;
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
            transition: none;
        }

        /* Sidebar link hover (text + p tag + icon) - only when NOT collapsed */
        body:not(.sidebar-collapse) .main-sidebar .nav-sidebar>.nav-item>.nav-link:hover,
        body:not(.sidebar-collapse) .main-sidebar .nav-sidebar>.nav-item>.nav-link:hover p {
            background: #7fb069 !important;
            color: #ffffff !important;
            transform: translateX(5px);
        }

        body:not(.sidebar-collapse) .main-sidebar .nav-sidebar>.nav-item>.nav-link:hover .nav-icon {
            color: white !important;
            opacity: 1 !important;
        }

        /* Active link (text + p tag + icon) */
        .main-sidebar .nav-sidebar>.nav-item>.nav-link.active {
            background: rgba(127, 176, 105, 0.8) !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .main-sidebar .nav-sidebar>.nav-item>.nav-link.active p {
            background: transparent !important;
            /* No background on text */
            color: #ffffff !important;
            font-weight: 600;

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

        /* Sidebar Header */
        .sidebar-header {
            padding: 1rem 1.25rem 0.5rem 1.25rem;
            margin-bottom: 0.5rem;
        }

        .sidebar-header-title {
            color: #9ca3af !important;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 0;
        }

        /* Sidebar Section Headings */
        .sidebar-section-heading {
            color: #9ca3af !important;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.25rem 1.25rem 0.5rem 1.25rem;
            list-style: none;
            pointer-events: none;
        }

        /* Hide section headings when sidebar is collapsed */
        .sidebar-collapse .sidebar-section-heading {
            display: none !important;
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
            background: rgb(252, 255, 253);
            padding: 1.25rem 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Add padding to sidebar content to prevent overlap */
        .main-sidebar .sidebar {
            padding-bottom: 180px;
        }

        /* User Info Section */
        .sidebar-user-info {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .sidebar-user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #7fb069;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            margin-right: 0.75rem;
            flex-shrink: 0;
        }

        .sidebar-user-details {
            flex: 1;
            min-width: 0;
        }

        .sidebar-user-name {
            color: rgb(15, 15, 15);
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-user-email {
            color: rgb(10, 10, 10);
            font-size: 0.8rem;
            opacity: 0.9;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Sign Out Button */
        .sidebar-signout-btn {
            width: 100%;
            background: #7fb069;
            color: #ffffff;
            border: none;
            border-radius: 0.375rem;
            padding: 0.625rem 1rem;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .sidebar-signout-btn:hover {
            background: #6fa05a;
        }

        .sidebar-signout-btn:active {
            background: #5f8f4a;
        }

        /* Header Styling */
        .main-header {
            position: fixed !important;
            top: 0;
            z-index: 1031 !important;
            /* Higher than sidebar to appear above shadow */
            background: var(--primary-green) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 0 !important;
            padding: 0 !important;
            height: 57px !important;
            min-height: 57px !important;
            max-height: 57px !important;
            line-height: 57px !important;
        }

        .main-header .navbar {
            height: 57px !important;
            min-height: 57px !important;
            padding: 0.25rem 1rem !important;
        }

        /* Remove background panel behind navbar */
        .wrapper {
            background: transparent !important;
        }

        body>.wrapper {
            background: transparent !important;
        }

        /* Remove any background from elements behind navbar */
        .main-header::before,
        .main-header::after {
            display: none !important;
        }

        /* Adjust navbar when sidebar is visible - match sidebar width exactly */
        @media (min-width: 992px) {

            /* Sidebar expanded (default) */
            body:not(.sidebar-collapse) .main-header,
            body.sidebar-mini:not(.sidebar-collapse) .main-header,
            .sidebar-mini:not(.sidebar-collapse) .main-header,
            .wrapper:not(.sidebar-collapse)~.main-header {
                left: 250px !important;
                right: 0 !important;
                margin-left: 0 !important;
                padding-left: 0 !important;
                border-left: none !important;
                transition: left 0.3s ease !important;
            }

            /* Sidebar collapsed */
            body.sidebar-collapse .main-header,
            body.sidebar-mini.sidebar-collapse .main-header,
            .sidebar-mini.sidebar-collapse .main-header,
            .wrapper.sidebar-collapse~.main-header {
                left: 78px !important;
                right: 0 !important;
                margin-left: 0 !important;
                padding-left: 0 !important;
                border-left: none !important;
                transition: left 0.3s ease !important;
            }
        }

        @media (max-width: 991.98px) {
            .main-header {
                left: 0 !important;
                right: 0 !important;
                width: 100% !important;
                margin-left: 0 !important;
                padding-left: 0 !important;
            }
        }

        /* Add padding to content-wrapper to account for fixed navbar */
        .content-wrapper {
            margin-top: 57px !important;
            background: transparent !important;
        }

        /* Remove any background panels */
        .content-wrapper::before,
        .content-wrapper::after {
            display: none !important;
        }

        /* Ensure navbar items are visible */
        .main-header .navbar {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .main-header .navbar-nav {
            margin: 0 !important;
        }

        .main-header .navbar-nav {
            display: flex !important;
            flex-direction: row !important;
        }

        .main-header .navbar-nav.ml-auto {
            margin-left: auto !important;
        }

        .main-header .nav-item {
            display: block !important;
            visibility: visible !important;
        }

        .main-header .nav-item .nav-link {
            white-space: nowrap !important;
            overflow: visible !important;
        }

        .main-header .navbar-nav .nav-link {
            color: #2d5a3d !important;
        }

        .main-header .navbar-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.2) !important;
            border-radius: 5px;
        }

        .main-header .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2d5a3d;
            margin: 0;
            padding: 0.5rem 0;
        }

        .main-header .user-info {
            display: flex;
            align-items: center;
            color: #2d5a3d !important;
            font-weight: 500;
        }

        .main-header .user-info i {
            font-size: 1.5rem;
            margin-right: 0.5rem;
            color: #2d5a3d !important;
        }

        .main-header .user-info span {
            color: #2d5a3d !important;
            font-weight: 500;
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* User Dropdown Styling */
        .main-header .dropdown-menu {
            border-radius: 0.375rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(0, 0, 0, 0.1);
            margin-top: 0.5rem;
        }

        .main-header .dropdown-item {
            padding: 0.625rem 1rem;
            transition: background-color 0.2s ease;
        }

        .main-header .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .main-header .dropdown-item i {
            width: 20px;
            text-align: center;
        }

        .main-header .dropdown-divider {
            margin: 0.5rem 0;
        }

        .main-header .user-info.dropdown-toggle::after {
            display: none;
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

        /* Global DataTables Pagination Fixes */
        .dataTables_paginate .paginate_button {
            padding: 6px 12px !important;
            margin: 0 2px !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 4px !important;
            background: white !important;
            color: #007bff !important;
            font-size: 0.875rem !important;
            min-width: auto !important;
            height: auto !important;
            line-height: 1.5 !important;
        }

        .dataTables_paginate .paginate_button:hover {
            background: #e9ecef !important;
            border-color: #adb5bd !important;
        }

        .dataTables_paginate .paginate_button.current {
            background: #007bff !important;
            color: white !important;
            border-color: #007bff !important;
        }

        .dataTables_paginate .paginate_button.disabled {
            color: #6c757d !important;
            background: #f8f9fa !important;
            border-color: #dee2e6 !important;
        }

        /* Fix oversized pagination elements */
        .dataTables_paginate .paginate_button.previous,
        .dataTables_paginate .paginate_button.next {
            padding: 6px 12px !important;
            font-size: 0.875rem !important;
            min-width: auto !important;
            height: auto !important;
        }

        .dataTables_paginate .paginate_button.first,
        .dataTables_paginate .paginate_button.last {
            padding: 6px 12px !important;
            font-size: 0.875rem !important;
            min-width: auto !important;
            height: auto !important;
        }

        /* Fix any oversized input fields in pagination */
        .dataTables_paginate input,
        .dataTables_paginate select {
            padding: 4px 8px !important;
            font-size: 0.875rem !important;
            height: auto !important;
            width: auto !important;
            max-width: 60px !important;
        }

        /* Ensure pagination container has proper sizing */
        .dataTables_paginate {
            font-size: 0.875rem !important;
            line-height: 1.5 !important;
        }

        /* Bootstrap Pagination Fixes */
        .pagination .page-link {
            padding: 6px 12px !important;
            font-size: 0.875rem !important;
            line-height: 1.5 !important;
        }

        .pagination .page-item .page-link {
            min-width: auto !important;
            height: auto !important;
        }

        /* General pagination fixes for any custom pagination */
        .pagination,
        .pagination-lg,
        .pagination-sm {
            font-size: 0.875rem !important;
        }

        .pagination-lg .page-link {
            padding: 8px 16px !important;
            font-size: 1rem !important;
        }

        .pagination-sm .page-link {
            padding: 4px 8px !important;
            font-size: 0.75rem !important;
        }

        /* Additional pagination improvements for consistency */
        .pagination .page-item.active .page-link {
            background-color: #007bff !important;
            border-color: #007bff !important;
            color: white !important;
        }

        .pagination .page-item.disabled .page-link {
            color: #6c757d !important;
            background-color: #fff !important;
            border-color: #dee2e6 !important;
        }

        /* Ensure consistent spacing for pagination */
        .pagination {
            margin-bottom: 0 !important;
        }

        /* Fix for any custom pagination containers */
        .d-flex.justify-content-center {
            margin-top: 1rem !important;
        }

        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.95);
            z-index: 9999;
            display: flex;
        }

        .preloader.hidden {
            display: none;
        }

        /* Global Margins and Spacing for All Pages */
        .container-fluid {
            padding: 10px !important;
        }

        /* Container Shadow */
        .container-fluid .card {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
            border-radius: 8px;
        }

        .container-fluid .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        }

        /* DataTables Modern Design */
        .dataTables_wrapper {
            padding: 20px 0;
        }

        .dataTables_filter {
            margin-bottom: 20px;
        }

        .dataTables_filter input[type="search"] {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 8px 15px;
            font-size: 0.9rem;
            width: 300px;
            transition: all 0.3s ease;
        }

        .dataTables_filter input[type="search"]:focus {
            outline: none;
            border-color: #7fb069;
            box-shadow: 0 0 0 0.2rem rgba(127, 176, 105, 0.25);
        }

        .dataTables_length {
            margin-bottom: 20px;
        }

        .dataTables_length select {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 0.9rem;
            margin: 0 5px;
            transition: all 0.3s ease;
        }

        .dataTables_length select:focus {
            outline: none;
            border-color: #7fb069;
            box-shadow: 0 0 0 0.2rem rgba(127, 176, 105, 0.25);
        }

        .dataTables_wrapper .dataTables_paginate {
            margin-top: 20px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 6px;
            padding: 6px 12px;
            margin: 0 2px;
            border: 1px solid #dee2e6;
            background: white;
            color: #495057;
            transition: all 0.3s ease;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f8f9fa;
            border-color: #7fb069;
            color: #7fb069;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #7fb069;
            border-color: #7fb069;
            color: white;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #6a9a5a;
            border-color: #6a9a5a;
            color: white;
        }

        .dataTables_wrapper .dataTables_info {
            padding-top: 10px;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .dataTables_wrapper table.dataTable {
            border-collapse: separate;
            border-spacing: 0;
            border: none !important;
            width: 100% !important;
        }

        .dataTables_wrapper table.dataTable thead th {
            background-color: transparent;
            border: none !important;
            font-weight: 600;
            color: #495057;
            padding: 12px 15px;
            text-align: left;
        }

        .dataTables_wrapper table.dataTable tbody tr {
            transition: background-color 0.2s ease;
            border: none !important;
        }

        .dataTables_wrapper table.dataTable tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        .dataTables_wrapper table.dataTable tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .dataTables_wrapper table.dataTable tbody tr:hover {
            background-color: #e9ecef !important;
        }

        .dataTables_wrapper table.dataTable tbody td {
            padding: 12px 15px;
            border: none !important;
        }
    </style>



    @stack('styles')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <div class="preloader flex-column justify-content-center align-items-center"
            style="background-color: rgba(255, 255, 255, 0.9); z-index: 9999;">
            <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
            <p class="mt-3 text-muted">Loading...</p>
        </div>

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <span class="page-title">@yield('page-title', 'Dashboard')</span>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                @if(Auth::check())
                    @php
                        $user = Auth::user()->load('employee');
                        $userName = 'User';
                        $userEmail = $user->Email ?? '';
                        $userImage = null;

                        if ($user->employee) {
                            $userName = $user->employee->full_name ?? 'User';
                            $userImage = $user->employee->image_path ?? null;
                        } else {
                            // Extract name from email if no employee
                            $emailParts = explode('@', $userEmail);
                            $userName = ucfirst($emailParts[0]);
                        }

                        $initial = strtoupper(substr(trim($userName), 0, 1));
                        if (empty($initial)) {
                            $initial = 'U';
                        }
                    @endphp
                    <!-- User Name with Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link user-info dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                            style="padding: 0.5rem 0.75rem; cursor: pointer;">
                            <i class="fas fa-user-circle"></i>
                            <span style="text-decoration: underline;">{{ $userName }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown"
                            style="min-width: 200px; margin-top: 0.5rem;">
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#accountInfoModal">
                                <i class="fas fa-user mr-2"></i>Account Information
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        </div>
                    </li>
                @endif
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Logout Form (for navbar dropdown) -->
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>

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
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <li class="sidebar-section-heading">Main</li>
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
                                        <li class="nav-item">
                                            <a href="{{ route('inventory.index') }}"
                                                class="nav-link {{ request()->routeIs('inventory.index') || request()->routeIs('inventory.create') || request()->routeIs('inventory.show') || request()->routeIs('inventory.edit') ? 'active' : '' }}">
                                                <i class="nav-icon fas fa-boxes"></i>
                                                <p>Inventory</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('inventory.requests.index') }}"
                                                class="nav-link {{ request()->routeIs('inventory.requests.*') ? 'active' : '' }}">
                                                <i class="nav-icon fas fa-clipboard-list"></i>
                                                <p>Inventory Requests</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('prodhead.attendance') }}"
                                                class="nav-link {{ request()->routeIs('prodhead.attendance*') ? 'active' : '' }}">
                                                <i class="nav-icon fas fa-chart-line"></i>
                                                <p>Attendance Overview</p>
                                            </a>
                                        </li>
                                    @elseif(Auth::user()->UserTypeID == 2)
                                    @php
                                        $user = Auth::user();
                                        $isAdmin = \App\Models\ProjectMilestone::isAdmin($user);
                                        $isEngineer = false;
                                        if ($user->EmployeeID) {
                                            $employee = \App\Models\Employee::with('position')->find($user->EmployeeID);
                                            if ($employee) {
                                                $isEngineer = \App\Models\ProjectMilestone::isEngineer($employee);
                                            }
                                        }
                                        $canAccessMilestoneReports = $isAdmin || $isEngineer;
                                    @endphp
                                    <li class="nav-item">
                                        <a href="{{ route('go_newPage') }}"
                                            class="nav-link {{ request()->routeIs('go_newPage') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-tachometer-alt"></i>
                                            <p>Dashboard</p>
                                        </a>
                                    </li>

                                    <!-- MANAGEMENT Section -->
                                    <li class="sidebar-section-heading">MANAGEMENT</li>

                                    <li class="nav-item">
                                        <a href="{{ route('ProdHead.projects') }}"
                                            class="nav-link {{ request()->routeIs('ProdHead.projects*') || request()->routeIs('projects.*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-project-diagram"></i>
                                            <p>Projects</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('clients.index') }}"
                                            class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-handshake"></i>
                                            <p>Clients</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('employees.index') }}"
                                            class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-users"></i>
                                            <p>Employees</p>
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
                                        <a href="{{ route('positions.index') }}"
                                            class="nav-link {{ request()->routeIs('positions.*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-briefcase"></i>
                                            <p>Positions</p>
                                        </a>
                                    </li>

                                    <!-- INVENTORY DATA Section -->
                                    <li class="sidebar-section-heading">INVENTORY DATA</li>

                                    <li class="nav-item">
                                        <a href="{{ route('resource-catalog.index') }}"
                                            class="nav-link {{ request()->routeIs('resource-catalog.*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-box-open"></i>
                                            <p>Resource Catalog</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('inventory.index') }}"
                                            class="nav-link {{ request()->routeIs('inventory.index') || request()->routeIs('inventory.create') || request()->routeIs('inventory.show') || request()->routeIs('inventory.edit') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-boxes"></i>
                                            <p>Inventory Items</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('suppliers.index') }}"
                                            class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-truck"></i>
                                            <p>Suppliers</p>
                                        </a>
                                    </li>

                                    <!-- DAILY OPERATIONS Section -->
                                    <li class="sidebar-section-heading">DAILY OPERATIONS</li>

                                    <li class="nav-item">
                                        <a href="{{ route('inventory.requests.index') }}"
                                            class="nav-link {{ request()->routeIs('inventory.requests.*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-clipboard-list"></i>
                                            <p>Inventory Requests</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('purchase-orders.index') }}"
                                            class="nav-link {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-file-invoice"></i>
                                            <p>Purchase Orders</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('receiving.index') }}"
                                            class="nav-link {{ request()->routeIs('receiving.*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-dolly"></i>
                                            <p>Receiving</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('issuance.index') }}"
                                            class="nav-link {{ request()->routeIs('issuance.*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-hand-holding"></i>
                                            <p>Issuance</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('equipment.returns.index') }}"
                                            class="nav-link {{ request()->routeIs('equipment.returns.*') || request()->routeIs('equipment.incidents.*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-undo"></i>
                                            <p>Equipment Returns</p>
                                        </a>
                                    </li>

                                    <!-- REPORTS Section -->
                                    <li class="sidebar-section-heading">REPORTS</li>

                                    <li class="nav-item">
                                        <a href="{{ route('reports.inventory.index') }}"
                                            class="nav-link {{ request()->routeIs('reports.inventory.*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-chart-bar"></i>
                                            <p>Inventory Reports</p>
                                        </a>
                                    </li>
                                    @if($canAccessMilestoneReports)
                                    <li class="nav-item">
                                        <a href="{{ route('reports.milestones.index') }}"
                                            class="nav-link {{ request()->routeIs('reports.milestones.*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-tasks"></i>
                                            <p>Milestone Reports</p>
                                        </a>
                                    </li>
                                    @endif
                                    <li class="nav-item">
                                        <a href="{{ route('prodhead.attendance') }}"
                                            class="nav-link {{ request()->routeIs('prodhead.attendance*') || request()->routeIs('attendance.*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-chart-line"></i>
                                            <p>Attendance Overview</p>
                                        </a>
                                    </li>
                                @elseif(Auth::user()->UserTypeID == 3)
                                    <li class="nav-item">
                                        <a href="{{ route('foreman.projects') }}"
                                            class="nav-link {{ request()->routeIs('foreman.*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-project-diagram"></i>
                                            <p>My Projects</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('attendance.index') }}"
                                            class="nav-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-clock"></i>
                                            <p>Employee Attendance</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('inventory.requests.index') }}"
                                            class="nav-link {{ request()->routeIs('inventory.requests.index') || request()->routeIs('inventory.requests.create') || request()->routeIs('inventory.requests.show') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-clipboard-list"></i>
                                            <p>Inventory Requests</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('inventory.requests.history') }}"
                                            class="nav-link {{ request()->routeIs('inventory.requests.history') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-history"></i>
                                            <p>Request History</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('issuance.index') }}"
                                            class="nav-link {{ request()->routeIs('issuance.*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-dolly"></i>
                                            <p>Issuance Records</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('equipment.returns.index') }}"
                                            class="nav-link {{ request()->routeIs('equipment.returns.*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-undo"></i>
                                            <p>Equipment Returns</p>
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

            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->

            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    @yield('content')
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>


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
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Toastr Configuration -->
    <script>
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        // Show toastr notifications from session flash messages
        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        @if(session('warning'))
            toastr.warning("{{ session('warning') }}");
        @endif

        @if(session('info'))
            toastr.info("{{ session('info') }}");
        @endif

        // Global SweetAlert2 confirmation handler
        function attachSwalConfirmHandlers() {
            // Handle forms with swal-confirm-form class
            document.querySelectorAll('form.swal-confirm-form:not([data-swal-attached])').forEach(function(form) {
                form.setAttribute('data-swal-attached', 'true');
                
                const submitHandler = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const formEl = this;
                    
                    Swal.fire({
                        title: formEl.dataset.title || 'Are you sure?',
                        text: formEl.dataset.text || 'This action cannot be undone.',
                        icon: formEl.dataset.icon || 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#87A96B',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: formEl.dataset.confirmText || 'Yes, proceed',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Remove the handler temporarily and submit
                            formEl.removeAttribute('data-swal-attached');
                            formEl.removeEventListener('submit', submitHandler);
                            formEl.submit();
                        }
                    });
                };
                
                form.addEventListener('submit', submitHandler);
            });
            
            // Handle buttons with swal-confirm-form class that submit a form via form attribute
            document.querySelectorAll('button.swal-confirm-form[form]:not([data-swal-attached])').forEach(function(button) {
                button.setAttribute('data-swal-attached', 'true');
                
                const clickHandler = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const buttonEl = this;
                    const formId = buttonEl.getAttribute('form');
                    const formEl = document.getElementById(formId);
                    
                    if (!formEl) return;
                    
                    Swal.fire({
                        title: buttonEl.dataset.title || 'Are you sure?',
                        text: buttonEl.dataset.text || 'This action cannot be undone.',
                        icon: buttonEl.dataset.icon || 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#87A96B',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: buttonEl.dataset.confirmText || 'Yes, proceed',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            formEl.submit();
                        }
                    });
                };
                
                button.addEventListener('click', clickHandler);
            });
        }

        // Attach handlers on page load
        document.addEventListener('DOMContentLoaded', attachSwalConfirmHandlers);
        
        // Re-attach handlers when modals are shown (for dynamically added forms)
        $(document).on('shown.bs.modal', function() {
            setTimeout(attachSwalConfirmHandlers, 100);
        });
    </script>

    @stack('scripts')

    <!-- Account Information Modal -->
    <div class="modal fade" id="accountInfoModal" tabindex="-1" role="dialog" aria-labelledby="accountInfoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="accountInfoModalLabel">
                        <i class="fas fa-user mr-2"></i>Account Information
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if(Auth::check())
                        @php
                            $user = Auth::user()->load('employee');
                            $userName = 'User';
                            $userEmail = $user->Email ?? '';

                            if ($user->employee) {
                                $userName = $user->employee->full_name ?? 'User';
                            } else {
                                $emailParts = explode('@', $userEmail);
                                $userName = ucfirst($emailParts[0]);
                            }
                        @endphp
                        <div class="form-group">
                            <label><strong>Full Name:</strong></label>
                            <p>{{ $userName }}</p>
                        </div>
                        <div class="form-group">
                            <label><strong>Email:</strong></label>
                            <p>{{ $userEmail }}</p>
                        </div>
                        @if($user->employee)
                            <div class="form-group">
                                <label><strong>Position:</strong></label>
                                <p>{{ $user->employee->position->PositionName ?? 'N/A' }}</p>
                            </div>
                        @endif
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fix navbar position when sidebar is toggled
        (function () {
            function updateNavbarPosition() {
                const header = document.querySelector('.main-header');
                if (!header) return;

                const body = document.body;
                const isCollapsed = body.classList.contains('sidebar-collapse');

                if (window.innerWidth >= 992) {
                    const newLeft = isCollapsed ? '78px' : '250px';
                    header.style.setProperty('left', newLeft, 'important');
                    header.style.setProperty('right', '0', 'important');
                } else {
                    header.style.setProperty('left', '0', 'important');
                    header.style.setProperty('right', '0', 'important');
                }
            }

            // Run immediately
            updateNavbarPosition();

            // Also run on DOM ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', updateNavbarPosition);
            }

            // Listen for sidebar toggle (AdminLTE pushmenu) - use event delegation with capture
            document.addEventListener('click', function (e) {
                const pushMenu = e.target.closest('[data-widget="pushmenu"]');
                if (pushMenu) {
                    // Wait for AdminLTE to toggle the class
                    setTimeout(function () {
                        updateNavbarPosition();
                        // Also check again after a longer delay
                        setTimeout(updateNavbarPosition, 300);
                    }, 100);
                }
            }, true);

            // Listen for window resize
            window.addEventListener('resize', updateNavbarPosition);

            // Use MutationObserver to watch for class changes on body
            const observer = new MutationObserver(function (mutations) {
                let shouldUpdate = false;
                mutations.forEach(function (mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        shouldUpdate = true;
                    }
                });
                if (shouldUpdate) {
                    setTimeout(updateNavbarPosition, 50);
                }
            });

            observer.observe(document.body, {
                attributes: true,
                attributeFilter: ['class']
            });

            // Also observe wrapper if it exists
            const wrapper = document.querySelector('.wrapper');
            if (wrapper) {
                observer.observe(wrapper, {
                    attributes: true,
                    attributeFilter: ['class']
                });
            }

            // Periodic check as fallback
            setInterval(updateNavbarPosition, 1000);
        })();
    </script>
</body>

</html>
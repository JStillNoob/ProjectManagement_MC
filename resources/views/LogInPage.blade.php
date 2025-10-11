<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Management - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-green: #52b788;
            --dark-green: #7fb069;
            --light-green: #f0f8f0;
            --accent-green: #88d8a3;
            --reseda-green: #f8fcf7;
            --text-dark: #2d5a3d;
            --text-light: #6c757d;
            --white: #ffffff;
            --shadow: rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--light-green) 0%, var(--reseda-green) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        /* Background Pattern */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(82, 183, 136, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(136, 216, 163, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(127, 176, 105, 0.05) 0%, transparent 50%);
            z-index: 0;
        }

        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .login-container {
            background: var(--white);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 600px;
        }

        /* Left Side - Branding */
        .branding-section {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: var(--white);
            position: relative;
        }

        .branding-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .logo-container {
            position: relative;
            z-index: 2;
            margin-bottom: 2rem;
        }

        .logo-img {
            width: 120px;
            height: auto;
            filter: brightness(0) invert(1);
            transition: transform 0.3s ease;
        }

        .logo-img:hover {
            transform: scale(1.05);
        }

        .brand-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            position: relative;
            z-index: 2;
        }

        .brand-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            position: relative;
            z-index: 2;
        }

        .features-list {
            list-style: none;
            position: relative;
            z-index: 2;
        }

        .features-list li {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            font-size: 1rem;
        }

        .features-list i {
            margin-right: 0.75rem;
            font-size: 1.2rem;
            color: var(--accent-green);
        }

        /* Right Side - Login Form */
        .login-section {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-title {
            font-size: 2rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            color: var(--text-light);
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-green);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(82, 183, 136, 0.1);
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-size: 1.1rem;
            z-index: 3;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            font-size: 1.1rem;
            z-index: 3;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: var(--primary-green);
        }

        .login-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
            color: var(--white);
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(82, 183, 136, 0.3);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .forgot-password {
            text-align: center;
            margin-top: 1rem;
        }

        .forgot-password a {
            color: var(--primary-green);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: var(--dark-green);
        }

        .signup-link {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e9ecef;
        }

        .signup-link p {
            color: var(--text-light);
            margin-bottom: 0;
        }

        .signup-link a {
            color: var(--primary-green);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .signup-link a:hover {
            color: var(--dark-green);
        }

        /* Alert Styling */
        .alert {
            border-radius: 12px;
            border: none;
            margin-bottom: 1.5rem;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .alert-success {
            background: #d1edff;
            color: #0c5460;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-container {
                grid-template-columns: 1fr;
                margin: 1rem;
                border-radius: 16px;
            }

            .branding-section {
                padding: 2rem;
                order: 2;
            }

            .login-section {
                padding: 2rem;
                order: 1;
            }

            .brand-title {
                font-size: 2rem;
            }

            .login-title {
                font-size: 1.75rem;
            }
        }

        @media (max-width: 480px) {
            .login-wrapper {
                padding: 1rem;
            }

            .branding-section,
            .login-section {
                padding: 1.5rem;
            }

            .brand-title {
                font-size: 1.75rem;
            }

            .login-title {
                font-size: 1.5rem;
            }

            .form-control {
                padding: 0.875rem 0.875rem 0.875rem 2.5rem;
            }

            .input-icon {
                left: 0.875rem;
            }

            .password-toggle {
                right: 0.875rem;
            }
        }

        /* Loading Animation */
        .loading {
            display: none;
        }

        .login-btn.loading {
            position: relative;
            color: transparent;
        }

        .login-btn.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid var(--white);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <div class="login-container">
            <!-- Left Side - Branding -->
            <div class="branding-section">
                <div class="logo-container">
                    <img src="{{ asset('images/Screenshot_2025-06-23_082305-removebg-preview.png') }}" alt="Project Management Logo" class="logo-img">
                </div>
                <h1 class="brand-title">Project Management</h1>
                <p class="brand-subtitle">Streamline your projects with our comprehensive management system</p>
                <ul class="features-list">
                    <li>
                        <i class="bi bi-people-fill"></i>
                        <span>Team Collaboration</span>
                    </li>
                    <li>
                        <i class="bi bi-kanban-fill"></i>
                        <span>Project Tracking</span>
                    </li>
                    <li>
                        <i class="bi bi-graph-up-arrow"></i>
                        <span>Progress Analytics</span>
                    </li>
                    <li>
                        <i class="bi bi-shield-check"></i>
                        <span>Secure & Reliable</span>
                    </li>
                </ul>
            </div>

            <!-- Right Side - Login Form -->
            <div class="login-section">
                <div class="login-header">
                    <h2 class="login-title">Welcome Back!</h2>
                    <p class="login-subtitle">Sign in to your account to continue</p>
                </div>

                <form action="{{ route('login_now') }}" method="POST" id="loginForm">
                    @csrf
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="bi bi-envelope input-icon"></i>
                            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Enter your password" required>
                            <button type="button" id="togglePassword" class="password-toggle">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="login-btn" id="loginBtn">
                        <span class="btn-text">Sign In</span>
                        <div class="loading">
                            <div class="spinner"></div>
                        </div>
                    </button>
                </form>

                <div class="forgot-password">
                    <a href="#" onclick="alert('Forgot password functionality coming soon!')">Forgot your password?</a>
                </div>

                <div class="signup-link">
                    <p>Don't have an account? <a href="#" onclick="alert('Sign up functionality coming soon!')">Create one here</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password toggle functionality
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('passwordInput');
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });

        // Form submission with loading state
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const loginBtn = document.getElementById('loginBtn');
            const btnText = loginBtn.querySelector('.btn-text');
            
            // Add loading state
            loginBtn.classList.add('loading');
            btnText.style.display = 'none';
            
            // Disable button to prevent double submission
            loginBtn.disabled = true;
            
            // Optional: Add a timeout to reset if something goes wrong
            setTimeout(() => {
                if (loginBtn.classList.contains('loading')) {
                    loginBtn.classList.remove('loading');
                    btnText.style.display = 'inline';
                    loginBtn.disabled = false;
                }
            }, 10000); // 10 second timeout
        });

        // Input focus effects
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });

        // Add some interactive animations
        document.addEventListener('DOMContentLoaded', function() {
            // Animate elements on load
            const elements = document.querySelectorAll('.branding-section > *, .login-section > *');
            elements.forEach((element, index) => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>

</html>
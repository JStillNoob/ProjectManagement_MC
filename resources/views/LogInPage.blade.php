<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Montserrat', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #929292 0%, #9e9d9d 100%);
            position: relative;
            overflow-x: hidden;
        }

        .login-container {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: 400px;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            background: linear-gradient(135deg, #fdfafa 0%, #838282 90%);
            box-shadow: -5px 0 25px rgba(0, 0, 0, 0.5);
        }

        .login-card {
            width: 100%;
            max-width: 320px;
            padding: 20px;
        }

        .login-title {
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
            color: #ffffff;
            font-weight: 800;
            text-align: center;
        }

        .login-subtitle {
            color: #ffffff;
            font-size: 1rem;
            margin-bottom: 2rem;
            text-align: center;
            opacity: 0.9;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group-text {
            background-color: #03BD00;
            border: none;
            color: white;
        }

        .form-control {
            border: none;
            border-radius: 0 5px 5px 0;
            padding: 12px;
        }

        .form-control:focus {
            box-shadow: 0 0 0 2px rgba(3, 189, 0, 0.3);
        }

        .login-btn {
            background: white;
            color: #03BD00;
            border: none;
            padding: 12px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 6px rgba(26, 37, 61, 0.2);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(1, 45, 12, 0.3);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .login-container {
                position: relative;
                width: 100%;
                min-height: 100vh;
            }
        }

        @media (max-width: 576px) {
            .login-card {
                padding: 15px;
            }

            .login-title {
                font-size: 1.8rem;
            }
        }

        .content-left {
            padding: 40px;
            color: white;
            max-width: 600px;
        }

        .main-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            color: white;
        }

        .main-subtitle {
            font-size: 1.2rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .feature-list {
            list-style-type: none;
            padding: 0;
        }

        .feature-list li {
            margin-bottom: 15px;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
        }

        .feature-list i {
            color: #03BD00;
            margin-right: 10px;
            font-size: 1.5rem;
        }

        .logo-img {
            justify-content: center;
            width: 300px;
            /* Resize width */
            height: auto;
            /* Keep aspect ratio */
            max-height: 450px;
            /* Optional limit */
            object-fit: contain;
            /* Prevent stretching */
            margin-top: -150px;


            /* pull the logo up */
            margin-inline-start: -10px;


        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">

        </div>
    </div>

    <div class="login-container">
        <div class="login-card">
            <form action="{{ route('login_now') }}" method="POST">
                @csrf
                <div class="text-center mb-4">
                    <a class="active" href="#">
                        <img src="{{ asset('images/Screenshot_2025-06-23_082305-removebg-preview.png') }}" alt="Logo"
                            class="logo-img">
                    </a>
                </div>

                <div class="text-center mb-4">
                    <h1 class="login-title">Welcome Back!</h1>
                    <p class="login-subtitle">Sign in to access your account</p>
                </div>

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

                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="bi bi-person-square"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="Email" aria-label="Email"
                        required>
                </div>

                <div class="input-group mb-4">
                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                    <input type="password" name="password" id="passwordInput" class="form-control"
                        placeholder="Password" aria-label="Password" required>
                    <button type="button" id="togglePassword" class="btn btn-light">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>



                <button type="submit" class="login-btn">Sign In</button>

                <div class="text-center mt-3">
                    <a href="#" class="text-light" style="text-decoration: none;">Forgot password?</a>
                </div>

                <div class="text-center mt-4">
                    <p class="text-light">Don't have an account? <a href="#"
                            style="color: #03BD00; text-decoration: none;">Sign up</a></p>
                </div>
            </form>
        </div>
    </div>

    <script>
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
    </script>
</body>

</html>
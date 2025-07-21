<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal Login - {{ $schoolName->schoolname ?? 'DPSG' }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .login-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 10px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 5px;
        }

        .login-header {
            margin-bottom: 20px;
            text-align: center;
        }

        .login-header img {
            max-width: 100px;
        }

        .login-header h1 {
            margin-top: 10px;
        }

        .login-footer {
            text-align: center;
            margin-top: 20px;
        }

        .portal-label {
            font-weight: bold;
            /* Makes the label bold */
        }

        .register-link {
            text-align: right;
            margin-top: 20px;
        }

        .register-link a {
            font-size: 1em;
            color: darkblue;
            font-weight: bold;
        }

        .register-link a:hover {
            text-decoration: underline;
            color: #007bff;
        }

        .form-control {
            border: 1px solid #ced4da;
            border-radius: 8px;
            padding: 10px;
            font-size: 1em;
        }

        .form-control:focus {
            border-color: #00aaff;
            box-shadow: 0 0 0 0.2rem rgba(0, 170, 255, 0.25);
        }

        .btn-orange {
            background-color: darkorange;
            border-color: orange;
            color: white;
            font-weight: bold;
        }

        .btn-orange:hover {
            background-color: orange;
            border-color: darkorange;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header"><a href="{{ url('/') }}">
                    <img src="{{ asset('public/images/logo.png') }}" alt="School Logo"> </a>
                <h4><strong style="color:green">{{ $schoolName->schoolname ?? 'DPSG' }}</strong></h4>
                <h4>
                    <p class="text-muted portal-label">Student Portal</p>
                </h4>
            </div>
            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif


            <form method="POST" action="{{ route('portallogin') }}">
                @csrf
                <div class="form-group">
                    <label for="matno"><strong>Matriculation Number</strong></label>
                    <input type="text" class="form-control @error('matno') is-invalid @enderror" id="matno" name="matno" autocomplete="off" value="{{ old('matno') }}" required>
                    @error('matno')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password"><strong>Password</strong></label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-orange btn-block">Login</button>
            </form>

            <div align="right">
                <a href="{{ url('/forgotpass') }}">Forgot Password</a>
            </div>


            <div class="d-flex justify-content-between">
                <div class="register-link">
                    <a href="{{ url('/') }}">
                        << Back to Home</a>
                </div>
                <div class="register-link">
                    <a href="{{ url('/portalverify') }}">Student Verification >></a>
                </div>
            </div>


            <div class="login-footer">
                <p class="text-muted">© {{ date('Y') }} {{ $schoolName->schoolname ?? 'DPSG' }}</p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
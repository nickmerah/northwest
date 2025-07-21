<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Clearance - {{ $schoolName->schoolname ?? 'DPSG' }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
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
            margin-top: 50px;
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
            background-color: darkgreen;
            border-color: green;
            color: white;
            font-weight: bold;
        }

        .btn-orange:hover {
            background-color: green;
            border-color: darkgreen;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header"><a href="{{ url('/') }}">
                    <img src="{{ asset('public/images/logo-big.png') }}" alt="School Logo"> </a>
                <h4><strong style="color:green">{{ $schoolName->schoolname ?? 'DPSG' }}</strong></h4>
                <h4>
                    <p class="text-muted portal-label">Final Clearance</p>
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
            <form method="POST" action="{{ route('clearancelogin') }}">
                @csrf
                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-user"></i></div>
                        </div>
                        <input type="text" class="form-control @error('matno') is-invalid @enderror" id="matno" name="matno" placeholder="Matriculation No" required autocomplete="off">
                        @error('matno')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-lock"></i></div>
                        </div>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" required autocomplete="off">
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-orange btn-block">Login</button>
            </form>
            <div align="right">
                <a href="{{ url('/clearanceforgotpass') }}">Forgot Password</a>
            </div>
            <div class="d-flex justify-content-between">
                <div class="register-link">
                    <a href="{{ url('/') }}">
                        << Back to Home</a>
                </div>
                <div class="register-link">
                    <a href="{{ url('/clearanceregister') }}">New Registration</a>
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
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - {{ $schoolName->schoolname ?? 'DPSG' }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .register-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
            font-size: medium;
        }

        .register-header {
            margin-bottom: 20px;
            text-align: center;
        }

        .register-header img {
            max-width: 100px;
        }

        .register-header h1 {
            margin-top: 10px;
        }

        .register-footer {
            text-align: center;
            margin-top: 20px;
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

        .btn-submit {
            background-color: darkgreen;
            border-color: green;
            color: white;
            font-weight: bold;
        }

        .btn-submit:hover {
            background-color: green;
            border-color: darkgreen;
        }

        .g-recaptcha {
            margin-top: 20px;
        }

        .login-footer {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="register-container">
            <div class="register-header">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('public/images/logo.png') }}" alt="School Logo">
                </a>
                <h4><strong style="color:green">{{ $schoolName->schoolname ?? 'DPSG' }}</strong></h4>
                <h4>
                    <p class="text-muted">Student Portal</p>
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

            <form method="POST" action="{{ route('portalregister') }}">
                @csrf



                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-user"></i></div>
                        </div>
                        <input type="text" name="matno" class="form-control @error('matno') is-invalid @enderror" value="{{ $student->matno }}" readonly>

                    </div>
                </div>


                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-user"></i></div>
                        </div>
                        <input type="text" class="form-control @error('surname') is-invalid @enderror" value="{{ $student->surname }}" disabled>

                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-user"></i></div>
                        </div>
                        <input type="text" class="form-control @error('firstname') is-invalid @enderror" value="{{ $student->firstname }}" disabled>

                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-user"></i></div>
                        </div>
                        <input type="text" class="form-control @error('othernames') is-invalid @enderror" value="{{ $student->othername ?? 'N/A' }}" disabled>
                    </div>
                </div>


                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-lock"></i></div>
                        </div>
                        <input name="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Choose a Password" required>
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-shield-alt"></i></div>
                        </div>
                        <input name="captcha" type="text" class="form-control @error('captcha') is-invalid @enderror" placeholder="Solve: {{ $question }}" required>
                        @error('captcha')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </form>

            <div class="register-footer">
                <p class="text-muted">Already registered? <a href="{{ route('portallogin') }}">Log in here</a></p>
            </div>

            <div class="login-footer">
                <p class="text-muted">© {{ date('Y') }} {{ $schoolName->schoolname ?? 'DPSG' }}</p>
            </div>
        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
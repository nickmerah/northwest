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
                    <p class="text-muted">Final Clearance</p>
                </h4>
            </div>

            <form method="POST" action="{{ route('clearanceregister') }}">
                @csrf
                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-user"></i></div>
                        </div>
                        <input name="surname" type="text" class="form-control @error('surname') is-invalid @enderror" placeholder="Surname" required autocomplete="off" value="{{ old('surname') }}">
                        @error('surname')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-user"></i></div>
                        </div>
                        <input name="firstname" type="text" class="form-control @error('firstname') is-invalid @enderror" placeholder="First Name" required autocomplete="off" value="{{ old('firstname') }}">
                        @error('firstname')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-user"></i></div>
                        </div>
                        <input name="othernames" type="text" class="form-control @error('othernames') is-invalid @enderror" placeholder="Other Names" autocomplete="off" value="{{ old('othernames') }}">
                        @error('othernames')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-envelope"></i></div>
                        </div>
                        <input name="email" type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email Address" autocomplete="off" required value="{{ old('email') }}">
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>


                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-phone"></i></div>
                        </div>
                        <input name="phone" type="tel" class="form-control @error('phone') is-invalid @enderror" placeholder="Phone Number" autocomplete="off" required value="{{ old('phone') }}">
                        @error('phone')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>


                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-id-card"></i></div>
                        </div>
                        <input name="matric_number" type="text" class="form-control @error('matric_number') is-invalid @enderror" placeholder="Matriculation Number" required autocomplete="off" value="{{ old('matric_number') }}">
                        @error('matric_number')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                        </div>
                        <select name="year_of_graduation" id="year_of_graduation" class="form-control @error('year_of_graduation') is-invalid @enderror" required>
                            <option value=""> Select Year of Graduation</option>
                            @for ($year = date('Y'); $year >= 2003; $year--)
                            <option value="{{ $year }}" {{ old('year_of_graduation') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                        @error('year_of_graduation')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-building"></i></div>
                        </div>
                        <select name="programme" id="programme" class="form-control @error('programme') is-invalid @enderror" required onchange="fetchDepartments(); fetchLevels()">
                            <option value="">Select Programme</option>
                            @foreach ($programmes as $programme)
                            <option value="{{ $programme->programme_id }}" {{ old('programme') == $programme->programme_id ? 'selected' : '' }}>{{ $programme->programme_name }}</option>
                            @endforeach
                        </select>
                        @error('programme')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-building"></i></div>
                        </div>
                        <select name="department" id="department" class="form-control @error('department') is-invalid @enderror" required>
                            <option value="">Select Department</option>
                        </select>
                        @error('department')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <script>
                    function fetchDepartments() {
                        var programmeId = document.getElementById('programme').value;

                        // Clear the current options in the department dropdown
                        var departmentSelect = document.getElementById('department');
                        departmentSelect.innerHTML = '<option value="">Select Department</option>';

                        if (programmeId) {
                            fetch(`{{ secure_url('/') }}/get-departments/${programmeId}`)
                                .then(response => response.json())
                                .then(data => {
                                    data.forEach(function(department) {
                                        var option = document.createElement('option');
                                        option.value = department.do_id;
                                        option.textContent = department.programme_option;
                                        departmentSelect.appendChild(option);
                                    });
                                })
                                .catch(error => console.error('Error fetching departments:', error));
                        }
                    }
                </script>

                <div class="form-group">

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-graduation-cap"></i></div>
                        </div>
                        <select name="level" id="level" class="form-control @error('level') is-invalid @enderror" required>
                            <option value="">Select Level</option>
                        </select>
                        @error('level')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>

                <script>
                    function fetchLevels() {
                        var progId = document.getElementById('programme').value;


                        // Clear the current options in the department dropdown
                        var levelSelect = document.getElementById('level');
                        levelSelect.innerHTML = '<option value="">Select Level</option>';

                        if (progId) {
                            fetch(`{{ secure_url('/') }}/get-levels/${progId}`)
                                .then(response => response.json())
                                .then(data => {
                                    data.forEach(function(level) {
                                        var option = document.createElement('option');
                                        option.value = level.level_id;
                                        option.textContent = level.level_name;
                                        levelSelect.appendChild(option);
                                    });
                                })
                                .catch(error => console.error('Error fetching levels:', error));
                        }
                    }
                </script>

                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-lock"></i></div>
                        </div>
                        <input name="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required>
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
                <p class="text-muted">Already registered? <a href="{{ route('clearancelogin') }}">Log in here</a></p>
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
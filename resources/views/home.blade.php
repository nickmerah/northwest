<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($schoolName->schoolname) ? $schoolName->schoolname : "DPSG" ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom CSS to make all cards have the same height */
        .card {
            height: 100%;
            /* Makes the card stretch to its container height */
        }

        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            text-align: center;
        }

        .btn-primary {
            margin-top: auto;
            /* Pushes the button to the bottom */
        }

        /* Scrolling marquee styles */
        .marquee-container {
            background-color: #f8f9fa;
            padding: 10px 0;
            color: darkblue;
            font-weight: bold;
            overflow: hidden;
            margin-top: 20px;
            /* Adds space between banner and marquee */
            margin-bottom: 30px;
            /* Adds space between marquee and cards */
        }

        .marquee {
            display: inline-block;
            white-space: nowrap;
            animation: marquee 30s linear infinite;
        }

        @keyframes marquee {
            from {
                transform: translateX(100%);
            }

            to {
                transform: translateX(-100%);
            }
        }

        /* Banner styles */
        .banner {
            background-color: #0056b3;
            /* Dark blue background */
            padding: 40px 0;
            /* Increased padding for larger banner */
            color: white;
        }

        /* Navbar styles */
        .navbar-dark.bg-dark {
            background-color: darkgreen !important;
            /* Dark green background for navbar */
        }

        /* Footer styles */
        footer {
            background-color: #f8f9fa;
            width: 100%;
            position: static;
            /* Change to static to make footer responsive */
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

        .application-status {
            font-size: 1.5em;
            color: darkblue;
            font-weight: bold;
        }

        @media (max-width: 767px) {

            /* Responsive adjustments for mobile */
            .navbar-brand h3 {
                font-size: 1.2rem;
                /* Adjusted font size for smaller screens */
            }

            .marquee {
                font-size: 0.9rem;
                /* Adjusted marquee font size for smaller screens */
            }

            .application-status {
                font-size: 1rem;
                /* Adjusted application status font size for smaller screens */
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <h3> <img src="{{ asset('public/images/logox.png') }}" alt="School Logo" class="mr-2" style="max-width: 50px;">
                    {{ $schoolName->schoolname }}
                </h3>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="{{ url('/') }}">Home</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="#">Portal</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="{{ url('/clearancelogin') }}">Clearance</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="{{ url('/remediallogin') }}">Remedial</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Scrolling Marquee -->
    <div class="marquee-container">
        <div class="marquee">{{ $schoolName->markuee }}</div>
    </div>
    <br>

    <!-- Main Content -->
    <main class="container my-4">
        <div class="row">
            <!-- Student Portal Card -->
            <div class="col-md-4 col-sm-6 d-flex align-items-stretch mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title"><strong>Student Portal</strong> </h3>
                        <p class="card-text">Access your student resources here.</p>
                        <a href="{{ route('portallogin') }}" class="btn btn-orange">Go to Student Portal</a>
                    </div>
                </div>
            </div>

            <!-- Clearance Model Card -->
            <div class="col-md-4 col-sm-6 d-flex align-items-stretch mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title"><strong>Final Clearance </strong></h3>
                        <p class="card-text">Manage your clearance payments easily.</p>
                        <a href="{{ url('/clearancelogin') }}" class="btn btn-orange">Go to Final Clearance</a>
                    </div>
                </div>
            </div>

            <!-- Remedial Card -->
            <div class="col-md-4 col-sm-6 d-flex align-items-stretch mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title"><strong>Remedial Payment</strong></h3>
                        <p class="card-text">Make Remedial Seamlessly</p>
                        <a href="{{ url('/remediallogin') }}" class="btn btn-orange">Make your Remedial Payments</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <p id="demo" class="application-status text-center"></p>

    <!-- Footer -->
    <footer class="bg-light text-center py-3">
        <div class="container">
            <p>&copy; 2024 - {{ date('Y') }} {{ $schoolName->schoolabvname }}</p>
        </div>
    </footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
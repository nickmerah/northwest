<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $schoolName }} .:: Student Portal</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header */
        .header {
            background-color: #ffffff;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .header img {
            max-width: 80px;
        }

        .header h1 {
            font-size: 1.75rem;
            color: green;
            margin: 0;
        }

        .portal-label {
            color: #6c757d;
            font-size: 1.5rem;
            font-weight: bold;
            margin: 5px 0;
        }

        /* Sidebar */
        .sidebar {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .sidebar h4 {
            font-size: 1.25rem;
            margin-bottom: 15px;
        }

        .sidebar a {
            display: block;
            color: darkblue;
            font-weight: bold;
            padding: 8px 0;
            text-decoration: none;
        }

        .sidebar a:hover {
            text-decoration: underline;
            color: #007bff;
        }

        /* Passport / Photo Holder */
        .passport {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: #e9ecef;
            margin: 0 auto 15px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1rem;
            color: #6c757d;
            border: 2px solid #dee2e6;
        }

        /* Main Content */
        .main-content {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-check-input {
            margin-right: 10px;
        }

        .dashboard-cards {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 32%;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
        }

        .table {
            width: 100%;
            margin-bottom: 30px;
        }

        /* Footer */
        .footer {
            background-color: #ffffff;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .footer p {
            margin: 0;
            color: #6c757d;
        }

        /* Student Info */
        .student-info {
            margin-bottom: 20px;
            font-size: 1rem;
            color: #343a40;
        }

        /* Responsive Layout */
        @media (min-width: 768px) {
            .content-wrapper {
                display: flex;
            }

            .sidebar {
                flex: 0 0 25%;
                margin-right: 20px;
            }

            .main-content {
                flex: 1;
            }

            .dashboard-cards {
                justify-content: flex-start;
            }

            .card {
                width: 30%;
            }
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
    <div class="container-wrapper">
        <!-- Header -->
        <div class="header">
            <a href="{{ url('/portalDashboard') }}">
                <img src="https://portal.mydspg.edu.ng/eportal/public/images/logo-big.png" alt="School Logo">
            </a>
            <h1>{{ $schoolName }}</h1>
            <p class="portal-label">Student Portal</p>
            <p> <strong>{{ $student->surname }}</strong>, {{ $student->firstname }} {{ $student->othernames }} <br>
                <strong> {{ $student->matric_no }}</strong> <br>
                {{ $student->programmeType->programmet_aname }} {{ $student->level->level_name }} {{ $student->departmentOption->programme_option }}<br>
                Current Session: <strong>{{ $currentSession }}/{{ $currentSession + 1}} </strong>
            </p>
        </div>

        <!-- Main Content and Sidebar -->
        <div class="container">
            <div class="content-wrapper">
                <!-- Sidebar -->
                <div class="sidebar">
                    <!-- Passport Photo Holder -->
                    <div class="passport">

                        @php
                        $photoPath = storage_path('app/public/passport/' . $student->std_photo);
                        @endphp

                        @if (file_exists($photoPath))
                        <img alt="Photo" src="{{ asset('storage/app/public/passport/'.$student->std_photo) }}" class="rounded-circle img-responsive mt-3" width="135" height="131" />
                        @else
                        <img alt="Photo" src="https://portal.mydspg.edu.ng/admissions/writable/thumbs/avatar.jpg" class="rounded-circle img-responsive mt-3" width="135" height="131" />
                        @endif




                    </div>

                    <hr>
                    <h4>Menu</h4>
                    <a href="{{ url('/portalDashboard') }}">Dashboard</a>
                    <a href="#profileSubmenu" data-toggle="collapse" aria-expanded="false" aria-controls="profileSubmenu">Profile <span class="dropdown-indicator">▼</span></a>
                    <div class="collapse" id="profileSubmenu">
                        <a href="{{ url('/profile') }}"> > Update Profile</a>
                        <!--  <a href="#">Print Profile</a>-->
                    </div>
                    <a href="#coursesSubmenu" data-toggle="collapse" aria-expanded="false" aria-controls="coursesSubmenu">Fee Payment <span class="dropdown-indicator">▼</span></a>
                    <div class="collapse" id="coursesSubmenu">
                        <a href="{{ url('/fees') }}"> > School Fees</a>
                        <a href="{{ url('/ofees') }}"> > Other Fees</a>
                        <a href="{{ url('/bpfee') }}"> > Previous Fees</a>
                    </div>

                    <a href="#feeSubmenu" data-toggle="collapse" aria-expanded="false" aria-controls="feeSubmenu">2023 Fee Payment <span class="dropdown-indicator">▼</span></a>
                    <div class="collapse" id="feeSubmenu">
                        <a href="{{ url('/sfees') }}"> > School Fees</a>
                        <a href="{{ url('/bfees') }}"> > Balance Fees</a>
                    </div>

                    <a href="#courseSubmenu" data-toggle="collapse" aria-expanded="false" aria-controls="courseSubmenu">Course Registration <span class="dropdown-indicator">▼</span></a>
                    <div class="collapse" id="courseSubmenu">
                        <a href="{{ url('/courses') }}"> > Register</a>
                        <a href="{{ url('/viewcourse') }}"> > View / Drop</a>
                        <a href="{{ url('/creghistory') }}"> > History</a>
                    </div>

                    <a href="#hostelSubmenu" data-toggle="collapse" aria-expanded="false" aria-controls="hostelSubmenu">Hostel Accomodation <span class="dropdown-indicator">▼</span></a>
                    <div class="collapse" id="hostelSubmenu">
                        <a href="{{ url('/hostels') }}"> > Make Payment</a>
                        <a href="{{ url('/reserveRoom') }}"> > Reserve Room</a>
                        <a href="{{ url('/myRoom') }}"> > My Reservation</a>
                    </div>
                    <a href="{{ url('/pfhistory') }}">Payment History</a>
                    <a href="{{ url('/plogout') }}">Logout</a>
                </div>

                <!-- Dynamic Content Section -->
                <div class="main-content">
                    @yield('content') <!-- This will be filled by child templates -->
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>© <?= date('Y'); ?> - {{ $schoolName }}</p>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
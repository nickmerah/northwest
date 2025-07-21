<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $schoolName }} .:: Remedial Payment</title>
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
            <a href="{{ url('/remedialDashboard') }}">
                <img src="https://portal.mydspg.edu.ng/eportal/public/images/logo-big.png" alt="School Logo">
            </a>
            <h1>{{ $schoolName }}</h1>
            <p class="portal-label">Remedial Payment Portal</p>
            <p> <strong>{{ $student->surname }}</strong>, {{ $student->firstname }} {{ $student->othernames }} <br>
                <strong> {{ $student->matno }}</strong> <br>
                {{ $student->level }} Level<br>
                {{ $student->sess }}/{{ $student->sess + 1 }} Session
            </p>
        </div>

        <!-- Main Content and Sidebar -->
        <div class="container">
            <div class="content-wrapper">
                <!-- Sidebar -->
                <div class="sidebar">

                    <hr>
                    <h4>Menu</h4>
                    <a href="{{ url('/remedialDashboard') }}">Dashboard</a>
                    <a href="{{ url('/makepayment') }}">Make Payment</a>
                    <a href="{{ url('/rcourses') }}">Register Courses</a>
                    <a href="{{ url('/rphistory') }}">Payment History</a>
                    <a href="{{ url('/rlogout') }}">Logout</a>
                </div>

                <div class="main-content">
                    @yield('content')
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
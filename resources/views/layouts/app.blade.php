<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Clearance - {{ $schoolName->schoolname ?? 'DPSG' }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Sidebar styles */
        .sidebar {
            height: 100vh;
            position: fixed;
            left: 0;
            background-color: #f8f9fa;
            padding-top: 20px;
            width: 250px;
            overflow-y: auto;
            transition: margin-left 0.3s;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s;
        }

        .top-banner {
            background-color: #00008B;
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            width: calc(100% - 250px);
            left: 250px;
            top: 0;
            z-index: 1000;
        }

        .top-banner img {
            max-width: 50px;
            margin-right: 10px;
        }

        .top-banner .student-info {
            text-align: right;
        }

        .sidebar .nav-link.active {
            background-color: #00008B;
            color: white;
            font-weight: bold;
        }

        .sidebar .nav-link:hover {
            background-color: #00008B;
            color: white;
        }

        .table-custom {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .table-custom thead th {
            background-color: #00008B;
            color: white;
            text-align: center;
            font-weight: bold;
        }

        .table-custom tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        .table-custom tbody tr:hover {
            background-color: #e2e6ea;
        }

        .table-custom td {
            vertical-align: middle;
        }

        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }

        .payment-section {
            margin-top: 20px;
            text-align: center;
        }

        .btn-payment {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-payment:hover {
            background-color: #218838;
        }

        .status {
            font-weight: bold;
            margin-top: 10px;
        }

        /* Mobile adjustments */
        @media (max-width: 767px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                padding-top: 0;
                margin-left: 0;
            }

            .content {
                margin-left: 0;
                padding-top: 70px; /* Ensure content doesn’t overlap with header */
            }

            .top-banner {
                left: 0;
                width: 100%;
                position: relative; /* Adjusted for mobile */
                z-index: 1000;
                flex-direction: column;
                align-items: flex-start;
            }

            .top-banner .student-info {
                text-align: left;
                margin-top: 10px;
            }
        }
    </style>
</head>

<body>
    <!-- Top Banner -->
    <div class="top-banner">
        <div>
            <img src="{{ asset('public/images/logox.png') }}" alt="School Logo" style="max-width: 50px; margin-right: 10px;">
            <span class="large-text">{{ $schoolName->schoolname }}</span>
        </div>
        <br>
        <span>FINAL CLEARANCE</span>
           <br>
        <div>
            <span>{{ $student->surname }}, {{ $student->firstname }} {{ $student->othernames }} - {{ $student->matricno }}</span>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ Request::is('clearanceDashboard') ? 'active' : '' }}" href="{{ url('/clearanceDashboard') }}">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('clearanceFees') ? 'active' : '' }}" href="{{ url('/clearanceFees') }}">Pay Fees</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('clearancePayments') ? 'active' : '' }}" href="{{ url('/clearancePayments') }}">Payment History</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ url('/logout') }}">Logout</a>
            </li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="content">
        @yield('content')
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>

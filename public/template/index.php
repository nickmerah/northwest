<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Template with Sidebar</title>
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
            font-size: 1rem;
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

        .collapse a {
            font-weight: normal;
            padding-left: 15px;
        }

        .dropdown-indicator {
            float: right;
            font-size: 0.75rem;
        }

        /* Main Content */
        .main-content {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .main-content h2 {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .main-content p {
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        /* Dashboard Cards */
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

        .card h3 {
            font-size: 1.25rem;
            margin-bottom: 10px;
        }

        .card p {
            font-size: 1rem;
            margin: 0;
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
    </style>
</head>

<body>

    <div class="container-wrapper">
        <!-- Header -->
        <div class="header">
            <a href="{{ url('/') }}">
                <img src="https://portal.mydspg.edu.ng/eportal/public/images/logo-big.png" alt="School Logo">
            </a>
            <h1>Delta State Polytechnic, Ogwashi-Uku
            </h1>
            <p class="portal-label">Student Portal</p>
        </div>

        <!-- Main Content and Sidebar -->
        <div class="container">
            <div class="content-wrapper">
                <!-- Sidebar -->
                <div class="sidebar">
                    <div class="student-info">
                        <p><strong>Name:</strong> John Doe</p>
                        <p><strong>Matric No:</strong> DSPG12345</p>
                    </div>

                    <h4>Quick Links</h4>
                    <a href="#">Dashboard</a>
                    <a href="#">Results</a>
                    <a href="#">Settings</a>

                    <!-- Collapsible Courses Section with Dropdown Indicator -->
                    <a href="#coursesSubmenu" data-toggle="collapse" aria-expanded="false" aria-controls="coursesSubmenu">
                        Courses <span class="dropdown-indicator">▼</span>
                    </a>
                    <div class="collapse" id="coursesSubmenu">
                        <a href="#">Current Courses</a>
                        <a href="#">Past Courses</a>
                    </div>

                    <!-- Collapsible Profile Section with Dropdown Indicator -->
                    <a href="#profileSubmenu" data-toggle="collapse" aria-expanded="false" aria-controls="profileSubmenu">
                        Profile <span class="dropdown-indicator">▼</span>
                    </a>
                    <div class="collapse" id="profileSubmenu">
                        <a href="#">View Profile</a>
                        <a href="#">Edit Profile</a>
                        <a href="#">Change Password</a>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="main-content">
                    <h2>Welcome to the Student Portal</h2>
                    <p>Here, you can access your courses, view results, manage your profile, and much more. This student
                        portal is designed to be your one-stop destination for all your academic needs.</p>
                    <p>Get started by exploring the sidebar links, or check your notifications for important updates.</p>

                    <!-- Dashboard Cards -->
                    <div class="dashboard-cards">
                        <div class="card">
                            <h3>Fees Paid</h3>
                            <p>$1,200</p>
                        </div>&nbsp;&nbsp;
                        <div class="card">
                            <h3>Other Fees Paid</h3>
                            <p>$300</p>
                        </div>&nbsp;&nbsp;
                        <div class="card">
                            <h3>Course Registration</h3>
                            <p>5 Courses Registered</p>
                        </div>
                    </div>

                    <button class="btn btn-orange">Explore Courses</button>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>© DSPG</p>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>
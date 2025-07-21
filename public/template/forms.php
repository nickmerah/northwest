<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Payment</title>
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
            margin-bottom: 20px;
            width: 100%;
        }

        .table {
            margin-bottom: 30px;
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

        .form-check-input {
            margin-right: 10px;
        }

        /* Footer */
        .footer {
            background-color: #ffffff;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .footer p {
            margin: 0;
            color: #6c757d;
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
            <h1>Delta State Polytechnic, Ogwashi-Uku</h1>
            <p class="portal-label">Fee Payment</p>
        </div>

        <!-- Main Content and Sidebar -->
        <div class="container">
            <div class="content-wrapper">
                <!-- Sidebar -->
                <div class="sidebar">
                    <!-- Passport Photo Holder -->
                    <div class="passport">
                        Photo
                    </div>
                    <div class="student-info">
                        <p><strong>Name:</strong> John Doe</p>
                        <p><strong>Matric No:</strong> DSPG12345</p>
                    </div>
                    <hr>
                    <h4>Quick Links</h4>
                    <a href="#">Dashboard</a>
                    <a href="#">Courses</a>
                    <a href="#">Results</a>
                    <a href="#">Profile</a>
                    <a href="#">Settings</a>
                </div>

                <!-- Main Content -->
                <div class="main-content">
                    <h2>Your Profile</h2>

                    <!-- Form Starts Here -->
                    <form>
                        <!-- Full Name -->
                        <div class="form-group">
                            <label for="fullName">Full Name</label>
                            <input type="text" class="form-control" id="fullName" value="John Doe" required>
                        </div>

                        <!-- Address -->
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea class="form-control" id="address" rows="3" required>1234 Main Street, City, State</textarea>
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email">Email address</label>
                            <input type="email" class="form-control" id="email" value="johndoe@example.com" required>
                        </div>

                        <!-- Phone Number -->
                        <div class="form-group">
                            <label for="phoneNumber">Phone Number</label>
                            <input type="tel" class="form-control" id="phoneNumber" value="+1 234 567 8901" required>
                        </div>

                        <!-- Update Profile Button -->
                        <button type="submit" class="btn btn-orange btn-block">Update Profile</button>
                    </form>
                    <!-- Form Ends Here -->
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
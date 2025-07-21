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
                    <h2>Select Fees to Pay</h2>
                    <p>Please select the fee items you wish to pay for and click the "Pay Now" button to proceed.</p>

                    <!-- Responsive Table -->
                    <table class="table table-bordered table-responsive">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Select</th>
                                <th scope="col">Fee Item</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Due Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input class="form-check-input" type="checkbox" value="tuition">
                                </td>
                                <td>Tuition Fees</td>
                                <td>$1,200</td>
                                <td>31st Dec 2024</td>
                            </tr>
                            <tr>
                                <td>
                                    <input class="form-check-input" type="checkbox" value="library">
                                </td>
                                <td>Library Fees</td>
                                <td>$100</td>
                                <td>31st Dec 2024</td>
                            </tr>
                            <tr>
                                <td>
                                    <input class="form-check-input" type="checkbox" value="hostel">
                                </td>
                                <td>Hostel Fees</td>
                                <td>$500</td>
                                <td>31st Dec 2024</td>
                            </tr>
                            <tr>
                                <td>
                                    <input class="form-check-input" type="checkbox" value="exam">
                                </td>
                                <td>Examination Fees</td>
                                <td>$150</td>
                                <td>31st Dec 2024</td>
                            </tr>
                            <tr>
                                <td>
                                    <input class="form-check-input" type="checkbox" value="medical">
                                </td>
                                <td>Medical Fees</td>
                                <td>$75</td>
                                <td>31st Dec 2024</td>
                            </tr>
                        </tbody>
                    </table>

                    <button class="btn btn-orange">Pay Now</button>
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
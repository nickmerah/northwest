<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>{{ $SCHOOLNAME }} - Application Portal</title>
    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/app.min.css') }}">
    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/css/components.css') }}">
    <!-- Custom style CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/custom.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        .marquee-container {
            background-color: darkgreen;
            color: white;
            padding: 10px;
            font-size: 18px;
            overflow: hidden;
            white-space: nowrap;
            position: relative;
        }

        .marquee {
            display: inline-block;
            animation: marquee 20s linear infinite;
        }

        @keyframes marquee {
            from {
                transform: translateX(100%);
            }

            to {
                transform: translateX(-100%);
            }
        }
    </style>
</head>

<body style="background-color: white">

    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>
            <nav class="navbar navbar-expand-lg main-navbar sticky">
                <div class="form-inline mr-auto">
                    <ul class="navbar-nav mr-3">
                        <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg
									collapse-btn"> <i data-feather="align-justify"></i></a></li>
                        <li><a href="#" class="nav-link nav-link-lg fullscreen-btn">
                                <i data-feather="maximize"></i>
                            </a></li>

                        <h3> {{ $SCHOOLNAME }}</h3>

                    </ul>
                </div>
                <ul class="navbar-nav navbar-right">


                    <li class="dropdown"> <strong> {{ session('user') && session('user')['applicationNumber'] ? session('user')['applicationNumber'] : '' }}
                        </strong> </li>
                </ul>
            </nav>
            <div class="main-sidebar sidebar-style-2">
                <aside id="sidebar-wrapper">
                    <div class="sidebar-brand">
                        <a href="{{ route('admissions.dashboard') }}"> <img alt="image" src="{{ asset('public/assets/img/logo.png') }}" class="header-logo" /> <span class="logo-name">NW </span>
                        </a>
                    </div>
                    <div class="sidebar-user">

                        <div class="sidebar-user-details">
                            <div class="user-name">ADMISSION PORTAL </div>

                        </div>
                    </div>
                    <ul class="sidebar-menu">
                        <li class="menu-header">MENU</li>
                        <li><a class="nav-link" href="{{ route('admissions.dashboard') }}"><i data-feather="monitor"></i><span>Home</span></a></li>
                        <li><a class="nav-link" href="{{ route('admissions.myapplication') }}"><i data-feather="monitor"></i><span>My Application</span></a></li>
                        <li><a class="nav-link" href="#"><i data-feather="download"></i><span>Application Forms</span></a></li>







                        <li><a class="nav-link" href="{{ route('admissions.checkpayment') }}"><i data-feather="check"></i><span>Check Payment</span></a></li>
                        <li><a class="nav-link" href="{{ route('admissions.paymenthistory') }}"><i data-feather="dollar-sign"></i><span>Payment History</span></a></li>
                        <li><a class="nav-link" href="{{ route('admissions.logout') }}"><i data-feather="log-out"></i><span>Logout</span></a></li>


                    </ul>
                </aside>
            </div>

            <!-- Main Content -->
            @yield('content')
            <!-- End Main -->
            <footer class="main-footer">
                <div class="footer-left">
                    Copyright &copy; {{ date('Y') }}
                    <div class="bullet"></div> {{ $SCHOOLNAME }}</a>
                </div>
                <div class="footer-right">
                </div>
            </footer>
        </div>
    </div>
    <!-- General JS Scripts -->
    <script src="{{ asset('public/assets/js/app.min.js') }}"></script>
    <!-- JS Libraies -->
    <script src="{{ asset('public/assets/bundles/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('public/assets/bundles/amcharts4/core.js') }}"></script>
    <script src="{{ asset('public/assets/bundles/amcharts4/charts.js') }}"></script>
    <script src="{{ asset('public/assets/bundles/amcharts4/animated.js') }}"></script>
    <script src="{{ asset('public/assets/bundles/jquery.sparkline.min.js') }}"></script>
    <!-- Page Specific JS File -->
    <script src="{{ asset('public/assets/js/page/index.js') }}"></script>
    <!-- Template JS File -->
    <script src="{{ asset('public/assets/js/scripts.js') }}"></script>
    <!-- Custom JS File -->
    <script src="{{ asset('public/assets/js/custom.js') }}"></script>
</body>


</html>
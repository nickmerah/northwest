<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>{{ $SCHOOLNAME ?? 'DPSG' }} - Application Portal</title>
    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/app.min.css') }}">
    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/css/components.css') }}">
    <!-- Custom style CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/css/custom.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        .example5 {


            font-size: 15px;
            font-weight: bold;
            color: #00F
        }
    </style>
</head>

<body>

    <div id="app">
        <section class="section">
            <div class="container mt-0">
                @yield('content')
            </div>
            <div class="simple-footer">
                &copy; {{ date('Y') }} {{ $SCHOOLNAME }}. All rights reserved.
            </div>
    </div>
    </div>
    </div>
    </section>
    </div>

    <script>
        // Set the date we're counting down to
        var countDownDate = new Date("October 31, 2024").getTime();

        // Update the count down every 1 second
        var x = setInterval(function() {

            // Get today's date and time
            var now = new Date().getTime();

            // Find the distance between now and the count down date
            var distance = countDownDate - now;

            // Time calculations for days, hours, minutes and seconds
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Output the result in an element with id="demo"
            document.getElementById("demo").innerHTML = "Online Application Closes in: " + days + "Days " + hours + "Hrs " +
                minutes + "Mins " + seconds + "Secs ";

            // If the count down is over, write some text
            if (distance < 0) {
                clearInterval(x);
                document.getElementById("demo").innerHTML = "";
            }
        }, 1000);
    </script>


    <!-- General JS Scripts -->
    <script src="{{ asset('public/assets/js/app.min.js') }}"></script>
    <!-- JS Libraies -->

    <!-- Page Specific JS File -->
    <script src="{{ asset('public/assets/js/page/index2.js') }}"></script>
    <script src="{{ asset('public/assets/js/page/todo.js') }}"></script>
    <!-- Template JS File -->
    <script src="{{ asset('public/assets/js/scripts.js') }}"></script>
    <!-- Custom JS File -->
    <script src="{{ asset('public/assets/js/custom.js') }}"></script>

    <script src="{{ asset('public/assets/js/jquery.min.js') }}"></script>
</body>

</html>
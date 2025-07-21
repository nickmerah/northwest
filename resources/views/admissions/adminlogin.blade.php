<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Delta State Polytechnic, Ogwashi-Uku - Application Portal</title>
    <!-- General CSS Files -->
    <link rel="stylesheet" href="https://portal.mydspg.edu.ng/admissions/assets/css/app.min.css">
    <!-- Template CSS -->
    <link rel="stylesheet" href="https://portal.mydspg.edu.ng/admissions/assets/css/style.css">
    <link rel="stylesheet" href="https://portal.mydspg.edu.ng/admissions/assets/css/components.css">
    <!-- Custom style CSS -->
    <link rel="stylesheet" href="https://portal.mydspg.edu.ng/admissions/assets/css/custom.css">
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
                <div class="row">
                    <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-6 offset-xl-3">
                        <div class="login-brand">



                            <div align="center">
                                <a href="https://portal.mydspg.edu.ng/admissions/">
                                    <picture>
                                        <source media="(max-width: 799px)" srcset="https://portal.mydspg.edu.ng/admissions/assets/img/delta.png">
                                        <source media="(min-width: 800px)" srcset="https://portal.mydspg.edu.ng/admissions/assets/img/delta.png">
                                        <img src="https://portal.mydspg.edu.ng/admissions/assets/img/delta.png" alt="School Logo" class="responsive-img valign profile-image-login">
                                    </picture>

                                </a>
                            </div>

                        </div>
                        <div class="card card-primary">
                            <div class="card-header">
                                <h4>Login</h4>

                            </div>
                            <div class="card-body">





                                <form method="POST" action="https://portal.mydspg.edu.ng/admissions/account/alogin" class="needs-validation" novalidate="" autocomplete="off">
                                    <input type="hidden" name="csrf_test_name" value="d91c5e8b056466aa7b38d732fdb123c0">
                                    <div class="form-group">
                                        <label for="email"> Application Number</label>
                                        <input id="email" type="text" class="form-control" name="email" tabindex="1" required autofocus maxlength="15" minlength="4">
                                        <div class="invalid-feedback">
                                            Please fill in your corrent Application No
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="d-block">
                                            <label for="password" class="control-label">Password</label>

                                        </div>
                                        <input id="password" type="password" class="form-control" name="passkey" minlength="4" tabindex="2" required>
                                        <div class="invalid-feedback">
                                            please fill in your password of atleast 4 characters
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                                            Login
                                        </button>
                                    </div>
                                    <div class="mt-1 text-muted text-center">
                                        <div align="center" class="example5">
                                            <marquee> Application for Admission starts on Friday 30th May 2025 and closes on Friday 31st October 2025 </marquee>
                                        </div>
                                        <div align="center">
                                            <p id="demo" style="text-align:center; font-weight:bold; font-size: 16px;color:red"> </p>
                                        </div>
                                    </div>
                                </form>


                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="simple-footer">
                Copyright &copy; DSPG
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
    <script src="https://portal.mydspg.edu.ng/admissions/assets/js/app.min.js"></script>
    <!-- JS Libraies -->

    <!-- Page Specific JS File -->
    <script src="https://portal.mydspg.edu.ng/admissions/assets/js/page/index2.js"></script>
    <script src="https://portal.mydspg.edu.ng/admissions/assets/js/page/todo.js"></script>
    <!-- Template JS File -->
    <script src="https://portal.mydspg.edu.ng/admissions/assets/js/scripts.js"></script>
    <!-- Custom JS File -->
    <script src="https://portal.mydspg.edu.ng/admissions/assets/js/custom.js"></script>
</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $SCHOOLNAME ?? 'DPSG' }} - @yield('title')</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            /* White background to match the logo background */
        }

        header {
            background-color: #003366;
            /* Navy blue from the logo */
            color: white;
            text-align: center;
            padding: 1em 0;
        }

        .header-content {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .logo {
            width: 50px;
            height: 50px;
            margin-right: 1em;
        }

        nav ul {
            list-style: none;
            padding: 0;
        }

        nav ul li {
            display: inline;
            margin: 0 1em;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        main {
            text-align: center;
            margin: 2em 0;
        }

        .notice {
            background-color: #f2f2f2;
            /* Light grey for notices */
            padding: 1em;
            font-size: 1.2em;
            margin-bottom: 2em;
            color: #003366;
            /* Navy blue text */
        }

        .applications {
            display: flex;
            justify-content: space-around;
            margin: 2em 0;
        }

        .applications div {
            border: 1px solid #ccc;
            padding: 2em;
            width: 50%;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
            /* White background for application sections */
        }

        .applications h2 {
            margin-top: 0;
            color: #F7941D;
            /* Orange from the logo */
        }

        button {
            background-color: #003366;
            /* Navy blue background for buttons */
            color: white;
            border: 1px solid #003366;
            /* Navy blue border */
            padding: 0.5em 1em;
            cursor: pointer;
        }

        button:hover {
            background-color: #002244;
            /* Darker navy blue on hover */
        }

        .application-status {
            font-size: 1.5em;
            color: #ce7305;
            /* Orange text for application status */
            font-weight: bold;
        }
    </style>
</head>

<body>
    <header>
        <div class="header-content">
            <img src="https://portal.mydspg.edu.ng/admissions/assets/img/logox.png" alt="College Logo" class="logo">
            <h1>{{ $SCHOOLNAME }}</h1>
        </div>
        <nav>
            <ul>
                <li><a href="{{ route('admissions') }}">Home</a></li>
                <li><a href="{{ route('admissions.admreq') }}">Instructions/Guidelines</a></li>
                <li><a href="{{ route('admissions.startpart') }}">Start Application</a></li>
                <li><a href="{{ route('admissions.starting') }}">Continue Application</a></li>
            </ul>
        </nav>
    </header>
    <main>

        <section class="applications">
            @yield('message')
        </section>

    </main>
</body>

</html>
@extends('admissions.layout')

@section('content')
<p class="notice">
    <marquee> {{ $schoolInfo['appmarque'] }} </marquee>
</p>
<section class="applications">
    <div class="app-instruction">
        <h2>Application Instructions</h2>
        <button onclick="window.location.href='{{ route('admissions.admreq') }}'">Click Here for Application Guidelines</button>
    </div>
    <div class="start-app">
        <h2>Start Application</h2>
        <button onclick="window.location.href='{{ route('admissions.startpart') }}'">Click Here to start a new Application</button>
    </div>
    <div class="continue-app">
        <h2>Continue Application</h2>
        <button onclick="window.location.href='{{ route('admissions.starting') }}'">Click Here to Continue your Application</button>
    </div>
</section>
<p id="demo" class="application-status" style="text-align:center; font-weight:bold; font-size: 16px;color:red"> </p>

@endsection

<script>
    // Set the date we're counting down to
    var countDownDate = new Date("{{ $schoolInfo['appenddate'] }}").getTime();

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

</html>
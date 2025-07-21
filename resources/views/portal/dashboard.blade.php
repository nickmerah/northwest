@extends('layouts.portal') <!-- This extends the layout -->

@section('content')
<h2>Welcome to the Student Portal</h2>
<p>Here, you can access your courses, view results, manage your profile, and much more. This student portal is designed to be your one-stop destination for all your academic needs.</p>
<p>Get started by exploring the sidebar links, or check your notifications for important updates.</p>

<div class="dashboard-cards">
    <div class="card">
        <h5>Fees Paid</h5>
        <p>&#8358;{{number_format($schoolfeespaid->sum('trans_amount'))}}</p>
    </div>&nbsp;
    <div class="card">
        <h5>Other Fees Paid</h5>
        <p>&#8358;{{number_format($otherfeespaid->sum('trans_amount'))}}</p>
    </div>&nbsp;
    <div class="card">
        <h5>Course Registration</h5>
        <p>0</p>
    </div>
</div>
@endsection
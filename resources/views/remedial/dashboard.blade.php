@extends('layouts.remedial')

@section('content')
<h2>Welcome to the Remedial Payment Portal</h2>
<p>Here, you can make payment and register your courses.</p>

<div class="dashboard-cards">
    <div class="card">
        <h5>Fees Paid</h5>
        <p>&#8358;{{ number_format($feesPaid->sum('totalamount'), 2) }}</p>
    </div>&nbsp;
    <div class="card">
        <h5>No of Courses Paid</h5>
        <p>&#8358;{{ $feesPaid->sum('course') }}</p>
    </div>&nbsp;
    <div class="card">
        <h5>Courses Registered</h5>
        <p>{{ $noCourse }}</p>
    </div>
</div>
@endsection
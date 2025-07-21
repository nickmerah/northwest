@extends('layouts.remedial')

@section('content')
<h2>Pay Fees </h2>

<hr>
@if (session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif
@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<form method="POST" action="{{ route('payremedialfees') }}">
    @csrf
    <div>
        <p><strong>Number of Courses:</strong> {{ $CourseRegFees['noCourses'] }}</p>
        <h4>Fee Details:</h4>
        <input type="hidden" name="noCourses" value="{{ $CourseRegFees['noCourses'] }}">
        <ul>
            @foreach ($CourseRegFees['feeDetails'] as $feeName => $feeData)
            <li>
                <strong>{{ $feeName }}:</strong>
                Amount = {{ number_format($feeData['amount']) }}
            </li>
            @endforeach
        </ul>
        <p><strong>Total Fees:</strong> {{ number_format($CourseRegFees['total']) }}</p>
    </div>
    @if (!$feesPaid->isEmpty())
    <div class="alert alert-danger">
        We noticed you already paid. If you intend to make further payment, then you can proceed, else you can print your receipt.
    </div>
    @endif

    <button class="btn btn-success">Pay Fees</button>

</form>
@endsection
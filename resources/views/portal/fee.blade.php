@extends('layouts.portal')

@section('content')
<h2>Pay Fees - {{ $student->stateor->state_name }}</h2>

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
<form method="POST" action="{{ route('previewfee') }}">
    @csrf
    <table class="table table-bordered">
        <thead class="thead-light">
            <tr>
                <th scope="col"></th>
                <th scope="col">Fee Item</th>
                <th scope="col">Amount</th>

            </tr>
        </thead>
        <tbody>
            <tr>
                <th colspan="4">Compulsory Fee Items</th>
            </tr>
            @foreach($fees as $index => $fee)
            @if($fee->group == 1)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td>{{ $fee->field_name }}</td>
                <td>{{ number_format($fee->amount) }}</td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>


    <table class="table table-bordered mt-4">
        <thead class="thead-light">
            <tr>
                <th scope="col"></th>
                <th scope="col">Fee Item</th>
                <th scope="col">Amount</th>

            </tr>
        </thead>
        <tbody>
            <tr>
                <th colspan="4">School Fee Items</th>
            </tr>
            @foreach($fees as $indexx => $fee)
            @if($fee->group == 0)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td>{{ $fee->field_name }}</td>
                <td>{{ number_format($fee->amount) }}</td>

            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
    <button class="btn btn-success">Pay Fees</button>
</form>
@endsection
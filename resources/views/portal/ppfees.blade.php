@extends('layouts.portal')

@section('content')
<h3>Pay Fees - {{ $student->stateor->state_name }} - {{ $psess }}/{{ $psess+1 }} Session </h3>

<hr>
@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
@if (session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif
<form method="POST" action="{{ route('savepfees') }}">
    <input type="hidden" name="psess" value="{{ $psess }}" />
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
                <th colspan="4"> Fee Items</th>
            </tr>
            @foreach($fees as $index => $fee)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td>{{ $fee->field_name }}</td>
                <td>{{ number_format($fee->amount) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" class="text-right"><strong>TOTAL</strong></td>
                <td><strong>{{ number_format(array_sum(array_column($fees, 'amount')), 2) }}</strong></td>
            </tr>

        </tbody>
    </table>



    <button class="btn btn-success">Make Payment</button>
</form>
@endsection
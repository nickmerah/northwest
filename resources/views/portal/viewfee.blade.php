@extends('layouts.portal')

@section('content')
<h2>Pay Fees</h2>

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

<table class="table table-bordered">
    <thead class="thead-light">
        <tr>
            <th scope="col"></th>
            <th scope="col">Fee Item</th>
            <th scope="col">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($trans as $index => $tran)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $tran->trans_name }}</td>
            <td>{{ number_format($tran->trans_amount, 2) }}</td>
        </tr>
        @endforeach

        <tr class="total-row">
            <td colspan="2" class="text-right"><strong>Total</strong></td>
            <td><strong>{{ number_format($trans->sum('trans_amount') , 2) }} </strong></td>
        </tr>
        <tr class="total-row">
            <td colspan="2" class="text-right"><strong>TransactionID</strong></td>
            <td><strong>{{ $tran->trans_no }} </strong></td>
        </tr>
    </tbody>
</table>
<button class="btn btn-success" onclick="makePayment({{ ($trans->sum('trans_amount') + $serviceCharge) }}, '{{ $tran->rrr }}')">Pay Now</button>
<div align="center">
    <img src="{{ asset('public/images/remita.png') }}" alt="Remita" style="max-width: 100px;">

</div>

<script>
    function makePayment(amount, rrr) {
        var confirmation = confirm(`You are about to Pay ${amount} which includes the Transaction Fee. Click OK to continue or Cancel to abort.`);

        if (confirmation) {
            var url = `https://checkout.paystack.com/${rrr}`; // Use template literals
            window.location.href = url;
        }
    }
</script>

@endsection
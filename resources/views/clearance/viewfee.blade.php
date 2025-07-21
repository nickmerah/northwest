@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4 class="mb-4">Transaction Details</h4>
    <table class="table table-bordered table-custom">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Item</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($trans as $index => $tran)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $tran->fee_name }}</td>
                <td>{{ number_format($tran->fee_amount, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" class="text-right">Total</td>
                <td>{{ number_format($trans->sum('fee_amount'), 2) }} </td>
            </tr>
            <tr class="total-row">
                <td colspan="2" class="text-right">RRR</td>
                <td>{{ $tran->rrr }} </td>
            </tr>
        </tbody>
    </table>
    <div class="payment-section">
        <button class="btn-payment" onclick="makePayment({{ $tran->rrr }}, {{ $trans->sum('fee_amount') }})">Pay Now</button>

    </div>
    <div align="center">
        <img src="{{ asset('public/images/remita.png') }}" alt="Remita" style="max-width: 100px;">

    </div>
</div>
<script>
    function makePayment(rrr, amount) {

        var confirmation = confirm(`You are about to Pay ${amount}, click OK to continue or Cancel to abort.`);

        if (confirmation) {
            var url = "{{ route('processfee', ['rrr' => '__RRR__']) }}".replace('__RRR__', rrr);
            window.location.href = url;
        }

    }
</script>

@endsection
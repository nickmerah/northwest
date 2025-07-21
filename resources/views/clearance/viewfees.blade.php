@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4 class="mb-4">{{ $packName }}</h4>
    <table class="table table-bordered table-custom">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Item</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($fees as $index => $fee)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $fee->feeField->field_name ?? 'Unknown Item' }}</td>
                <td>{{ number_format($fee->amount, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" class="text-right">Total</td>
                <td>{{ number_format($fees->sum('amount'), 2) }}</td>
            </tr>
        </tbody>
    </table>
    <div class="payment-section">
        <button class="btn-payment" onclick="makePayment({{ $fee->pack_id }}, {{ $fees->sum('amount') }})">Make Payment</button>

    </div>
    <div align="center">
        <img src="{{ asset('public/images/remita.png') }}" alt="Remita" style="max-width: 100px;">

    </div>
</div>

<script>
    function makePayment(packId, amount) {

        var confirmation = confirm(`You are about to Pay ${amount}, click OK to continue or Cancel to abort.`);

        if (confirmation) {
            var url = "{{ route('paypacknow', ['packid' => '__PACKID__']) }}".replace('__PACKID__', packId);
            window.location.href = url;
        }

    }
</script>
@endsection
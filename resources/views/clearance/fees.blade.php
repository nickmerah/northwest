@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4 class="mb-4">Fee Details</h4>
    <div class="table-responsive">
        <div class="container mt-4">
            @foreach ($groupedFees as $packId => $fees)
            <h4 class="mb-4">{{ $student->prog_id == 1 ? 'ND' : 'HND' }} - {{ $packNames[$packId] }}</h4>
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
                        <td>{{ $fee['fee_field']['field_name']  }}</td>
                        <td>{{ number_format($fee['amount']) }}</td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="2" class="text-right">Total</td>
                        <td>{{ number_format(array_sum(array_column($fees, 'amount')), 2) }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="payment-section">
                <a href="#" class="btn-payment" onclick="makePayment({{ $packId }})">Make Payment</a>
                
            </div>
            @endforeach
        </div>

    </div>
</div>
<script>
    function makePayment(packId) {
        window.location.href = "{{ route('viewfeepack', ['packid' => '__PACKID__']) }}".replace('__PACKID__', packId);
    }
</script>
@endsection
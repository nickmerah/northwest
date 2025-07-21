@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4 class="mb-4">Payment History</h4>
    <table class="table table-bordered table-custom">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Pack</th>
                <th>RRR</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($trans as $index => $tran)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td> @if (!empty($tran->packs))
                    @foreach ($tran->packs as $packId)
                    {{ $packNames[$packId] ?? 'Unknown Pack' }}
                    @if (!$loop->last), @endif
                    @endforeach
                    @else
                    No packs associated.
                    @endif
                </td>
                <td>{{ $tran->rrr }}</td>
                <td>{{ number_format($tran->totalamount, 2) }}</td>
                <td>{{ \Carbon\Carbon::parse($tran->t_date)->format('d-M-Y') }}</td>
                <td> @if ($tran->trans_custom1 === 'Paid')
                    {{ $tran->trans_custom1 }} - <a href="{{ route('printreceipt', ['trans_no' => $tran->trans_no]) }}" target="_blank" class="btn btn-success">Print Receipt</a>
                    @else
                    Not Paid
                    @endif
                </td>
            </tr>
            @endforeach

        </tbody>
    </table>
    <div class="payment-section">
        <button class="btn-payment" onclick="checkPayment()">ReQuery Unpaid Transactions</button>

    </div>

</div>

<script>
    function checkPayment(packId, amount) {

        var confirmation = confirm(`You are about to Requery pending transactions, click OK to continue or Cancel to abort.`);
        if (confirmation) {
            var url = "{{ route('checkpayment') }}";
            window.location.href = url;
        }
    }
</script>
@endsection
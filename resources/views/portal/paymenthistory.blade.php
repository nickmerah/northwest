@extends('layouts.portal')

@section('content')
<h2>Payment History</h2>

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
<table class="table table-bordered">
    <thead class="thead-light">
        <tr>
            <th>S/N</th>
            <th>RRR</th>
            <th>Amount</th>
            <th>Session</th>
            <th>Date</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($trans as $index => $tran)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $tran->rrr }}</td>
            <td>{{ number_format($tran->totalamount, 2) }}</td>
            <td>{{ $tran->trans_year }}</td>
            <td>{{ \Carbon\Carbon::parse($tran->t_date)->format('d-M-Y') }}</td>
            <td> @if ($tran->pay_status === 'Paid')
                {{ $tran->pay_status }} - <a href="{{ route('printfreceipt', ['trans_no' => $tran->trans_no]) }}" target="_blank" class="btn btn-success">Print Receipt</a>
                @else
                Not Paid
                @endif
            </td>
        </tr>
        @endforeach

    </tbody>
</table>
<div class="payment-section">
    <button class="btn btn-success" onclick="checkPayment()">ReQuery Unpaid Transactions</button>

</div>


</div>

<script>
    function checkPayment(packId, amount) {

        var confirmation = confirm(`You are about to Requery pending transactions, click OK to continue or Cancel to abort.`);
        if (confirmation) {
            var url = "{{ route('checkpayments') }}";
            window.location.href = url;
        }
    }
</script>
@endsection
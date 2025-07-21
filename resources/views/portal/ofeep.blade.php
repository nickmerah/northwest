@extends('layouts.portal')

@section('content')
<h2>Pay Other Fees</h2>

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
<form method="POST" id="payment-form" action="{{ route('saveofees') }}">
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

            @foreach($ofees as $index => $ofee)
            @php
            $feeamount = $ofee->of_amount;
            @endphp
            <tr>
                <td>
                    <input type="hidden" name="ofee[]" value="{{ $ofee->of_id }}"> {{ $index + 1 }}
                </td>
                <td>{{ $ofee->ofield_name }}</td>
                <td>
                    @if ($ofee->of_id == 11)
                    <!-- Special case for fee_id 11 (Library Binding) -->
                    {{ number_format($ofee->of_amount) }} (Copies: {{ $libraryBindingCopies }}) |
                    {{ number_format($ofee->of_amount) }} * {{ $libraryBindingCopies }} = {{ number_format($ofee->of_amount * $libraryBindingCopies) }}
                    @else
                    {{ number_format($ofee->of_amount) }}
                    @endif

                </td>
            </tr>
            @endforeach
            <input type="hidden" name="copies" value="{{ $libraryBindingCopies }}">

            <tr class="total-row">
                <td colspan="2" class="text-right"><strong>TOTAL</strong></td>
                <td><strong>{{ number_format($grandTotal, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>
    <button type="button" class="btn btn-success" onclick="confirmPayment({{ $grandTotal }})">Make Payment</button>
    <div align="center">
        <img src="{{ asset('public/images/remita.png') }}" alt="Remita" style="max-width: 100px;">

    </div>
</form>

<script>
    function confirmPayment(amount) {
        let confirmation = confirm(`You are about to Pay N${amount} inclusive of the service charge, click OK to continue or Cancel to abort.`);
        if (confirmation) {
            document.getElementById('payment-form').submit();
        }
    }
</script>

@endsection
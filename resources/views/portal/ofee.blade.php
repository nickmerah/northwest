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
<form method="POST" action="{{ route('previewofee') }}">
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
            <tr>
                <td>
                    <!-- Replace checkbox with radio button -->
                    <input type="radio" name="ofee" value="{{ $ofee->of_id }}"> {{ $index + 1 }}
                </td>
                <td>{{ $ofee->ofield_name }}</td>
                <td>{{ number_format($ofee->of_amount) }}
                    @if ($ofee->of_id == 11)
                    | Copies:
                    <select name="copies" class="form-control" style="width: 70px; display: inline-block; margin-left: 10px;">
                        @for ($i = 1; $i <= 20; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                    </select>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>

    </table>
    <button class="btn btn-success">Pay Selected Fee</button>
</form>
@endsection
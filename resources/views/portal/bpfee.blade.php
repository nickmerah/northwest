@extends('layouts.portal')

@section('content')
<h2>Pay Previous Fees</h2>

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
<form method="POST" action="{{ route('ppfee') }}">
    @csrf

    <div class="form-group">
        <label for="gender"><strong>Select Session to Pay </strong></label>
        <select class="form-control" id="psess" name="psess" required>
            <option value=""> Select Session </option>

            @php
            for ($pyear = 2021; $pyear < $currentSession; $pyear++) {

                @endphp
                <option value="{{$pyear}}">{{$pyear}}/{{$pyear+1}}</option>

                @php
                }

                @endphp
        </select
            </div><br>
        <div class="form-group">
            <button class="btn btn-success">Continue</button>
        </div>
</form>
@endsection
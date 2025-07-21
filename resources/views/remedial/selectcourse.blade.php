@extends('layouts.remedial')

@section('content')
<h2>Course Payment</h2>
@if (session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif
@if (session('success'))
<div class="alert alert-success">
    {{ session('success') }}
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
<hr>
<form method="POST" action="{{ route('rfees') }}">
    @csrf

    <div class="form-group">
        <label for="fullName"><strong>Full Names</strong></label>
        <input type="text" class="form-control" value="{{ $student->surname }} {{ $student->firstname }} {{ $student->othernames }}" disabled>
    </div>
    <hr>
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif




    <div class="form-group">
        <label for="gender"><strong>No of Courses to Register</strong></label>
        <select class="form-control" id="noCourse" name="noCourse" required>
            <option value="">Select</option>
            @for ($i = 1; $i <= 15; $i++)
                <option value="{{ $i }}">{{ $i }}</option>
                @endfor

        </select>
    </div>


    <button type="submit" class="btn btn-orange btn-block">Preview Fees</button>
</form>
@endsection
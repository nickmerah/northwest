@extends('layouts.portal')

@section('content')
<h2>Upload Passport</h2>
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

<form method="POST" action="{{ route('updatepassport') }}" enctype="multipart/form-data">
    @csrf

    <div class="form-group">
        <label for="passport"><strong>Passport</strong></label>
        <input type="file" class="form-control" name="passport" id="passport" accept=".jpg,.jpeg" required>
    </div>

    @php
    $photoPath = storage_path('app/public/passport/' . $student->std_photo);
    @endphp
    @if (!file_exists($photoPath))
    <button type="submit" class="btn btn-orange btn-block">Update Passport</button>
    @endif
</form>

@endsection
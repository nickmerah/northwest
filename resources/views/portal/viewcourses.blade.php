@extends('layouts.portal')

@section('content')
<h2>Course Registration</h2>

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
@if (session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
<form method="POST" action="{{ url('removecourse') }}">
    @csrf

    <table class="table table-bordered mt-4">
        <thead class="thead-light">
            <tr>
                <th colspan="4">First Semester </th>
            </tr>
            <tr>
                <th scope="col"></th>
                <th scope="col">Code</th>
                <th scope="col">Title</th>
                <th scope="col">Unit</th>
            </tr>
        </thead>
        <tbody>

            @foreach($firstSemesterCourses as $fcourse)
            <tr>
                <td><input type="checkbox" name="courseids[]" value="{{ $fcourse->stdcourse_id }}" /> {{$loop->iteration}}</td>
                <td>{{ $fcourse->c_code }}</td>
                <td>{{ $fcourse->c_title }}</td>
                <td>{{ $fcourse->c_unit }}</td>
            </tr>
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td>Total</td>
                <td>{{ $firstSemesterCourses->sum('c_unit') }}</td>
            </tr>
        </tbody>
    </table>
    <hr>
    <table class="table table-bordered mt-4">
        <thead class="thead-light">
            <tr>
                <th colspan="4">Second Semester </th>
            </tr>
            <tr>
                <th scope="col"></th>
                <th scope="col">Code</th>
                <th scope="col">Title</th>
                <th scope="col">Unit</th>
            </tr>
        </thead>
        <tbody>

            @foreach($secondSemesterCourses as $scourse)
            <tr>
                <td><input type="checkbox" name="courseids[]" value="{{ $scourse->stdcourse_id }}" /> {{$loop->iteration}}</td>
                <td>{{ $scourse->c_code }}</td>
                <td>{{ $scourse->c_title }}</td>
                <td>{{ $scourse->c_unit }}</td>
            </tr>
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td>Total</td>
                <td>{{ $secondSemesterCourses->sum('c_unit') }}</td>
            </tr>
        </tbody>
    </table>
    Total Unit Selected: {{ $totunit = $firstSemesterCourses->sum('c_unit') + $secondSemesterCourses->sum('c_unit')}}
    <hr>

    @php if ($totunit == 0) { @endphp
    <a class="btn btn-danger" href="{{ url('courses') }}">No Course(s) registered</a>
    @php }else{ @endphp
    <a class="btn btn-danger" href="{{ url('printcreg', ['sess' => $sess]) }}">Print Courses</a>
    @php } @endphp


    <br><br><a href="{{ url('courses') }}" class="btn btn-warning">Register More Course(s)</a> | <button class="btn btn-danger">Remove Selected Courses</button>
</form>
@endsection
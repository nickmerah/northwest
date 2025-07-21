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
<form method="POST" action="{{ route('savecourses') }}">
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
                <td> <input type="hidden" name="courseids[]" value="{{ $fcourse->thecourse_id }}" />{{$loop->iteration}}</td>
                <td>{{ $fcourse->thecourse_code }}</td>
                <td>{{ $fcourse->thecourse_title }}</td>
                <td>{{ $fcourse->thecourse_unit }}</td>
            </tr>
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td>Total</td>
                <td>{{ $firstSemesterCourses->sum('thecourse_unit') }}</td>
            </tr>
        </tbody>
    </table>
     Total Unit Already Registered: {{ $firstSemesterRegisterUnits }}
     <br> Total Units: {{ $firstSemesterCourses->sum('thecourse_unit') +  $firstSemesterRegisterUnits }}
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
                <td> <input type="hidden" name="courseids[]" value="{{ $scourse->thecourse_id }}" />{{$loop->iteration}}</td>
                <td>{{ $scourse->thecourse_code }}</td>
                <td>{{ $scourse->thecourse_title }}</td>
                <td>{{ $scourse->thecourse_unit }}</td>
            </tr>
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td>Total</td>
                <td>{{ $secondSemesterCourses->sum('thecourse_unit') }}</td>
            </tr>
        </tbody>
    </table>
    Total Unit Already Registered: {{ $secondSemesterRegisterUnits }}
      <br> Total Units: {{ $secondSemesterCourses->sum('thecourse_unit') +  $secondSemesterRegisterUnits }}
    <br>
   
    <hr>@php if (
    (($firstSemesterCourses->sum('thecourse_unit') + $firstSemesterRegisterUnits) > $maxUnitToRegister) ||
    (($secondSemesterCourses->sum('thecourse_unit') + $secondSemesterRegisterUnits) > $maxUnitToRegister)
) { @endphp
    <a href="{{ url('courses') }}" class="btn btn-danger">Maximum Courses Exceeded, Go back to try again</a>
    @php }else{ @endphp
    <button class="btn btn-success">Register Courses</button>
    @php } @endphp
</form><br><a href="{{ url('courses') }}" class="btn btn-warning">Back to Courses</a>
@endsection
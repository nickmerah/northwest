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

@php $totfunit = 0 ; $totsunit = 0 ; $totfcunit = 0 ; $totscunit = 0; @endphp
<form method="POST" action="{{ route('previewcourse') }}">
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
                <td><input type="checkbox" name="courseids[]" value="{{ $fcourse->thecourse_id }}" /> {{$loop->iteration}}</td>
                <td>{{ $fcourse->thecourse_code }}</td>
                <td>{{ $fcourse->thecourse_title }}</td>
                <td>{{ $fcourse->thecourse_unit }}</td>
            </tr>
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td>Total</td>
                <td>{{ $totfunit = $firstSemesterCourses->sum('thecourse_unit') }}</td>
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
                <td><input type="checkbox" name="courseids[]" value="{{ $scourse->thecourse_id }}" /> {{$loop->iteration}}</td>
                <td>{{ $scourse->thecourse_code }}</td>
                <td>{{ $scourse->thecourse_title }}</td>
                <td>{{ $scourse->thecourse_unit }}</td>
            </tr>
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td>Total</td>
                <td>{{ $totsunit =  $secondSemesterCourses->sum('thecourse_unit') }}</td>
            </tr>
        </tbody>
    </table>
     {{-- First Semester CarryOver --}}
@if ($firstSemesterCarryOverCourses->count())
     
      <hr><strong>REGISTER CARRY OVER</strong>
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
            @foreach($firstSemesterCarryOverCourses as $fccourse)
            <tr>
                <td><input type="checkbox" name="courseids[]" value="{{ $fccourse->thecourse_id }}" /> {{$loop->iteration}}</td>
                <td>{{ $fccourse->thecourse_code }}</td>
                <td>{{ $fccourse->thecourse_title }}</td>
                <td>{{ $fccourse->thecourse_unit }}</td>
            </tr>
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td>Total</td>
                <td>{{ $totfcunit = $firstSemesterCarryOverCourses->sum('thecourse_unit') }}</td>
            </tr>
        </tbody>
    </table>
      
    
    @endif
    
    
    {{-- Second Semester CarryOver --}}
@if ($firstSemesterCarryOverCourses->count())
     
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
            @foreach($secondSemesterCarryOverCourses as $sccourse)
            <tr>
                <td><input type="checkbox" name="courseids[]" value="{{ $sccourse->thecourse_id }}" /> {{$loop->iteration}}</td>
                <td>{{ $sccourse->thecourse_code }}</td>
                <td>{{ $sccourse->thecourse_title }}</td>
                <td>{{ $sccourse->thecourse_unit }}</td>
            </tr>
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td>Total</td>
                <td>{{ $totscunit = $secondSemesterCarryOverCourses->sum('thecourse_unit') }}</td>
            </tr>
        </tbody>
    </table>
      
    
    @endif
    
    
    @php if ($totfunit == 0 and $totsunit == 0 and $totfcunit == 0 and $totscunit == 0) { @endphp
    <a class="btn btn-danger" href="">No Course to Register</a>
    @php }else{ @endphp
    <button class="btn btn-success">Preview Courses</button>
    @php } @endphp
</form>
@endsection
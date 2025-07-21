@extends('layouts.remedial')

@section('content')
<h2>Course Registration</h2>

<hr>

<form method="POST" action="{{ route('rcourses') }}">
    @csrf
    <div>
        <p><strong>Number of Courses to be Registered:</strong> {{ $noCourse }}</p>
        <h5>Register Courses</h5>

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

        @if(isset($noCourse) && $noCourse > 0)
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Course Code</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 1; $i <= $noCourse; $i++)
                    <tr>
                    <td>{{ $i }}</td>
                    <td>
                        <input type="text" class="form-control" name="coursecode[{{ $i }}]" placeholder="Enter Course code"
                            value="{{ isset($regcourse[$i - 1]) ? $regcourse[$i - 1]['c_code'] : '' }}" required autocomplete="off">
                    </td>
                    </tr>
                    @endfor
            </tbody>
        </table>
        <div class="mt-4">
            @if($regcourse->isEmpty())
            <button class="btn btn-success">Register Courses</button>
            @else
            <button class="btn btn-success">Update Courses</button>
            <a href="{{ route('printcourses') }}" target="_blank" class="btn btn-success">Print Courses</a>

            @endif
        </div>
        @else
        <p class="alert alert-warning">No course has been paid for</p>
        @endif

    </div>
</form>
@endsection
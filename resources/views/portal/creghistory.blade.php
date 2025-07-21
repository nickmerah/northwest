@extends('layouts.portal')

@section('content')
<h2>Course Registration History</h2>

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
            <th>Session</th>
            <th>Level</th>
            <th>Units</th>
            <th>Date</th>
            <th>Status/Remark</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $creg)
        <tr>
            <td>{{$loop->iteration}}</td>
            <td>{{ $creg->cyearsession }} / {{ $creg->cyearsession + 1 }}</td>
            <td>{{ $creg->level->level_name }}</td>
            <td>{{ $creg->totunit }}</td>
            <td>{{ \Carbon\Carbon::parse($creg->cdate_reg)->format('d-M-Y') }}</td>
            <td> @if ($creg->status === 'Approved')
                {{ $creg->status }} - <a href="{{ url('printcreg', ['sess' => $creg->cyearsession]) }}" target="_blank" class="btn btn-success">Print Courses</a>
                @else
                {{ $creg->status }} - {{ $creg->remark ?? 'NA' }}
                @endif
            </td>
        </tr>
        @endforeach

    </tbody>
</table>



</div>


@endsection
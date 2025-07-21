@extends('layouts.portal')

@section('content')
<h2>Room Availability</h2>

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
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if($hostels->isEmpty())
<div class="alert alert-info">You have not make payment for hostel accomodation.</div>
@else
@foreach ($hostels as $hostel)
<h2>{{ $hostel->hostelname }} - {{ $hostel->blockname }} ({{ $hostel->blocktype }})</h2>

<table class="table">
    <thead>
        <tr>
            <th>Room Number</th>
            <th>Capacity</th>
            <th>Occupied</th>
            <th>Reserve</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($hostel->rooms as $room)
        @if ($room->hasSpace())
        <tr>
            <td>{{ $room->roomno }}</td>
            <td>{{ $room->capacity }}</td>
            <td>{{ $room->allocations->count() }}</td>
            <td>
                <form action="{{ route('hostels.allocateRoom', $room->roomid) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">Reserve</button>
                </form>
            </td>
        </tr>
        @endif
        @endforeach
    </tbody>
</table>
@endforeach


@endif


@endsection
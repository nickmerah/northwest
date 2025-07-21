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
<h4>{{ $hostel->hostelname }} - {{ $hostel->blockname }} ({{ $hostel->blocktype }})</h4>

<!-- Summary Table -->
<table class="table">
    <thead>
        <tr>
            <th>No of Rooms</th>
            <th>Total Capacity</th>
            <th>Total Occupied</th>
            <th>Rate</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $hostel->rooms->count() }}</td>
            <td>{{ $hostel->rooms->sum('capacity') }}</td>
            <td>{{ $hostel->rooms->sum(function($room) {
                        return $room->allocations->count(); 
                    }) }}</td>
            <td>{{ number_format($hostel->ofee->of_amount)}}</td>
            <td>
                @if ($hostel->rooms->sum('capacity') > $hostel->rooms->sum(function($room) {
                return $room->allocations->count();
                }))
                <form action="{{ route('payment.route', ['hostel_id' => $hostel->hid, 'of_id' => $hostel->ofee->of_id]) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success">Pay</button>
                </form>
                @else
                <span class="text-danger">Fully Occupied</span>
                @endif
            </td>
        </tr>
    </tbody>
</table>

@endforeach

@endif

@endsection
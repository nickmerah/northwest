@extends('layouts.portal')

@section('content')
<h2>My Reservation</h2>

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

@if($myreservation->isEmpty())
<div class="alert alert-info">You have no reservations.</div>
@else
<div class="row">
    @foreach ($myreservation as $reservation)
    <div class="col-md-12">

        <div class="card-header">
            <h5>Reservation Details</h5>
        </div>
        <div class="card-body">
            @if ($reservation->room && $reservation->room->hostel)
            <p><strong>Hostel Name:</strong> {{ $reservation->room->hostel->hostelname }}</p>
            <p><strong>Hostel Block:</strong> {{ $reservation->room->hostel->blockname }}</p>
            <p><strong>Room Number:</strong> {{ $reservation->room->roomno }}</p>
            <p><strong>Room Type:</strong> {{ $reservation->room->room_type }}</p>
            @else
            <p class="text-danger"><strong>Reservation Status:</strong> Not reserved.</p>
            @endif
        </div>
    </div>

    @endforeach
</div>
<a href="{{ route('printReservation') }}" target="_blank" class="btn btn-success">Print Hostel Reservation</a>

@endif
@endsection
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4 class="mb-4">Student Details</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-custom">
            <tbody>
                <tr>
                    <td class="field-label">Fullnames</td>
                    <td class="field-value"><strong>{{ $student->surname }}</strong>, {{ $student->firstname }} {{ $student->othernames }}</td>
                </tr>
                <tr>
                    <td class="field-label">Matric Number</td>
                    <td class="field-value">{{ $student->matricno }}</td>
                </tr>
                <tr>
                    <td class="field-label">Programme</td>
                    <td class="field-value">{{ $student?->programme->programme_name }}</td>
                </tr>
                <tr>
                    <td class="field-label">Department</td>
                    <td class="field-value">{{ $student?->department->programme_option }}</td>
                </tr>
                <tr>
                    <td class="field-label">Level</td>
                    <td class="field-value">{{ $student?->level->level_name }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="container mt-4">
    <h4 class="mb-4">Fees Paid</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-custom">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Pack</th>
                    <th>RRR</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trans as $index => $tran)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td> @if (!empty($tran->packs))
                        @foreach ($tran->packs as $packId)
                        {{ $student?->programme->aprogramme_name }} {{ $packNames[$packId] ?? 'Unknown Pack' }}
                        @if (!$loop->last), @endif
                        @endforeach
                        @else
                        No packs associated.
                        @endif
                    </td>
                    <td>{{ $tran->rrr }}</td>
                    <td>{{ number_format($tran->totalamount, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($tran->t_date)->format('d-M-Y') }}</td>
                    <td> @if ($tran->trans_custom1 === 'Paid')
                        {{ $tran->trans_custom1 }} - <a href="{{ route('printreceipt', ['trans_no' => $tran->trans_no]) }}" target="_blank" class="btn btn-success">Print Receipt</a>
                        @else
                        Not Paid
                        @endif
                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</div>
@endsection
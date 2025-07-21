<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remedial Payment Portal - {{$schoolName}}</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-custom {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .table-custom thead th {
            background-color: #007bff;
            color: white;
            text-align: center;
            font-weight: bold;
        }

        .table-custom tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        .table-custom tbody tr:hover {
            background-color: #e2e6ea;
        }

        .table-custom td {
            vertical-align: middle;
        }

        .table-custom .field-label {
            font-weight: bold;
            color: #333;
        }

        .table-custom .field-value {
            color: #555;
        }

        .table-custom {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .table-custom thead th {
            background-color: #007bff;
            color: white;
            text-align: center;
            font-weight: bold;
        }

        .table-custom tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        .table-custom tbody tr:hover {
            background-color: #e2e6ea;
        }

        .table-custom td {
            vertical-align: middle;
        }

        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }

        .watermarked-page {
            position: relative;
            width: 100%;
            height: 100vh;
            /* Full viewport height */
            padding: 20px;
            /* Adjust as needed */
            box-sizing: border-box;
        }

        .watermarked-page .watermark {
            position: absolute;
            top: -6%;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            opacity: 0.1;
            background: url(<?= asset('public/images/logo.png'); ?>) no-repeat center center;
            background-size: 40% auto;
        }
    </style>
</head>

<body>
    <div class="watermarked-page">
        <div class="watermark">
        </div>
        <div class="content">
            <div align="center"> <br>
                <img src="{{ asset('public/images/logo.png') }}" alt="School Logo" style="width: 120px; margin-right: 10px;">
                <br><br>
                <h1><span class="large-text">{{ $schoolName }}</span></h1>

                <h3> <u> Remedial Payment Receipt</u> </h3>
            </div>


            <!-- Main Content Area -->
            <div class="content">
                <div class="table-responsive">
                    <div class="container mt-4">
                        <table class="table table-bordered  ">
                            <tbody>

                                <tr>
                                    <td class="field-label">Fullnames</td>
                                    <td class="field-value"><strong>{{ $student->surname }}</strong>, {{ $student->firstname }} {{ $student->othernames }}</td>

                                </tr>
                                <tr>
                                    <td class="field-label">Matriculation Number</td>
                                    <td class="field-value">{{ $student->matno }}</td>
                                </tr>
                                <tr>
                                    <td class="field-label">Level</td>
                                    <td class="field-value">{{ $student?->level }}</td>
                                </tr>
                                <tr>
                                    <td class="field-label">Session</td>
                                    <td class="field-value">{{ $student?->sess}}/{{ $student?->sess+1}}</td>
                                </tr>
                                <tr>
                                    <td class="field-label">RRR</td>
                                    <td class="field-value">{{ $trans[0]['rrr'] }}</td>
                                </tr>
                                <tr>
                                    <td class="field-label">No of Courses</td>
                                    <td class="field-value">{{ $trans[0]['course'] }}</td>
                                </tr>
                                <tr>
                                    <td class="field-label">Date Paid</td>
                                    <td class="field-value">{{ \Carbon\Carbon::parse($trans[0]['t_date'])->format('d-M-Y') }}</td>
                                </tr>
                                <tr>
                                    <th colspan="2">FEE DETAILS</th>
                                </tr>
                                @php
                                $feeAmounts = array_column($trans, 'fee_amount');
                                $totalAmount = array_sum($feeAmounts);
                                @endphp

                                @foreach($trans as $index => $tran)
                                <tr>

                                    <td>{{ $tran['fee_name'] }}</td>
                                    <td>{{ number_format($tran['fee_amount'], 2) }}</td>
                                </tr>

                                @endforeach
                                <tr class="total-row">
                                    <td class="text-right">Total</td>
                                    <td>{{ number_format($totalAmount, 2) }}</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>
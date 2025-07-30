<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $SCHOOLNAME }} - Application Portal</title>

    <style>
        body,

        html {

            margin: 0;

            padding: 0;

            height: 100%;

            width: 100%;

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

            top: 0;

            left: 0;

            width: 100%;

            height: 100%;

            pointer-events: none;

            opacity: 0.1;

            background: url("{{ asset('public/assets/img/logo.png') }}") no-repeat center center;

            background-size: 40% auto;

        }



        .content {

            position: relative;

            z-index: 1;

        }



        table {

            width: 100%;

            border-collapse: collapse;

            font-size: large;

        }



        th,

        td {

            border: 1px solid #ddd;

            padding: 10px;

        }



        th {

            background-color: #f2f2f2;

        }



        .table-image-cell {

            text-align: right;

            vertical-align: middle;

        }



        .table-image-cell img {

            display: inline-block;

        }



        .table {

            width: 100%;

            table-layout: fixed;

        }



        .table td {

            padding: 8px;

        }
    </style>



    <script language="JavaScript" type="text/JavaScript">

        function MM_openBrWindow(theURL,winName,features) { //v2.0

  window.open(theURL,winName,features);

}







</script>

    <script>
        function printWindow() {



            bV = parseInt(navigator.appVersion)



            if (bV >= 4) window.print()



        }
    </script>

</head>



<body>



    <div class="watermarked-page">

        <div class="watermark">

        </div>

        @if(isset($paymentData) && count($paymentData) > 0)
        @foreach($paymentData as $paydetail)
        <div class="content">
            <div align="center"><br>
                <img src="{{ asset('public/assets/img/logo.png') }}" alt="dspg" width="120" height="131">
                <br>
                <h2>{{ strtoupper($SCHOOLNAME) }}</h2>
                <h3><u>{{ strtoupper($paydetail['feeName']) }} - Payment Receipt</u></h3>
            </div>

            <div class="card-body">
                <br>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td style="width: 25%;"><strong>Application No:</strong></td>
                            <td style="width: 50%;">{{ $paydetail['applicationNo'] }}</td>
                            <td rowspan="3" class="table-image-cell" style="width: 25%;">
                                <img alt="Photo" src="{{ $passportUrl }}" class="rounded-circle img-responsive mt-3" width="135" height="131" />
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Fullnames:</strong></td>
                            <td><strong>{{ $paydetail['fullNames'] }}</strong></td>
                        </tr>

                        <tr>
                            <td><strong>Fee Name:</strong></td>
                            <td>{{ $paydetail['feeName'] }}</td>
                        </tr>

                        <tr>
                            <td><strong>Transaction ID:</strong></td>
                            <td colspan="2">{{ $paydetail['transactionID'] }}</td>
                        </tr>

                        <tr>
                            <td><strong>Session:</strong></td>
                            <td colspan="2">
                                {{ $paydetail['sessionPaid'] }} / {{ $paydetail['sessionPaid'] + 1 }}
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Amount:</strong></td>
                            <td colspan="2">&#8358;{{ number_format($paydetail['amount']) }}</td>
                        </tr>

                        <tr>
                            <td><strong>Date:</strong></td>
                            <td colspan="2">
                                {{ \Carbon\Carbon::parse($paydetail['datePaid'])->format('d-M-Y') }}
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Status:</strong></td>
                            <td colspan="2"><strong>{{ strtoupper($paydetail['paymentStatus']) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
        @else
        <p class="text-center mt-4">No payment receipt found.</p>
        @endif


    </div>

    </div>


    <script>
        printWindow()
    </script>
</body>

</html>
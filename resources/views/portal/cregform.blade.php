<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - {{$schoolName}}</title>
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

                <h3> <u> Course Registration for {{$sess}} / {{$sess+1}} Session</u> </h3>
            </div>


            <!-- Main Content Area -->
            <div class="content">
                <div class="table-responsive">
                    <div class="container mt-4">
                        <table class="table table-bordered" style="font-size: small;">
                            <tbody> @php
                                $firstCourses = $firstSemesterCourses->values();
                                $secondCourses = $secondSemesterCourses->values();
                                $maxRows = max($firstCourses->count(), $secondCourses->count());
                                @endphp

                                <tr>
                                    <td class="field-label">Fullnames</td>
                                    <td class="field-value"><strong>{{ $student->surname }}</strong>, {{ $student->firstname }} {{ $student->othernames }}</td>
                                    <td class="field-label" colspan="5" rowspan="5">
                                        <div class="passport" align="center">
                                            @php
                                            $photoPath = storage_path('app/public/passport/' . $student->std_photo);
                                            @endphp

                                            @if (file_exists($photoPath))
                                            <img alt="Photo" src="{{ asset('storage/app/public/passport/'.$student->std_photo) }}" class="rounded-circle img-responsive mt-3" width="135" height="131" />
                                            @else
                                            <img alt="Photo" src="https://portal.mydspg.edu.ng/admissions/writable/thumbs/avatar.jpg" class="rounded-circle img-responsive mt-3" width="135" height="131" />
                                            @endif
                                        </div>
                                    </td>

                                </tr>
                                <tr>
                                    <td class="field-label">Matriculation Number</td>
                                    <td class="field-value">{{ $student->matric_no }}</td>
                                </tr>
                                <tr>
                                    <td class="field-label">School</td>
                                    <td class="field-value">{{ $student?->school->faculties_name }}</td>
                                </tr>
                                <tr>
                                    <td class="field-label">Department</td>
                                    <td class="field-value">{{ $student?->department->departments_name }}</td>
                                </tr>
                                <tr>
                                    <td class="field-label">Course of Study</td>
                                    <td class="field-value">{{ $student?->departmentOption->programme_option }}</td>
                                </tr>
                                <tr>
                                    <td class="field-label">Programme</td>
                                    <td class="field-value">{{ $student?->programme->programme_name }}</td>
                                    <td class="field-label">Programme Type</td>
                                    <td class="field-value">{{ $student?->programmeType->programmet_name }}</td>
                                </tr>
                                <tr>
                                    <td class="field-label">Level</td>
                                    <td class="field-value">{{ $firstCourses[0]->level->level_name ?? $secondCourses[0]->level->level_name }}</td>
                                    <td class="field-label">Session</td>
                                    <td class="field-value">{{$sess}} / {{$sess+1}}</td>
                                </tr>

                            </tbody>
                        </table>
                        <h6>COURSE DETAILS</h6>
                        <table class="table table-bordered" style="font-size: small;">
                            <thead class="thead-light">
                                <tr>
                                    <th colspan="3" class="text-center">First Semester</th>
                                    <th colspan="3" class="text-center">Second Semester</th>
                                </tr>
                                <tr>
                                    <th>Code</th>
                                    <th>Title</th>
                                    <th>Unit</th>
                                    <th>Code</th>
                                    <th>Title</th>
                                    <th>Unit</th>
                                </tr>
                            </thead>
                            <tbody>


                                @for ($i = 0, $f = 0, $s = 0; $i < $maxRows; $i++)
                                    <tr>
                                    <!-- First Semester Data -->
                                    @if(isset($firstCourses[$f]))
                                    <td>{{ $firstCourses[$f]->c_code }}</td>
                                    <td>{{ $firstCourses[$f]->c_title }}</td>
                                    <td>{{ $firstCourses[$f]->c_unit }}</td>
                                    @php $f++; @endphp
                                    @else
                                    <td colspan="3"></td>
                                    @endif

                                    <!-- Second Semester Data -->
                                    @if(isset($secondCourses[$s]))
                                    <td>{{ $secondCourses[$s]->c_code }}</td>
                                    <td>{{ $secondCourses[$s]->c_title }}</td>
                                    <td>{{ $secondCourses[$s]->c_unit }}</td>
                                    @php $s++; @endphp
                                    @else
                                    <td colspan="3"></td>
                                    @endif
                                    </tr>
                                    @endfor

                                    <!-- Total Units Row -->
                                    <tr>
                                        <td colspan="2"><strong>Total Units</strong></td>
                                        <td><strong>{{ $firstSemesterCourses->sum('c_unit') }}</strong></td>
                                        <td colspan="2"><strong>Total Units</strong></td>
                                        <td><strong>{{ $secondSemesterCourses->sum('c_unit') }}</strong></td>
                                    </tr>

                                    <tr>
                                        <td colspan="3"><strong> Total Unit Registered: <strong>{{ $firstSemesterCourses->sum('c_unit') + $secondSemesterCourses->sum('c_unit') }}</strong></strong></td>
                                        <td colspan="3"><strong>Date Registered: <strong>{{ \Carbon\Carbon::parse($firstCourses[0]->cdate_reg ?? $secondCourses[0]->cdate_reg)->format('d-M-Y') }}
                                                </strong></td>

                                    </tr>
                            </tbody>
                        </table>

                        <br>
                        <table style="width: 100%; margin-top: 20px;">
                            <tr>
                                <td style="width: 50%;"><strong>Course Adviser Name:</strong> {{ $courseAdviser?->u_surname }} {{ $courseAdviser?->u_firstname }}</td>
                                <td style="width: 50%;"><strong>Course Adviser Signature:</strong> ___________________________</td>
                            </tr>
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
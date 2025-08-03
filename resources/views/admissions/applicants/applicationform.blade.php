  @extends('admissions.applicants.layout')

  @section('content')

  <!-- Main Content -->
  <div class="main-content">
      <section class="section">
          <ul class="breadcrumb breadcrumb-style">
              <li class="breadcrumb-item">
                  <h4 class="page-title m-b-0">APPLICATION FORM</h4>
              </li>
              <li class="breadcrumb-item">
                  <a href="{{ route('admissions.dashboard') }}">
                      <i class="fas fa-home"></i>
                  </a>
              </li>
              <li class="breadcrumb-item active">Dashboard</li>
          </ul>

          @php
          use Illuminate\Support\Str;
          @endphp

          <div class="col-md-9 col-xl-12">
              <div class="tab-content">
                  <div class="tab-pane fade show active" id="payslip" role="tabpanel">
                      <div id="printableArea">
                          @isset($biodetail)

                          <div class="card">
                              <br />
                              <div align="center">
                                  <img src="{{ asset('public/assets/img/logo.png') }}" alt="nw" width="120" height="131">
                                  <br><br>
                                  <h3>{{ strtoupper($SCHOOLNAME) }}</h3>
                                  <strong style="font-size:20px; color:#900">
                                      <u>{{ $applicantStatus['appyear'] }} / {{ $applicantStatus['appyear'] + 1 }} ADMISSION APPLICATION FORM</u>
                                  </strong>
                              </div>

                              <div class="card-body">
                                  <h5 class="card-title mb-0"><strong>CANDIDATE'S INFORMATION</strong></h5>
                                  <div class="row">
                                      <table class="table table-bordered table-sm" width="100%" style="font-size:13px">
                                          <tbody>
                                              <tr>
                                                  <td width="170"><strong>Application No</strong></td>
                                                  <td colspan="2">{{ $biodetail->applicationNumber }}</td>
                                                  <td rowspan="4">
                                                      <img alt="Photo" src="{{ $passportUrl }}"
                                                          class="rounded-circle img-responsive mt-3" width="135" height="131" />
                                                  </td>
                                              </tr>
                                              <tr>
                                                  <td><strong>Surname</strong></td>
                                                  <td colspan="2">{{ $biodetail->surname }}</td>
                                              </tr>
                                              <tr>
                                                  <td><strong>Firstname</strong></td>
                                                  <td colspan="2">{{ $biodetail->firstname }}</td>
                                              </tr>
                                              <tr>
                                                  <td><strong>Othernames</strong></td>
                                                  <td colspan="2">{{ $biodetail->othernames }}</td>
                                              </tr>
                                              <tr>
                                                  <td><strong>Email</strong></td>
                                                  <td>{{ $biodetail->studentEmail }}</td>
                                                  <td><strong>HomeTown/Village</strong></td>
                                                  <td>{{ $biodetail->homeTown }}</td>
                                              </tr>
                                              <tr>
                                                  <td><strong>Phone Number</strong></td>
                                                  <td>{{ $biodetail->studentPhoneNo }}</td>
                                                  <td><strong>Gender</strong></td>
                                                  <td>{{ $biodetail->gender }}</td>
                                              </tr>
                                              <tr>
                                                  <td><strong>Date of Birth</strong></td>
                                                  <td>{{ \Carbon\Carbon::parse($biodetail->birthDate)->format('F d, Y') }}</td>
                                                  <td><strong>Marital Status</strong></td>
                                                  <td>{{ $biodetail->maritalStatus }}</td>
                                              </tr>
                                              <tr>
                                                  <td><strong>Permanent Home Address</strong></td>
                                                  <td colspan="3">{{ $biodetail->studentHomeAddress }}</td>
                                              </tr>
                                              <tr>
                                                  <td><strong>Contact Address</strong></td>
                                                  <td colspan="3">{{ $biodetail->contactAddress }}</td>
                                              </tr>
                                              <tr>
                                                  <td><strong>State</strong></td>
                                                  <td>{{ $biodetail->stateofOrigin['state_name'] }}</td>
                                                  <td><strong>LGA</strong></td>
                                                  <td>{{ $biodetail->lga['lga_name'] }}</td>
                                              </tr>
                                              <tr>
                                                  <td><strong>First Course</strong></td>
                                                  <td>{{ $biodetail->firstChoiceCourse['programme_option'] }}</td>
                                                  <td><strong>Second Course</strong></td>
                                                  <td>{{ $biodetail->secondChoiceCourse['programme_option'] }}</td>
                                              </tr>
                                              <tr>
                                                  <td><strong>Programme</strong></td>
                                                  <td>{{ $biodetail->programme['programme_name'] }}</td>
                                                  <td><strong>Programme Type</strong></td>
                                                  <td>{{ $biodetail->programmeType['programmet_name'] }}</td>
                                              </tr>
                                          </tbody>
                                      </table>

                                      @if (!empty($olevelResults))
                                      <br />
                                      <h5 class="card-title mb-0"><strong>O'LEVEL RESULTS</strong></h5>
                                      <hr>
                                      <table class="table table-bordered table-sm" width="100%" style="font-size:13px">
                                          <thead>
                                              <tr>
                                                  <th>Exam Type</th>
                                                  <th>Subject Name</th>
                                                  <th>Grade</th>
                                                  <th>Date Obtained</th>
                                                  <th>CenterNo</th>
                                                  <th>ExamNo</th>
                                                  <th>Sitting</th>
                                              </tr>
                                          </thead>
                                          <tbody>
                                              @foreach($olevelResults->data as $olevel)
                                              <tr>
                                                  <td>{{ $olevel['examName'] }}</td>
                                                  <td>{{ $olevel['subjectName'] }}</td>
                                                  <td>{{ $olevel['grade'] }}</td>
                                                  <td>{{ $olevel['examMonth'] }}, {{ $olevel['examYear'] }}</td>
                                                  <td>{{ $olevel['centerNo'] }}</td>
                                                  <td>{{ $olevel['examNo'] }}</td>
                                                  <td>{{ $olevel['sitting'] }}</td>
                                              </tr>
                                              @endforeach
                                          </tbody>
                                      </table>
                                      @endif

                                      @if (!empty($jambResults))
                                      <br />
                                      <h5 class="card-title mb-0"><strong>UTME RESULTS DETAILS</strong> | UTME
                                          No: {{ $jambResults['data'][0]['jambNo'] }}
                                      </h5>
                                      <hr>
                                      <table class="table table-bordered table-sm" width="100%" style="font-size:13px">
                                          <thead>
                                              <tr>
                                                  <th>Subject</th>
                                                  <th>Score</th>
                                              </tr>
                                          </thead>
                                          <tbody> @php $utmescore=0; @endphp
                                              @for ($i = 0; $i < 4; $i++)
                                                  @php
                                                  $savedSubject=$jambResults['data'][$i]['subjectName'] ?? '' ;
                                                  $savedScore=$jambResults['data'][$i]['jambScore'] ?? '' ;

                                                  @endphp
                                                  <tr>
                                                  <td>{{ $savedSubject }}</td>
                                                  <td>{{ $savedScore }}</td>
                                                  </tr>
                                                  @php $utmescore += $savedScore; @endphp
                                                  @endfor
                                                  <tr>
                                                      <td><strong>TOTAL SCORE</strong></td>
                                                      <td><strong>{{ $utmescore }}</strong></td>
                                                  </tr>
                                          </tbody>
                                      </table>
                                      @endif

                                      @if (!empty($schoolDetails))
                                      <hr />
                                      <h5 class="card-title mb-0"><strong>SCHOOL ATTENDED AND DATES</strong></h5>
                                      <hr>
                                      <table class="table table-bordered table-sm" width="100%" style="font-size:13px">
                                          <thead>
                                              <tr>
                                                  <th>School Name</th>
                                                  <th>Registration No</th>
                                                  <th>Course</th>
                                                  <th>From</th>
                                                  <th>To</th>
                                              </tr>
                                          </thead>
                                          <tbody>
                                              <tr>
                                                  <td>{{ $schoolDetails['data'][0]['schoolName'] }}</td>
                                                  <td>{{ $schoolDetails['data'][0]['ndMatno'] }}</td>
                                                  <td>{{ $schoolDetails['data'][0]['courseofStudy'] }}</td>
                                                  <td>{{ $schoolDetails['data'][0]['fromDate'] }}</td>
                                                  <td>{{ $schoolDetails['data'][0]['toDate'] }}</td>
                                              </tr>
                                          </tbody>
                                      </table>
                                      @endif

                                      @if ($certificates->status != 'error')
                                      <hr />
                                      <h5 class="card-title mb-0"><strong>CERTIFICATES UPLOADED</strong></h5>
                                      <hr>

                                      <table class="table table-hover my-0" width="100%" style="font-size:12px">

                                          <tbody>


                                              <tr style="font-size: 14px;">
                                                  <td>{{ implode(', ', array_column($certificates->data, 'documentName')) }}</td>
                                              </tr>


                                          </tbody>
                                      </table>
                                      @endif
                                      <br />
                                      <h5 class="card-title mb-0"><strong>DECLARATION</strong></h5>
                                      <hr>
                                      <p>
                                          {!! $declaration['data']['declarationtext'] !!}
                                      </p>
                                  </div>
                              </div>
                          </div>

                          @endisset
                      </div>
                  </div>
              </div>
              <input type="button" onClick="printDiv('printableArea')" value="Print Application Form" class='btn btn-primary'>
          </div>

      </section>
  </div>

  <script type="application/javascript">
      function printDiv(divName) {
          var printContents = document.getElementById(divName).innerHTML;
          var originalContents = document.body.innerHTML;

          document.body.innerHTML = printContents;

          window.print();

          document.body.innerHTML = originalContents;
      }
  </script>
  @endsection
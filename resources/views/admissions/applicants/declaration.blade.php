  @extends('admissions.applicants.layout')

  @section('content')

  <!-- Main Content -->
  <div class="main-content">
      <section class="section">
          <ul class="breadcrumb breadcrumb-style">
              <li class="breadcrumb-item">
                  <h4 class="page-title m-b-0">Declaration</h4>
              </li>
              <li class="breadcrumb-item">
                  <a href="{{ route('admissions.dashboard') }}">
                      <i class="fas fa-home"></i>
                  </a>
              </li>
              <li class="breadcrumb-item active">Dashboard</li>
          </ul>

          <div class="col-md-9 col-xl-12">
              <div class="tab-content">
                  <div class="tab-pane fade show active" id="application" role="tabpanel">
                      <div class="card">
                          <div class="card-body">

                              @isset($biodetail)
                              <h5 class="card-title mb-0"><strong>BIODATA</strong></h5>
                              <hr>
                              <div class="row">
                                  <div class="mb-3 col-md-6">
                                      <strong>Application Number:</strong><br> {{ $biodetail->applicationNumber }}
                                  </div>
                                  <div class="mb-3 col-md-6">
                                      <strong>Surname:</strong><br> {{ $biodetail->surname }}
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="mb-3 col-md-6">
                                      <strong>Firstname:</strong><br> {{ $biodetail->firstname }}
                                  </div>
                                  <div class="mb-3 col-md-6">
                                      <strong>Othername:</strong><br> {{ $biodetail->othernames }}
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="mb-3 col-md-6">
                                      <strong>Email:</strong><br> {{ $biodetail->studentEmail }}
                                  </div>
                                  <div class="mb-3 col-md-6">
                                      <strong>Phone Number:</strong><br> {{ $biodetail->studentPhoneNo }}
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="mb-3 col-md-6">
                                      <strong>Gender:</strong><br> {{ $biodetail->gender }}
                                  </div>
                                  <div class="mb-3 col-md-6">
                                      <strong>Marital Status:</strong><br> {{ $biodetail->maritalStatus }}
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="mb-3 col-md-6">
                                      <strong>Date of Birth:</strong><br> {{ \Carbon\Carbon::parse($biodetail->birthDate)->format('F d, Y') }}
                                  </div>
                                  <div class="mb-3 col-md-6">
                                      <strong>HomeTown:</strong><br> {{ $biodetail->homeTown }}
                                  </div>
                              </div>
                              <div class="mb-3">
                                  <strong>Permanent Home Address:</strong><br> {{ $biodetail->studentHomeAddress }}
                              </div>
                              <div class="mb-3">
                                  <strong>Contact Address:</strong><br> {{ $biodetail->contactAddress }}
                              </div>
                              <div class="row">
                                  <div class="mb-3 col-md-6">
                                      <strong>State:</strong><br> {{ $biodetail->stateofOrigin['state_name'] }}
                                  </div>
                                  <div class="mb-3 col-md-6">
                                      <strong>LGA:</strong><br> {{ $biodetail->lga['lga_name'] }}
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="mb-3 col-md-6">
                                      <strong>Next of Kin Name:</strong><br> {{ $biodetail->nextofKin }}
                                  </div>
                                  <div class="mb-3 col-md-6">
                                      <strong>Next of Kin Phone Number:</strong><br> {{ $biodetail->nextofKinPhoneNo }}
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="mb-3 col-md-6">
                                      <strong>Next of Kin Address:</strong><br> {{ $biodetail->nextofKinAddress }}
                                  </div>
                                  <div class="mb-3 col-md-6">
                                      <strong>Next of Kin Email:</strong><br> {{ $biodetail->nextofKinEmail }}
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="mb-3 col-md-6">
                                      <strong>Next of Kin Relationship:</strong><br> {{ $biodetail->nextofKinRelationship }}
                                  </div>
                                  <div class="mb-3 col-md-6">
                                      <strong>Course of Study:</strong><br> {{ $biodetail->firstChoiceCourse['programme_option'] }}
                                  </div>
                              </div>

                              @endisset

                              @if (!empty($olevelResults))
                              <hr>
                              <h5 class="card-title mb-0"><strong>O'LEVEL RESULTS</strong></h5>
                              <hr>
                              <table class="table table-hover my-0" width="100%">
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
                              <hr>
                              <h5 class="card-title mb-0"><strong>UTME DETAILS</strong></h5>
                              <hr>
                              <div class="row">
                                  <div class="mb-3 col-md-6">
                                      <strong>UTME No:</strong><br> {{ $jambResults['data'][0]['jambNo'] }}
                                  </div>
                              </div>
                              <table class="table table-hover my-0" width="100%">
                                  <thead>
                                      <tr>
                                          <th>Subject</th>
                                          <th>Score</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      @for ($i = 0; $i < 4; $i++)
                                          @php
                                          $savedSubject=$jambResults['data'][$i]['subjectName'] ?? '' ;
                                          $savedScore=$jambResults['data'][$i]['jambScore'] ?? '' ;
                                          $utmescore=0;
                                          @endphp
                                          <tr>
                                          <td>{{ $savedSubject }}</td>
                                          <td>{{ $savedScore }}</td>
                                          </tr>
                                          @php $utmescore += $savedScore; @endphp
                                          @endfor
                                  </tbody>
                              </table>
                              <br>
                              <div class="row">
                                  <div class="mb-3 col-md-6">
                                      <strong>UTME Score:</strong> {{ $utmescore }}
                                  </div>
                              </div>
                              @endif

                              @if (!empty($schoolDetails))
                              <hr>
                              <h5 class="card-title mb-0"><strong>SCHOOL ATTENDED</strong></h5>
                              <hr>
                              <table class="table table-hover my-0" width="100%" style="font-size:12px">
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
                              <hr>
                              <h5 class="card-title mb-0"><strong>CERTIFICATES UPLOADED</strong></h5>
                              <hr>
                              <table class="table table-hover my-0" width="100%" style="font-size:12px">
                                  <thead>
                                      <tr>
                                          <th>DOCUMENT NAME</th>
                                      </tr>
                                  </thead>
                                  <tbody>

                                      @foreach($certificates->data as $docs)
                                      <tr>
                                          <td>{{ $docs['documentName']}}</td>
                                      </tr>
                                      @endforeach

                                  </tbody>
                              </table>
                              @endif

                              <hr>
                              <h5 class="card-title mb-0"><strong>DECLARATION/ATTESTATION</strong></h5>
                              <hr>
                              <p>
                                  {!! $declaration['data']['declarationtext'] !!}
                              </p>
                              <form action="{{ route('admissions.declaration') }}" method="post">
                                  @csrf
                                  <button type="submit" class="btn btn-success"> <i class="fas fa-check"></i> Accept Declaration/Attestation</button>

                              </form>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </section>
  </div>


  @endsection
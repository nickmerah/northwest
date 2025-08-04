  @extends('admissions.applicants.layout')

  @section('content')

  <!-- Main Content -->
  <div class="main-content">
      <section class="section">
          <ul class="breadcrumb breadcrumb-style ">
              <li class="breadcrumb-item">
                  <h4 class="page-title m-b-0">My Olevel Results</h4>
              </li>
              <li class="breadcrumb-item">
                  <a href="{{ route('admissions.dashboard') }}">
                      <i class="fas fa-home"></i></a>
              </li>
              <li class="breadcrumb-item active">Dashboard</li>
          </ul>
          <div class="container-fluid p-0">
              <div class="row">
                  <div class="col-md-9 col-xl-12">
                      <div class="tab-content">
                          <div class="tab-pane fade show active" id="application" role="tabpanel">
                              <div class="card">
                                  <div class="card-body">
                                      <h5 class="card-title mb-0"><strong>O'LEVEL RESULTS</strong></h5>
                                      <hr>
                                      <form id="update_profile" name="update_profile" action="{{ route('admissions.olevel') }}" method="post">
                                          @csrf
                                          @if (empty($olevelResults->data))
                                          <div class="row">
                                              <div class="col-12 col-md-6 col-lg-6">
                                                  <div class="card">
                                                      <div class="card-header">
                                                          <h4>First Sitting</h4>
                                                      </div>
                                                      <div class="card-body">
                                                          <div class="table-responsive">
                                                              <table class="table">
                                                                  <tr>
                                                                      <td colspan="3">
                                                                          <label class="form-label"><strong>Examination Type</strong></label>
                                                                          <select name="first[examName]" class="form-control" required>
                                                                              <option value="">Select Exam Type</option>
                                                                              @foreach(['NECO', 'WAEC/WASCE', 'NABTEB'] as $etypes)
                                                                              <option>{{ $etypes }}</option>
                                                                              @endforeach
                                                                          </select>
                                                                      </td>
                                                                  </tr>
                                                                  <tr>
                                                                      <td colspan="3">
                                                                          <label class="form-label"><strong>Center No</strong></label>
                                                                          <input name="first[centerNo]" type="text" class="form-control" required>
                                                                      </td>
                                                                  </tr>
                                                                  <tr>
                                                                      <td colspan="3">
                                                                          <label class="form-label"><strong>Examination No</strong></label>
                                                                          <input name="first[examNo]" type="text" class="form-control" required>
                                                                      </td>
                                                                  </tr>
                                                                  <tr>
                                                                      <td colspan="3">
                                                                          <label class="form-label"><strong>Month</strong></label>
                                                                          <select name="first[examMonth]" class="form-control" required>
                                                                              <option value="">Select Month</option>
                                                                              @foreach(['Jan','Feb','March','Apr','May','Jun','Jul','Aug','Sept','Oct','Nov','Dec'] as $month)
                                                                              <option value='{{ $month }}'>{{ $month }}</option>
                                                                              @endforeach
                                                                          </select>
                                                                      </td>
                                                                  </tr>
                                                                  <tr>
                                                                      <td colspan="3">
                                                                          <label class="form-label"><strong>Year</strong></label>
                                                                          <select name="first[examYear]" class="form-control" required>
                                                                              <option value="">Select Year</option>
                                                                              @for($syear = now()->year; $syear >= 1980; $syear--)
                                                                              <option value="{{ $syear }}">{{ $syear }}</option>
                                                                              @endfor
                                                                          </select>
                                                                      </td>
                                                                  </tr>

                                                                  <tr>
                                                                      <td colspan="3">
                                                                          <br>
                                                                          <h6>Subject & Grades</h6>
                                                                          <div class="row">
                                                                              @for ($i = 1; $i <= 8; $i++)
                                                                                  <div class="mb-1 col-md-8">
                                                                                  <select name="first[subjectName][]" class="form-control">
                                                                                      <option value="">Select Subject</option>
                                                                                      @foreach ($olevelSubjects['data'] as $subject)
                                                                                      <option>{{ $subject['subjectname'] }}</option>
                                                                                      @endforeach
                                                                                  </select>
                                                                          </div>
                                                                          <div class="mb-1 col-md-4">
                                                                              <select name="first[grade][]" class="form-control">
                                                                                  <option value="">Grade</option>
                                                                                  @foreach ($olevelGrades['data']['data'] as $grade)
                                                                                  <option>{{ $grade  }}</option>
                                                                                  @endforeach
                                                                              </select>
                                                                          </div>
                                                                          @endfor
                                                          </div>
                                                          </td>
                                                          </tr>
                                                          </table>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>

                                          <div class="col-12 col-md-6 col-lg-6">
                                              <div class="card">
                                                  <div class="card-header">
                                                      <h4>Second Sitting</h4>
                                                  </div>
                                                  <div class="card-body">
                                                      <div class="table-responsive">
                                                          <table class="table">
                                                              <tr>
                                                                  <td colspan="3">
                                                                      <label class="form-label"><strong>Examination Type</strong></label>
                                                                      <select name="second[examName]" class="form-control">
                                                                          <option value="">Select Exam Type</option>
                                                                          @foreach(['NECO', 'WAEC/WASCE', 'NABTEB'] as $etypes)
                                                                          <option>{{ $etypes }}</option>
                                                                          @endforeach
                                                                      </select>
                                                                  </td>
                                                              </tr>
                                                              <tr>
                                                                  <td colspan="3">
                                                                      <label class="form-label"><strong>Center No</strong></label>
                                                                      <input name="second[centerNo]" type="text" class="form-control">
                                                                  </td>
                                                              </tr>
                                                              <tr>
                                                                  <td colspan="3">
                                                                      <label class="form-label"><strong>Examination No</strong></label>
                                                                      <input name="second[examNo]" type="text" class="form-control">
                                                                  </td>
                                                              </tr>
                                                              <tr>
                                                                  <td colspan="3">
                                                                      <label class="form-label"><strong>Month</strong></label>
                                                                      <select name="second[examMonth]" class="form-control">
                                                                          <option value="">Select Month</option>
                                                                          @foreach(['Jan','Feb','March','Apr','May','Jun','Jul','Aug','Sept','Oct','Nov','Dec'] as $month)
                                                                          <option value='{{ $month }}'>{{ $month }}</option>
                                                                          @endforeach
                                                                      </select>
                                                                  </td>
                                                              </tr>
                                                              <tr>
                                                                  <td colspan="3">
                                                                      <label class="form-label"><strong>Year</strong></label>
                                                                      <select name="second[examYear]" class="form-control">
                                                                          <option value="">Select Year</option>
                                                                          @for($syear = now()->year; $syear >= 1980; $syear--)
                                                                          <option value="{{ $syear }}">{{ $syear }}</option>
                                                                          @endfor
                                                                      </select>
                                                                  </td>
                                                              </tr>
                                                              <tr>
                                                                  <td colspan="3"><br>
                                                                      <h6>Subject & Grades</h6>
                                                                      <div class="row">
                                                                          @for ($i = 1; $i <= 8; $i++)
                                                                              <div class="mb-1 col-md-8">
                                                                              <select name="second[subjectName][]" class="form-control">
                                                                                  <option value="">Select Subject</option>
                                                                                  @foreach ($olevelSubjects['data'] as $subject)
                                                                                  <option>{{ $subject['subjectname'] }}</option>
                                                                                  @endforeach
                                                                              </select>
                                                                      </div>
                                                                      <div class="mb-1 col-md-4">
                                                                          <select name="second[grade][]" class="form-control">
                                                                              <option value="">Grade</option>
                                                                              @foreach ($olevelGrades['data']['data'] as $grade)
                                                                              <option>{{ $grade  }}</option>
                                                                              @endforeach
                                                                          </select>
                                                                      </div>
                                                                      @endfor
                                                      </div>
                                                      </td>
                                                      </tr>
                                                      </table>
                                                  </div>
                                              </div>
                                          </div>
                                  </div>
                                  @if ($applicantStatus['biodata'] == 1 )
                                  <button class="btn btn-success"><i class="fas fa-check"></i> Save O'Level Results</button>
                                  @else
                                  <div class="alert alert-danger alert-dismissible" role="alert">
                                      <div class="alert-message">
                                          <strong>BIODATA </strong> NOT YET SAVED.
                                      </div>
                                  </div>
                                  @endif
                              </div>
                              @else
                              <table class="table table-hover my-0" style="font-size:12px">
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
                              <br />

                              @if ($applicantStatus['olevels'] == 1 )
                              <a href="{{ route('admissions.jamb') }}" class="btn btn-info">
                                  <i class="fas fa-info"></i> Click here to Continue
                              </a>
                              @endif
                              @endif
                              @endsection
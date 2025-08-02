  @extends('admissions.applicants.layout')

  @section('content')

  <!-- Main Content -->
  <div class="main-content">
      <section class="section">

          <ul class="breadcrumb breadcrumb-style">
              <li class="breadcrumb-item">
                  <h4 class="page-title m-b-0">School Attended</h4>
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
                              <h5 class="card-title mb-0"><strong>SCHOOL ATTENDED AND DATE </strong></h5>
                              <hr>
                              <form name="add_school" action="{{ route('admissions.school') }}" method="post">
                                  @csrf
                                  <div class="row">
                                      <div class="mb-3 col-md-12">
                                          <label class="form-label"><strong>Name of School</strong></label>
                                          <input type="text" class="form-control" name="schoolName" autocomplete="off"
                                              value="{{ $schoolDetails['data'][0]['schoolName'] ?? '' }}">
                                      </div>

                                      <div class="mb-3 col-md-12" id="othercolor">
                                          <label class="form-label"><strong>Registration No</strong></label>
                                          <input type="text" class="form-control" name="ndMatno" autocomplete="off"
                                              value="{{ $schoolDetails['data'][0]['ndMatno'] ?? '' }}">
                                      </div>

                                      <div class="mb-3 col-md-6">
                                          <label class="form-label"><strong>Course</strong></label>
                                          <input type="text" class="form-control" name="courseofstudy" autocomplete="off"
                                              value="{{ $schoolDetails['data'][0]['courseofStudy'] ?? '' }}">
                                      </div>

                                      <div class="mb-3 col-md-6">
                                          <label class="form-label"><strong>Grade</strong></label>
                                          <select name="grade" class="form-control" required>
                                              <option value="">Select Grade</option>
                                              @foreach (['Distinction', 'Upper Credit', 'Lower Credit', 'Pass'] as $grade)
                                              <option value="{{ $grade }}" {{ (strtolower($grade) == strtolower($schoolDetails['data'][0]['grade'] ?? '')) ? 'selected' : '' }}>
                                                  {{ $grade }}
                                              </option>
                                              @endforeach
                                          </select>
                                      </div>

                                      <div class="mb-3 col-md-6">
                                          <label class="form-label"><strong>From</strong></label>
                                          <select name="fromDate" class="form-control" required>
                                              <option value="">Select Year</option>
                                              @for ($year = now()->year; $year >= 1980; $year--)
                                              <option value="{{ $year }}" {{ (isset($schoolDetails['data'][0]['fromDate']) && $schoolDetails['data'][0]['fromDate'] == $year) ? 'selected' : '' }}>
                                                  {{ $year }}
                                              </option>
                                              @endfor
                                          </select>
                                      </div>

                                      <div class="mb-3 col-md-6">
                                          <label class="form-label"><strong>To</strong></label>
                                          <select name="toDate" class="form-control" required>
                                              <option value="">Select Year</option>
                                              @for ($year = now()->year; $year >= 1980; $year--)
                                              <option value="{{ $year }}" {{ (isset($schoolDetails['data'][0]['toDate']) && $schoolDetails['data'][0]['toDate'] == $year) ? 'selected' : '' }}>
                                                  {{ $year }}
                                              </option>
                                              @endfor
                                          </select>
                                      </div>

                                      <div class="mb-3 col-md-12">
                                          <button class="btn btn-success"><i class="fas fa-check"></i> Save School Details</button>
                                      </div>
                                  </div>
                              </form>

                              <br />

                          </div>
                      </div> @if (!empty($schoolDetails))
                      <a href="{{ route('admissions.certupload') }}" class="btn btn-info">
                          <i class="fas fa-info"></i> Click here to Save and Continue
                      </a>
                      @endif
                  </div>
              </div>
          </div>
      </section>
  </div>



  @endsection
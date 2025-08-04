  @extends('admissions.applicants.layout')

  @section('content')

  <!-- Main Content -->
  <div class="main-content">
      <section class="section">
          <ul class="breadcrumb breadcrumb-style">
              <li class="breadcrumb-item">
                  <h4 class="page-title m-b-0">UTME Results</h4>
              </li>
              <li class="breadcrumb-item">
                  <a href="{{ route('admissions.dashboard') }}">
                      <i class="fas fa-home"></i>
                  </a>
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
                                      <strong>ADD UTME RESULT DETAILS</strong>
                                      <hr />

                                      <form name="add_jamb" action="{{ route('admissions.jamb') }}" method="post">
                                          @csrf
                                          <div class="row">
                                              <div class="mb-3 col-md-12">
                                                  <label class="form-label" for="jambno"><strong>UTME No</strong></label>
                                                  <input name="jambNo" type="text" class="form-control" id="jambno"
                                                      value="{{ $jambResults['data'][0]['jambNo'] ?? '' }}"
                                                      autocomplete="off" required placeholder="Enter UTME No" minlength="14" maxlength="14">
                                              </div>

                                              <div class="col-lg-12">
                                                  <div class="alert alert-light">UTME SUBJECTS & SCORE</div>
                                              </div>

                                              @for ($i = 0; $i < 4; $i++)
                                                  @php
                                                  $savedSubject=$jambResults['data'][$i]['subjectName'] ?? '' ;
                                                  $savedScore=$jambResults['data'][$i]['jambScore'] ?? '' ;
                                                  @endphp

                                                  <div class="mb-1 col-md-6">
                                                  <select name="subjectName[]" id="subject{{ $i + 1 }}" class="form-control" required>
                                                      <option value="">Select Subject</option>
                                                      @foreach ($olevelSubjects['data'] as $subject)
                                                      <option value="{{ $subject['subjectname'] }}"
                                                          {{ $subject['subjectname'] == $savedSubject ? 'selected' : '' }}>
                                                          {{ $subject['subjectname'] }}
                                                      </option>
                                                      @endforeach
                                                  </select>
                                          </div>

                                          <div class="mb-1 col-md-6">
                                              <input type="number" class="form-control" name="jambScore[]" autocomplete="off"
                                                  value="{{ $savedScore }}" required placeholder="Enter Score" min="1" max="100">
                                          </div>
                                          @endfor
                                  </div>
                                  <br> @if ($applicantStatus['olevels'] == 1 )
                                  <button class="btn btn-success"><i class="fas fa-check"></i> Save UTME Results</button>
                                  @else
                                  <div class="alert alert-danger alert-dismissible" role="alert">
                                      <div class="alert-message">
                                          <strong>OLEVEL RESULTS </strong> NOT YET SAVED.
                                      </div>
                                  </div>
                                  @endif

                                  </form>


                              </div>
                          </div>

                          @if (!empty($jambResults['data']))
                          <a href="{{ route('admissions.school') }}" class="btn btn-info">
                              <i class="fas fa-info"></i> Click here to Save and Continue
                          </a>
                          @endif
                      </div>
                  </div>
              </div>
          </div>
  </div>
  </section>
  </div>


  @endsection
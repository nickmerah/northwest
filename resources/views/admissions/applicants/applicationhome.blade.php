  @extends('admissions.applicants.layout')

  @section('content')

  <!-- Main Content -->

  <div class="main-content">
      <section class="section">

          <ul class="breadcrumb breadcrumb-style ">
              <li class="breadcrumb-item">

                  <h4 class="page-title m-b-0">My Application</h4>
              </li>
              <li class="breadcrumb-item">
                  <a href="{{ route('admissions.dashboard') }}">
                      <i class="fas fa-home"></i></a>
              </li>
              <li class="breadcrumb-item active">Dashboard</li>
          </ul>
          <div class="row">

              <div class="col-12">
                  <div class="card">
                      <div class="card-header">
                          <h4> Application Status</h4>
                      </div>

                      <div class="card-body p-0">
                          <div class="table-responsive">
                              <table class="table table-striped" id="sortable-table">

                                  <thead>

                                      <tr>

                                          <th class="text-center">

                                              <i class="fas fa-th"></i>

                                          </th>

                                          <th>Name</th>

                                          <th>Status</th>

                                          <th>Action</th>

                                      </tr>

                                  </thead>

                                  <tbody>

                                      @php
                                      //feeId = 1 for appplication form fee
                                      $filtered = collect($applicantPayments)->filter(fn($p) => $p['feeId'] == 1);
                                      $pstat = $filtered->isEmpty() ? 0 : 1;

                                      $biodata = $applicantStatus['biodata'];
                                      $olevel = $applicantStatus['olevels'];
                                      $jambCompleted = $applicantStatus['jambResult'] == 0 ? 0 : 1;
                                      $schoolAttended = $applicantStatus['schoolattended'] == 0 ? 0 : 1;
                                      $declaration = $applicantStatus['declaration'] == 0 ? 0 : 1;
                                      $applicationSubmit = $applicantStatus['applicationSubmit'] == 0 ? 0 : 1;
                                      @endphp
                                      <tr>
                                          <td>
                                              <div class="sort-handler">
                                                  <i class="fas fa-th"></i>
                                              </div>
                                          </td>
                                          <td>Application Fee</td>
                                          <td>
                                              <div class="badge badge-{{ $pstat == 0 ? 'danger' : 'success' }}">
                                                  {{ $pstat == 0 ? 'Not Paid' : 'Paid' }}
                                              </div>
                                          </td>
                                          <td>
                                              @if ($pstat == 0)
                                              <a href="{{ route('admissions.paynow') }}" class="btn btn-danger btn-icon icon-left"> <i class="fas fa-credit-card"></i> PAY NOW</a>
                                              @else
                                              <a href="#" class="btn btn-success">PAID</a>
                                              @endif
                                          </td>
                                      </tr>
                                      <tr>
                                          <td>
                                              <div class="sort-handler">
                                                  <i class="fas fa-th"></i>
                                              </div>
                                          </td>

                                          <td>Biodata</td>

                                          <td>
                                              <div class="badge badge-{{ $biodata == 0 ? 'danger' : 'success' }}">
                                                  {{ $biodata == 0 ? 'Not Completed' : 'Completed' }}
                                              </div>
                                          </td>

                                          <td>
                                              <a href="{{ route('admissions.biodata') }}" class="btn btn-{{ $biodata == 0 ? 'warning' : 'success' }}">
                                                  {{ $biodata == 0 ? 'Start' : 'Completed' }}
                                              </a>
                                          </td>
                                      </tr>

                                      <tr>
                                          <td>
                                              <div class="sort-handler">
                                                  <i class="fas fa-th"></i>
                                              </div>
                                          </td>

                                          <td>O'Level</td>

                                          <td>
                                              <div class="badge badge-{{ $olevel == 0 ? 'danger' : 'success' }}">
                                                  {{ $olevel == 0 ? 'Not Completed' : 'Completed' }}
                                              </div>
                                          </td>

                                          <td>
                                              <a href="{{ route('admissions.olevel') }}" class="btn btn-{{ $olevel == 0 ? 'warning' : 'success' }}">
                                                  {{ $olevel == 0 ? 'Start' : 'Completed' }}
                                              </a>
                                          </td>
                                      </tr>

                                      {{-- @if ($applicantProgramme == 1 && $applicantProgrammeType == 1)--}}

                                      <tr>
                                          <td>
                                              <div class="sort-handler">
                                                  <i class="fas fa-th"></i>
                                              </div>
                                          </td>

                                          <td>JAMB/UTME Result</td>

                                          <td>
                                              <div class="badge badge-{{ !$jambCompleted ? 'danger' : 'success' }}">
                                                  {{ !$jambCompleted ? 'Not Completed' : 'Completed' }}
                                              </div>
                                          </td>

                                          <td>
                                              <a href="{{ route('admissions.jamb') }}" class="btn btn-{{ !$jambCompleted ? 'warning' : 'success' }}">
                                                  {{ !$jambCompleted ? 'Start' : 'Completed' }}
                                              </a>
                                          </td>
                                      </tr>
                                      {{-- @endif--}}
                                      {{-- @if ($applicantProgramme == 2)--}}
                                      <tr>
                                          <td>
                                              <div class="sort-handler">
                                                  <i class="fas fa-th"></i>
                                              </div>
                                          </td>

                                          <td>Schools Attended </td>

                                          <td>
                                              <div class="badge badge-{{ $schoolAttended == 0 ? 'danger' : 'success' }}">
                                                  {{ $schoolAttended == 0 ? 'Not Completed' : 'Completed' }}
                                              </div>
                                          </td>

                                          <td>
                                              <a href="{{ route('admissions.school') }}" class="btn btn-{{ $schoolAttended == 0 ? 'warning' : 'success' }}">
                                                  {{ $schoolAttended == 0 ? 'Start' : 'Completed' }}
                                              </a>
                                          </td>
                                      </tr>
                                      {{-- @endif--}}
                                      <tr>
                                          <td>
                                              <div class="sort-handler">
                                                  <i class="fas fa-th"></i>
                                              </div>
                                          </td>

                                          <td>Declaration</td>

                                          <td>
                                              <div class="badge badge-{{ $declaration == 0 ? 'danger' : 'success' }}">
                                                  {{ $declaration == 0 ? 'Not Completed' : 'Completed' }}
                                              </div>
                                          </td>

                                          <td>
                                              <a href="{{ route('admissions.declaration') }}" class="btn btn-{{ $declaration == 0 ? 'warning' : 'success' }}">
                                                  {{ $declaration == 0 ? 'Start' : 'Completed' }}
                                              </a>
                                          </td>
                                      </tr>

                                  </tbody>
                              </table>
                          </div>
                      </div>

                      @if ($applicationSubmit)
                      <a href="{{ url('applicant/application_forms') }}" class="btn btn-success">
                          <i class="fas fa-check"></i> Proceed to Print Your Application Forms
                      </a>
                      @elseif (
                      $biodata &&
                      $schoolAttended &&
                      $olevel &&
                      $declaration &&
                      $applicantProgramme == 2
                      )
                      <a href="{{ url('applicant/application_preview') }}" class="btn btn-warning">
                          <i class="fas fa-check"></i> Preview & Submit Application
                      </a>
                      @elseif (
                      $biodata &&
                      $olevel &&
                      $jambCompleted &&
                      $declaration &&
                      $applicantProgramme == 1
                      )
                      <a href="{{ url('applicant/application_preview') }}" class="btn btn-warning">
                          <i class="fas fa-check"></i> Preview & Submit Application
                      </a>
                      @endif

                  </div>

              </div>

          </div>

          @endsection
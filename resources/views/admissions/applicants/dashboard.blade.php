  @extends('admissions.applicants.layout')

  @section('content')

  <div class="main-content">
      <section class="section">

          <ul class="breadcrumb breadcrumb-style ">
              <li class="breadcrumb-item">
                  <h4 class="page-title m-b-0">Dashboard</h4>
              </li>
              <li class="breadcrumb-item">
                  <a href="{{ route('admissions.dashboard') }}">
                      <i class="fas fa-home"></i></a>
              </li>
              <li class="breadcrumb-item active">Dashboard</li>
          </ul>


          <div class="alert alert-light alert-has-icon">
              <div class="alert-icon"><i class="far fa-user"></i></div>
              <div class="alert-body">
                  <div class="alert-title"> <small>
                          {{ $data->user['applicationNumber'] }} | <strong> {{ $data->user['surname'] }}</strong>, {{ $data->user['firstname'] }} {{ $data->user['othernames'] }} |
                          <strong> {{ $data->user['programme']['aprogramme_name'] }} {{ $data->user['firstChoiceCourse']['programme_option'] }} </strong>|
                          {{ $data->stats['appyear'] }} / {{ $data->stats['appyear'] + 1 }}</small>

                  </div>

              </div>
          </div>

          <div class="marquee-container">
              <div class="marquee">
                  If you have successfully made payment and its not reflecting, click on 'Check Payment' to reprocess.
              </div>
          </div>
          <div class="row">

              <div class="col-lg-4 col-sm-4">
                  <div class="card">
                      <div class="card-statistic-4">
                          <div class="info-box7-block">
                              <h6 class="m-b-20 text-right">Application Fee</h6>
                              <h4 class="text-right"><i class="fas fa-dollar-sign pull-left bg-green c-icon"></i><span>&#8358;
                                      @foreach ($data->applicationFee as $fee)
                                      @if ($fee['feeId'] == 1)
                                      {{ number_format($fee['amount']) }}
                                      @endif
                                      @endforeach
                                  </span>
                              </h4>

                          </div>
                      </div>
                  </div>
              </div>


              <div class="col-lg-4 col-sm-6">
                  <div class="card">
                      <div class="card-statistic-4">
                          <div class="info-box7-block">
                              <h6 class="m-b-20 text-right">Application Status</h6>
                              <h4 class="text-right"><i class="fas fa-users pull-left bg-cyan c-icon"></i><span>
                                      @if ($data->stats['applicationSubmit'])
                                      <b style="color:green">SUBMITTED</b>
                                      @else
                                      <b style="color:red">NOT SUBMITTED</b>
                                      @endif

                                  </span>
                              </h4>

                          </div>
                      </div>
                  </div>
              </div>

              <div class="col-lg-4 col-sm-6">
                  <div class="card">
                      <div class="card-statistic-4">
                          <div class="info-box7-block">
                              <h6 class="m-b-20 text-right">Admission Status</h6>
                              <h4 class="text-right"><i class="fas fa-user-tag pull-left bg-red c-icon"></i><span>
                                      @if ($data->stats['admissionStatus'])
                                      <b style="color:green">ADMITTED</b>
                                      @else
                                      <b style="color:red">NOT ADMITTED</b>
                                      @endif

                                  </span>
                              </h4>

                          </div>
                      </div>
                  </div>
              </div>
          </div>



          <div class="row">
              <div class="col-12 col-sm-12 col-lg-12">
                  <div class="activities">

                      <div class="activity">
                          <div class="activity-icon bg-success text-white">
                              <i class="fas fa-money-bill"></i>
                          </div>
                          <div class="activity-detail">
                              <div class="mb-2">
                                  <span class="text-job">
                                      <h6>Application Fee </h6>
                                  </span>

                              </div>
                              <p><span class="font-13"> @php
                                      $hasApplicationFee = false;

                                      if (!empty($data->applicationPaymentStatus)) {
                                      foreach ($data->applicationPaymentStatus as $apayment) {
                                      if (isset($apayment['feeId']) && $apayment['feeId'] == 1) {
                                      $hasApplicationFee = true;
                                      break;
                                      }
                                      }
                                      }
                                      @endphp

                                      @if ($hasApplicationFee)
                                      <b style="color:green">PAID</b>
                                      @else
                                      <b style="color:red">NOT PAID | <a href="{{ route('admissions.paynow') }}">PAY NOW</a></b>
                                      @endif </span></p>
                          </div>
                      </div>


                      <div class=" activity">
                          <div class="activity-icon bg-info text-white">
                              <i class="fas fa-user-alt"></i>
                          </div>
                          <div class="activity-detail">
                              <div class="mb-2">
                                  <span class="text-job">
                                      <h6>Biodata </h6>
                                  </span>

                              </div>
                              <p><span class="font-13"> @if ($data->stats['biodata'] == 1)
                                      <b style="color:green">COMPLETED</b>
                                      @else
                                      <b style="color:red">INCOMPLETE</b>
                                      @endif</span></p>
                          </div>
                      </div>

                      <div class="activity">
                          <div class="activity-icon bg-warning text-white">
                              <i class="fab fa-wpforms"></i>
                          </div>
                          <div class="activity-detail">
                              <div class="mb-2">
                                  <span class="text-job">
                                      <h6>Application Forms </h6>
                                  </span>

                              </div>
                              <p><span class="font-13"> @if ($data->stats['applicationSubmit'])
                                      <b style="color:green">SUBMITTED</b>
                                      @else
                                      <b style="color:red">NOT SUBMITTED</b>
                                      @endif </span></p>
                          </div>
                      </div>

                      <div class="activity">
                          <div class="activity-icon bg-danger text-white">
                              <i class="fas fa-user-graduate"></i>
                          </div>
                          <div class="activity-detail">
                              <div class="mb-2">
                                  <span class="text-job">
                                      <h6>Admission Status </h6>
                                  </span>

                              </div>
                              <p><span class="font-13"> @if ($data->stats['admissionStatus'])
                                      <b style="color:green">ADMITTED</b>
                                      @else
                                      <b style="color:red">NOT ADMITTED</b>
                                      @endif </span></p>

                          </div>
                      </div>

                      <div class="activity">
                          <div class="activity-icon bg-secondary text-white">
                              <i class="fas fa-money-bill-alt"></i>
                          </div>
                          <div class="activity-detail">
                              <div class="mb-2">
                                  <span class="text-job">
                                      <h6>Acceptance Fee </h6>
                                  </span>

                              </div>
                              <p><span class="font-13"> @php
                                      $hasAcceptanceFee = false;

                                      if (!empty($data->applicationPaymentStatus)) {
                                      foreach ($data->applicationPaymentStatus as $payment) {
                                      if (isset($payment['feeId']) && $payment['feeId'] == 2) {
                                      $hasAcceptanceFee = true;
                                      break;
                                      }
                                      }
                                      }
                                      @endphp

                                      @if ($hasAcceptanceFee)
                                      <b style="color:green">PAID</b>
                                      @else
                                      <b style="color:red">NOT PAID</b>
                                      @endif </span></p>
                          </div>
                      </div>

                      <div class="activity">
                          <div class="activity-icon bg-primary text-white">
                              <i class="fas fa-money-bill-alt"></i>
                          </div>
                          <div class="activity-detail">
                              <div class="mb-2">
                                  <span class="text-job">
                                      <h6>Result Verification Fee </h6>
                                  </span>

                              </div>
                              <p><span class="font-13"> @php
                                      $hasResultVerificationFee = false;

                                      if (!empty($data->applicationPaymentStatus)) {
                                      foreach ($data->applicationPaymentStatus as $vpayment) {
                                      if (isset($vpayment['feeId']) && $vpayment['feeId'] == 4) {
                                      $hasResultVerificationFee = true;
                                      break;
                                      }
                                      }
                                      }
                                      @endphp

                                      @if ($hasResultVerificationFee)
                                      <b style="color:green">PAID</b>
                                      @else
                                      <b style="color:red">NOT PAID</b>
                                      @endif </span></p>
                          </div>
                      </div>


                  </div>
              </div>
          </div>


  </div>

  </section>

  </div>
  @endsection
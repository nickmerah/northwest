  @extends('admissions.applicants.layout')

  @section('content')

  <!-- Main Content -->

  <div class="main-content">

      <section class="section">

          <ul class="breadcrumb breadcrumb-style ">

              <li class="breadcrumb-item">

                  <h4 class="page-title m-b-0">e-Invoice</h4>

              </li>

              <li class="breadcrumb-item">

                  <a href="{{ route('admissions.dashboard') }}">

                      <i class="fas fa-home"></i></a>

              </li>

              <li class="breadcrumb-item active">Pay Now</li>

          </ul>

          <div class="invoice">

              <div class="invoice-print">

                  <div class="row">

                      <div class="col-lg-12">

                          <div class="invoice-title">

                              <h2>e-Invoice - @php
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
                                  <b style="color:red">NOT PAID </b>
                                  @endif
                              </h2>



                          </div>

                          <hr>

                          <div class="row">

                              <div class="col-md-6">

                                  <address>

                                      <strong>Billed To:</strong><br>

                                      <strong> {{ $data->user['surname'] }}</strong>, {{ $data->user['firstname'] }} {{ $data->user['othernames'] }}<br>

                                      {{ $data->user['applicationNumber'] }} <br>

                                      {{ $data->user['programme']['aprogramme_name'] }} {{ $data->user['firstChoiceCourse']['programme_option'] }}<br>

                                      {{ $data->stats['appyear'] }} / {{ $data->stats['appyear'] + 1 }} Session

                                  </address>

                              </div>
                              <div class="col-md-6 text-md-right">
                                  <address>
                                      <strong>From:</strong><br>
                                      {{ $SCHOOLNAME }}
                                  </address>
                              </div>
                          </div>

                          <div class="row">

                              <div class="col-md-6">

                                  <address>

                                      <strong>Order Date:</strong><br>

                                      {{ date('d M Y h:i:s') }} 

                                  </address>

                              </div>



                          </div>

                      </div>

                  </div>

                  <div class="row mt-4">

                      <div class="col-md-12">

                          <div class="section-title">Payment Details</div>



                          <div class="table-responsive">

                              <table class="table table-striped table-hover table-md">

                                  <tr>

                                      <th data-width="40">#</th>

                                      <th>Fee Item</th>

                                      <th class="text-center">Amount</th>



                                      <th class="text-right">Total</th>

                                  </tr>

                                  <tr>

                                      <td>1</td>

                                      <td>{{ $data->applicationFee[0]['feeName'] }}</td>
                                      @php

                                      $amount = $data->applicationFee[0]['amount'] ??= 0;
                                      @endphp
                                      <td class="text-center">&#8358; {{ number_format($amount) }} </td>

                                      <td class="text-right">&#8358; {{ number_format($amount) }}</td>

                                  </tr>





                              </table>

                          </div>

                          <div class="row mt-4">

                              <div class="col-lg-8">

                                  <div class="section-title">Payment Method</div>

                                  <p class="section-lead">The payment method that we provide is to make it easier for you to pay

                                      e-invoices.</p>
                              </div>
                              <hr class="mt-2 mb-2">
                              <div class="invoice-detail-item">
                                  <div class="invoice-detail-name">Total</div>
                                  <div class="invoice-detail-value invoice-detail-value-lg">&#8358; {{ number_format($amount) }}</div>
                              </div>
                          </div>
                      </div>
                      <hr>
                      <form id="pay_now" name="pay_now" action="{{ route('admissions.processpayment') }}" method="post">
                          @csrf
                          <div class="text-md-right">

                              <div class="float-lg-left mb-lg-0 mb-3">

                                  @if ($hasApplicationFee)
                                  <b style="color:green"><button type='button' class='btn btn-danger btn-icon icon-left'><i class='fas fa-credit-card'></i> PAYMENT ALREADY MADE</button></b>
                                  @else
                                  <b style="color:red"><button type='submit' class='btn btn-success btn-icon icon-left'><i class='fas fa-credit-card'></i> PAY NOW</button> </b>
                                  <input type="hidden" name="feeId" value="{{ $feeId }}">
                                  @endif
                              </div>
                          </div>
                      </form>
                  </div>

              </div>

          </div>


  </div>
  </div>
  @endsection
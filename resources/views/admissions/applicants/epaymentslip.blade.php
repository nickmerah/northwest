  @extends('admissions.applicants.layout')

  @section('content')

  <!-- Main Content -->

  <div class="main-content">
      <section class="section">

          <ul class="breadcrumb breadcrumb-style ">
              <li class="breadcrumb-item">

                  <h4 class="page-title m-b-0">e-Payment Slip</h4>
              </li>
              <li class="breadcrumb-item">
                  <a href="{{ route('admissions.dashboard') }}">
                      <i class="fas fa-home"></i></a>
              </li>
              <li class="breadcrumb-item active">Dashboard</li>
          </ul>
          <div class="invoice">
              <div class="invoice-print">

                  <div class="row">
                      <div class="col-lg-12">
                          <div class="invoice-title">
                              <h2>e-Invoice - {{ $paymentData->paymentDetails['paymentStatus'] }}</h2>

                          </div>
                          <hr>
                          <div class="row">
                              <div class="col-md-6">
                                  <address>
                                      <strong>Billed To:</strong><br>
                                      <strong> {{ $paymentData->paymentDetails['fullNames'] }}</strong><br>
                                      {{ $paymentData->paymentDetails['applicationNo'] }} <br>
                                      TransactionID: <b> {{ $paymentData->paymentDetails['transactionID'] }} </b><br>
                                      {{ $paymentData->paymentDetails['appYear'] }} / {{ $paymentData->paymentDetails['appYear'] + 1 }} Session
                                  </address>
                              </div>
                              <div class="col-md-6 text-md-right">
                                  <address>
                                      <strong>From:</strong><br>
                                      {{ $SCHOOLNAME }}<br>
                                  </address>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-md-6">
                                  <address>
                                      <strong>Generated Date:</strong><br>
                                      {{ date('d-M-Y', strtotime($paymentData->paymentDetails['transactionDate'])) }}
                                  </address>
                              </div>

                          </div>
                      </div>
                  </div>
                  <div class="col-md-12">
                      <div class="section-title">Payment Details</div>

                      <div class="table-responsive">
                          <table class="table table-striped table-hover table-md">
                              <tr>
                                  <th>Fee Item</th>
                                  <th class="text-center">Amount</th>
                                  <th class="text-right">Total</th>
                              </tr>
                              <tr>
                                  <td>{{ $paymentData->paymentDetails['feeName'] }}</td>
                                  @php

                                  $amount = $paymentData->paymentDetails['feeAmount'] ??= 0;
                                  @endphp
                                  <td class="text-center">&#8358;{{ number_format($amount) }} </td>

                                  <td class="text-right">&#8358; {{ number_format($amount) }} </td>
                              </tr>
                          </table>
                      </div>
                      <div class="row mt-4">
                          <div class="col-lg-8">
                              <div class="section-title"></div>
                              <p class="section-lead">

                                  <img src="{{ asset('public/assets/img/cards/remita.png') }}" alt="paystack" width="160px" class="center" />

                                  <a href="{{ $paymentData->paymentDetails['paymentUrl'] }}" class="btn btn-success btn-icon icon-left" target="_blank">
                                      PAY ONLINE NOW </a>

                              </p>

                          </div>


                          <hr class="mt-2 mb-2">
                          <div class="invoice-detail-item">
                              <div class="invoice-detail-name">Total</div>
                              <div class="invoice-detail-value invoice-detail-value-lg">&#8358; {{ number_format($amount) }} </div>
                          </div>
                          <hr class="mt-2 mb-2">

                      </div>
                  </div>
              </div>
          </div>

          <hr>

  </div>

  @endsection
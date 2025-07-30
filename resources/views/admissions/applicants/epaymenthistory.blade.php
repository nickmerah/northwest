  @extends('admissions.applicants.layout')

  @section('content')

  <!-- Main Content -->

  <div class="main-content">
      <section class="section">

          <ul class="breadcrumb breadcrumb-style ">
              <li class="breadcrumb-item">

                  <h4 class="page-title m-b-0">e-Payment History</h4>
              </li>
              <li class="breadcrumb-item">
                  <a href="{{ route('admissions.dashboard') }}">
                      <i class="fas fa-home"></i></a>
              </li>
              <li class="breadcrumb-item active">Dashboard</li>
          </ul>
          <div class="invoice">
              <div class="invoice-print">
                  <div class="col-md-12">
                      <div class="section-title">Payment History</div>

                      <div class="table-responsive">
                          <table class="table table-striped table-hover table-md">
                              <tr>
                                  <th class="text-center">Fee Name</th>
                                  <th class="text-center">Transaction ID</th>
                                  <th class="text-center">Amount</th>
                                  <th class="text-center">Date Paid</th>
                                  <th class="text-center">Action</th>
                              </tr>

                              @forelse($paymentDatas as $paymentData)
                              <tr>
                                  <td class="text-center">{{ $paymentData['feeName'] ?? '-' }}</td>
                                  <td class="text-center">{{ $paymentData['transactionID'] ?? '-' }}</td>
                                  <td class="text-center">₦{{ number_format($paymentData['amount'] ?? 0) }}</td>
                                  <td class="text-center">{{ $paymentData['datePaid'] ?? '-' }}</td>
                                  <td class="text-center">
                                      <a href="{{ route('admissions.receipt', $paymentData['transactionID']) }}" class="btn btn-success" target="_blank">
                                          <i class="fas fa-print"></i> Print Receipt
                                      </a>
                                  </td>
                              </tr>
                              @empty
                              <tr>
                                  <td class="text-center" colspan="5">No payment record found.</td>
                              </tr>
                              @endforelse
                          </table>

                      </div>

                  </div>
              </div>
          </div>
          <a href="{{ route('admissions.checkpayment') }}" class="btn btn-info"><i class="fas fa-dollar-sign"></i> Check Payment Status</a>
          <hr>
  </div>

  @endsection
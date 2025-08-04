  @extends('admissions.applicants.layout')

  @section('content')

  <!-- Main Content -->
  <div class="main-content">
      <section class="section">
          <ul class="breadcrumb breadcrumb-style">
              <li class="breadcrumb-item">
                  <h4 class="page-title m-b-0">Certificates Upload</h4>
              </li>
              <li class="breadcrumb-item">
                  <a href="{{ url('applicant') }}">
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
                              @if(session('success'))
                              <div class="alert alert-success">
                                  {{ session('success') }}
                              </div>
                              @endif

                              @if(session('error'))
                              <div class="alert alert-danger">
                                  {{ session('error') }}
                              </div>
                              @endif

                              @if ($certificates->status != 'error')

                              <table class="table table-bordered" style="font-size: 13px;">
                                  <thead>
                                      <tr>
                                          <th>DOCUMENT NAME</th>
                                          <th>ACTION</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      @foreach($certificates->data as $docs)
                                      <tr>
                                          <td>{{ $docs['documentName']}}</td>
                                          <td>
                                              <a href="{{ Storage::disk('public')->url('app/public/documents/' . $docs['uploadName']) }}" target="_blank">
                                                  View Document
                                              </a>
                                          </td>
                                      </tr>
                                      @endforeach
                                  </tbody>
                              </table>
                              <br>
                              <a class="btn btn-danger"
                                  href="{{ route('admissions.removecert') }}"
                                  onclick="return confirm('Are you sure you want to re-upload the documents? This will remove any existing uploaded documents.');">
                                  ReUpload Documents
                              </a>
                              @else
                              <hr />
                              <strong>UPLOAD DOCUMENT</strong>

                              <div class="alert alert-warning">
                                  <strong>File Upload Guidelines:</strong>
                                  <ul>
                                      <li>Only PDF files are allowed.</li>
                                      <li>Minimum file size: 100KB.</li>
                                  </ul>
                              </div>
                              <hr />

                              <form name="addcert" action="{{ route('admissions.certupload') }}" method="post" enctype="multipart/form-data">
                                  @csrf
                                  <div class="row">

                                      <div class="mb-3 col-md-12">
                                          <label class="form-label"><strong>O’ Level Result (If you have 2 results, combine in one file)</strong></label>
                                          <input name="o_level_result" type="file" class="form-control" required accept=".pdf">
                                      </div>
                                      <div class="mb-3 col-md-12">
                                          <label class="form-label"><strong>Jamb Result</strong></label>
                                          <input name="jamb_result" type="file" class="form-control" required accept=".pdf">
                                      </div>
                                      <div class="mb-3 col-md-12">
                                          <label class="form-label"><strong>Birth Certificate</strong></label>
                                          <input name="birth_certificate" type="file" class="form-control" required accept=".pdf">
                                      </div>
                                  </div>
                                  @if ($applicantStatus['schoolattended'] == 1 )
                                  <button type="submit" class="btn btn-primary">Upload Documents</button>
                                  @else
                                  <div class="alert alert-danger alert-dismissible" role="alert">
                                      <div class="alert-message">
                                          <strong>SCHOOL ATTENDED </strong> NOT YET SAVED.
                                      </div>
                                  </div>
                                  @endif
                              </form>
                              @endif
                          </div>
                      </div>@if ($certificates->status != 'error')
                      <a href="{{ route('admissions.declaration') }}" class="btn btn-info">
                          <i class="fas fa-info"></i> Click here to Save and Continue
                      </a>
                      @endif
                  </div>
              </div>
          </div>
      </section>
  </div>

  @endsection